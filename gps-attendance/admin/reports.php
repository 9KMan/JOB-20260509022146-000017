<?php
$page_title = 'Reports';
require_once 'partials/header.php';

$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-t');
$empId = $_GET['emp_id'] ?? null;

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

$sql .= ' ORDER BY a.check_in_time DESC';
$stmt = $db->prepare($sql);
$stmt->execute($params);
$records = $stmt->fetchAll();

$employees = $db->query('SELECT id, name FROM employees ORDER BY name')->fetchAll();
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Attendance Report</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label>From Date</label>
                        <input type="date" name="from" class="form-control" value="<?php echo $from; ?>">
                    </div>
                    <div class="col-md-3">
                        <label>To Date</label>
                        <input type="date" name="to" class="form-control" value="<?php echo $to; ?>">
                    </div>
                    <div class="col-md-3">
                        <label>Employee</label>
                        <select name="emp_id" class="form-control">
                            <option value="">All Employees</option>
                            <?php foreach ($employees as $emp): ?>
                            <option value="<?php echo $emp['id']; ?>" <?php echo $empId == $emp['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($emp['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>

                <div class="mb-3">
                    <a href="../api/routes/export?format=excel&from=<?php echo $from; ?>&to=<?php echo $to; ?>" class="btn btn-success">Export Excel</a>
                    <a href="../api/routes/export?format=pdf&from=<?php echo $from; ?>&to=<?php echo $to; ?>" class="btn btn-danger">Export PDF</a>
                </div>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Site</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Check In Valid</th>
                            <th>Check Out Valid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['employee_name']); ?></td>
                            <td><?php echo htmlspecialchars($r['department']); ?></td>
                            <td><?php echo htmlspecialchars($r['site_name']); ?></td>
                            <td><?php echo $r['check_in_time']; ?></td>
                            <td><?php echo $r['check_out_time'] ?? '-'; ?></td>
                            <td><?php echo $r['check_in_valid'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></td>
                            <td><?php echo $r['check_out_valid'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($records)): ?>
                        <tr><td colspan="7" class="text-center text-muted">No records found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>