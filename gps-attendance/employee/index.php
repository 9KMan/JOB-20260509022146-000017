<?php session_start(); ?>
<?php
if (!isset($_SESSION['employee_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/../api/functions.php';

$db = getDB();
$empId = $_SESSION['employee_id'];
$payload = ['employee_id' => $empId, 'role' => $_SESSION['role']];

$stmt = $db->prepare('SELECT * FROM employees WHERE id = ?');
$stmt->execute([$empId]);
$employee = $stmt->fetch();

$sites = $db->query('SELECT * FROM sites ORDER BY name')->fetchAll();

$stmt = $db->prepare('SELECT a.*, s.name as site_name 
                      FROM attendance a 
                      LEFT JOIN sites s ON a.site_id = s.id 
                      WHERE a.employee_id = ? 
                      ORDER BY a.check_in_time DESC LIMIT 30');
$stmt->execute([$empId]);
$recentAttendance = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Portal - GPS Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><i class="bi bi-geo-alt"></i> GPS Attendance</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><?php echo htmlspecialchars($employee['name']); ?></h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($employee['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($employee['phone']); ?></p>
                        <p><strong>Designation:</strong> <?php echo htmlspecialchars($employee['designation']); ?></p>
                        <p><strong>Department:</strong> <?php echo htmlspecialchars($employee['department']); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h5>Check In / Check Out</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Select Site</label>
                                <select id="siteSelect" class="form-control mb-3">
                                    <?php foreach ($sites as $site): ?>
                                    <option value="<?php echo $site['id']; ?>"><?php echo htmlspecialchars($site['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div id="locationStatus" class="alert alert-info">Click button to get your location...</div>
                                <button id="checkinBtn" class="btn btn-success w-100 mb-2"><i class="bi bi-check-circle"></i> Check In</button>
                                <button id="checkoutBtn" class="btn btn-danger w-100"><i class="bi bi-x-circle"></i> Check Out</button>
                            </div>
                            <div class="col-md-6">
                                <div id="map" style="height: 200px; background: #eee; border-radius: 10px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h5>My Recent Attendance (Last 30 Days)</h5></div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Site</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentAttendance as $att): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d', strtotime($att['check_in_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($att['site_name']); ?></td>
                                    <td><?php echo $att['check_in_time']; ?></td>
                                    <td><?php echo $att['check_out_time'] ?? '-'; ?></td>
                                    <td>
                                        <?php if ($att['check_out_time']): ?>
                                            <span class="badge bg-success">Complete</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Checked In</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
let currentPosition = null;
const token = '<?php echo $_SESSION['token'] ?? ''; ?>';

function updateLocation() {
    if (!navigator.geolocation) {
        document.getElementById('locationStatus').className = 'alert alert-danger';
        document.getElementById('locationStatus').textContent = 'Geolocation not supported';
        return;
    }

    document.getElementById('locationStatus').className = 'alert alert-warning';
    document.getElementById('locationStatus').textContent = 'Getting location...';

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            currentPosition = {
                lat: pos.coords.latitude,
                lng: pos.coords.longitude
            };
            document.getElementById('locationStatus').className = 'alert alert-success';
            document.getElementById('locationStatus').textContent = `Location captured: ${currentPosition.lat.toFixed(6)}, ${currentPosition.lng.toFixed(6)}`;
        },
        (err) => {
            document.getElementById('locationStatus').className = 'alert alert-danger';
            document.getElementById('locationStatus').textContent = 'Location error: ' + err.message;
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

document.getElementById('checkinBtn').addEventListener('click', async function() {
    updateLocation();
    await new Promise(r => setTimeout(r, 1000));
    if (!currentPosition) {
        alert('Please enable location services');
        return;
    }

    const siteId = document.getElementById('siteSelect').value;
    const response = await fetch('../api/routes/checkin', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
        body: JSON.stringify({
            site_id: siteId,
            latitude: currentPosition.lat,
            longitude: currentPosition.lng
        })
    });
    const result = await response.json();
    if (result.success) {
        alert('Checked in successfully!');
        location.reload();
    } else {
        alert('Error: ' + result.error);
    }
});

document.getElementById('checkoutBtn').addEventListener('click', async function() {
    updateLocation();
    await new Promise(r => setTimeout(r, 1000));
    if (!currentPosition) {
        alert('Please enable location services');
        return;
    }

    const response = await fetch('../api/routes/checkout', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
        body: JSON.stringify({
            latitude: currentPosition.lat,
            longitude: currentPosition.lng
        })
    });
    const result = await response.json();
    if (result.success) {
        alert('Checked out successfully!');
        location.reload();
    } else {
        alert('Error: ' + result.error);
    }
});

updateLocation();
    </script>
</body>
</html>