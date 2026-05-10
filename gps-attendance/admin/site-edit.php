<?php
$page_title = 'Edit Site';
require_once 'partials/header.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM sites WHERE id = ?');
$stmt->execute([$id]);
$site = $stmt->fetch();

if (!$site): ?>
<div class="alert alert-danger">Site not found</div>
<a href="sites.php" class="btn btn-secondary">Back</a>
<?php else: ?>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Edit Site</h5></div>
            <div class="card-body">
                <form id="editSiteForm">
                    <input type="hidden" name="id" value="<?php echo $site['id']; ?>">
                    <div class="mb-3"><label>Site Name</label><input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($site['name']); ?>" required></div>
                    <div class="mb-3"><label>Address</label><textarea name="address" class="form-control"><?php echo htmlspecialchars($site['address']); ?></textarea></div>
                    <div class="mb-3"><label>Latitude</label><input type="number" step="any" name="latitude" class="form-control" value="<?php echo $site['latitude']; ?>" required></div>
                    <div class="mb-3"><label>Longitude</label><input type="number" step="any" name="longitude" class="form-control" value="<?php echo $site['longitude']; ?>" required></div>
                    <div class="mb-3"><label>Radius (meters)</label><input type="number" name="radius_meters" class="form-control" value="<?php echo $site['radius_meters']; ?>"></div>
                    <button type="submit" class="btn btn-primary">Update Site</button>
                    <a href="sites.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('editSiteForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    try {
        const response = await fetch('../api/admin/sites', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.success) {
            alert('Site updated successfully');
            location.href = 'sites.php';
        } else {
            alert('Error: ' + (result.error || 'Unknown error'));
        }
    } catch (err) {
        alert('Request failed: ' + err.message);
    }
});
</script>
<?php endif; ?>
<?php require_once 'partials/footer.php'; ?>