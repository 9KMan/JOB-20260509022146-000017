<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed', 405);
}

$payload = authenticateJWT();
$input = json_decode(file_get_contents('php://input'), true);

$employeeId = $input['employee_id'] ?? $payload['employee_id'];
$attendanceId = intval($input['attendance_id'] ?? 0);
$lat = floatval($input['latitude'] ?? 0);
$lng = floatval($input['longitude'] ?? 0);

if (!$employeeId || !$attendanceId || !$lat || !$lng) {
    respondError('employee_id, attendance_id, latitude, longitude required');
}

$db = getDB();

$stmt = $db->prepare('SELECT * FROM attendance WHERE id = ? AND employee_id = ? AND check_out_time IS NULL');
$stmt->execute([$attendanceId, $employeeId]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    respondError('No open attendance record found');
}

$stmt = $db->prepare('SELECT * FROM sites WHERE id = ?');
$stmt->execute([$record['site_id']]);
$site = $stmt->fetch(PDO::FETCH_ASSOC);

$distance = haversineDistance($lat, $lng, $site['latitude'], $site['longitude']);
$valid = $distance <= $site['radius_meters'];

$stmt = $db->prepare('UPDATE attendance SET check_out_time = NOW(), check_out_lat = ?, check_out_lng = ?, check_out_valid = ? WHERE id = ?');
$stmt->execute([$lat, $lng, $valid ? 1 : 0, $attendanceId]);

respond([
    'success' => true,
    'check_out_time' => date('Y-m-d H:i:s'),
    'location_valid' => $valid,
    'distance_meters' => round($distance, 2)
]);
