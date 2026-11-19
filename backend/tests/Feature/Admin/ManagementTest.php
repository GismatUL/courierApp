<?php

namespace Tests\Feature\Admin;

use App\Enums\TariffType;
use App\Livewire\Admin\Management\Hubs;
use App\Livewire\Admin\Management\PackageTypes;
use App\Livewire\Admin\Management\Regions;
use App\Livewire\Admin\Management\RejectReasons;
use App\Livewire\Admin\Management\Shelves;
use App\Livewire\Admin\Management\Zones;
use App\Livewire\Admin\Management\ZoneTariffs;
use App\Models\Hub;
use App\Models\PackageType;
use App\Models\Region;
use App\Models\RejectReason;
use App\Models\Shelf;
use App\Models\User;
use App\Models\Zone;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->superAdmin()->create());
    }

    private function hub(string $name = 'Bakı'): Hub
    {
        return Hub::create(['name' => $name, 'address' => 'Nizami küç. 1', 'latitude' => 40.4093, 'longitude' => 49.8671]);
    }

    public static function pages(): array
    {
        return [['/admin/hubs'], ['/admin/zones'], ['/admin/regions'], ['/admin/package-types'], ['/admin/shelves'], ['/admin/reject-reasons']];
    }

    #[DataProvider('pages')]
    public function test_pages_render_for_logged_in_users_only(string $url): void
    {
        $this->get($url)->assertOk();

        auth()->logout();
        $this->get($url)->assertRedirect('/admin/login');
    }

    public function test_sidebar_links_the_built_management_pages(): void
    {
        $this->get('/admin')
            ->assertSee('href="'.route('admin.hubs.index').'"', false)
            ->assertSee('href="'.route('admin.reject-reasons.index').'"', false);
    }

    public function test_hub_requires_address_and_valid_coordinates(): void
    {
        Livewire::test(Hubs::class)
            ->call('create')
            ->set('form.name', 'Gəncə')
            ->set('form.latitude', '95')
            ->call('save')
            ->assertHasErrors(['form.address' => 'required', 'form.latitude' => 'between', 'form.longitude' => 'required']);

        $this->assertDatabaseCount('hubs', 0);
    }

    public function test_hub_can_be_created_edited_and_name_is_unique(): void
    {
        $this->hub('Bakı');

        Livewire::test(Hubs::class)
            ->call('create')
            ->set('form.name', 'Bakı')
            ->set('form.address', 'x')->set('form.latitude', '40.1')->set('form.longitude', '49.1')
            ->call('save')
            ->assertHasErrors(['form.name' => 'unique'])
            ->set('form.name', 'Gəncə')
            ->set('form.phone', '+994 22 256 00 00')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showForm', false)
            ->assertDispatched('toast');

        $ganja = Hub::where('name', 'Gəncə')->firstOrFail();

        Livewire::test(Hubs::class)
            ->call('edit', $ganja->id)
            ->assertSet('form.name', 'Gəncə')
            ->set('form.status', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertFalse($ganja->fresh()->status);
    }

    public function test_hub_filters_by_name(): void
    {
        $this->hub('Bakı');
        $this->hub('Lənkəran');

        Livewire::test(Hubs::class)->set('name', 'lənk')->assertSee('Lənkəran')->assertDontSee('Bakı');
    }

    public function test_hub_with_zones_cannot_be_deleted(): void
    {
        $hub = $this->hub();
        Zone::create(['name' => '1-ci zona', 'hub_id' => $hub->id]);

        Livewire::test(Hubs::class)
            ->call('confirmDelete', $hub->id)
            ->assertSet('deletingId', $hub->id)
            ->call('delete')
            ->assertDispatched('toast', type: 'error');

        $this->assertModelExists($hub);
    }

    public function test_unused_hub_is_deleted_after_confirmation(): void
    {
        $hub = $this->hub();

        Livewire::test(Hubs::class)->call('confirmDelete', $hub->id)->call('delete')->assertSet('deletingId', null);

        $this->assertModelMissing($hub);
    }

    public function test_package_type_ranges_must_not_overlap_but_may_touch(): void
    {
        $this->seed(ReferenceDataSeeder::class);

        Livewire::test(PackageTypes::class)
            ->call('create')
            ->set('form.name', 'Orta')->set('form.min_weight', '5')->set('form.max_weight', '15')
            ->call('save')
            ->assertHasErrors('form.min_weight');

        PackageType::where('name', 'Bağlama50+')->update(['status' => false]);

        Livewire::test(PackageTypes::class)
            ->call('create')
            ->set('form.name', 'Palet')->set('form.min_weight', '50')->set('form.max_weight', '')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertNull(PackageType::where('name', 'Palet')->value('max_weight'));
    }

    public function test_package_type_max_must_exceed_min(): void
    {
        Livewire::test(PackageTypes::class)
            ->call('create')
            ->set('form.name', 'X')->set('form.min_weight', '10')->set('form.max_weight', '5')
            ->call('save')
            ->assertHasErrors(['form.max_weight' => 'gt']);
    }

    public function test_zone_name_is_unique_within_its_hub(): void
    {
        $baku = $this->hub('Bakı');
        $ganja = $this->hub('Gəncə');
        Zone::create(['name' => '1-ci zona', 'hub_id' => $baku->id]);

        Livewire::test(Zones::class)
            ->call('create')->set('form.name', '1-ci zona')->set('form.hub_id', (string) $baku->id)
            ->call('save')->assertHasErrors(['form.name' => 'unique'])
            ->set('form.hub_id', (string) $ganja->id)
            ->call('save')->assertHasNoErrors();
    }

    public function test_zone_tariff_matrix_saves_updates_and_removes_prices(): void
    {
        $this->seed(ReferenceDataSeeder::class);
        $zone = Zone::create(['name' => '1-ci zona']);
        $envelope = PackageType::where('name', 'Zərf')->firstOrFail();
        $urgent = TariffType::Urgent->value;

        Livewire::test(ZoneTariffs::class, ['zone' => $zone])
            ->set("rows.{$envelope->id}.{$urgent}.tracking_time", '30 dəqiqə')
            ->set("rows.{$envelope->id}.{$urgent}.delivery_time", '25 dəqiqə')
            ->set("rows.{$envelope->id}.{$urgent}.price", '3.9')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('zone_tariffs', [
            'zone_id' => $zone->id, 'package_type_id' => $envelope->id, 'tariff_type' => 'urgent', 'price' => 3.9, 'tracking_time' => '30 dəqiqə',
        ]);

        Livewire::test(ZoneTariffs::class, ['zone' => $zone])
            ->assertSet("rows.{$envelope->id}.{$urgent}.price", '3.9')
            ->set("rows.{$envelope->id}.{$urgent}.tracking_time", '')
            ->set("rows.{$envelope->id}.{$urgent}.delivery_time", '')
            ->set("rows.{$envelope->id}.{$urgent}.price", '')
            ->call('save');

        $this->assertDatabaseCount('zone_tariffs', 0);
    }

    public function test_zone_tariff_needs_a_price_when_times_are_given_and_rejects_negative_prices(): void
    {
        $this->seed(ReferenceDataSeeder::class);
        $zone = Zone::create(['name' => '1-ci zona']);
        $id = PackageType::where('name', 'Zərf')->value('id');

        Livewire::test(ZoneTariffs::class, ['zone' => $zone])
            ->set("rows.{$id}.regular.tracking_time", '1 gün')
            ->set("rows.{$id}.express.price", '-1')
            ->call('save')
            ->assertHasErrors(["rows.{$id}.regular.price", "rows.{$id}.express.price"]);

        $this->assertDatabaseCount('zone_tariffs', 0);
    }

    public function test_zone_with_regions_cannot_be_deleted(): void
    {
        $zone = Zone::create(['name' => '1-ci zona']);
        Region::create(['name' => 'Yasamal', 'zone_id' => $zone->id]);

        Livewire::test(Zones::class)->call('confirmDelete', $zone->id)->call('delete')->assertDispatched('toast', type: 'error');

        $this->assertModelExists($zone);
    }

    public function test_new_region_defaults_to_the_filtered_zone(): void
    {
        $zone = Zone::create(['name' => '1-ci zona']);

        Livewire::withQueryParams(['zone' => (string) $zone->id])
            ->test(Regions::class)
            ->call('create')
            ->assertSet('form.zone_id', (string) $zone->id)
            ->set('form.name', 'Nəsimi')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('regions', ['name' => 'Nəsimi', 'zone_id' => $zone->id]);
    }

    public function test_shelf_code_is_uppercased_unique_and_scanner_safe(): void
    {
        $zone = Zone::create(['name' => '1-ci zona']);

        Livewire::test(Shelves::class)
            ->call('create')->set('form.name', 'Sumqayıt / Qaradağ')->set('form.code', '04a')->set('form.zone_id', (string) $zone->id)
            ->call('save')->assertHasNoErrors();

        $this->assertDatabaseHas('shelves', ['code' => '04A', 'limit' => null]);

        Livewire::test(Shelves::class)
            ->call('create')->set('form.name', 'Təkrar')->set('form.code', '04A')->set('form.zone_id', (string) $zone->id)
            ->call('save')->assertHasErrors(['form.code' => 'unique'])
            ->set('form.code', '04 B')
            ->call('save')->assertHasErrors(['form.code' => 'regex']);
    }

    public function test_shelf_label_shows_code_and_qr(): void
    {
        $shelf = Shelf::create(['name' => 'Təhvillər', 'code' => '02C', 'zone_id' => Zone::create(['name' => '1-ci zona'])->id, 'limit' => 100000]);

        $this->get(route('admin.shelves.label', $shelf))->assertOk()->assertSee('02C')->assertSee('<svg', false);
    }

    public function test_reject_reason_crud(): void
    {
        Livewire::test(RejectReasons::class)
            ->call('create')->set('form.name', 'Qapı bağlıdır')->call('save')->assertHasNoErrors();

        $reason = RejectReason::firstOrFail();

        Livewire::test(RejectReasons::class)
            ->call('create')->set('form.name', 'Qapı bağlıdır')->call('save')->assertHasErrors(['form.name' => 'unique']);

        Livewire::test(RejectReasons::class)->call('confirmDelete', $reason->id)->call('delete');
        $this->assertModelMissing($reason);
    }

    public function test_reference_seeder_is_idempotent(): void
    {
        $this->seed(ReferenceDataSeeder::class);
        $this->seed(ReferenceDataSeeder::class);

        $this->assertDatabaseCount('package_types', 5);
        $this->assertDatabaseCount('reject_reasons', 6);
    }
}
