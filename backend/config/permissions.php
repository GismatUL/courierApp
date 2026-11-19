<?php

/*
| Permission catalogue (spec §9.1). Keys are kept exactly as in sistem.kuryer.net, typos included
| ("menually", "summery", "assing", "deliverd"), because both systems must share them (spec §13.1.4).
| Modules the old template had but the menu never used are not carried over (spec §9.1).
| Package types and return reasons have no keys of their own in the spec; they use the zone keys.
*/

$crud = fn (string $prefix, array $verbs = ['read', 'create', 'update', 'delete']) => array_combine(
    array_map(fn ($verb) => "{$prefix}_{$verb}", $verbs),
    array_map(fn ($verb) => ['read' => 'Oxumaq', 'create' => 'Yaratmaq', 'update' => 'Yeniləmək', 'delete' => 'Silmək', 'view' => 'Baxmaq', 'edit' => 'Redaktə', 'assigned' => 'Təyin etmək'][$verb], $verbs),
);

return [
    'modules' => [
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
        'hub' => ['label' => 'Filial', 'keys' => $crud('hub', ['read', 'create', 'update', 'delete', 'view'])],
        'hub_incharge' => ['label' => 'Filial məsulu', 'keys' => $crud('hub_incharge', ['read', 'create', 'update', 'delete', 'assigned'])],
        'account' => ['label' => 'Hesablar', 'keys' => $crud('account') + ['account_heads_read' => 'Hesab başlıqları']],
        'income' => ['label' => 'Gəlir', 'keys' => $crud('income')],
        'expense' => ['label' => 'Xərc', 'keys' => $crud('expense')],
        'bank_transaction' => ['label' => 'Bank əməliyyatları', 'keys' => ['bank_transaction_read' => 'Oxumaq']],
        'cash_received' => ['label' => 'Kuryerdən alınan nağd', 'keys' => $crud('cash_received_from_delivery_man')],
        'todo' => ['label' => 'Ediləcək işlər', 'keys' => $crud('todo')],
        'role' => ['label' => 'Rollar', 'keys' => $crud('role')],
        'designation' => ['label' => 'Vəzifələr', 'keys' => $crud('designation')],
        'department' => ['label' => 'Şöbələr', 'keys' => $crud('department')],
        'user' => ['label' => 'İstifadəçilər', 'keys' => $crud('user') + ['permission_update' => 'İcazələri dəyişmək']],
        'merchant' => ['label' => 'Partnyor', 'keys' => $crud('merchant', ['read', 'create', 'update', 'delete', 'view'])],
        'merchant_delivery_charge' => ['label' => 'Partnyor tarifləri', 'keys' => $crud('merchant_delivery_charge')],
        'merchant_shop' => ['label' => 'Partnyor filialları', 'keys' => $crud('merchant_shop')],
        'merchant_payment' => ['label' => 'Partnyor ödəniş hesabları', 'keys' => $crud('merchant_payment')],
        'invoice' => ['label' => 'Faktura', 'keys' => [
            'invoice_read' => 'Oxumaq', 'invoice_status_update' => 'Status dəyişmək',
            'paid_invoice_read' => 'Ödənilmiş fakturalar', 'invoice_generate_menually' => 'Əl ilə yaratmaq',
        ]],
        'parcel' => ['label' => 'Paket', 'keys' => $crud('parcel') + ['parcel_status_update' => 'Status dəyişmək']],
        'delivery_man' => ['label' => 'Kuryer', 'keys' => $crud('delivery_man')],
        'deliveryman_panel' => ['label' => 'Kuryer Paneli', 'keys' => [
            'deliveryman_panel_read' => 'Oxumaq', 'deliveryman_panel_parcel_return' => 'Paket qaytarmaq',
            'deliveryman_panel_change_deliveryman' => 'Kuryer dəyişmək', 'deliveryman_document_read' => 'Kuryer sənədi',
            'order_read' => 'Sifarişlər', 'customer_read' => 'Müştərilər',
        ]],
        'sorting' => ['label' => 'Çeşidləmə və qutular', 'keys' => [
            'sorting_read' => 'Çeşidləmə', 'sorted_packages' => 'Sıralanmış paketlər',
            'shelf_read' => 'Qutular: oxumaq', 'shelf_create' => 'Qutular: yaratmaq', 'shelf_edit' => 'Qutular: redaktə', 'shelf_delete' => 'Qutular: silmək',
            'parcel_shelf' => 'Paketi qutuya əlavə et', 'shelf_assing' => 'Qutunu kuryerə təyin et',
        ]],
        'zone' => ['label' => 'Zona, region, paket növü, qaytarma səbəbi', 'keys' => [
            'zone' => 'Oxumaq', 'zone_create' => 'Yaratmaq', 'zone_edit' => 'Redaktə', 'zone_delete' => 'Silmək',
            'region' => 'Regionlar: oxumaq', 'region_create' => 'Regionlar: yaratmaq', 'region_edit' => 'Regionlar: redaktə', 'region_delete' => 'Regionlar: silmək',
        ]],
        'salary' => ['label' => 'Maaş', 'keys' => $crud('salary') + $crud('salary_generate')],
        'reports' => ['label' => 'Hesabatlar', 'keys' => [
            'parcel_status_reports' => 'Paket statusu', 'parcel_wise_profit' => 'Paket üzrə gəlir',
            'parcel_total_summery' => 'Ümumi xülasə', 'salary_reports' => 'Maaş', 'merchant_hub_deliveryman' => 'Partnyor / filial / kuryer',
        ]],
        'settings' => ['label' => 'Tənzimləmələr', 'keys' => [
            'general_settings_read' => 'Oxumaq', 'general_settings_update' => 'Yeniləmək',
        ] + $crud('currency')],
    ],
];
