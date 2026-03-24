<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Support\JsonStore;
use App\Support\Request;

final class FabricAdminController
{
    public function __construct(private readonly JsonStore $store)
    {
    }

    public function index(Request $request): array
    {
        return ['status' => 200, 'body' => ['data' => $this->store->read('fabrics.json', [])]];
    }

    public function create(Request $request): array
    {
        $fabrics = $this->store->read('fabrics.json', []);
        $fabric = [
            'id' => $request->body['id'] ?? ('fab_' . bin2hex(random_bytes(3))),
            'name' => $request->body['name'] ?? 'Unnamed Fabric',
            'price_adjustment' => (int) ($request->body['price_adjustment'] ?? 0),
            'hex' => $request->body['hex'] ?? '#445566',
            'active' => (bool) ($request->body['active'] ?? true),
        ];

        $fabrics[] = $fabric;
        $this->store->write('fabrics.json', array_values($fabrics));

        return ['status' => 201, 'body' => ['message' => 'Fabric created', 'data' => $fabric]];
    }

    public function delete(Request $request, array $params): array
    {
        $id = (string) ($params['id'] ?? '');
        $fabrics = $this->store->read('fabrics.json', []);

        $before = count($fabrics);
        $fabrics = array_values(array_filter($fabrics, static fn(array $item) => ($item['id'] ?? '') !== $id));

        if ($before === count($fabrics)) {
            return ['status' => 404, 'body' => ['error' => 'Fabric not found']];
        }

        $this->store->write('fabrics.json', $fabrics);
        return ['status' => 200, 'body' => ['message' => 'Fabric deleted']];
    }
}
