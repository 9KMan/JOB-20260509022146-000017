<?php
$payload = authenticateJWT();
if ($payload['role'] !== 'admin') {
    respondError('Access denied', 403);
}

$db = getDB();
$input = json_decode(file_get_contents('php://input'), true);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $stmt = $db->query('SELECT e.*, u.username, u.role, u.status as user_status
                            FROM employees e
                            LEFT JOIN users u ON e.id = u.employee_id
                            ORDER BY e.id DESC');
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        respond(['employees' => $employees]);
        break;

    case 'POST':
        $name = $input['name'] ?? '';
        $email = $input['email'] ?? '';
        $phone = $input['phone'] ?? '';
        $designation = $input['designation'] ?? '';
        $department = $input['department'] ?? '';
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';
        $role = $input['role'] ?? 'employee';

        if (empty($name) || empty($email) || empty($username) || empty($password)) {
            respondError('name, email, username, password required');
        }

        $db->beginTransaction();
        try {
            $stmt = $db->prepare('INSERT INTO employees (name, email, phone, designation, department) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$name, $email, $phone, $designation, $department]);
            $empId = $db->lastInsertId();

            $stmt = $db->prepare('INSERT INTO users (employee_id, username, password_hash, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$empId, $username, password_hash($password, PASSWORD_DEFAULT), $role]);
            $db->commit();

            respond(['success' => true, 'employee_id' => $empId], 201);
        } catch (Exception $e) {
            $db->rollBack();
            respondError('Failed to create employee: ' . $e->getMessage(), 500);
        }
        break;

    case 'PUT':
        $id = intval($input['id'] ?? 0);
        if (!$id) respondError('Employee ID required');

        $stmt = $db->prepare('UPDATE employees SET name=?, email=?, phone=?, designation=?, department=? WHERE id=?');
        $stmt->execute([$input['name'], $input['email'], $input['phone'], $input['designation'], $input['department'], $id]);

        if (!empty($input['password'])) {
            $stmt = $db->prepare('UPDATE users SET password_hash=? WHERE employee_id=?');
            $stmt->execute([password_hash($input['password'], PASSWORD_DEFAULT), $id]);
        }
        respond(['success' => true]);
        break;

    case 'DELETE':
        $id = intval($input['id'] ?? 0);
        if (!$id) respondError('Employee ID required');

        $stmt = $db->prepare('DELETE FROM users WHERE employee_id=?');
        $stmt->execute([$id]);
        $stmt = $db->prepare('DELETE FROM employees WHERE id=?');
        $stmt->execute([$id]);
        respond(['success' => true]);
        break;
}
