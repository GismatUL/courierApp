<?php

/*
| Admin sidebar (spec §1 "Menyu strukturu"). Each item names a route; items whose route
| does not exist yet render as disabled, so a module appears in the menu once its routes land.
| Items whose `permission` key (spec §9.1) the user lacks are hidden, and so are groups left empty.
*/

return [
    'menu' => [
        // Open to every signed-in user (it is where login lands); its cards will check their own keys.
        ['label' => 'İdarə Paneli', 'icon' => 'layout-dashboard', 'route' => 'admin.dashboard'],

        ['label' => 'Paketlər', 'icon' => 'package', 'items' => [
            ['label' => 'Paketlər', 'route' => 'admin.parcels.index', 'permission' => 'parcel_read'],
            ['label' => 'Əlavə et', 'route' => 'admin.parcels.create', 'permission' => 'parcel_create'],
        ]],

        ['label' => 'Kuryer Paneli', 'icon' => 'scan-barcode', 'items' => [
            ['label' => 'Sifarişlər', 'route' => 'admin.panel.orders', 'permission' => 'deliveryman_panel_read'],
            ['label' => 'Paketlər', 'route' => 'admin.panel.parcels', 'permission' => 'deliveryman_panel_read'],
            ['label' => 'Qutular', 'route' => 'admin.panel.boxes', 'permission' => 'deliveryman_panel_read'],
            ['label' => 'Kuryerdədir', 'route' => 'admin.panel.assigned', 'permission' => 'deliveryman_panel_read'],
            ['label' => 'Çatdırıldı', 'route' => 'admin.panel.delivered', 'permission' => 'deliveryman_panel_read'],
            ['label' => 'Qaytarıldı', 'route' => 'admin.panel.returned', 'permission' => 'deliveryman_panel_read'],
            ['label' => 'İtirilmiş', 'route' => 'admin.panel.lost', 'permission' => 'deliveryman_panel_read'],
            ['label' => 'Anbar', 'route' => 'admin.panel.warehouse', 'permission' => 'deliveryman_panel_read'],
            ['label' => 'Kuryer Sənədi', 'route' => 'admin.courier-documents.index', 'permission' => 'deliveryman_document_read'],
            ['label' => 'Hesabatlar', 'route' => 'admin.panel.statistics', 'permission' => 'deliveryman_panel_read'],
        ]],

        ['label' => 'Partnyor İdarəetməsi', 'icon' => 'briefcase', 'items' => [
            ['label' => 'Partnyorlar', 'route' => 'admin.merchants.index', 'permission' => 'merchant_read'],
        ]],

        ['label' => 'İdarəetmə', 'icon' => 'settings-2', 'items' => [
            ['label' => 'Filiallar', 'route' => 'admin.hubs.index', 'permission' => 'hub_read'],
            ['label' => 'Kuryer', 'route' => 'admin.couriers.index', 'permission' => 'delivery_man_read'],
            ['label' => 'Zona', 'route' => 'admin.zones.index', 'permission' => 'zone'],
            ['label' => 'Paket Növləri', 'route' => 'admin.package-types.index', 'permission' => 'zone'],
            ['label' => 'Xəritə', 'route' => 'admin.map', 'permission' => 'delivery_man_read'],
            ['label' => 'Xəritə Yüklə', 'route' => 'admin.map.upload', 'permission' => 'zone'],
            ['label' => 'Qutular', 'route' => 'admin.shelves.index', 'permission' => 'shelf_read'],
            ['label' => 'Geri Qaytarma səbəbləri', 'route' => 'admin.reject-reasons.index', 'permission' => 'zone'],
        ]],

        ['label' => 'Hesablar', 'icon' => 'wallet', 'items' => [
            ['label' => 'Hesablar', 'route' => 'admin.accounts.index', 'permission' => 'account_read'],
            ['label' => 'Gəlir', 'route' => 'admin.incomes.index', 'permission' => 'income_read'],
            ['label' => 'Xərc', 'route' => 'admin.expenses.index', 'permission' => 'expense_read'],
            ['label' => 'Bank Əməliyyatları', 'route' => 'admin.bank-transactions.index', 'permission' => 'bank_transaction_read'],
        ]],

        ['label' => 'İstifadəçilər və Rollar', 'icon' => 'users', 'items' => [
            ['label' => 'Rollar', 'route' => 'admin.roles.index', 'permission' => 'role_read'],
            ['label' => 'Vəzifələr', 'route' => 'admin.designations.index', 'permission' => 'designation_read'],
            ['label' => 'Şöbələr', 'route' => 'admin.departments.index', 'permission' => 'department_read'],
            ['label' => 'İstifadəçilər', 'route' => 'admin.users.index', 'permission' => 'user_read'],
            ['label' => 'Müştəri', 'route' => 'admin.customers.index', 'permission' => 'customer_read'],
        ]],

        ['label' => 'Maaş Cədvəli', 'icon' => 'banknote', 'items' => [
            ['label' => 'Əmək haqqı yaradın', 'route' => 'admin.salary-generates.index', 'permission' => 'salary_generate_read'],
            ['label' => 'Əməkhaqqı', 'route' => 'admin.salary-payments.index', 'permission' => 'salary_read'],
        ]],

        ['label' => 'Hesabatlar', 'icon' => 'chart-column', 'items' => [
            ['label' => 'Paket Statusu', 'route' => 'admin.reports.parcel-status', 'permission' => 'parcel_status_reports'],
            ['label' => 'Maaş', 'route' => 'admin.reports.salary', 'permission' => 'salary_reports'],
            ['label' => 'Kuryer', 'route' => 'admin.reports.entity', 'permission' => 'merchant_hub_deliveryman'],
            ['label' => 'Ümumi Xülasə', 'route' => 'admin.reports.summary', 'permission' => 'parcel_total_summery'],
        ]],

        ['label' => 'Tənzimləmələr', 'icon' => 'sliders-horizontal', 'items' => [
            ['label' => 'Ümumi', 'route' => 'admin.settings.general', 'permission' => 'general_settings_read'],
            ['label' => 'Google Xəritə', 'route' => 'admin.settings.map', 'permission' => 'general_settings_read'],
            ['label' => 'Aktiv Gündəliklər', 'route' => 'admin.activity-logs.index', 'permission' => 'log_read'],
            ['label' => 'Valyuta', 'route' => 'admin.currencies.index', 'permission' => 'currency_read'],
        ]],
    ],

    'locales' => ['az' => 'Azərbaycan', 'en' => 'English', 'ru' => 'Русский', 'tr' => 'Türkçe'],
];
