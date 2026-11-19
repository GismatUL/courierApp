<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/')->assertRedirect('/admin');
    }

    public function test_login_page_renders(): void
    {
        $this->get('/admin/login')->assertOk()->assertSeeLivewire(Login::class)->assertSee('İdarə panelinə giriş');
    }

    public function test_user_can_log_in_and_sees_dashboard(): void
    {
        $user = User::factory()->create(['password' => 'correct-horse-battery']);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'correct-horse-battery')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
        $this->get('/admin')->assertOk()->assertSee($user->name);
    }

    public function test_wrong_password_unknown_email_and_inactive_user_get_the_same_error(): void
    {
        $user = User::factory()->create(['password' => 'correct-horse-battery']);
        $inactive = User::factory()->inactive()->create(['password' => 'correct-horse-battery']);

        $attempts = [
            [$user->email, 'wrong-password'],
            ['nobody@example.com', 'correct-horse-battery'],
            [$inactive->email, 'correct-horse-battery'],
        ];

        foreach ($attempts as [$email, $password]) {
            Livewire::test(Login::class)
                ->set('email', $email)
                ->set('password', $password)
                ->call('login')
                ->assertHasErrors(['email' => 'Email və ya şifrə yanlışdır.']);
        }

        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_five_failures(): void
    {
        $user = User::factory()->create(['password' => 'correct-horse-battery']);
        RateLimiter::clear(strtolower($user->email).'|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            Livewire::test(Login::class)->set('email', $user->email)->set('password', 'wrong')->call('login');
        }

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'correct-horse-battery')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertGuest();
    }

    public function test_deactivated_user_is_logged_out_on_next_request(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin')->assertOk();

        $user->update(['status' => false]);

        $this->get('/admin')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_user_can_log_out(): void
    {
        $this->actingAs(User::factory()->create())->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_locale_switch_accepts_only_supported_languages(): void
    {
        $this->actingAs(User::factory()->create());

        $this->from('/admin')->get('/admin/localization/ru')->assertRedirect('/admin');
        $this->assertSame('ru', session('locale'));
        $this->get('/admin/localization/de')->assertNotFound();
    }

    public function test_sidebar_links_existing_routes_and_disables_unbuilt_modules(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create())
            ->get('/admin')
            ->assertOk()
            ->assertSee('href="'.route('admin.dashboard').'"', false)
            ->assertSee('Partnyorlar')
            ->assertSee('title="Tezliklə"', false);
    }
}
