<?php

namespace App\Modules\Couriers\Services;

use App\Modules\Access\Services\UserAccounts;
use App\Modules\Couriers\Models\Courier;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Courier lifecycle (spec §7.2):
 * - the login account is an app-only user created through Access (role "delivery-man");
 * - the opening balance is fixed at creation and starts the cash channel (what the courier owes in cash);
 * - photo and driving licence are private files under generated names;
 * - "Cihazı sıfırla" unbinds the phone so the courier can sign in on a new device.
 */
class CourierService
{
    public const DISK = 'local';

    public const ROLE = 'delivery-man';

    public function __construct(private readonly UserAccounts $accounts) {}

    /**
     * @param  array{name: string, email: string, mobile?: ?string, password?: ?string, status?: bool}  $account
     * @param  array<string, mixed>  $profile
     * @param  array<string, ?UploadedFile>  $files
     */
    public function save(?Courier $courier, array $account, array $profile, array $files = []): Courier
    {
        return DB::transaction(function () use ($courier, $account, $profile, $files) {
            $user = $this->accounts->saveAppAccount($courier?->user, $account, self::ROLE);

            if ($courier === null) {
                $courier = new Courier;
                $courier->user_id = $user->id;
                $courier->opening_balance = $profile['opening_balance'] ?? 0;
                $courier->balance_cash = $profile['opening_balance'] ?? 0;
            }
            unset($profile['opening_balance']);

            $courier->fill($profile)->save();

            foreach ($files as $column => $file) {
                if ($file instanceof UploadedFile && array_key_exists($column, Courier::FILES)) {
                    $this->storeFile($courier, $column, $file);
                }
            }

            return $courier->refresh();
        });
    }

    public function storeFile(Courier $courier, string $column, UploadedFile $file): void
    {
        $old = $courier->{$column};
        $path = Storage::disk(self::DISK)->putFileAs("couriers/{$courier->id}", $file, Str::uuid().'.'.$file->extension());
        $courier->forceFill([$column => $path])->save();

        if ($old) {
            Storage::disk(self::DISK)->delete($old);
        }
    }

    public function resetDevice(Courier $courier): void
    {
        $courier->forceFill(['device_id' => null])->save();
    }

    public function delete(Courier $courier): void
    {
        DB::transaction(function () use ($courier) {
            $user = $courier->user;
            $courier->delete();
            $this->accounts->delete($user);
        });

        Storage::disk(self::DISK)->deleteDirectory("couriers/{$courier->id}");
    }
}
