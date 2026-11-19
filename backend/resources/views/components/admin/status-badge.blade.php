@props(['active'])

<span @class([
    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold',
    'bg-emerald-50 text-emerald-700' => $active,
    'bg-surface text-muted' => ! $active,
])>
    <span @class(['size-1.5 rounded-full', 'bg-emerald-500' => $active, 'bg-muted' => ! $active])></span>
    {{ $active ? 'Aktiv' : 'Passiv' }}
</span>
