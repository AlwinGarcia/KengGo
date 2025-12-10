<?php
require_once __DIR__ . '/../../../includes/Session.php';

class AdminController
{
	private $conn;

	public function __construct(mysqli $conn)
	{
		$this->conn = $conn;
	}

	private function clean(string $value): string
	{
		return trim($value);
	}

	public function login(string $email, string $password): array
	{
		$email = $this->clean($email);
		$password = $this->clean($password);

		if ($email === '' || $password === '') {
			return ['success' => false, 'message' => 'Email and password are required'];
		}

		$stmt = $this->conn->prepare("SELECT id, admin_code, name, email, password, role, status FROM admins WHERE email = ? LIMIT 1");
		$stmt->bind_param('s', $email);
		$stmt->execute();
		$result = $stmt->get_result();
		$admin = $result->fetch_assoc();
		$stmt->close();

		if (!$admin) {
			return ['success' => false, 'message' => 'Invalid credentials'];
		}

		if ($admin['status'] !== 'active') {
			return ['success' => false, 'message' => 'Account is not active'];
		}

		if (!password_verify($password, $admin['password'])) {
			return ['success' => false, 'message' => 'Invalid credentials'];
		}

		Session::set('admin_id', $admin['id']);
		Session::set('admin_email', $admin['email']);
		Session::set('admin_name', $admin['name']);
		Session::set('admin_role', $admin['role']);
		Session::set('admin_code', $admin['admin_code']);

		// update last_login
		$update = $this->conn->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
		$update->bind_param('i', $admin['id']);
		$update->execute();
		$update->close();

		return [
			'success' => true,
			'message' => 'Login successful',
			'data' => [
				'id' => $admin['id'],
				'name' => $admin['name'],
				'email' => $admin['email'],
				'role' => $admin['role'],
				'admin_code' => $admin['admin_code']
			]
		];
	}

	public function requestAccount(string $name, string $email, string $role = 'staff', ?string $requestCode = null): array
	{
		$name = $this->clean($name);
		$email = $this->clean($email);
		$role = in_array($role, ['manager', 'staff'], true) ? $role : 'staff';
		$requestCode = $requestCode ? $this->clean($requestCode) : 'REQ-' . strtoupper(bin2hex(random_bytes(3)));

		if ($name === '' || $email === '') {
			return ['success' => false, 'message' => 'Name and email are required'];
		}

		$stmt = $this->conn->prepare("INSERT INTO admin_requests (request_code, name, email, role_requested, status) VALUES (?, ?, ?, ?, 'pending')");
		$stmt->bind_param('ssss', $requestCode, $name, $email, $role);
		$stmt->execute();
		$stmt->close();

		return [
			'success' => true,
			'message' => 'Request submitted',
			'data' => ['request_code' => $requestCode]
		];
	}

	public function addAdmin(string $adminCode, string $name, string $email, string $password, string $role = 'manager', string $status = 'active'): array
	{
		$adminCode = $this->clean($adminCode);
		$name = $this->clean($name);
		$email = $this->clean($email);
		$password = $this->clean($password);
		$role = in_array($role, ['superadmin', 'manager', 'staff'], true) ? $role : 'manager';
		$status = in_array($status, ['active', 'pending', 'disabled'], true) ? $status : 'active';

		if ($adminCode === '' || $name === '' || $email === '' || $password === '') {
			return ['success' => false, 'message' => 'All fields are required'];
		}

		$hash = password_hash($password, PASSWORD_BCRYPT);
		$stmt = $this->conn->prepare("INSERT INTO admins (admin_code, name, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param('ssssss', $adminCode, $name, $email, $hash, $role, $status);
		$stmt->execute();
		$stmt->close();

		return ['success' => true, 'message' => 'Admin added'];
	}
}
