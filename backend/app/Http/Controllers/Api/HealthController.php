<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Support\Request;

final class HealthController
{
    public function __invoke(Request $request): array
    {
        return ['status' => 200, 'body' => ['status' => 'ok', 'service' => 'suit-platform-api', 'time' => gmdate(DATE_ATOM)]];
    }
}
