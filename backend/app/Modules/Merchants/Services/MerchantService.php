<?php

namespace App\Modules\Merchants\Services;

use App\Modules\Merchants\Models\Merchant;
use App\Modules\Merchants\Models\MerchantShop;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Merchant lifecycle rules (spec §6.1, §6.3):
 * - the system assigns a unique 6-digit ID and starts the current balance at the opening balance;
 * - documents are stored privately under generated names (the old file is removed on replace);
 * - exactly one active pickup point is the default; the first one added becomes it.
 */
class MerchantService
{
    /** Private disk; files are served only through MerchantFileController after a permission check. */
    public const DISK = 'local';

    /**
     * @param  array<string, mixed>  $data
     * @param  list<int>  $hubIds
     * @param  array<string, ?UploadedFile>  $files  column => upload
     */
    public function save(?Merchant $merchant, array $data, array $hubIds, array $files = []): Merchant
    {
        return DB::transaction(function () use ($merchant, $data, $hubIds, $files) {
            if ($merchant === null) {
                $merchant = new Merchant;
                $merchant->unique_id = $this->newUniqueId();
                $merchant->current_balance = $data['opening_balance'] ?? 0;
            } else {
                unset($data['opening_balance']); // Fixed after creation; later changes go through Finance.
            }

            if (($data['password'] ?? '') === '') {
                unset($data['password']); // Empty means "keep" (or no portal login yet).
            }

            $merchant->fill($data)->save();
            $merchant->hubs()->sync($hubIds);

            foreach ($files as $column => $file) {
                if ($file instanceof UploadedFile && array_key_exists($column, Merchant::FILES)) {
                    $this->storeFile($merchant, $column, $file);
                }
            }

            return $merchant->refresh(); // includes column defaults set by the database
        });
    }

    public function storeFile(Merchant $merchant, string $column, UploadedFile $file): void
    {
        $old = $merchant->{$column};
        $path = Storage::disk(self::DISK)->putFileAs("merchants/{$merchant->id}", $file, Str::uuid().'.'.$file->extension());

        $merchant->forceFill([$column => $path])->save();

        if ($old) {
            Storage::disk(self::DISK)->delete($old);
        }
    }

    public function delete(Merchant $merchant): void
    {
        DB::transaction(fn () => $merchant->delete());
        Storage::disk(self::DISK)->deleteDirectory("merchants/{$merchant->id}");
    }

    /** @param array<string, mixed> $data */
    public function saveShop(Merchant $merchant, ?MerchantShop $shop, array $data): MerchantShop
    {
        return DB::transaction(function () use ($merchant, $shop, $data) {
            $shop ??= new MerchantShop(['merchant_id' => $merchant->id]);
            $shop->fill($data)->save();

            if (! $merchant->shops()->where('is_default', true)->exists() && $shop->status) {
                $this->makeDefault($shop);
            }

            return $shop->refresh();
        });
    }

    public function makeDefault(MerchantShop $shop): void
    {
        DB::transaction(function () use ($shop) {
            MerchantShop::where('merchant_id', $shop->merchant_id)->where('is_default', true)->update(['is_default' => false]);
            $shop->forceFill(['is_default' => true, 'status' => true])->save();
        });
    }

    /** Deleting the default passes the role to the oldest remaining active pickup point. */
    public function deleteShop(MerchantShop $shop): void
    {
        DB::transaction(function () use ($shop) {
            $wasDefault = $shop->is_default;
            $shop->delete();

            if ($wasDefault && $next = MerchantShop::where('merchant_id', $shop->merchant_id)->where('status', true)->orderBy('id')->first()) {
                $this->makeDefault($next);
            }
        });
    }

    private function newUniqueId(): string
    {
        do {
            $id = (string) random_int(100000, 999999);
        } while (Merchant::where('unique_id', $id)->exists());

        return $id;
    }
}
