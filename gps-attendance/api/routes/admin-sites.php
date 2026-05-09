<?php
$payload = authenticateJWT();
if ($payload['role'] !== 'admin') {
    respondError('Access denied', 403);
}

$db = getDB();
$input = json_decode(file_get_contents('php://input'), true);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $stmt = $db->query('SELECT * FROM sites ORDER BY id DESC');
        $sites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        respond(['sites' => $sites]);
        break;

    case 'POST':
        $name = $input['name'] ?? '';
        $address = $input['address'] ?? '';
        $lat = floatval($input['latitude'] ?? 0);
        $lng = floatval($input['longitude'] ?? 0);
        $radius = intval($input['radius_meters'] ?? 100);

        if (empty($name) || !$lat || !$lng) {
            respondError('name, latitude, longitude required');
        }

        $stmt = $db->prepare('INSERT INTO sites (name, address, latitude, longitude, radius_meters) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $address, $lat, $lng, $radius]);
        respond(['success' => true, 'site_id' => $db->lastInsertId()], 201);
        break;

    case 'PUT':
        $id = intval($input['id'] ?? 0);
        if (!$id) respondError('Site ID required');

        $stmt = $db->prepare('UPDATE sites SET name=?, address=?, latitude=?, longitude=?, radius_meters=? WHERE id=?');
        $stmt->execute([$input['name'], $input['address'], floatval($input['latitude']), floatval($input['longitude']), intval($input['radius_meters']), $id]);
        respond(['success' => true]);
        break;

    case 'DELETE':
        $id = intval($input['id'] ?? 0);
        if (!$id) respondError('Site ID required');

        $stmt = $db->prepare('DELETE FROM sites WHERE id=?');
        $stmt->execute([$id]);
        respond(['success' => true]);
        break;
}
