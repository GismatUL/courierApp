<?php

namespace App\Modules\Parcels\Workflow;

use App\Modules\Parcels\Enums\ParcelStatus as S;

/**
 * The central transition table (spec §3: "keçid cədvəli backend-də mərkəzi saxlanılmalı").
 * Each action: label, statuses it may start from, resulting status (null = status unchanged),
 * the fields its form needs, and who may get an SMS. Anything not listed here is refused (422).
 */
final class ParcelActions
{
    /** @return array<string, array{label: string, from: list<S>, to: ?S, fields: list<string>, required: list<string>, sms: list<string>}> */
    public static function all(): array
    {
        return [
            'assign_pickup' => ['label' => 'Yığma kuryeri təyin et', 'from' => [S::Pending, S::PickupAssigned, S::PickupRescheduled], 'to' => S::PickupAssigned,
                'fields' => ['delivery_man_id', 'note'], 'required' => ['delivery_man_id'], 'sms' => ['courier', 'merchant']],
            'pickup_reschedule' => ['label' => 'Yığmanı yenidən cədvəllə', 'from' => [S::PickupAssigned, S::PickupRescheduled], 'to' => S::PickupRescheduled,
                'fields' => ['delivery_man_id', 'date', 'note'], 'required' => ['delivery_man_id', 'date'], 'sms' => ['courier', 'merchant']],
            'pickup_received' => ['label' => 'Kuryer tərəfindən qəbul edildi', 'from' => [S::PickupAssigned, S::PickupRescheduled], 'to' => S::ReceivedByPickupman,
                'fields' => ['note'], 'required' => [], 'sms' => []],
            'received_warehouse' => ['label' => 'Anbara qəbul et', 'from' => [S::Pending, S::PickupAssigned, S::PickupRescheduled, S::ReceivedByPickupman, S::ReturnedToMerchant], 'to' => S::ReceivedWarehouse,
                'fields' => ['hub_id', 'note'], 'required' => ['hub_id'], 'sms' => ['customer', 'merchant']],
            'transfer_to_hub' => ['label' => 'Mərkəzə transfer', 'from' => [S::ReceivedWarehouse, S::ReceivedByHub], 'to' => S::TransferredToHub,
                'fields' => ['hub_id', 'delivery_man_id', 'note'], 'required' => ['hub_id'], 'sms' => []],
            'received_by_hub' => ['label' => 'Mərkəz tərəfindən qəbul et', 'from' => [S::TransferredToHub], 'to' => S::ReceivedByHub,
                'fields' => ['note'], 'required' => [], 'sms' => []],
            'assign_delivery' => ['label' => 'Çatdırma kuryerini təyin et', 'from' => [S::ReceivedWarehouse, S::ReceivedByHub, S::DeliverymanAssigned], 'to' => S::DeliverymanAssigned,
                'fields' => ['delivery_man_id', 'date', 'note'], 'required' => ['delivery_man_id'], 'sms' => ['courier', 'customer']],
            'out_for_delivery' => ['label' => 'Kuryerdədir', 'from' => [S::DeliverymanAssigned], 'to' => S::OutForDelivery,
                'fields' => ['note'], 'required' => [], 'sms' => ['customer']],
            'change_courier' => ['label' => 'Kuryeri dəyiş', 'from' => [S::DeliverymanAssigned, S::OutForDelivery], 'to' => null,
                'fields' => ['delivery_man_id', 'note'], 'required' => ['delivery_man_id'], 'sms' => ['courier']],
            'delivered' => ['label' => 'Çatdırıldı', 'from' => [S::DeliverymanAssigned, S::OutForDelivery], 'to' => S::Delivered,
                'fields' => ['payment', 'note'], 'required' => ['payment'], 'sms' => ['customer', 'merchant']],
            'partial_delivered' => ['label' => 'Qismən çatdırıldı', 'from' => [S::DeliverymanAssigned, S::OutForDelivery], 'to' => S::PartialDelivered,
                'fields' => ['collected', 'note'], 'required' => ['collected'], 'sms' => ['customer', 'merchant']],
            'return_to_courier' => ['label' => 'Kuryerə qaytarıldı', 'from' => [S::DeliverymanAssigned, S::OutForDelivery], 'to' => S::ReturnToCourier,
                'fields' => ['reject_reason_id', 'note'], 'required' => [], 'sms' => ['merchant']],
            'assign_return' => ['label' => 'Qaytarmanı partnyora təyin et', 'from' => [S::ReturnToCourier, S::ReceivedWarehouse], 'to' => S::ReturnAssignedToMerchant,
                'fields' => ['delivery_man_id', 'date', 'note'], 'required' => ['delivery_man_id'], 'sms' => ['courier', 'merchant']],
            'return_reschedule' => ['label' => 'Qaytarmanı yenidən cədvəllə', 'from' => [S::ReturnAssignedToMerchant, S::ReturnAssignedRescheduled], 'to' => S::ReturnAssignedRescheduled,
                'fields' => ['delivery_man_id', 'date', 'note'], 'required' => ['delivery_man_id', 'date'], 'sms' => ['courier', 'merchant']],
            'return_received_by_merchant' => ['label' => 'Partnyor qaytarmanı qəbul etdi', 'from' => [S::ReturnAssignedToMerchant, S::ReturnAssignedRescheduled], 'to' => S::ReturnReceivedByMerchant,
                'fields' => ['note'], 'required' => [], 'sms' => []],
            'returned_to_merchant' => ['label' => 'Partnyora qaytarıldı', 'from' => [S::ReceivedWarehouse, S::ReturnToCourier], 'to' => S::ReturnedToMerchant,
                'fields' => ['note'], 'required' => [], 'sms' => ['merchant']],
            'mark_lost' => ['label' => 'İtirilmiş kimi qeyd et', 'from' => [S::ReceivedWarehouse, S::TransferredToHub, S::ReceivedByHub, S::DeliverymanAssigned, S::OutForDelivery, S::ReturnToCourier], 'to' => S::Lost,
                'fields' => ['note'], 'required' => [], 'sms' => []],
        ];
    }

    /** @return array{label: string, from: list<S>, to: ?S, fields: list<string>, required: list<string>, sms: list<string>} */
    public static function get(string $action): array
    {
        return self::all()[$action] ?? throw new \InvalidArgumentException("Unknown parcel action [{$action}].");
    }

    /** @return array<string, string> action => label, for actions allowed from this status */
    public static function availableFor(S $status): array
    {
        return array_map(fn ($a) => $a['label'], array_filter(self::all(), fn ($a) => in_array($status, $a['from'], true)));
    }
}
