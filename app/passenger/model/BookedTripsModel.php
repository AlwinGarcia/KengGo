<?php
class BookedTripsModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Latest 6 active bookings
    public function getLatestBookedTrips($passengerId) {
        $sql = "SELECT
                    b.id AS booking_id,
                    s.route,
                    s.trip_date,
                    s.departure_time,
                    s.arrival_time,
                    s.price,
                    b.seat_number,
                    b.status
                FROM bookings b
                INNER JOIN shuttles s ON b.shuttle_id = s.id
                WHERE b.passenger_id = ? AND b.status = 'booked'
                ORDER BY s.trip_date DESC, s.departure_time DESC
                LIMIT 6";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $passengerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // All active bookings
    public function getAllBookedTrips($passengerId) {
        $sql = "SELECT
                    b.id AS booking_id,
                    s.route,
                    s.trip_date,
                    s.departure_time,
                    s.arrival_time,
                    s.price,
                    b.seat_number,
                    b.status
                FROM bookings b
                INNER JOIN shuttles s ON b.shuttle_id = s.id
                WHERE b.passenger_id = ? AND b.status = 'booked'
                ORDER BY s.trip_date DESC, s.departure_time DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $passengerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Cancel a booking
    public function cancelBooking($bookingId, $passengerId) {
        $sql = "UPDATE bookings SET status = 'cancelled'
                WHERE id = ? AND passenger_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $bookingId, $passengerId);
        return $stmt->execute();
    }

    // Check if a seat is actively booked
    public function isSeatBooked($shuttleId, $seatNumber) {
        $sql = "SELECT id FROM bookings
                WHERE shuttle_id = ? AND seat_number = ? AND status = 'booked'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $shuttleId, $seatNumber);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // Insert a booking
    public function insertBooking($passengerId, $shuttleId, $seatNumber) {
        // Step 1: Check if a booking already exists for this passenger and shuttle
        $checkSql = "SELECT id, status FROM bookings
                     WHERE passenger_id = ? AND shuttle_id = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->bind_param('ii', $passengerId, $shuttleId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $existing = $checkResult->fetch_assoc();

            if ($existing['status'] === 'booked') {
                // Already booked — cannot rebook
                return "duplicate";
            }

            // Step 2: Reuse cancelled/completed booking
            $updateSql = "UPDATE bookings
                          SET seat_number = ?, status = 'booked', booked_at = NOW()
                          WHERE id = ?";
            $updateStmt = $this->conn->prepare($updateSql);
            $updateStmt->bind_param('ii', $seatNumber, $existing['id']);
            return $updateStmt->execute() ? "rebooked" : "error";
        }

        // Step 3: No existing booking — insert new
        $sql = "INSERT INTO bookings (passenger_id, shuttle_id, seat_number, status, booked_at)
                VALUES (?, ?, ?, 'booked', NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iii', $passengerId, $shuttleId, $seatNumber);
        return $stmt->execute() ? "success" : "error";
    }


    // Get all booked seat numbers for a shuttle
    public function getBookedSeats($shuttleId) {
        $sql = "SELECT seat_number FROM bookings
                WHERE shuttle_id = ? AND status = 'booked'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $shuttleId);
        $stmt->execute();
        $result = $stmt->get_result();

        $seats = [];
        while ($row = $result->fetch_assoc()) {
            $seats[] = (int)$row['seat_number'];
        }
        return $seats;
    }
public function getShuttleCapacity($shuttleId) {
    $sql = "SELECT capacity FROM shuttles WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param('i', $shuttleId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    return $row['capacity'] ?? 12; // fallback to 12 if not found
}

}
