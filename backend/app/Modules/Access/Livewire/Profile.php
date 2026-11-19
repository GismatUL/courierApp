<?php

namespace App\Modules\Access\Livewire;

use App\Shared\Livewire\Concerns\Toasts;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Profil + Şifrəni Dəyiş (spec §2.4): own details read-only, password change with the old password. */
#[Title('Profil')]
class Profile extends Component
{
    use Toasts;

    public string $old_password = '';

    public string $new_password = '';

    public string $new_password_confirmation = '';

    protected function rules(): array
    {
        return [
            'old_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:10', 'max:72', 'confirmed', 'different:old_password'],
        ];
    }

    protected function messages(): array
    {
        return [
            'old_password.current_password' => 'Köhnə şifrə yanlışdır.',
            'new_password.confirmed' => 'Yeni şifrə ilə təsdiq uyğun gəlmir.',
            'new_password.different' => 'Yeni şifrə köhnəsindən fərqli olmalıdır.',
        ];
    }

    protected function validationAttributes(): array
    {
        return ['old_password' => 'Köhnə şifrə', 'new_password' => 'Yeni şifrə'];
    }

    public function updatePassword(): void
    {
        $this->validate();

        $user = Auth::user();
        $user->forceFill(['password' => $this->new_password])->setRememberToken(null);
        $user->save();
        session()->regenerate();

        $this->reset('old_password', 'new_password', 'new_password_confirmation');
        $this->toast('Şifrə dəyişdirildi.');
    }

    public function render()
    {
        return view('access::profile', [
            'user' => Auth::user()->load('role', 'designation', 'department', 'hubs:id,name'),
        ]);
    }
}
