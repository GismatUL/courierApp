<?php

namespace App\Shared\Support;

/**
 * Permission catalogue (spec §9.1). Each module registers its rows from app/Modules/<Name>/permissions.php;
 * config/permissions.php holds the rows of modules that are not built yet. Keys stay identical to
 * sistem.kuryer.net (spec §13.1.4).
 */
class Permissions
{
    /** @var array<string, array{label: string, keys: array<string, string>}> */
    private static array $registered = [];

    /** @param array<string, array{label: string, keys: array<string, string>}> $modules */
    public static function register(array $modules): void
    {
        self::$registered = array_merge(self::$registered, $modules);
    }

    /** @return array<string, array{label: string, keys: array<string, string>}> */
    public static function modules(): array
    {
        return array_merge(self::$registered, config('permissions.modules', []));
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

    /**
     * Builds "<prefix>_<verb>" keys with their Azerbaijani labels.
     *
     * @param  list<string>  $verbs
     * @return array<string, string>
     */
    public static function crud(string $prefix, array $verbs = ['read', 'create', 'update', 'delete']): array
    {
        $labels = ['read' => 'Oxumaq', 'create' => 'Yaratmaq', 'update' => 'Yeniləmək', 'delete' => 'Silmək', 'view' => 'Baxmaq', 'edit' => 'Redaktə', 'assigned' => 'Təyin etmək'];

        return array_combine(array_map(fn ($verb) => "{$prefix}_{$verb}", $verbs), array_map(fn ($verb) => $labels[$verb], $verbs));
    }
}
