<?php
require_once __DIR__ . '/../../../includes/Session.php';

class DriverController
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

    /**
     * Driver login
     */
    public function login(string $email, string $password): array
    {
        $email = $this->clean($email);
        $password = $this->clean($password);

        if ($email === '' || $password === '') {
            return ['success' => false, 'message' => 'Email and password are required'];
        }

        $stmt = $this->conn->prepare(
            "SELECT id, driver_code, name, email, password, status FROM drivers WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $driver = $result->fetch_assoc();
        $stmt->close();

        if (!$driver) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        if ($driver['status'] !== 'active') {
            return ['success' => false, 'message' => 'Account is not active'];
        }

        if (!password_verify($password, $driver['password'])) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        Session::set('driver_id', $driver['id']);
        Session::set('driver_email', $driver['email']);
        Session::set('driver_name', $driver['name']);
        Session::set('driver_code', $driver['driver_code']);

        // Update last_login
        $update = $this->conn->prepare("UPDATE drivers SET last_login = NOW() WHERE id = ?");
        $update->bind_param('i', $driver['id']);
        $update->execute();
        $update->close();

        return [
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'id' => $driver['id'],
                'name' => $driver['name'],
                'email' => $driver['email'],
                'driver_code' => $driver['driver_code']
            ]
        ];
    }

    /**
     * Driver registration
     */
    public function register(string $name, string $email, string $password, string $license_number): array
    {
        $name = $this->clean($name);
        $email = $this->clean($email);
        $password = $this->clean($password);
        $license_number = $this->clean($license_number);

        if ($name === '' || $email === '' || $password === '' || $license_number === '') {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }

        // Check if email exists
        $check = $this->conn->prepare("SELECT id FROM drivers WHERE email = ? OR license_number = ?");
        $check->bind_param('ss', $email, $license_number);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $check->close();
            return ['success' => false, 'message' => 'Email or license number already registered'];
        }
        $check->close();

        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $driver_code = 'DRV-' . strtoupper(bin2hex(random_bytes(3)));

        $stmt = $this->conn->prepare(
            "INSERT INTO drivers (driver_code, name, email, password, license_number, status)
             VALUES (?, ?, ?, ?, ?, 'active')"
        );
        
        $stmt->bind_param('sssss', $driver_code, $name, $email, $password_hash, $license_number);

        if (!$stmt->execute()) {
            $stmt->close();
            return ['success' => false, 'message' => 'Registration failed'];
        }

        $driver_id = $stmt->insert_id;
        $stmt->close();

        return [
            'success' => true,
            'message' => 'Registration successful. Please login.',
            'data' => ['id' => $driver_id, 'driver_code' => $driver_code]
        ];
    }

    /**
     * Get driver dashboard data
     */
    public function getDashboardData(int $driver_id): array
    {
        $driver = $this->conn->query(
            "SELECT id, driver_code, name, email, phone, vehicle_number, plate_number, 
                    status, experience_years, rating, total_trips
             FROM drivers WHERE id = $driver_id LIMIT 1"
        )->fetch_assoc();

        if (!$driver) {
            return [];
        }

        // Get assigned trips
        $trips = $this->conn->query(
            "SELECT id, shuttle_number, from_address, to_address, trip_date, depart_time, 
                    arrive_time, price, status 
             FROM trips WHERE driver_id = $driver_id ORDER BY trip_date DESC LIMIT 5"
        );
        $assigned_trips = [];
        while ($trip = $trips->fetch_assoc()) {
            $assigned_trips[] = $trip;
        }

        return [
            'driver' => $driver,
            'trips' => $assigned_trips
        ];
    }
}
?>
