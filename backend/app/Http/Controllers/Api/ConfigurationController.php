<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Services\PricingService;
use App\Support\JsonStore;
use App\Support\Request;

final class ConfigurationController
{
    public function __construct(
        private readonly JsonStore $store,
        private readonly PricingService $pricingService,
    ) {
    }

    public function preview(Request $request): array
    {
        $fabricId = (string) ($request->body['fabric_id'] ?? '');
        $fabrics = $this->store->read('fabrics.json', []);
        foreach ($fabrics as $fabric) {
            if ($fabric['id'] === $fabricId && ($fabric['active'] ?? true)) {
                return ['status' => 200, 'body' => $this->pricingService->preview($fabric, $request->body['options'] ?? [])];
            }
        }

        return ['status' => 422, 'body' => ['error' => 'Invalid fabric_id']];
    }

    public function save(Request $request): array
    {
        $configs = $this->store->read('saved_configurations.json', []);
        $record = [
            'id' => 'cfg_' . bin2hex(random_bytes(4)),
            'email' => strtolower(trim((string) ($request->body['email'] ?? 'guest@example.com'))),
            'fabric_id' => (string) ($request->body['fabric_id'] ?? ''),
            'options' => is_array($request->body['options'] ?? null) ? $request->body['options'] : [],
            'created_at' => gmdate(DATE_ATOM),
        ];
        $configs[] = $record;
        $this->store->write('saved_configurations.json', array_values($configs));

        return ['status' => 201, 'body' => ['message' => 'Configuration saved', 'data' => $record]];
    }

    public function list(Request $request): array
    {
        $email = strtolower(trim((string) ($request->query['email'] ?? 'guest@example.com')));
        $configs = $this->store->read('saved_configurations.json', []);
        $filtered = array_values(array_filter($configs, static fn(array $cfg) => ($cfg['email'] ?? '') === $email));

        return ['status' => 200, 'body' => ['data' => $filtered]];
    }
}
