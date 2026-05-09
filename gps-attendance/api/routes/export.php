<?php
$payload = authenticateJWT();
if ($payload['role'] !== 'admin') {
    respondError('Access denied', 403);
}

$format = $_GET['format'] ?? 'excel';
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-t');

$db = getDB();
$stmt = $db->prepare('SELECT a.*, e.name as employee_name, e.department, e.designation, s.name as site_name 
                      FROM attendance a 
                      LEFT JOIN employees e ON a.employee_id = e.id 
                      LEFT JOIN sites s ON a.site_id = s.id 
                      WHERE DATE(a.check_in_time) >= ? AND DATE(a.check_in_time) <= ?
                      ORDER BY a.check_in_time DESC');
$stmt->execute([$from, $to]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($format === 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="attendance_report_' . date('Y-m-d') . '.xls"');
    echo "Employee\tDepartment\tSite\tCheck In\tCheck Out\tCheck In Valid\tCheck Out Valid\n";
    foreach ($records as $r) {
        echo "{$r['employee_name']}\t{$r['department']}\t{$r['site_name']}\t{$r['check_in_time']}\t{$r['check_out_time']}\t" . ($r['check_in_valid'] ? 'Yes' : 'No') . "\t" . ($r['check_out_valid'] ? 'Yes' : 'No') . "\n";
    }
} elseif ($format === 'pdf') {
    require_once __DIR__ . '/../../vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf();
    $html = '<h1>Attendance Report</h1>';
    $html .= '<p>Period: ' . $from . ' to ' . $to . '</p>';
    $html .= '<table border="1"><tr><th>Employee</th><th>Department</th><th>Site</th><th>Check In</th><th>Check Out</th></tr>';
    foreach ($records as $r) {
        $html .= '<tr><td>' . $r['employee_name'] . '</td><td>' . $r['department'] . '</td><td>' . $r['site_name'] . '</td><td>' . $r['check_in_time'] . '</td><td>' . $r['check_out_time'] . '</td></tr>';
    }
    $html .= '</table>';
    $mpdf->WriteHTML($html);
    $mpdf->Output('attendance_report_' . date('Y-m-d') . '.pdf', 'D');
}
exit;
