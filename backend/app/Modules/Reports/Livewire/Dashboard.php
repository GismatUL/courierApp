<?php

namespace App\Modules\Reports\Livewire;

use App\Modules\Access\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Spec §2.3. Parcel, merchant, courier, hub and finance cards are added with their modules. */
#[Title('İdarə Paneli')]
class Dashboard extends Component
{
    public function render()
    {
        return view('reports::dashboard', [
            'cards' => [
                ['label' => 'Paketlər', 'value' => null],
                ['label' => 'Gözləyir', 'value' => null],
                ['label' => 'Kuryerdədir', 'value' => null],
                ['label' => 'Çatdırılmış', 'value' => null],
                ['label' => 'Qaytarılan', 'value' => null],
                ['label' => 'İstifadəçi', 'value' => User::count()],
            ],
        ]);
    }
}
