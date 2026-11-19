<?php

namespace Tests\Feature\Merchants;

use App\Modules\Access\Models\User;
use App\Modules\Merchants\Livewire\MerchantCharges;
use App\Modules\Merchants\Livewire\MerchantEditor;
use App\Modules\Merchants\Livewire\MerchantPaymentAccounts;
use App\Modules\Merchants\Livewire\Merchants;
use App\Modules\Merchants\Livewire\MerchantShops;
use App\Modules\Merchants\Livewire\MerchantTypes;
use App\Modules\Merchants\Models\Merchant;
use App\Modules\Merchants\Models\MerchantDeliveryCharge;
use App\Modules\Merchants\Models\MerchantShop;
use App\Modules\Merchants\Services\MerchantService;
use App\Modules\Network\Database\Seeders\NetworkSeeder;
use App\Modules\Network\Livewire\Hubs;
use App\Modules\Network\Models\Hub;
use App\Modules\Network\Models\PackageType;
use App\Modules\Network\Models\Zone;
use App\Shared\Support\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class MerchantsTest extends TestCase
{
    use RefreshDatabase;

    private Hub $hub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->superAdmin()->create());
        $this->hub = Hub::create(['name' => 'Bakı', 'address' => 'Nizami 1', 'latitude' => 40.4, 'longitude' => 49.8]);
    }

    private function merchant(array $attributes = []): Merchant
    {
        return app(MerchantService::class)->save(null, $attributes + ['business_name' => 'AZERCELL', 'opening_balance' => 0], [$this->hub->id]);
    }

    private function editor(): Testable
    {
        return Livewire::test(MerchantEditor::class)
            ->set('data.business_name', 'Yelo Bank')
            ->set('hubIds', [(string) $this->hub->id]);
    }

    public function test_pages_render_and_require_permission(): void
    {
        $merchant = $this->merchant();

        foreach (['/admin/merchants', '/admin/merchants/create', "/admin/merchants/{$merchant->id}", "/admin/merchants/{$merchant->id}/edit", '/admin/merchant-types'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get("/admin/merchants/{$merchant->id}?tab=charges")->assertOk()->assertSeeLivewire(MerchantCharges::class);

        $this->actingAs(User::factory()->withPermissions(['merchant_read'])->create());
        $this->get('/admin/merchants')->assertOk()->assertDontSee('Əlavə et');
        $this->get("/admin/merchants/{$merchant->id}")->assertForbidden();
        $this->get('/admin/merchants/create')->assertForbidden();
    }

    public function test_merchant_permission_rows_come_from_the_module(): void
    {
        $this->assertSame('Partnyor', Permissions::modules()['merchant']['label']);
        $this->assertContains('merchant_payment_delete', Permissions::all());
        $this->assertSame(array_values(array_unique(Permissions::all())), Permissions::all());
    }

    public function test_create_assigns_unique_id_opening_balance_hubs_and_hashed_password(): void
    {
        $this->editor()
            ->set('data.email', 'yelo@kuryer.test')->set('data.mobile', '994501234567')
            ->set('data.password', 'merchant-pass-1')->set('data.opening_balance', '-13.90')
            ->call('save')
            ->assertHasNoErrors();

        $merchant = Merchant::where('business_name', 'Yelo Bank')->firstOrFail();
        $this->assertMatchesRegularExpression('/^\d{6}$/', $merchant->unique_id);
        $this->assertSame('-13.90', $merchant->current_balance);
        $this->assertSame([$this->hub->id], $merchant->hubs->pluck('id')->all());
        $this->assertTrue(Hash::check('merchant-pass-1', $merchant->password));
        $this->assertSame('1.00', $merchant->cod_charge_inside_city);
    }

    public function test_validation_hubs_phone_email_and_percentages(): void
    {
        $this->merchant(['email' => 'taken@kuryer.test']);

        Livewire::test(MerchantEditor::class)
            ->set('data.mobile', '0501234567')
            ->set('data.email', 'taken@kuryer.test')
            ->set('data.vat', '120')
            ->call('save')
            ->assertHasErrors(['data.business_name', 'hubIds', 'data.mobile', 'data.email' => 'unique', 'data.vat' => 'max']);
    }

    public function test_edit_keeps_opening_balance_and_password_when_left_empty(): void
    {
        $merchant = $this->merchant(['opening_balance' => 50, 'password' => 'merchant-pass-1']);

        Livewire::test(MerchantEditor::class, ['merchant' => $merchant])
            ->assertSet('data.business_name', 'AZERCELL')
            ->set('data.opening_balance', '999')
            ->set('data.business_name', 'AZERCELL Telekom')
            ->call('save')
            ->assertHasNoErrors();

        $merchant->refresh();
        $this->assertSame('AZERCELL Telekom', $merchant->business_name);
        $this->assertSame('50.00', $merchant->opening_balance);
        $this->assertSame('50.00', $merchant->current_balance);
        $this->assertTrue(Hash::check('merchant-pass-1', $merchant->password));
    }

    public function test_documents_are_private_and_served_only_with_permission(): void
    {
        Storage::fake('local');

        $this->editor()
            ->set('files.trade_license_file', UploadedFile::fake()->create('contract.pdf', 100, 'application/pdf'))
            ->set('files.image_file', UploadedFile::fake()->image('logo.png'))
            ->call('save')
            ->assertHasNoErrors();

        $merchant = Merchant::firstOrFail();
        $this->assertStringStartsWith("merchants/{$merchant->id}/", $merchant->trade_license_file);
        Storage::disk('local')->assertExists($merchant->trade_license_file);

        $this->get(route('admin.merchants.file', [$merchant, 'trade_license_file']))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertDownload();
        $this->assertStringContainsString('inline', $this->get(route('admin.merchants.file', [$merchant, 'image_file']))->headers->get('content-disposition'));
        $this->get("/admin/merchants/{$merchant->id}/files/password")->assertNotFound();

        $this->actingAs(User::factory()->withPermissions(['merchant_read'])->create());
        $this->get(route('admin.merchants.file', [$merchant, 'trade_license_file']))->assertForbidden();
    }

    public function test_svg_or_executable_documents_are_rejected(): void
    {
        $this->editor()
            ->set('files.image_file', UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"/>'))
            ->set('files.nid_file', UploadedFile::fake()->createWithContent('id.php', '<?php echo 1;'))
            ->call('save')
            ->assertHasErrors(['files.image_file', 'files.nid_file']);
    }

    public function test_merchant_with_balance_cannot_be_deleted_and_deleting_removes_files(): void
    {
        Storage::fake('local');
        $rich = $this->merchant(['opening_balance' => 10]);
        Livewire::test(Merchants::class)->call('confirmDelete', $rich->id)->call('delete')->assertDispatched('toast', type: 'error');
        $this->assertModelExists($rich);

        $merchant = $this->merchant();
        app(MerchantService::class)->storeFile($merchant, 'nid_file', UploadedFile::fake()->image('id.jpg'));
        $path = $merchant->fresh()->nid_file;

        Livewire::test(Merchants::class)->call('confirmDelete', $merchant->id)->call('delete');
        $this->assertModelMissing($merchant);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_list_search_by_unique_id_and_name(): void
    {
        $azercell = $this->merchant();
        $this->merchant(['business_name' => 'SOCAR AQS']);

        Livewire::test(Merchants::class)->set('search', $azercell->unique_id)->assertSee('AZERCELL')->assertDontSee('SOCAR AQS');
        Livewire::test(Merchants::class)->set('search', 'socar')->assertSee('SOCAR AQS')->assertDontSee('AZERCELL');
    }

    public function test_charges_are_unique_per_combination_and_scoped_to_the_merchant(): void
    {
        $this->seed(NetworkSeeder::class);
        $zone = Zone::create(['name' => '5-ci zona', 'hub_id' => $this->hub->id]);
        $envelope = PackageType::where('name', 'Zərf')->firstOrFail();
        $merchant = $this->merchant();

        $add = fn () => Livewire::test(MerchantCharges::class, ['merchantId' => $merchant->id])
            ->call('create')
            ->set('form.zone_id', (string) $zone->id)->set('form.package_type_id', (string) $envelope->id)
            ->set('form.tariff_type', 'urgent')->set('form.price', '15.5')->set('form.tracking_time', '1 gün')
            ->call('save');

        $add()->assertHasNoErrors();
        $add()->assertHasErrors(['form.tariff_type' => 'unique']);
        $this->assertDatabaseHas('merchant_delivery_charges', ['merchant_id' => $merchant->id, 'price' => 15.5]);

        $other = $this->merchant(['business_name' => 'Other']);
        $foreign = MerchantDeliveryCharge::create(['merchant_id' => $other->id, 'zone_id' => $zone->id, 'package_type_id' => $envelope->id, 'tariff_type' => 'regular', 'price' => 1]);

        Livewire::test(MerchantCharges::class, ['merchantId' => $merchant->id])->call('edit', $foreign->id)->assertNotFound();
    }

    public function test_tab_cannot_delete_another_merchants_record(): void
    {
        $merchant = $this->merchant();
        $other = $this->merchant(['business_name' => 'Other']);
        $shop = app(MerchantService::class)->saveShop($other, null, ['name' => 'Anbar', 'address' => 'x', 'latitude' => 40, 'longitude' => 49]);

        Livewire::test(MerchantShops::class, ['merchantId' => $merchant->id])->call('confirmDelete', $shop->id)->call('delete')->assertNotFound();

        $this->assertModelExists($shop);
    }

    public function test_first_shop_is_default_and_default_moves_on_change_and_delete(): void
    {
        $merchant = $this->merchant();
        $add = fn (string $name) => Livewire::test(MerchantShops::class, ['merchantId' => $merchant->id])
            ->call('create')->set('form.name', $name)->set('form.address', "{$name} küç.")
            ->set('form.latitude', '40.41')->set('form.longitude', '49.86')
            ->call('save')->assertHasNoErrors();

        $add('Mərkəz');
        $add('Gəncə');
        [$first, $second] = MerchantShop::orderBy('id')->get();
        $this->assertTrue($first->is_default);
        $this->assertFalse($second->is_default);

        Livewire::test(MerchantShops::class, ['merchantId' => $merchant->id])->call('makeDefault', $second->id);
        $this->assertFalse($first->fresh()->is_default);
        $this->assertTrue($second->fresh()->is_default);

        Livewire::test(MerchantShops::class, ['merchantId' => $merchant->id])->call('confirmDelete', $second->id)->call('delete');
        $this->assertTrue($first->fresh()->is_default);
        $this->assertSame(1, MerchantShop::where('is_default', true)->count());
    }

    public function test_shop_requires_address_and_coordinates(): void
    {
        $merchant = $this->merchant();

        Livewire::test(MerchantShops::class, ['merchantId' => $merchant->id])
            ->call('create')->set('form.name', 'X')->set('form.latitude', '200')
            ->call('save')
            ->assertHasErrors(['form.address', 'form.latitude' => 'between', 'form.longitude']);
    }

    public function test_payment_accounts_validate_per_method_and_drop_stale_details(): void
    {
        $merchant = $this->merchant();
        $tab = fn () => Livewire::test(MerchantPaymentAccounts::class, ['merchantId' => $merchant->id]);

        $tab()->call('create')->set('form.method', 'bank')
            ->set('form.details.bank_name', 'Kapital Bank')->set('form.details.holder_name', 'AZERCELL')
            ->set('form.details.iban', 'AZ12 345')
            ->call('save')->assertHasErrors(['form.details.iban' => 'regex']);

        $tab()->call('create')->set('form.method', 'bank')
            ->set('form.details.bank_name', 'Kapital Bank')->set('form.details.holder_name', 'AZERCELL')
            ->set('form.details.iban', 'az21 nabz 0000 0000 1370 1000 1944')
            ->call('save')->assertHasNoErrors();

        $account = $merchant->paymentAccounts()->firstOrFail();
        $this->assertSame('AZ21NABZ00000000137010001944', $account->details['iban']);

        $tab()->call('edit', $account->id)->set('form.method', 'mobile')->set('form.details.number', '994501234567')->call('save')->assertHasNoErrors();
        $this->assertSame(['number' => '994501234567'], $account->fresh()->details);
    }

    public function test_merchant_types_crud_and_filter(): void
    {
        Livewire::test(MerchantTypes::class)->call('create')->set('form.title', 'Bank')->call('save')->assertHasNoErrors();
        Livewire::test(MerchantTypes::class)->call('create')->set('form.title', 'Bank')->call('save')->assertHasErrors(['form.title' => 'unique']);
        Livewire::test(MerchantTypes::class)->call('create')->set('form.title', 'Telekom')->call('save');

        $this->merchant()->forceFill(['merchant_type_id' => 1])->save();

        Livewire::test(MerchantTypes::class)->set('hasMerchants', true)->assertSee('Bank')->assertDontSee('Telekom');
    }

    public function test_hub_used_by_a_merchant_cannot_be_deleted(): void
    {
        $this->merchant();

        Livewire::test(Hubs::class)->call('confirmDelete', $this->hub->id)->call('delete')->assertDispatched('toast', type: 'error');
        $this->assertModelExists($this->hub);
    }
}
