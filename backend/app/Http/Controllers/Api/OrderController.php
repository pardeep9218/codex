<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Support\JsonStore;
use App\Support\Request;

final class OrderController
{
    public function __construct(private readonly JsonStore $store)
    {
    }

    public function create(Request $request): array
    {
        $orders = $this->store->read('orders.json', []);
        $order = [
            'order_number' => 'ORD-' . strtoupper(bin2hex(random_bytes(3))),
            'email' => strtolower(trim((string) ($request->body['email'] ?? 'guest@example.com'))),
            'configuration_id' => (string) ($request->body['configuration_id'] ?? ''),
            'total' => (int) ($request->body['total'] ?? 0),
            'currency' => 'USD',
            'status' => 'new',
            'created_at' => gmdate(DATE_ATOM),
        ];

        $orders[] = $order;
        $this->store->write('orders.json', array_values($orders));

        return ['status' => 201, 'body' => ['message' => 'Order created', 'data' => $order]];
    }

    public function show(Request $request, array $params): array
    {
        $orderNumber = (string) ($params['orderNumber'] ?? '');
        $orders = $this->store->read('orders.json', []);

        foreach ($orders as $order) {
            if (($order['order_number'] ?? '') === $orderNumber) {
                return ['status' => 200, 'body' => ['data' => $order]];
            }
        }

        return ['status' => 404, 'body' => ['error' => 'Order not found']];
    }

    public function adminList(Request $request): array
    {
        $orders = $this->store->read('orders.json', []);
        return ['status' => 200, 'body' => ['data' => array_values($orders)]];
    }
}
