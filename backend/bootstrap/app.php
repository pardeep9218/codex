<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Admin\FabricAdminController;
use App\Http\Controllers\Api\ConfigurationController;
use App\Http\Controllers\Api\FabricController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\OrderController;
use App\Services\PricingService;
use App\Support\JsonStore;
use App\Support\Request;
use App\Support\Router;

require_once __DIR__ . '/../app/Support/Request.php';
require_once __DIR__ . '/../app/Support/JsonStore.php';
require_once __DIR__ . '/../app/Support/Router.php';
require_once __DIR__ . '/../app/Services/PricingService.php';
require_once __DIR__ . '/../app/Http/Controllers/Api/HealthController.php';
require_once __DIR__ . '/../app/Http/Controllers/Api/FabricController.php';
require_once __DIR__ . '/../app/Http/Controllers/Api/ConfigurationController.php';
require_once __DIR__ . '/../app/Http/Controllers/Api/OrderController.php';
require_once __DIR__ . '/../app/Http/Controllers/Api/Admin/FabricAdminController.php';

$store = new JsonStore(__DIR__ . '/../storage');
$router = new Router();

$healthController = new HealthController();
$fabricController = new FabricController($store);
$configController = new ConfigurationController($store, new PricingService());
$orderController = new OrderController($store);
$adminFabricController = new FabricAdminController($store);

$requireAdmin = static function (Request $request): ?array {
    if ($request->header('X-ADMIN-KEY') !== 'demo-admin-key') {
        return ['status' => 401, 'body' => ['error' => 'Unauthorized admin access']];
    }

    return null;
};

$router->add('GET', '/api/v1/health', fn(Request $request) => $healthController($request));
$router->add('GET', '/api/v1/fabrics', fn(Request $request) => $fabricController->index($request));
$router->add('POST', '/api/v1/configurations/price-preview', fn(Request $request) => $configController->preview($request));
$router->add('POST', '/api/v1/me/saved-configurations', fn(Request $request) => $configController->save($request));
$router->add('GET', '/api/v1/me/saved-configurations', fn(Request $request) => $configController->list($request));
$router->add('POST', '/api/v1/orders', fn(Request $request) => $orderController->create($request));
$router->add('GET', '/api/v1/orders/{orderNumber}', fn(Request $request, array $params) => $orderController->show($request, $params));

$router->add('GET', '/api/v1/admin/fabrics', function (Request $request) use ($requireAdmin, $adminFabricController) {
    if ($response = $requireAdmin($request)) {
        return $response;
    }
    return $adminFabricController->index($request);
});

$router->add('POST', '/api/v1/admin/fabrics', function (Request $request) use ($requireAdmin, $adminFabricController) {
    if ($response = $requireAdmin($request)) {
        return $response;
    }
    return $adminFabricController->create($request);
});

$router->add('DELETE', '/api/v1/admin/fabrics/{id}', function (Request $request, array $params) use ($requireAdmin, $adminFabricController) {
    if ($response = $requireAdmin($request)) {
        return $response;
    }
    return $adminFabricController->delete($request, $params);
});

$router->add('GET', '/api/v1/admin/orders', function (Request $request) use ($requireAdmin, $orderController) {
    if ($response = $requireAdmin($request)) {
        return $response;
    }
    return $orderController->adminList($request);
});

return $router;
