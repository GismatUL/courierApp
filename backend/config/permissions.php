<?php

use App\Shared\Support\Permissions;

/*
| Permission rows for modules that are not built yet (spec §9.1). When a module is built, its rows move
| to app/Modules/<Name>/permissions.php. Keys stay exactly as in sistem.kuryer.net, typos included
| ("menually", "summery", "assing", "deliverd"), because both systems must share them (spec §13.1.4).
*/

return [
    'modules' => [
        'account' => ['label' => 'Hesablar', 'keys' => Permissions::crud('account') + ['account_heads_read' => 'Hesab başlıqları']],
        'income' => ['label' => 'Gəlir', 'keys' => Permissions::crud('income')],
        'expense' => ['label' => 'Xərc', 'keys' => Permissions::crud('expense')],
        'bank_transaction' => ['label' => 'Bank əməliyyatları', 'keys' => ['bank_transaction_read' => 'Oxumaq']],
        'cash_received' => ['label' => 'Kuryerdən alınan nağd', 'keys' => Permissions::crud('cash_received_from_delivery_man')],
        'todo' => ['label' => 'Ediləcək işlər', 'keys' => Permissions::crud('todo')],
        'invoice' => ['label' => 'Faktura', 'keys' => [
            'invoice_read' => 'Oxumaq', 'invoice_status_update' => 'Status dəyişmək',
            'paid_invoice_read' => 'Ödənilmiş fakturalar', 'invoice_generate_menually' => 'Əl ilə yaratmaq',
        ]],
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
        'salary' => ['label' => 'Maaş', 'keys' => Permissions::crud('salary') + Permissions::crud('salary_generate')],
    ],
];
