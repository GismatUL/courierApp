<?php

namespace App\Modules\Access\Livewire;

use App\Modules\Access\Models\Department;
use Livewire\Attributes\Title;

/** Şöbələr (spec §9.2, /admin/departments). */
#[Title('Şöbələr')]
class Departments extends TitleList
{
    protected function model(): string
    {
        return Department::class;
    }

    protected function permissionPrefix(): string
    {
        return 'department';
    }

    protected function description(): string
    {
        return 'İstifadəçilərin aid olduğu şöbələr.';
    }
}
