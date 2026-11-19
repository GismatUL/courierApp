<?php

namespace App\Modules\Couriers\Livewire;

use App\Modules\Couriers\Models\Courier;
use App\Modules\Couriers\Services\CourierService;
use App\Modules\Network\Models\Hub;
use App\Shared\Livewire\Concerns\ConfirmsDeletion;
use App\Shared\Livewire\Concerns\HasAbilities;
use App\Shared\Livewire\Concerns\Toasts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Kuryerlər (spec §7.2): ID · İstifadəçi · Mərkəz · Kuryer Növü · Minimum Çatdırılma Paketi · Çatdırılma haqqı ·
 * Geri qaytarma haqqı · Cari balans · Nağd · P/Terminal · CardToCard · Status; filters hub, name, email, phone.
 */
#[Title('Kuryerlər')]
class Couriers extends Component
{
    use ConfirmsDeletion, HasAbilities, Toasts, WithPagination;

    #[Url(as: 'hub', except: '')]
    public string $hubId = '';

    #[Url(except: '')]
    public string $name = '';

    #[Url(except: '')]
    public string $email = '';

    #[Url(except: '')]
    public string $phone = '';

    public function updated(string $property): void
    {
        if (in_array($property, ['hubId', 'name', 'email', 'phone'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset('hubId', 'name', 'email', 'phone');
        $this->resetPage();
    }

    protected function abilities(): array
    {
        return ['create' => 'delivery_man_create', 'update' => 'delivery_man_update', 'delete' => 'delivery_man_delete'];
    }

    /** "Cihazı sıfırla": the courier can then sign in to the app on a new phone. */
    public function resetDevice(int $id, CourierService $service): void
    {
        $this->authorizeAction('update');
        $service->resetDevice(Courier::findOrFail($id));
        $this->toast('Cihaz sıfırlandı; kuryer yeni telefonda daxil ola bilər.');
    }

    protected function deletableQuery(): Builder
    {
        return Courier::query();
    }

    protected function performDelete(Model $record): void
    {
        app(CourierService::class)->delete($record);
    }

    public function render()
    {
        $userFilter = fn (string $column, string $value) => fn ($q) => $q->whereHas('user', fn ($u) => $u->whereLike($column, "%{$value}%"));

        return view('couriers::couriers', [
            'couriers' => Courier::query()
                ->with(['user:id,name,email,mobile,status', 'hub:id,name'])
                ->when($this->hubId !== '', fn ($q) => $q->where('hub_id', (int) $this->hubId))
                ->when($this->name !== '', $userFilter('name', $this->name))
                ->when($this->email !== '', $userFilter('email', $this->email))
                ->when($this->phone !== '', $userFilter('mobile', $this->phone))
                ->orderBy('id')
                ->paginate(10),
            'hubs' => Hub::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
