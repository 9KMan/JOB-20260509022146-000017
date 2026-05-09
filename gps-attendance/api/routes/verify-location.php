<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed', 405);
}

$payload = authenticateJWT();
$input = json_decode(file_get_contents('php://input'), true);

$employeeId = $input['employee_id'] ?? $payload['employee_id'];
$lat = floatval($input['latitude'] ?? 0);
$lng = floatval($input['longitude'] ?? 0);
$siteId = intval($input['site_id'] ?? 0);

if (!$employeeId || !$lat || !$lng || !$siteId) {
    respondError('employee_id, latitude, longitude, site_id required');
}

$db = getDB();
$stmt = $db->prepare('SELECT * FROM sites WHERE id = ?');
$stmt->execute([$siteId]);
$site = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$site) {
    respondError('Site not found', 404);
}

$distance = haversineDistance($lat, $lng, $site['latitude'], $site['longitude']);
$valid = $distance <= $site['radius_meters'];

respond([
    'valid' => $valid,
    'distance_meters' => round($distance, 2),
    'allowed_radius' => $site['radius_meters'],
    'site_name' => $site['name']
]);
