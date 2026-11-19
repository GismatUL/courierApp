<?php

namespace App\Support;

/** Read access to the permission catalogue in config/permissions.php. */
class Permissions
{
    /** @return array<string, array{label: string, keys: array<string, string>}> */
    public static function modules(): array
    {
        return config('permissions.modules');
    }

    /** @return list<string> */
    public static function all(): array
    {
        return array_merge(...array_map(fn (array $module) => array_keys($module['keys']), array_values(self::modules())));
    }

    /**
     * Keeps only known keys, without duplicates, in catalogue order.
     *
     * @param  array<int, string>  $keys
     * @return list<string>
     */
    public static function sanitize(array $keys): array
    {
        return array_values(array_intersect(self::all(), $keys));
    }
}
