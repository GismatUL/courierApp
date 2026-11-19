<?php

namespace App\Modules\Access\Livewire;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts::guest')]
#[Title('Daxil ol')]
class Login extends Component
{
    private const MAX_ATTEMPTS = 5;

    #[Validate('required|string|email|max:255')]
    public string $email = '';

    #[Validate('required|string|max:72')]
    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();
        $this->ensureIsNotRateLimited();

        $active = Auth::attempt(
            ['email' => $this->email, 'password' => $this->password, 'status' => true],
            $this->remember,
        );

        if (! $active) {
            RateLimiter::hit($this->throttleKey(), 60);
            $this->reset('password');

            // One generic message for unknown email, wrong password and deactivated account.
            throw ValidationException::withMessages(['email' => __('Email və ya şifrə yanlışdır.')]);
        }

        RateLimiter::clear($this->throttleKey());
        session()->regenerate();
        Auth::user()->forceFill(['last_login_at' => now()])->save();

        $this->redirectIntended(route('admin.dashboard', absolute: false), navigate: true);
    }

    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_ATTEMPTS)) {
            return;
        }

        event(new Lockout(request()));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('Çox sayda cəhd. :seconds saniyə sonra yenidən yoxlayın.', ['seconds' => $seconds]),
        ]);
    }

    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    public function render()
    {
        return view('access::login');
    }
}
