<?php

namespace App\Modules\Parcels;

use App\Modules\Parcels\Events\ParcelStatusChanged;
use App\Modules\Parcels\Listeners\SendParcelSms;
use App\Modules\Parcels\Livewire\ParcelActionPanel;
use App\Shared\Providers\ModuleServiceProvider;
use App\Shared\Sms\LogSmsGateway;
use App\Shared\Sms\SmsGateway;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

/** Parcels, pricing and the status workflow (spec §3, §4). */
class ParcelsServiceProvider extends ModuleServiceProvider
{
    protected function module(): string
    {
        return 'Parcels';
    }

    public function register(): void
    {
        parent::register();
        $this->app->bindIf(SmsGateway::class, LogSmsGateway::class);
    }

    public function boot(): void
    {
        parent::boot();
        Livewire::component('parcels-action-panel', ParcelActionPanel::class);
        Event::listen(ParcelStatusChanged::class, SendParcelSms::class);
    }
}
