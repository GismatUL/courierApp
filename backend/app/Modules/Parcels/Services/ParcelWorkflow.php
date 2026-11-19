<?php

namespace App\Modules\Parcels\Services;

use App\Modules\Couriers\Enums\BalanceChannel;
use App\Modules\Couriers\Models\Courier;
use App\Modules\Couriers\Services\CourierBalance;
use App\Modules\Parcels\Enums\ParcelStatus;
use App\Modules\Parcels\Events\ParcelStatusChanged;
use App\Modules\Parcels\Models\Parcel;
use App\Modules\Parcels\Models\ParcelEvent;
use App\Modules\Parcels\Models\ParcelProof;
use App\Modules\Parcels\Workflow\ParcelActions;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Applies status actions (spec §3). Rules:
 * - only transitions listed in ParcelActions are allowed, otherwise 422;
 * - every action writes a parcel_event with the previous values of what it changed;
 * - undo reverts only the latest event and restores those values; undoing a delivery also takes the
 *   collected money back off the courier's balance (and Finance reverses its ledger row via the event);
 * - "delivered" requires the cash / POS / card split to equal the amount to collect (spec §5.1);
 * - bulk runs each parcel separately and reports the ones that failed (spec §3).
 */
class ParcelWorkflow
{
    private const TRACKED = ['status', 'hub_id', 'pickupman_id', 'delivery_man_id', 'reject_reason_id', 'delivered_at', 'status_updated_at'];

    public function __construct(private readonly CourierBalance $balances) {}

    /** @param array<string, mixed> $input */
    public function apply(Parcel $parcel, string $action, array $input, ?int $actorId): ParcelEvent
    {
        $definition = ParcelActions::get($action);

        return DB::transaction(function () use ($parcel, $action, $definition, $input, $actorId) {
            $parcel = Parcel::query()->lockForUpdate()->findOrFail($parcel->id);

            if (! in_array($parcel->status, $definition['from'], true)) {
                throw ValidationException::withMessages(['action' => "\"{$definition['label']}\" əməliyyatı \"{$parcel->status->label()}\" statusundan mümkün deyil."]);
            }

            $data = $this->validateInput($parcel, $definition, $input);
            $before = $parcel->only(self::TRACKED);
            $before['status'] = $parcel->status->value;
            $meta = array_filter([
                'date' => $data['date'] ?? null,
                'sms' => array_values(array_intersect($input['sms'] ?? [], $definition['sms'])),
            ]);

            match ($action) {
                'assign_pickup', 'pickup_reschedule' => $parcel->pickupman_id = (int) $data['delivery_man_id'],
                'received_warehouse', 'received_by_hub' => $parcel->hub_id = (int) ($data['hub_id'] ?? $parcel->hub_id),
                'transfer_to_hub' => $parcel->hub_id = (int) $data['hub_id'],
                'assign_delivery', 'change_courier', 'assign_return', 'return_reschedule' => $parcel->delivery_man_id = (int) $data['delivery_man_id'],
                'return_to_courier' => $parcel->reject_reason_id = $data['reject_reason_id'] ?? null,
                default => null,
            };

            if ($action === 'delivered' || $action === 'partial_delivered') {
                $meta['payment'] = $action === 'delivered'
                    ? ['cash' => (float) ($data['cash_pay'] ?? 0), 'pos' => (float) ($data['pos_pay'] ?? 0), 'card' => (float) ($data['card_pay'] ?? 0)]
                    : ['cash' => (float) $data['collected'], 'pos' => 0.0, 'card' => 0.0];
                $parcel->delivered_at = now();
                $this->creditCourier($parcel, $meta['payment']);
                ParcelProof::create(['parcel_id' => $parcel->id, 'cash_pay' => $meta['payment']['cash'], 'pos_pay' => $meta['payment']['pos'], 'card_pay' => $meta['payment']['card'], 'note' => $data['note'] ?? null]);
            }

            if ($definition['to'] !== null) {
                $parcel->status = $definition['to'];
            }
            $parcel->status_updated_at = now();
            $parcel->save();

            $event = ParcelEvent::create([
                'parcel_id' => $parcel->id,
                'action' => $action,
                'from_status' => $before['status'],
                'status' => $parcel->status,
                'actor_user_id' => $actorId,
                'delivery_man_id' => $data['delivery_man_id'] ?? (in_array($action, ['delivered', 'partial_delivered', 'out_for_delivery', 'return_to_courier'], true) ? $parcel->delivery_man_id : null),
                'hub_id' => $parcel->hub_id,
                'from_hub_id' => $action === 'transfer_to_hub' ? $before['hub_id'] : null,
                'note' => $data['note'] ?? null,
                'latitude' => $input['latitude'] ?? null,
                'longitude' => $input['longitude'] ?? null,
                'before' => $before,
                'meta' => $meta,
            ]);

            ParcelStatusChanged::dispatch($parcel, $event);

            return $event;
        });
    }

