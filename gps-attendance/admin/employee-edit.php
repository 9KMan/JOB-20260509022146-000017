<?php
$page_title = 'Edit Employee';
require_once 'partials/header.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT e.*, u.username, u.role, u.status as user_status FROM employees e LEFT JOIN users u ON e.id = u.employee_id WHERE e.id = ?');
$stmt->execute([$id]);
$employee = $stmt->fetch();

if (!$employee): ?>
<div class="alert alert-danger">Employee not found</div>
<a href="employees.php" class="btn btn-secondary">Back</a>
<?php else: ?>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Edit Employee</h5></div>
            <div class="card-body">
                <form id="editEmployeeForm">
                    <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">
                    <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($employee['name']); ?>" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($employee['email']); ?>" required></div>
                    <div class="mb-3"><label>Phone</label><input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($employee['phone']); ?>"></div>
                    <div class="mb-3"><label>Designation</label><input type="text" name="designation" class="form-control" value="<?php echo htmlspecialchars($employee['designation']); ?>"></div>
                    <div class="mb-3"><label>Department</label><input type="text" name="department" class="form-control" value="<?php echo htmlspecialchars($employee['department']); ?>"></div>
                    <div class="mb-3"><label>Username</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($employee['username']); ?>" disabled></div>
                    <div class="mb-3"><label>New Password (leave blank to keep current)</label><input type="password" name="password" class="form-control"></div>
                    <button type="submit" class="btn btn-primary">Update Employee</button>
                    <a href="employees.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('editEmployeeForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    try {
        const response = await fetch('../api/admin/employees', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.success) {
            alert('Employee updated successfully');
            location.href = 'employees.php';
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