<?php
// Trip.php - Trip Model for database operations

class Trip {
    private $conn;
    private $table = 'shuttles';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Add a new trip/shuttle to the database
     * @param array $data - Trip data
     * @return array - Response with status and message
     */
    public function addTrip($data) {
        try {
            $shuttle_number = htmlspecialchars(trim($data['shuttle_number'] ?? ''));
            $driver_name = htmlspecialchars(trim($data['driver_name'] ?? ''));
            $plate_number = htmlspecialchars(trim($data['plate_number'] ?? ''));
            $seats = intval($data['seats'] ?? 0);
            $from_address = htmlspecialchars(trim($data['from_address'] ?? ''));
            $to_address = htmlspecialchars(trim($data['to_address'] ?? ''));
            $trip_date = htmlspecialchars(trim($data['trip_date'] ?? ''));
            $depart_time = htmlspecialchars(trim($data['depart_time'] ?? ''));
            $arrive_time = htmlspecialchars(trim($data['arrive_time'] ?? ''));
            $price = floatval($data['price'] ?? 0);
            $status = htmlspecialchars(trim($data['status'] ?? 'active'));
            $notes = htmlspecialchars(trim($data['notes'] ?? ''));

            // Validation
            if (empty($shuttle_number) || empty($from_address) || empty($to_address) || 
                empty($trip_date) || empty($depart_time) || $price <= 0) {
                return [
                    'success' => false,
                    'message' => 'Required fields are missing'
                ];
            }

            // Build route string
            $route = $from_address . ' to ' . $to_address;

            // Get driver_id from driver name (if exists)
            $driver_id = $this->getDriverIdByName($driver_name);

            // Prepare SQL statement with all columns
            $query = "INSERT INTO " . $this->table . " 
                      (shuttle_number, plate_number, route, departure_time, arrival_time, 
                       capacity, price, status, trip_date, notes, driver_id) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($query);

            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Database error: ' . $this->conn->error
                ];
            }

            // Bind parameters: s=string, i=integer, d=double/decimal
            $stmt->bind_param("sssssidsssi", 
                $shuttle_number,
                $plate_number,
                $route, 
                $depart_time,
                $arrive_time,
                $seats,
                $price,
                $status,
                $trip_date,
                $notes,
                $driver_id
            );

            if ($stmt->execute()) {
                $insert_id = $this->conn->insert_id;
                return [
                    'success' => true,
                    'message' => 'Trip added successfully!',
                    'trip_id' => $insert_id
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to add trip: ' . $stmt->error
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Edit an existing trip
     * @param int $trip_id - Trip ID
     * @param array $data - Updated trip data
     * @return array - Response with status and message
     */
    public function editTrip($trip_id, $data) {
        try {
            $trip_id = intval($trip_id);
            
            if ($trip_id <= 0) {
                return [
                    'success' => false,
                    'message' => 'Invalid trip ID'
                ];
            }

            $shuttle_number = htmlspecialchars(trim($data['shuttle_number'] ?? ''));
            $driver_name = htmlspecialchars(trim($data['driver_name'] ?? ''));
            $plate_number = htmlspecialchars(trim($data['plate_number'] ?? ''));
            $seats = intval($data['seats'] ?? 0);
            $from_address = htmlspecialchars(trim($data['from_address'] ?? ''));
            $to_address = htmlspecialchars(trim($data['to_address'] ?? ''));
            $trip_date = htmlspecialchars(trim($data['trip_date'] ?? ''));
            $depart_time = htmlspecialchars(trim($data['depart_time'] ?? ''));
            $arrive_time = htmlspecialchars(trim($data['arrive_time'] ?? ''));
            $price = floatval($data['price'] ?? 0);
            $status = htmlspecialchars(trim($data['status'] ?? 'active'));
            $notes = htmlspecialchars(trim($data['notes'] ?? ''));

            // Validation
            if (empty($shuttle_number) || empty($from_address) || empty($to_address) || 
                empty($trip_date) || empty($depart_time) || empty($price)) {
                return [
                    'success' => false,
                    'message' => 'Required fields are missing'
                ];
            }

            // Check if trip exists
            if (!$this->tripExists($trip_id)) {
                return [
                    'success' => false,
                    'message' => 'Trip not found'
                ];
            }

            // Build route string
            $route = $from_address . ' to ' . $to_address;

            // Get driver_id from driver name (if exists)
            $driver_id = $this->getDriverIdByName($driver_name);

            // Prepare SQL statement
            $query = "UPDATE " . $this->table . " 
                      SET shuttle_number = ?, plate_number = ?, route = ?, 
                          departure_time = ?, arrival_time = ?, capacity = ?, 
                          price = ?, status = ?, trip_date = ?, notes = ?, driver_id = ?
                      WHERE id = ?";

            $stmt = $this->conn->prepare($query);

            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Database error: ' . $this->conn->error
                ];
            }

            // Bind parameters: s=string, i=integer, d=double
            $stmt->bind_param("sssssidsssii", 
                $shuttle_number,
                $plate_number,
                $route, 
                $depart_time,
                $arrive_time,
                $seats,
                $price,
                $status,
                $trip_date,
                $notes,
                $driver_id,
                $trip_id
            );

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Trip updated successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update trip: ' . $stmt->error
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a trip
     * @param int $trip_id - Trip ID
     * @return array - Response with status and message
     */
    public function deleteTrip($trip_id) {
        try {
            $trip_id = intval($trip_id);

            if ($trip_id <= 0) {
                return [
                    'success' => false,
                    'message' => 'Invalid trip ID'
                ];
            }

            // Check if trip exists
            if (!$this->tripExists($trip_id)) {
                return [
                    'success' => false,
                    'message' => 'Trip not found'
                ];
            }

            // Check for bookings on this trip
            $bookingsQuery = "SELECT COUNT(*) as count FROM bookings WHERE shuttle_id = ?";
            $stmt = $this->conn->prepare($bookingsQuery);
            $stmt->bind_param("i", $trip_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete trip with existing bookings'
                ];
            }

            // Delete the trip
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);

            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Database error: ' . $this->conn->error
                ];
            }

            $stmt->bind_param("i", $trip_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Trip deleted successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to delete trip: ' . $stmt->error
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all trips
     * @return array - Array of trips
     */
    public function getAllTrips() {
        $query = "SELECT s.*, d.name as driver_name FROM " . $this->table . " s 
                  LEFT JOIN drivers d ON s.driver_id = d.id 
                  ORDER BY s.id DESC";
        
        $result = $this->conn->query($query);
        
        if (!$result) {
            return [];
        }

        $trips = [];
        while ($row = $result->fetch_assoc()) {
            $trips[] = $row;
        }

        return $trips;
    }

    /**
     * Get a single trip by ID
     * @param int $trip_id - Trip ID
     * @return array - Trip data or empty array
     */
    public function getTripById($trip_id) {
        $trip_id = intval($trip_id);

        $query = "SELECT s.*, d.name as driver_name FROM " . $this->table . " s 
                  LEFT JOIN drivers d ON s.driver_id = d.id 
                  WHERE s.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $trip_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: [];
    }

    /**
     * Check if trip exists
     * @param int $trip_id - Trip ID
     * @return bool
     */
    private function tripExists($trip_id) {
        $query = "SELECT id FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $trip_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    /**
     * Get driver ID by name
     * @param string $driver_name - Driver name
     * @return int|null - Driver ID or null
     */
    private function getDriverIdByName($driver_name) {
        if (empty($driver_name)) {
            return null;
        }

        $query = "SELECT id FROM drivers WHERE name = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $driver_name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['id'];
        }

        return null;
    }
}
