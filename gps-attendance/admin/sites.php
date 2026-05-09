<?php
$page_title = 'Site Management';
require_once 'partials/header.php';

$stmt = $db->query('SELECT * FROM sites ORDER BY id DESC');
$sites = $stmt->fetchAll();
?>

<div class="row mb-3">
    <div class="col-md-12">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSiteModal">
            <i class="bi bi-plus-circle"></i> Add Site
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h5>Site Locations</h5></div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Radius (m)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sites as $site): ?>
                        <tr>
                            <td><?php echo $site['id']; ?></td>
                            <td><?php echo htmlspecialchars($site['name']); ?></td>
                            <td><?php echo htmlspecialchars($site['address']); ?></td>
                            <td><?php echo $site['latitude']; ?></td>
                            <td><?php echo $site['longitude']; ?></td>
                            <td><?php echo $site['radius_meters']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addSiteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Site</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="../api/routes/admin-sites">
                <div class="modal-body">
                    <div class="mb-3"><label>Site Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label>Address</label><textarea name="address" class="form-control"></textarea></div>
                    <div class="mb-3"><label>Latitude</label><input type="number" step="any" name="latitude" class="form-control" required></div>
                    <div class="mb-3"><label>Longitude</label><input type="number" step="any" name="longitude" class="form-control" required></div>
                    <div class="mb-3"><label>Radius (meters)</label><input type="number" name="radius_meters" class="form-control" value="100"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>