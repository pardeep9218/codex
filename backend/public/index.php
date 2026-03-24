<?php

declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($path === '/' || $path === '/admin') {
    header('Content-Type: text/html; charset=UTF-8');
    $viewFile = $path === '/admin'
        ? __DIR__ . '/../resources/views/admin.blade.php'
        : __DIR__ . '/../resources/views/storefront.blade.php';

    if (is_file($viewFile)) {
        readfile($viewFile);
        exit;
    }

    http_response_code(500);
    echo 'View not found.';
    exit;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Admin-Key');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

/** @var \App\Support\Router $router */
$router = require __DIR__ . '/../bootstrap/app.php';
$request = \App\Support\Request::capture();
$response = $router->dispatch($request);

http_response_code((int) ($response['status'] ?? 200));
echo json_encode($response['body'] ?? ['error' => 'Invalid response'], JSON_PRETTY_PRINT);