    /** Reverts the latest event (spec §3 "geri alma yalnız son hadisəni ləğv edir"). */
    public function undo(Parcel $parcel, ?int $actorId): ParcelEvent
    {
        return DB::transaction(function () use ($parcel, $actorId) {
            $parcel = Parcel::query()->lockForUpdate()->findOrFail($parcel->id);
            $last = ParcelEvent::where('parcel_id', $parcel->id)->where('reverted', false)->where('action', '!=', 'undo')->orderByDesc('id')->first();

            if ($last === null || $last->action === 'create') {
                throw ValidationException::withMessages(['action' => 'Geri alınacaq əməliyyat yoxdur.']);
            }

            if (isset($last->meta['payment'])) {
                $this->debitCourier($parcel, $last->meta['payment']);
                ParcelProof::where('parcel_id', $parcel->id)->latest('id')->first()?->delete();
            }

            foreach ($last->before as $field => $value) {
                $parcel->{$field} = $value;
            }
            $parcel->status_updated_at = now();
            $parcel->save();

            $last->forceFill(['reverted' => true])->save();
            $event = ParcelEvent::create([
                'parcel_id' => $parcel->id, 'action' => 'undo', 'from_status' => $last->status, 'status' => $parcel->status,
                'actor_user_id' => $actorId, 'note' => 'Geri alındı: '.(ParcelActions::all()[$last->action]['label'] ?? $last->action),
                'meta' => ['reverted_event_id' => $last->id],
            ]);

            ParcelStatusChanged::dispatch($parcel, $event, $last);

            return $event;
        });
    }

    /**
     * @param  list<string>  $trackingIds
     * @param  array<string, mixed>  $input
     * @return array{done: list<string>, failed: array<string, string>}
     */
    public function bulk(array $trackingIds, string $action, array $input, ?int $actorId): array
    {
        $result = ['done' => [], 'failed' => []];

        foreach (array_unique(array_filter(array_map('trim', $trackingIds))) as $trackingId) {
            $parcel = Parcel::where('tracking_id', $trackingId)->first();

            if ($parcel === null) {
                $result['failed'][$trackingId] = 'Paket tapılmadı.';

                continue;
            }

            try {
                $this->apply($parcel, $action, $input, $actorId);
                $result['done'][] = $trackingId;
            } catch (ValidationException $e) {
                $result['failed'][$trackingId] = collect($e->errors())->flatten()->first();
            } catch (DomainException $e) {
                $result['failed'][$trackingId] = $e->getMessage();
            }
        }

        return $result;
    }

    /** @return array<string, mixed> */
    private function validateInput(Parcel $parcel, array $definition, array $input): array
    {
        $activeCourier = Rule::exists('delivery_men', 'id')->where(fn ($q) => $q->whereIn('user_id', fn ($u) => $u->select('id')->from('users')->where('status', true)));
        $all = [
            'delivery_man_id' => ['integer', $activeCourier],
            'hub_id' => ['integer', Rule::exists('hubs', 'id')->where('status', true)],
            'date' => ['date'],
            'note' => ['nullable', 'string', 'max:1000'],
            'reject_reason_id' => ['nullable', 'integer', 'exists:reject_reasons,id'],
            'collected' => ['numeric', 'min:0', 'max:'.(float) $parcel->total],
        ];
        $rules = [];

        foreach ($definition['fields'] as $field) {
            if ($field === 'payment') {
                foreach (['cash_pay', 'pos_pay', 'card_pay'] as $channel) {
                    $rules[$channel] = ['nullable', 'numeric', 'min:0', 'max:9999999'];
                }

                continue;
            }
            $rules[$field] = [in_array($field, $definition['required'], true) ? 'required' : 'nullable', ...$all[$field]];
        }

        $data = Validator::make($input, $rules, [
            'collected.max' => 'Toplanan məbləğ ümumi məbləğdən ('.$parcel->total.' ₼) çox ola bilməz.',
        ], ['delivery_man_id' => 'Kuryer', 'hub_id' => 'Mərkəz', 'date' => 'Tarix', 'collected' => 'Nağd toplama'])->validate();

        if (in_array('payment', $definition['fields'], true)) {
            $sum = round((float) ($data['cash_pay'] ?? 0) + (float) ($data['pos_pay'] ?? 0) + (float) ($data['card_pay'] ?? 0), 2);
            if (abs($sum - (float) $parcel->total) > 0.001) {
                throw ValidationException::withMessages(['cash_pay' => "Ödənişlərin cəmi ({$sum} ₼) toplanmalı məbləğə ({$parcel->total} ₼) bərabər olmalıdır."]);
            }
        }

        if (($definition['to'] ?? null) === ParcelStatus::TransferredToHub && (int) $data['hub_id'] === (int) $parcel->hub_id) {
            throw ValidationException::withMessages(['hub_id' => 'Paket artıq bu mərkəzdədir.']);
        }

        return $data;
    }

    /** @param array{cash: float, pos: float, card: float} $payment */
    private function creditCourier(Parcel $parcel, array $payment): void
    {
        $courier = Courier::findOrFail($parcel->delivery_man_id);

        foreach ([BalanceChannel::Cash, BalanceChannel::Pos, BalanceChannel::Card] as $channel) {
            if (($payment[$channel->value] ?? 0) > 0) {
                $this->balances->credit($courier, $channel, $payment[$channel->value]);
            }
        }
    }

    /** @param array{cash: float, pos: float, card: float} $payment */
    private function debitCourier(Parcel $parcel, array $payment): void
    {
        $courier = Courier::findOrFail($parcel->delivery_man_id);

        try {
            foreach ([BalanceChannel::Cash, BalanceChannel::Pos, BalanceChannel::Card] as $channel) {
                if (($payment[$channel->value] ?? 0) > 0) {
                    $this->balances->debit($courier, $channel, $payment[$channel->value]);
                }
            }
        } catch (DomainException $e) {
            throw ValidationException::withMessages(['action' => 'Çatdırılma geri alına bilməz: '.$e->getMessage().' Pul artıq kassaya təhvil verilib.']);
        }
    }
}
