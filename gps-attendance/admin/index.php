<?php
require_once 'partials/header.php';

$today = date('Y-m-d');
$stmt = $db->query("SELECT COUNT(*) as total FROM attendance WHERE DATE(check_in_time) = CURDATE()");
$todayCount = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM employees");
$totalEmployees = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM sites");
$totalSites = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM attendance WHERE DATE(check_in_time) = CURDATE() AND check_out_time IS NULL");
$stillCheckedIn = $stmt->fetch()['total'];

$stmt = $db->query("SELECT a.*, e.name as emp_name, s.name as site_name 
                    FROM attendance a 
                    LEFT JOIN employees e ON a.employee_id = e.id 
                    LEFT JOIN sites s ON a.site_id = s.id 
                    WHERE DATE(a.check_in_time) = CURDATE() 
                    ORDER BY a.check_in_time DESC LIMIT 10");
$recentAttendance = $stmt->fetchAll();
?>

<div class="row">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-calendar-check"></i> Today's Check-ins</h5>
                <h2><?php echo $todayCount; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-people"></i> Total Employees</h5>
                <h2><?php echo $totalEmployees; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-map"></i> Total Sites</h5>
                <h2><?php echo $totalSites; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-arrow-clockwise"></i> Still Checked In</h5>
                <h2><?php echo $stillCheckedIn; ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h5>Today's Attendance</h5></div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Site</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentAttendance as $att): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($att['emp_name']); ?></td>
                            <td><?php echo htmlspecialchars($att['site_name']); ?></td>
                            <td><?php echo $att['check_in_time']; ?></td>
                            <td><?php echo $att['check_out_time'] ?? '<span class="badge bg-success">Active</span>'; ?></td>
                            <td><?php echo $att['check_in_valid'] ? '<span class="badge bg-success">Valid</span>' : '<span class="badge bg-danger">Invalid</span>'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentAttendance)): ?>
                        <tr><td colspan="5" class="text-center text-muted">No attendance records for today</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>