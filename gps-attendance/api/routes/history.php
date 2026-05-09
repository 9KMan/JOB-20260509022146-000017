<?php
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    respondError('Method not allowed', 405);
}

$payload = authenticateJWT();
$empId = $_GET['emp_id'] ?? $payload['employee_id'];
$from = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$to = $_GET['to'] ?? date('Y-m-d');

if ($payload['role'] !== 'admin' && intval($empId) !== intval($payload['employee_id'])) {
    respondError('Access denied', 403);
}

$db = getDB();
$stmt = $db->prepare('SELECT a.*, s.name as site_name, e.name as employee_name 
                      FROM attendance a 
                      LEFT JOIN sites s ON a.site_id = s.id 
                      LEFT JOIN employees e ON a.employee_id = e.id 
                      WHERE a.employee_id = ? AND DATE(a.check_in_time) >= ? AND DATE(a.check_in_time) <= ?
                      ORDER BY a.check_in_time DESC');
$stmt->execute([$empId, $from, $to]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

respond(['records' => $records]);
