<?php

namespace App\Modules\Merchants\Enums;

/** How a merchant is paid out (spec §6.4). Each method has its own detail fields. */
enum PaymentMethod: string
{
    case Bank = 'bank';
    case Mobile = 'mobile';
    case Cash = 'cash';

    public function label(): string
    {
        return match ($this) {
            self::Bank => 'Bank',
            self::Mobile => 'Mobil',
            self::Cash => 'Nağd',
        };
    }

    /**
     * Detail fields shown and validated for this method (spec §6.4 recommendation).
     *
     * @return array<string, array{label: string, rules: list<string>}>
     */
    public function fields(): array
    {
        return match ($this) {
            self::Bank => [
                'bank_name' => ['label' => 'Bank', 'rules' => ['required', 'string', 'max:100']],
                'branch' => ['label' => 'Filial', 'rules' => ['nullable', 'string', 'max:100']],
                'holder_name' => ['label' => 'Hesab sahibi', 'rules' => ['required', 'string', 'max:150']],
                'iban' => ['label' => 'IBAN', 'rules' => ['required', 'string', 'regex:/^AZ\d{2}[A-Z]{4}[A-Z0-9]{20}$/']],
            ],
            self::Mobile => [
                'number' => ['label' => 'Nömrə', 'rules' => ['required', 'string', 'regex:/^\+?994\d{9}$/']],
            ],
            self::Cash => [
                'note' => ['label' => 'Qeyd', 'rules' => ['nullable', 'string', 'max:255']],
            ],
        };
    }
}
