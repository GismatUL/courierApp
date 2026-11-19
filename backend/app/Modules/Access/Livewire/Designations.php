<?php

namespace App\Modules\Access\Livewire;

use App\Modules\Access\Models\Designation;
use Livewire\Attributes\Title;

/** Vəzifələr (spec §9.2, /admin/designations). */
#[Title('Vəzifələr')]
class Designations extends TitleList
{
    protected function model(): string
    {
        return Designation::class;
    }

    protected function permissionPrefix(): string
    {
        return 'designation';
    }

    protected function description(): string
    {
        return 'İstifadəçilərin vəzifələri (məs. Operator, Anbardar, Kassir).';
    }
}
