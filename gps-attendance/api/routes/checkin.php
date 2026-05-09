<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed', 405);
}

$payload = authenticateJWT();
$input = json_decode(file_get_contents('php://input'), true);

$employeeId = $input['employee_id'] ?? $payload['employee_id'];
$siteId = intval($input['site_id'] ?? 0);
$lat = floatval($input['latitude'] ?? 0);
$lng = floatval($input['longitude'] ?? 0);

if (!$employeeId || !$siteId || !$lat || !$lng) {
    respondError('employee_id, site_id, latitude, longitude required');
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

$stmt = $db->prepare('SELECT * FROM attendance WHERE employee_id = ? AND check_out_time IS NULL ORDER BY check_in_time DESC LIMIT 1');
$stmt->execute([$employeeId]);
$openRecord = $stmt->fetch(PDO::FETCH_ASSOC);

if ($openRecord) {
    respondError('Already checked in. Please check out first.');
}

$stmt = $db->prepare('INSERT INTO attendance (employee_id, site_id, check_in_time, check_in_lat, check_in_lng, check_in_valid) VALUES (?, ?, NOW(), ?, ?, ?)');
$stmt->execute([$employeeId, $siteId, $lat, $lng, $valid ? 1 : 0]);

respond([
    'success' => true,
    'attendance_id' => $db->lastInsertId(),
    'check_in_time' => date('Y-m-d H:i:s'),
    'location_valid' => $valid,
    'distance_meters' => round($distance, 2)
]);
