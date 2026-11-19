<?php

namespace App\Modules\Couriers\Http;

use App\Http\Controllers\Controller;
use App\Modules\Couriers\Models\Courier;
use App\Modules\Couriers\Services\CourierService;
use App\Shared\Http\PrivateFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Serves a courier's photo or driving licence after the route's delivery_man_read check. */
class CourierFileController extends Controller
{
    public function __invoke(Courier $courier, string $column): StreamedResponse
    {
        abort_unless(array_key_exists($column, Courier::FILES), 404);

        return PrivateFile::response(CourierService::DISK, $courier->{$column}, "courier-{$courier->id}-".str_replace('_file', '', $column));
    }
}
