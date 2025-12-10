<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/db_connect.php';
require_once __DIR__ . '/../../../includes/Session.php';
require_once __DIR__ . '/../../controller/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['success' => false, 'message' => 'Method not allowed']);
	exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$controller = new AdminController($conn);

try {
	$result = $controller->login($email, $password);
	if ($result['success']) {
		http_response_code(200);
	} else {
		http_response_code(401);
	}
	echo json_encode($result);
} catch (Throwable $e) {
	http_response_code(500);
	echo json_encode(['success' => false, 'message' => 'Server error']);
}