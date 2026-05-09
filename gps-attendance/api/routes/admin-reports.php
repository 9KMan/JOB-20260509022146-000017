<?php
$payload = authenticateJWT();
if ($payload['role'] !== 'admin') {
    respondError('Access denied', 403);
}

$db = getDB();
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-t');
$empId = $_GET['emp_id'] ?? null;
$siteId = $_GET['site_id'] ?? null;

$sql = 'SELECT a.*, e.name as employee_name, e.department, s.name as site_name 
        FROM attendance a 
        LEFT JOIN employees e ON a.employee_id = e.id 
        LEFT JOIN sites s ON a.site_id = s.id 
        WHERE DATE(a.check_in_time) >= ? AND DATE(a.check_in_time) <= ?';
$params = [$from, $to];

if ($empId) {
    $sql .= ' AND a.employee_id = ?';
    $params[] = $empId;
}
if ($siteId) {
    $sql .= ' AND a.site_id = ?';
    $params[] = $siteId;
}

$sql .= ' ORDER BY a.check_in_time DESC';
$stmt = $db->prepare($sql);
$stmt->execute($params);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$summary = [];
foreach ($records as $r) {
    $emp = $r['employee_id'];
    if (!isset($summary[$emp])) {
        $summary[$emp] = ['name' => $r['employee_name'], 'department' => $r['department'], 'days' => 0, 'check_ins' => 0];
    }
    $summary[$emp]['check_ins']++;
    if ($r['check_out_time']) $summary[$emp]['days']++;
}

respond(['records' => $records, 'summary' => array_values($summary)]);
