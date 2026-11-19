@props(['status'])

<span @class([
    'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold whitespace-nowrap',
    'bg-emerald-50 text-emerald-700' => $status->tone() === 'success',
    'bg-brand-surface text-brand' => $status->tone() === 'danger',
    'bg-sky-50 text-sky-700' => $status->tone() === 'info',
    'bg-surface text-muted' => $status->tone() === 'muted',
])>{{ $status->label() }}</span>
