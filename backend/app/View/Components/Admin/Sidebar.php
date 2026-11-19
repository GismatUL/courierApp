<?php

namespace App\View\Components\Admin;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\View\View;

/** Resolves config('admin.menu') into links: disabled until the route exists, active for the current route. */
class Sidebar extends Component
{
    /** @var list<array<string, mixed>> */
    public array $menu;

    public function __construct()
    {
        $this->menu = array_map(fn (array $entry) => isset($entry['items'])
            ? $this->group($entry)
            : $this->link($entry), config('admin.menu', []));
    }

    /** @param array<string, mixed> $group */
    private function group(array $group): array
    {
        $items = array_map(fn (array $item) => $this->link($item), $group['items']);

        return [
            ...$group,
            'items' => $items,
            'active' => in_array(true, array_column($items, 'active'), true),
        ];
    }

    /** @param array<string, mixed> $item */
    private function link(array $item): array
    {
        $exists = Route::has($item['route']);

        return [
            ...$item,
            'url' => $exists ? route($item['route']) : null,
            // "admin.zones.index" stays active on its sub-pages, e.g. "admin.zones.tariffs".
            'active' => $exists && request()->routeIs($item['route'], preg_replace('/\.index$/', '.*', $item['route'])),
        ];
    }

    public function render(): View
    {
        return view('components.admin.sidebar');
    }
}
