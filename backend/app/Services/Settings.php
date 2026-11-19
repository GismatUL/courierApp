<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Application settings (spec §10.2 general, §10.3 Google Maps) stored as key/value rows.
 * All rows are cached together and the cache is dropped on every write.
 */
class Settings
{
    private const CACHE_KEY = 'settings.all';

    /** Defaults used until a value is saved. */
    public const DEFAULTS = [
        'name' => 'Kuryer.net',
        'phone' => '+994 12 400 00 00',
        'email' => 'info@kuryer.net',
        'address' => null,
        'tax' => '0',
        'currency' => 'AZN',
        'par_track_prefix' => 'KN',
        'invoice_prefix' => null,
        'copyright' => '© Kuryer.net',
        'logo' => null,
        'light_logo' => null,
        'favicon' => null,
        'primary_color' => '#C72127',
        'text_color' => '#242325',
        'map_key' => null,
    ];

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default ?? self::DEFAULTS[$key] ?? null;
    }

    /** @return array<string, ?string> */
    public function all(): array
    {
        return array_merge(self::DEFAULTS, Cache::rememberForever(
            self::CACHE_KEY,
            fn () => Setting::query()->pluck('value', 'key')->all(),
        ));
    }

    /** @param array<string, mixed> $values */
    public function set(array $values): void
    {
        DB::transaction(function () use ($values) {
            foreach ($values as $key => $value) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value === null ? null : (string) $value]);
            }
        });

        Cache::forget(self::CACHE_KEY);
    }
}
