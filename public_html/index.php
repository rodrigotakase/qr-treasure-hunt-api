<?php

use App\Admin\HuntAdmin;
use App\Admin\TreasureAdmin;
use App\Api\CollectApi;
use App\Api\HuntApi;
use App\Api\LeaderboardApi;
use App\Api\TreasureApi;
use App\Env;
use App\Response;
use App\Router;

$appDir = dirname(__DIR__);

require $appDir . '/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', $appDir . '/php_errors.log');

Env::load($appDir . '/.env');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

$client = parse_url(Env::get('CLIENT_BASE_URL', ''));
if (isset($client['scheme'], $client['host'])) {
    $origin = $client['scheme'] . '://' . $client['host'] . (isset($client['port']) ? ':' . $client['port'] : '');
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Vary: Origin');
}
if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

set_exception_handler(function (Throwable $exception) use ($path) {
    error_log((string) $exception);
    if (str_starts_with($path, '/api/')) {
        Response::error('Internal server error', 500);
    }
    Response::html('<h1>500 Internal Server Error</h1>', 500);
});

$router = new Router();

$router->get('/api/treasures/{id}', fn($id) => (new TreasureApi())->show($id));
$router->get('/api/hunts/{id}/treasures', fn($id) => (new HuntApi())->treasures((int) $id));
$router->post('/api/collect', fn() => (new CollectApi())->collect());
$router->get('/api/hunts/{id}/leaderboard', fn($id) => (new LeaderboardApi())->show((int) $id));

$router->get('/admin', fn() => (new HuntAdmin())->index());
$router->post('/admin/hunts', fn() => (new HuntAdmin())->create());
$router->post('/admin/hunts/{id}/update', fn($id) => (new HuntAdmin())->update((int) $id));
$router->get('/admin/hunts/{id}/delete', fn($id) => (new HuntAdmin())->confirmDelete((int) $id));
$router->post('/admin/hunts/{id}/delete', fn($id) => (new HuntAdmin())->delete((int) $id));
$router->get('/admin/hunts/{id}/leaderboard', fn($id) => (new HuntAdmin())->leaderboard((int) $id));

$router->get('/admin/hunts/{id}/treasures', fn($id) => (new TreasureAdmin())->index((int) $id));
$router->get('/admin/hunts/{id}/treasures/new', fn($id) => (new TreasureAdmin())->createForm((int) $id));
$router->post('/admin/hunts/{id}/treasures', fn($id) => (new TreasureAdmin())->create((int) $id));
$router->get('/admin/treasures/{id}/edit', fn($id) => (new TreasureAdmin())->editForm($id));
$router->post('/admin/treasures/{id}/update', fn($id) => (new TreasureAdmin())->update($id));
$router->get('/admin/treasures/{id}/delete', fn($id) => (new TreasureAdmin())->confirmDelete($id));
$router->post('/admin/treasures/{id}/delete', fn($id) => (new TreasureAdmin())->delete($id));
$router->get('/admin/treasures/{id}/qr', fn($id) => (new TreasureAdmin())->qrPage($id));
$router->get('/admin/treasures/{id}/qr.png', fn($id) => (new TreasureAdmin())->qrImage($id));

$router->dispatch($method, $path);
