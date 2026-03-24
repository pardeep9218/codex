<?php

declare(strict_types=1);

namespace App\Services;

final class PricingService
{
    /** @var array<string, array<string,int>> */
    private array $optionAdjustments = [
        'lapel' => ['notch' => 0, 'peak' => 2500, 'shawl' => 3000],
        'vents' => ['single' => 0, 'double' => 1500, 'none' => -500],
        'pockets' => ['flap' => 0, 'jetted' => 1000, 'patch' => -800],
    ];

    public function preview(array $fabric, array $options): array
    {
        $basePrice = 79900;
        $adjustments = [
            ['code' => 'fabric_adjustment', 'amount' => (int) ($fabric['price_adjustment'] ?? 0)],
        ];

        foreach ($this->optionAdjustments as $group => $values) {
            $selected = (string) ($options[$group] ?? array_key_first($values));
            $adjustments[] = [
                'code' => $group . '_' . $selected,
                'amount' => (int) ($values[$selected] ?? 0),
            ];
        }

        $total = $basePrice + array_reduce($adjustments, static fn(int $carry, array $row) => $carry + (int) $row['amount'], 0);

        return [
            'base_price' => $basePrice,
            'adjustments' => $adjustments,
            'total' => $total,
            'currency' => 'USD',
            'lead_time_days' => 21,
        ];
    }
}
