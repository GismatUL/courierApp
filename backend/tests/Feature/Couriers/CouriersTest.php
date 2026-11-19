<?php

namespace Tests\Feature\Couriers;

use App\Modules\Access\Database\Seeders\StaffSeeder;
use App\Modules\Access\Livewire\Login;
use App\Modules\Access\Livewire\Users;
use App\Modules\Access\Models\User;
use App\Modules\Couriers\Enums\BalanceChannel;
use App\Modules\Couriers\Livewire\CourierEditor;
use App\Modules\Couriers\Livewire\Couriers;
use App\Modules\Couriers\Models\Courier;
use App\Modules\Couriers\Services\CourierBalance;
use App\Modules\Couriers\Services\CourierService;
use App\Modules\Network\Livewire\Hubs;
use App\Modules\Network\Models\Hub;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class CouriersTest extends TestCase
{
    use RefreshDatabase;

    private Hub $hub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(StaffSeeder::class);
        $this->actingAs(User::factory()->superAdmin()->create());
        $this->hub = Hub::create(['name' => 'Bakı', 'address' => 'Nizami 1', 'latitude' => 40.4, 'longitude' => 49.8]);
    }

    private function courier(array $account = [], array $profile = []): Courier
    {
        return app(CourierService::class)->save(
            null,
            $account + ['name' => 'Nazim', 'email' => 'nazim'.uniqid().'@kuryer.test', 'mobile' => '994501112233', 'password' => 'courier-pass-1', 'status' => true],
            $profile + ['hub_id' => $this->hub->id, 'type' => 'company', 'car_type' => 'car', 'min_delivery_parcel' => 0, 'opening_balance' => 0],
        );
    }

    private function editor(): Testable
    {
        return Livewire::test(CourierEditor::class)
            ->set('account.name', 'Nazim Əliyev')->set('account.email', 'nazim@kuryer.test')
            ->set('account.mobile', '994501112233')->set('account.password', 'courier-pass-1')
            ->set('profile.hub_id', (string) $this->hub->id);
    }

    public function test_pages_render_and_require_permission(): void
    {
        $courier = $this->courier();

        $this->get('/admin/couriers')->assertOk()->assertSee('Nazim');
        $this->get('/admin/couriers/create')->assertOk();
        $this->get("/admin/couriers/{$courier->id}/edit")->assertOk();

        $this->actingAs(User::factory()->withPermissions(['delivery_man_read'])->create());
        $this->get('/admin/couriers')->assertOk()->assertDontSee('Əlavə et');
        $this->get('/admin/couriers/create')->assertForbidden();
    }

    public function test_create_makes_an_app_only_account_with_courier_role_and_cash_opening_balance(): void
    {
        $this->editor()->set('profile.opening_balance', '25.50')->set('profile.car_type', 'motorbike')
            ->call('save')->assertHasNoErrors()->assertRedirect(route('admin.couriers.index'));

        $courier = Courier::with('user.role')->firstOrFail();
        $this->assertFalse($courier->user->panel_access);
        $this->assertSame('delivery-man', $courier->user->role->slug);
        $this->assertTrue(Hash::check('courier-pass-1', $courier->user->password));
        $this->assertSame('25.50', $courier->opening_balance);
        $this->assertSame('25.50', $courier->balance_cash);
        $this->assertSame('25.50', $courier->currentBalance());
        $this->assertSame('motorbike', $courier->car_type->value);
    }

    public function test_validation(): void
    {
        $this->courier(['email' => 'taken@kuryer.test']);

        Livewire::test(CourierEditor::class)
            ->set('account.email', 'taken@kuryer.test')->set('account.mobile', '050111')
            ->set('profile.voen', '123')->set('profile.car_type', 'rocket')->set('profile.address', 'Yasamal')
            ->set('profile.opening_balance', '-5')
            ->call('save')
            ->assertHasErrors(['account.name', 'account.email' => 'unique', 'account.mobile', 'account.password', 'profile.hub_id',
                'profile.voen', 'profile.car_type', 'profile.latitude', 'profile.opening_balance']);
    }

    public function test_edit_keeps_password_and_opening_balance(): void
    {
        $courier = $this->courier(profile: ['opening_balance' => 10]);

        Livewire::test(CourierEditor::class, ['courier' => $courier])
            ->assertSet('account.name', 'Nazim')
            ->set('account.name', 'Nazim Əliyev')->set('profile.delivery_charge', '2')
            ->call('save')->assertHasNoErrors();

        $courier->refresh();
        $this->assertSame('Nazim Əliyev', $courier->user->name);
        $this->assertSame('2.00', $courier->delivery_charge);
        $this->assertSame('10.00', $courier->opening_balance);
        $this->assertTrue(Hash::check('courier-pass-1', $courier->user->password));
    }

    public function test_courier_cannot_sign_in_to_the_admin_panel_and_is_not_listed_as_a_user(): void
    {
        $courier = $this->courier(['email' => 'app@kuryer.test']);

        Livewire::test(Users::class)->assertDontSee('app@kuryer.test');

        auth()->logout();
        Livewire::test(Login::class)->set('email', 'app@kuryer.test')->set('password', 'courier-pass-1')->call('login')
            ->assertHasErrors(['email' => 'Email və ya şifrə yanlışdır.']);
        $this->assertGuest();

        // An already-open session is ended on the next request.
        $this->actingAs($courier->user)->get('/admin')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_reset_device(): void
    {
        $courier = $this->courier();
        $courier->forceFill(['device_id' => 'pixel-8'])->save();

        Livewire::test(Couriers::class)->assertSee('Cihazı sıfırla')->call('resetDevice', $courier->id)->assertDispatched('toast');
        $this->assertNull($courier->fresh()->device_id);
    }

    public function test_balance_credit_and_debit_per_channel_never_below_zero(): void
    {
        $courier = $this->courier();
        $balance = app(CourierBalance::class);

        $balance->credit($courier, BalanceChannel::Cash, 30);
        $balance->credit($courier, BalanceChannel::Pos, '12.40');
        $balance->debit($courier, BalanceChannel::Cash, 10);

        $courier->refresh();
        $this->assertSame(['20.00', '12.40', '0.00'], [$courier->balance_cash, $courier->balance_pos, $courier->balance_card]);
        $this->assertSame('32.40', $courier->currentBalance());

        $this->expectException(DomainException::class);
        $balance->debit($courier, BalanceChannel::Pos, 12.41);
    }

    public function test_balance_rejects_non_positive_amounts(): void
    {
        $this->expectException(DomainException::class);
        app(CourierBalance::class)->credit($this->courier(), BalanceChannel::Card, 0);
    }

    public function test_courier_with_balance_cannot_be_deleted_otherwise_account_and_files_go_too(): void
    {
        Storage::fake('local');
        $rich = $this->courier(profile: ['opening_balance' => 5]);
        Livewire::test(Couriers::class)->call('confirmDelete', $rich->id)->call('delete')->assertDispatched('toast', type: 'error');
        $this->assertModelExists($rich);

        $courier = $this->courier();
        app(CourierService::class)->storeFile($courier, 'driving_license_file', UploadedFile::fake()->image('licence.jpg'));
        $path = $courier->fresh()->driving_license_file;
        $userId = $courier->user_id;

        Livewire::test(Couriers::class)->call('confirmDelete', $courier->id)->call('delete');
        $this->assertModelMissing($courier);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_documents_are_private_and_need_permission(): void
    {
        Storage::fake('local');
        $courier = $this->courier();
        app(CourierService::class)->storeFile($courier, 'driving_license_file', UploadedFile::fake()->create('licence.pdf', 50, 'application/pdf'));

        $this->get(route('admin.couriers.file', [$courier, 'driving_license_file']))->assertOk()->assertDownload()->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->actingAs(User::factory()->withPermissions(['hub_read'])->create());
        $this->get(route('admin.couriers.file', [$courier, 'driving_license_file']))->assertForbidden();
    }

    public function test_filters_by_hub_and_name(): void
    {
        $ganja = Hub::create(['name' => 'Gəncə', 'address' => 'x', 'latitude' => 40.6, 'longitude' => 46.3]);
        $this->courier(['name' => 'Bakı Kuryeri']);
        $this->courier(['name' => 'Gəncə Kuryeri'], ['hub_id' => $ganja->id]);

        Livewire::test(Couriers::class)->set('hubId', (string) $ganja->id)->assertSee('Gəncə Kuryeri')->assertDontSee('Bakı Kuryeri');
        Livewire::test(Couriers::class)->set('name', 'bakı')->assertSee('Bakı Kuryeri')->assertDontSee('Gəncə Kuryeri');
    }

    public function test_hub_with_couriers_cannot_be_deleted(): void
    {
        $this->courier();

        Livewire::test(Hubs::class)->call('confirmDelete', $this->hub->id)->call('delete')->assertDispatched('toast', type: 'error');
        $this->assertModelExists($this->hub);
    }
}
