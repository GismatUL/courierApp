<?php

// Reports module rows of the permission matrix (spec §9.1).
return [
    'dashboard' => ['label' => 'Panel', 'keys' => [
        'dashboard_read' => 'Paneli görmək', 'calendar_read' => 'Təqvim',
        'total_parcel' => 'Paketlər (ümumi)', 'total_user' => 'İstifadəçi sayı', 'total_merchant' => 'Partnyor sayı',
        'total_delivery_man' => 'Kuryer sayı', 'total_hubs' => 'Filial sayı', 'total_accounts' => 'Hesab sayı',
        'total_parcels_pending' => 'Gözləyən', 'total_pickup_assigned' => 'Yığma təyin edilmiş',
        'total_received_warehouse' => 'Anbara qəbul', 'total_deliveryman_assigned' => 'Kuryerdə',
        'total_partial_deliverd' => 'Qismən çatdırılmış', 'total_parcels_deliverd' => 'Çatdırılmış',
        'income_expense_charts' => 'Gəlir/Xərc qrafiki', 'merchant_revenue_charts' => 'Partnyor gəlir qrafiki',
        'deliveryman_revenue_charts' => 'Kuryer gəlir qrafiki', 'courier_revenue_charts' => 'Kuryer gəlir (donut)',
        'recent_parcels' => 'Son paketlər',
    ]],
    'log' => ['label' => 'Jurnallar', 'keys' => ['log_read' => 'Oxumaq']],
    'reports' => ['label' => 'Hesabatlar', 'keys' => [
        'parcel_status_reports' => 'Paket statusu', 'parcel_wise_profit' => 'Paket üzrə gəlir',
        'parcel_total_summery' => 'Ümumi xülasə', 'salary_reports' => 'Maaş', 'merchant_hub_deliveryman' => 'Partnyor / filial / kuryer',
    ]],
];
