<?php
class Driver
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
     * Get all drivers
     */
    public function getAllDrivers(): array
    {
        $stmt = $this->conn->prepare(
            "SELECT id, driver_code, name, email, license_number, phone, 
                    vehicle_number, plate_number, status, experience_years, rating, 
                    total_trips, last_login, created_at 
             FROM drivers ORDER BY created_at DESC"
        );
        
        if (!$stmt) {
            return [];
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $drivers = [];
        
        while ($row = $result->fetch_assoc()) {
            $drivers[] = $row;
        }
        
        $stmt->close();
        return $drivers;
    }

    /**
     * Get driver by ID
     */
    public function getDriverById(int $id): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT id, driver_code, name, email, license_number, license_expiry, phone, 
                    vehicle_number, plate_number, status, experience_years, rating, 
                    total_trips, notes, created_at, last_login
             FROM drivers WHERE id = ? LIMIT 1"
        );
        
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $driver = $result->fetch_assoc();
        $stmt->close();
        
        return $driver;
    }

    /**
     * Create a new driver
     */
    public function createDriver(array $data): array
    {
        $name = $this->clean($data['name'] ?? '');
        $email = $this->clean($data['email'] ?? '');
        $license_number = $this->clean($data['license_number'] ?? '');
        $password = $this->clean($data['password'] ?? '');
        $license_expiry = $this->clean($data['license_expiry'] ?? null);
        $phone = $this->clean($data['phone'] ?? '');
        $vehicle_number = $this->clean($data['vehicle_number'] ?? '');
        $plate_number = $this->clean($data['plate_number'] ?? '');
        $experience_years = isset($data['experience_years']) ? intval($data['experience_years']) : 0;
        $status = in_array($data['status'] ?? 'active', ['active', 'inactive', 'suspended']) ? $data['status'] : 'active';
        $notes = $this->clean($data['notes'] ?? '');
        $driver_code = 'DRV-' . strtoupper(bin2hex(random_bytes(3)));

        // Validate required fields
        if (empty($name) || empty($email) || empty($license_number) || empty($password)) {
            return ['success' => false, 'message' => 'Name, email, license number, and password are required'];
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        // Check if email exists
        $check = $this->conn->prepare("SELECT id FROM drivers WHERE email = ? OR license_number = ?");
        $check->bind_param('ss', $email, $license_number);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            $check->close();
            return ['success' => false, 'message' => 'Email or license number already exists'];
        }
        $check->close();

        // Hash password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert driver
        $stmt = $this->conn->prepare(
            "INSERT INTO drivers (driver_code, name, email, password, license_number, license_expiry, phone, 
                                  vehicle_number, plate_number, status, experience_years, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error'];
        }

        $stmt->bind_param(
            'ssssssssssis',
            $driver_code, $name, $email, $password_hash, $license_number, $license_expiry,
            $phone, $vehicle_number, $plate_number, $status, $experience_years, $notes
        );

        if (!$stmt->execute()) {
            $stmt->close();
            return ['success' => false, 'message' => 'Failed to create driver'];
        }

        $driver_id = $stmt->insert_id;
        $stmt->close();

        return [
            'success' => true,
            'message' => 'Driver created successfully',
            'data' => ['id' => $driver_id, 'driver_code' => $driver_code]
        ];
    }

    /**
     * Update driver
     */
    public function updateDriver(int $id, array $data): array
    {
        $driver = $this->getDriverById($id);
        if (!$driver) {
            return ['success' => false, 'message' => 'Driver not found'];
        }

        $name = $this->clean($data['name'] ?? $driver['name']);
        $license_expiry = $this->clean($data['license_expiry'] ?? $driver['license_expiry']);
        $phone = $this->clean($data['phone'] ?? $driver['phone']);
        $vehicle_number = $this->clean($data['vehicle_number'] ?? $driver['vehicle_number']);
        $plate_number = $this->clean($data['plate_number'] ?? $driver['plate_number']);
        $status = in_array($data['status'] ?? $driver['status'], ['active', 'inactive', 'suspended']) 
            ? $data['status'] 
            : $driver['status'];
        $experience_years = isset($data['experience_years']) ? intval($data['experience_years']) : $driver['experience_years'];
        $notes = $this->clean($data['notes'] ?? $driver['notes']);

        if (empty($name)) {
            return ['success' => false, 'message' => 'Name is required'];
        }

        $stmt = $this->conn->prepare(
            "UPDATE drivers 
             SET name = ?, license_expiry = ?, phone = ?, vehicle_number = ?, plate_number = ?, 
                 status = ?, experience_years = ?, notes = ? 
             WHERE id = ?"
        );

        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error'];
        }

        $stmt->bind_param(
            'ssisssisi',
            $name, $license_expiry, $phone, $vehicle_number, $plate_number,
            $status, $experience_years, $notes, $id
        );

        if (!$stmt->execute()) {
            $stmt->close();
            return ['success' => false, 'message' => 'Failed to update driver'];
        }

        $stmt->close();
        return ['success' => true, 'message' => 'Driver updated successfully'];
    }

    /**
     * Delete driver
     */
    public function deleteDriver(int $id): array
    {
        $driver = $this->getDriverById($id);
        if (!$driver) {
            return ['success' => false, 'message' => 'Driver not found'];
        }

        $stmt = $this->conn->prepare("DELETE FROM drivers WHERE id = ?");
        
        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error'];
        }

        $stmt->bind_param('i', $id);

        if (!$stmt->execute()) {
            $stmt->close();
            return ['success' => false, 'message' => 'Failed to delete driver'];
        }

        $stmt->close();
        return ['success' => true, 'message' => 'Driver deleted successfully'];
    }

    /**
     * Search drivers
     */
    public function searchDrivers(string $query): array
    {
        $query = "%{$this->clean($query)}%";
        $stmt = $this->conn->prepare(
            "SELECT id, driver_code, name, email, license_number, phone, 
                    vehicle_number, plate_number, status, experience_years, rating, 
                    total_trips, created_at
             FROM drivers 
             WHERE name LIKE ? OR email LIKE ? OR license_number LIKE ? 
                OR driver_code LIKE ? OR phone LIKE ?
             ORDER BY name ASC"
        );

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param('sssss', $query, $query, $query, $query, $query);
        $stmt->execute();
        $result = $stmt->get_result();
        $drivers = [];

        while ($row = $result->fetch_assoc()) {
            $drivers[] = $row;
        }

        $stmt->close();
        return $drivers;
    }

    /**
     * Get driver statistics
     */
    public function getStatistics(): array
    {
        $total = $this->conn->query("SELECT COUNT(*) as count FROM drivers")->fetch_assoc()['count'] ?? 0;
        $active = $this->conn->query("SELECT COUNT(*) as count FROM drivers WHERE status = 'active'")->fetch_assoc()['count'] ?? 0;
        $inactive = $this->conn->query("SELECT COUNT(*) as count FROM drivers WHERE status = 'inactive'")->fetch_assoc()['count'] ?? 0;
        $suspended = $this->conn->query("SELECT COUNT(*) as count FROM drivers WHERE status = 'suspended'")->fetch_assoc()['count'] ?? 0;

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'suspended' => $suspended
        ];
    }
}
?>
