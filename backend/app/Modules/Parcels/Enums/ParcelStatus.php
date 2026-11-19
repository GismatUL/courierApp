<?php

namespace App\Modules\Parcels\Enums;

/**
 * Parcel statuses with the exact codes of sistem.kuryer.net (spec §3.1); both systems must use them
 * (spec §13.1.4). "Lost" has no visible code in the old system (spec §3), so it uses 50 here.
 */
enum ParcelStatus: int
{
    case Pending = 1;
    case PickupAssigned = 2;
    case PickupRescheduled = 3;
    case ReceivedByPickupman = 4;
    case ReceivedWarehouse = 5;
    case TransferredToHub = 6;
    case ReceivedByHub = 19;
    case DeliverymanAssigned = 7;
    case OutForDelivery = 34;
    case PartialDelivered = 32;
    case Delivered = 9;
    case ReturnToCourier = 24;
    case ReturnAssignedToMerchant = 26;
    case ReturnAssignedRescheduled = 27;
    case ReturnReceivedByMerchant = 30;
    case ReturnedToMerchant = 13;
    case Lost = 50;

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Gözləmədə',
            self::PickupAssigned => 'Kuryer (yığma) təyin edildi',
            self::PickupRescheduled => 'Kuryer yenidən təyin edildi',
            self::ReceivedByPickupman => 'Kuryer tərəfindən qəbul edildi',
            self::ReceivedWarehouse => 'Anbara qəbul edildi',
            self::TransferredToHub => 'Mərkəzə transfer edildi',
            self::ReceivedByHub => 'Mərkəz tərəfindən qəbul edildi',
            self::DeliverymanAssigned => 'Kuryer (çatdırma) təyin edildi',
            self::OutForDelivery => 'Kuryerdədir',
            self::PartialDelivered => 'Qismən çatdırıldı',
            self::Delivered => 'Çatdırıldı',
            self::ReturnToCourier => 'Kuryerə qaytarıldı',
            self::ReturnAssignedToMerchant => 'Partnyora qaytarma təyin edildi',
            self::ReturnAssignedRescheduled => 'Partnyora qaytarma yenidən cədvəl',
            self::ReturnReceivedByMerchant => 'Partnyor tərəfindən qaytarıldı',
            self::ReturnedToMerchant => 'Partnyora qaytarıldı',
            self::Lost => 'İtirilmiş',
        };
    }

    /** Badge colour group for lists. */
    public function tone(): string
    {
        return match ($this) {
            self::Delivered, self::PartialDelivered => 'success',
            self::ReturnToCourier, self::ReturnAssignedToMerchant, self::ReturnAssignedRescheduled,
            self::ReturnReceivedByMerchant, self::ReturnedToMerchant, self::Lost => 'danger',
            self::Pending => 'muted',
            default => 'info',
        };
    }

    /** Stage on the 5-step progress bar of the parcel log (spec §4.4). */
    public function stage(): int
    {
        return match ($this) {
            self::Pending => 1,
            self::PickupAssigned, self::PickupRescheduled, self::ReceivedByPickupman => 2,
            self::ReceivedWarehouse, self::TransferredToHub, self::ReceivedByHub => 3,
            self::DeliverymanAssigned, self::OutForDelivery => 4,
            default => 5,
        };
    }
}
