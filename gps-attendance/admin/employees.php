<?php
$page_title = 'Employees';
require_once 'partials/header.php';

$stmt = $db->query('SELECT e.*, u.username, u.role, u.status as user_status 
                    FROM employees e 
                    LEFT JOIN users u ON e.id = u.employee_id 
                    ORDER BY e.id DESC');
$employees = $stmt->fetchAll();
?>

<div class="row mb-3">
    <div class="col-md-12">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
            <i class="bi bi-plus-circle"></i> Add Employee
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h5>Employee Management</h5></div>
            <div class="card-body">
                <table class="table table-striped" id="employeeTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $emp): ?>
                        <tr>
                            <td><?php echo $emp['id']; ?></td>
                            <td><?php echo htmlspecialchars($emp['name']); ?></td>
                            <td><?php echo htmlspecialchars($emp['email']); ?></td>
                            <td><?php echo htmlspecialchars($emp['phone']); ?></td>
                            <td><?php echo htmlspecialchars($emp['designation']); ?></td>
                            <td><?php echo htmlspecialchars($emp['department']); ?></td>
                            <td><?php echo htmlspecialchars($emp['username'] ?? '-'); ?></td>
                            <td><span class="badge bg-<?php echo $emp['role'] === 'admin' ? 'primary' : 'secondary'; ?>"><?php echo $emp['role']; ?></span></td>
                            <td><span class="badge bg-<?php echo $emp['user_status'] === 'active' ? 'success' : 'danger'; ?>"><?php echo $emp['user_status']; ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editEmployee(<?php echo $emp['id']; ?>)"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteEmployee(<?php echo $emp['id']; ?>)"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addEmployeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="api/employees">
                <div class="modal-body">
                    <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label>Phone</label><input type="text" name="phone" class="form-control"></div>
                    <div class="mb-3"><label>Designation</label><input type="text" name="designation" class="form-control"></div>
                    <div class="mb-3"><label>Department</label><input type="text" name="department" class="form-control"></div>
                    <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div>
                    <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="mb-3"><label>Role</label><select name="role" class="form-control"><option value="employee">Employee</option><option value="admin">Admin</option></select></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
async function deleteEmployee(id) {
    if (!confirm('Delete this employee?')) return;
    const response = await fetch('../api/routes/admin-employees', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    });
    const result = await response.json();
    if (result.success) location.reload();
    else alert(result.error);
}
</script>

<?php require_once 'partials/footer.php'; ?>