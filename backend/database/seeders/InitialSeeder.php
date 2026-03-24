<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Support\JsonStore;

final class InitialSeeder
{
    public function run(JsonStore $store): void
    {
        $store->write('fabrics.json', [
            ['id' => 'fab_navy', 'name' => 'Navy Italian Wool', 'price_adjustment' => 12000, 'hex' => '#1f2f4a', 'active' => true],
            ['id' => 'fab_charcoal', 'name' => 'Charcoal Merino', 'price_adjustment' => 9000, 'hex' => '#3a3f46', 'active' => true],
            ['id' => 'fab_light_gray', 'name' => 'Light Gray Summer', 'price_adjustment' => 7000, 'hex' => '#8b919d', 'active' => true],
            ['id' => 'fab_black', 'name' => 'Midnight Black', 'price_adjustment' => 10000, 'hex' => '#1a1a1a', 'active' => true],
        ]);

        $store->write('saved_configurations.json', [
            [
                'id' => 'cfg_seed_001',
                'email' => 'demo@example.com',
                'fabric_id' => 'fab_navy',
                'options' => ['lapel' => 'notch', 'vents' => 'single', 'pockets' => 'flap'],
                'created_at' => gmdate(DATE_ATOM),
            ],
        ]);

        $store->write('orders.json', [
            [
                'order_number' => 'ORD-SEED1',
                'email' => 'demo@example.com',
                'configuration_id' => 'cfg_seed_001',
                'total' => 91900,
                'currency' => 'USD',
                'status' => 'new',
                'created_at' => gmdate(DATE_ATOM),
            ],
        ]);
    }
}
