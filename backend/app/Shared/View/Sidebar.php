<?php

namespace App\Shared\View;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Resolves config('admin.menu') into links: hidden without the item's permission, disabled until
 * the route exists, active for the current route. Groups with no visible items are dropped.
 */
class Sidebar extends Component
{
    /** @var list<array<string, mixed>> */
    public array $menu = [];

    public function __construct()
    {
        foreach (config('admin.menu', []) as $entry) {
            if (isset($entry['items'])) {
                $items = array_values(array_filter(array_map(fn (array $item) => $this->link($item), $entry['items'])));

                if ($items !== []) {
                    $this->menu[] = [...$entry, 'items' => $items, 'active' => in_array(true, array_column($items, 'active'), true)];
                }
            } elseif ($link = $this->link($entry)) {
                $this->menu[] = $link;
            }
        }
    }

    /** @param array<string, mixed> $item */
    private function link(array $item): ?array
    {
        if (isset($item['permission']) && Gate::denies($item['permission'])) {
            return null;
        }

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
