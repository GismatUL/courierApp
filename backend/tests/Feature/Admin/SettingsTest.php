<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Settings\Currencies;
use App\Livewire\Admin\Settings\General;
use App\Livewire\Admin\Settings\Map;
use App\Models\Currency;
use App\Models\User;
use App\Services\Settings;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ReferenceDataSeeder::class);
        $this->actingAs(User::factory()->superAdmin()->create());
    }

    public function test_settings_pages_render(): void
    {
        foreach (['/admin/settings/general', '/admin/settings/map', '/admin/currencies'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_general_settings_show_defaults_and_save(): void
    {
        Livewire::test(General::class)
            ->assertSet('values.name', 'Kuryer.net')
            ->assertSet('values.par_track_prefix', 'KN')
            ->set('values.name', 'Kuryer.net Test')
            ->set('values.par_track_prefix', 'kt')
            ->set('values.tax', '18')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $settings = app(Settings::class);
        $this->assertSame('Kuryer.net Test', $settings->get('name'));
        $this->assertSame('KT', $settings->get('par_track_prefix'));
        $this->assertSame('18', $settings->get('tax'));
    }

    public function test_general_settings_validate_prefix_colour_and_currency(): void
    {
        Livewire::test(General::class)
            ->set('values.par_track_prefix', 'K1')
            ->set('values.primary_color', 'red')
            ->set('values.currency', 'XXX')
            ->call('save')
            ->assertHasErrors(['values.par_track_prefix', 'values.primary_color', 'values.currency']);
    }

    public function test_logo_upload_is_stored_under_a_generated_name_and_replaced(): void
    {
        Storage::fake('uploads');

        Livewire::test(General::class)
            ->set('files.logo', UploadedFile::fake()->image('my logo.png', 200, 60))
            ->call('save')
            ->assertHasNoErrors();

        $first = app(Settings::class)->get('logo');
        $this->assertMatchesRegularExpression('#^settings/[0-9a-f-]{36}\.png$#', $first);
        Storage::disk('uploads')->assertExists($first);

        Livewire::test(General::class)->set('files.logo', UploadedFile::fake()->image('new.png'))->call('save');

        Storage::disk('uploads')->assertMissing($first);
        Storage::disk('uploads')->assertExists(app(Settings::class)->get('logo'));
    }

    public function test_svg_and_other_files_are_rejected_as_logo(): void
    {
        Storage::fake('uploads');

        Livewire::test(General::class)
            ->set('files.logo', UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'))
            ->call('save')
            ->assertHasErrors('files.logo');

        $this->assertNull(app(Settings::class)->get('logo'));
    }

    public function test_map_key_format_is_checked_and_can_be_cleared(): void
    {
        Livewire::test(Map::class)->set('mapKey', 'not-a-key')->call('save')->assertHasErrors('mapKey');

        $key = 'AIza'.str_repeat('a', 35);
        Livewire::test(Map::class)->set('mapKey', $key)->call('save')->assertHasNoErrors();
        $this->assertSame($key, app(Settings::class)->get('map_key'));

        Livewire::test(Map::class)->set('mapKey', '')->call('save');
        $this->assertNull(app(Settings::class)->get('map_key'));
    }

    public function test_default_currency_cannot_be_deleted_or_deactivated(): void
    {
        $azn = Currency::where('code', 'AZN')->firstOrFail();

        Livewire::test(Currencies::class)->call('confirmDelete', $azn->id)->call('delete')->assertDispatched('toast', type: 'error');
        $this->assertModelExists($azn);

        Livewire::test(Currencies::class)->call('edit', $azn->id)->set('form.status', false)->call('save')->assertHasErrors('form.status');
        $this->assertTrue($azn->fresh()->status);
    }

    public function test_currency_can_be_added_and_formats_amounts(): void
    {
        Livewire::test(Currencies::class)
            ->call('create')
            ->set('form.name', 'ABŞ dolları')->set('form.code', 'usd')->set('form.symbol', '$')
            ->set('form.exchange_rate', '1')->set('form.position', 'before')
            ->call('save')
            ->assertHasNoErrors();

        $usd = Currency::where('code', 'USD')->firstOrFail();
        $this->assertSame('$1 250.50', $usd->format(1250.5));
        $this->assertSame('1 250.50 ₼', Currency::where('code', 'AZN')->first()->format(1250.5));

        Livewire::test(Currencies::class)->call('confirmDelete', $usd->id)->call('delete');
        $this->assertModelMissing($usd);
    }
}
