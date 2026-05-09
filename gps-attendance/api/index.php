<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/api', '', $uri);

$db = getDB();

switch ($uri) {
    case '/login':
        require_once __DIR__ . '/routes/login.php';
        break;
    case '/verify-location':
        require_once __DIR__ . '/routes/verify-location.php';
        break;
    case '/attendance/checkin':
        require_once __DIR__ . '/routes/checkin.php';
        break;
    case '/attendance/checkout':
        require_once __DIR__ . '/routes/checkout.php';
        break;
    case '/attendance/history':
        require_once __DIR__ . '/routes/history.php';
        break;
    case '/admin/employees':
        require_once __DIR__ . '/routes/admin-employees.php';
        break;
    case '/admin/sites':
        require_once __DIR__ . '/routes/admin-sites.php';
        break;
    case '/admin/reports/attendance':
        require_once __DIR__ . '/routes/admin-reports.php';
        break;
    case '/admin/reports/export':
        require_once __DIR__ . '/routes/export.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}
