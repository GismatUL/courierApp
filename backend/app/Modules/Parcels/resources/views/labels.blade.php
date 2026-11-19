<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Etiketlər ({{ $parcels->count() }})</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Inter, system-ui, sans-serif; color: #242325; background: #f7f6f5; }
        .toolbar { display: flex; justify-content: center; padding: 16px; }
        .toolbar button { border: 0; border-radius: 12px; background: #c72127; color: #fff; font: 600 14px/1 inherit; padding: 12px 20px; cursor: pointer; }
        .label { width: 100mm; height: 100mm; margin: 0 auto 16px; padding: 6mm; background: #fff; border: 1px dashed #e6e1e1; display: flex; flex-direction: column; gap: 2mm; page-break-after: always; overflow: hidden; }
        .row { display: flex; justify-content: space-between; align-items: flex-start; gap: 4mm; }
        .brand { font: 700 10pt/1 inherit; color: #c72127; letter-spacing: 1px; }
        .code { font: 700 15pt/1.1 ui-monospace, monospace; }
        .label svg { width: 30mm; height: 30mm; flex-shrink: 0; }
        .small { font-size: 8.5pt; color: #686468; }
        .to { font-size: 10pt; line-height: 1.35; }
        .to b { font-size: 11pt; }
        .cod { margin-top: auto; display: flex; justify-content: space-between; border-top: 1px solid #242325; padding-top: 2mm; font: 700 11pt/1 inherit; }
        @media print { body { background: #fff; } .toolbar { display: none; } .label { border: 0; margin: 0; } @page { size: 100mm 100mm; margin: 0; } }
    </style>
</head>
<body>
    <div class="toolbar"><button type="button" onclick="window.print()">Çap et ({{ $parcels->count() }})</button></div>
    @foreach ($parcels as $parcel)
        <div class="label">
            <div class="row">
                <div>
                    <div class="brand">KURYER.NET</div>
                    <div class="code">{{ $parcel->tracking_id }}</div>
                    <div class="small">Faktura № {{ $parcel->invoice_no }} · {{ $parcel->created_date?->timezone('Asia/Baku')->format('d.m.Y H:i') }}</div>
                    <div class="small">Zona: {{ $parcel->zone->name }} · {{ $parcel->parcel_count }} ədəd · {{ (float) $parcel->weight }} kq</div>
                </div>
                {!! $qr[$parcel->id] !!}
            </div>
            <div class="small">Göndərən: {{ $parcel->merchant->business_name }}</div>
            <div class="to"><b>{{ $parcel->customer_name }}</b><br>{{ $parcel->customer_phone }}<br>{{ $parcel->customer_address }}</div>
            <div class="cod"><span>Toplanacaq</span><span>{{ $parcel->total }} ₼</span></div>
        </div>
    @endforeach
</body>
</html>
