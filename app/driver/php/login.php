<?php
// Start session BEFORE any output
session_start();

// From app/driver/php -> go up three levels to project root, then includes/
require_once __DIR__ . '/../../../includes/db_connect.php';

// Ensure session cookies are set correctly for root domain
if (ini_get('session.cookie_path') !== '/') {
	ini_set('session.cookie_path', '/');
}

// Only allow POST; redirect any other method back to the single login page.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: /app/driver/view/login.html');
	exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
	header('Location: /app/driver/view/login.html?error=missing');
	exit();
}

$stmt = $conn->prepare('SELECT id, driver_code, name, email, password FROM drivers WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$driver = $result->fetch_assoc();
$stmt->close();

if ($driver && password_verify($password, $driver['password'])) {
	$_SESSION['driver_id'] = (int) $driver['id'];
	$_SESSION['driver_name'] = $driver['name'];
	$_SESSION['driver_code'] = $driver['driver_code'];
	$_SESSION['role'] = 'driver';

	$update = $conn->prepare('UPDATE drivers SET last_login = NOW() WHERE id = ?');
	if ($update) {
		$update->bind_param('i', $_SESSION['driver_id']);
		$update->execute();
		$update->close();
	}

	header('Location: /app/driver/view/html/dashboard.html');
	exit();
}

	header('Location: /app/driver/view/login.html?error=invalid');
exit();