<?php

use App\Shared\Support\Permissions;

// Merchants module rows (spec §9.1). Merchant types have no keys of their own; they follow the merchant keys.
return [
    'merchant' => ['label' => 'Partnyor', 'keys' => Permissions::crud('merchant', ['read', 'create', 'update', 'delete', 'view'])],
    'merchant_delivery_charge' => ['label' => 'Partnyor tarifləri', 'keys' => Permissions::crud('merchant_delivery_charge')],
    'merchant_shop' => ['label' => 'Partnyor filialları', 'keys' => Permissions::crud('merchant_shop')],
    'merchant_payment' => ['label' => 'Partnyor ödəniş hesabları', 'keys' => Permissions::crud('merchant_payment')],
];
