<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (empty($username) || empty($password)) {
    respondError('Username and password required');
}

$db = getDB();
$stmt = $db->prepare('SELECT u.*, e.name as employee_name FROM users u LEFT JOIN employees e ON u.employee_id = e.id WHERE u.username = ? AND u.status = "active"');
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password_hash'])) {
    respondError('Invalid credentials', 401);
}

$token = createJWT([
    'user_id' => $user['id'],
    'employee_id' => $user['employee_id'],
    'role' => $user['role'],
    'username' => $user['username']
]);

respond([
    'token' => $token,
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'role' => $user['role'],
        'employee_id' => $user['employee_id'],
        'employee_name' => $user['employee_name']
    ]
]);
