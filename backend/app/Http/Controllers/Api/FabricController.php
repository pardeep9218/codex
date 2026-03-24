<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Support\JsonStore;
use App\Support\Request;

final class FabricController
{
    public function __construct(private readonly JsonStore $store)
    {
    }

    public function index(Request $request): array
    {
        $fabrics = $this->store->read('fabrics.json', []);
        $active = array_values(array_filter($fabrics, static fn(array $f) => ($f['active'] ?? false) === true));

        return ['status' => 200, 'body' => ['data' => $active]];
    }
}
