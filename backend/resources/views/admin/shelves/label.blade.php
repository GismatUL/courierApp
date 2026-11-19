<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Qutu etiketi · {{ $shelf->code }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Inter, system-ui, sans-serif; color: #242325; background: #f7f6f5; }
        .toolbar { display: flex; justify-content: center; gap: 8px; padding: 16px; }
        .toolbar button { border: 0; border-radius: 12px; background: #c72127; color: #fff; font: 600 14px/1 inherit; padding: 12px 20px; cursor: pointer; }
        .label { width: 100mm; margin: 0 auto 24px; padding: 8mm; background: #fff; border: 1px dashed #e6e1e1; text-align: center; }
        .label svg { display: block; width: 55mm; height: 55mm; margin: 4mm auto; }
        .code { font: 700 28pt/1 ui-monospace, monospace; letter-spacing: 2px; }
        .name { font: 600 13pt/1.3 inherit; margin-top: 3mm; }
        .zone { font: 400 10pt/1.3 inherit; color: #686468; margin-top: 1mm; }
        .brand { font: 700 9pt/1 inherit; color: #c72127; letter-spacing: 1px; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .label { border: 0; margin: 0; }
            @page { size: 100mm 120mm; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar"><button type="button" onclick="window.print()">Çap et</button></div>
    <div class="label">
        <div class="brand">KURYER.NET</div>
        {!! $qr !!}
        <div class="code">{{ $shelf->code }}</div>
        <div class="name">{{ $shelf->name }}</div>
        <div class="zone">{{ $shelf->zone->name }}</div>
    </div>
</body>
</html>
