<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ isset($title) ? $title.' · ' : '' }}Kuryer.net Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-brand-surface px-4 py-10 font-sans text-ink antialiased">
    <main class="w-full max-w-[420px]">
        {{ $slot }}
    </main>
</body>
</html>
