<?php
class SeatModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get booked seats for a given shuttle
    public function getBookedSeats($shuttleId) {
        $sql = "SELECT seat_number FROM bookings WHERE shuttle_id = ? AND status = 'booked'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $shuttleId);
        $stmt->execute();
        $result = $stmt->get_result();
        return array_column($result->fetch_all(MYSQLI_ASSOC), 'seat_number');
    }

    public function getShuttle($shuttleId) {
    $sql = "SELECT * FROM shuttles WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $shuttleId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
    }


    // Book a seat
    public function bookSeat($passengerId, $shuttleId, $seatNumber) {
        $sql = "INSERT INTO bookings (passenger_id, shuttle_id, seat_number, status)
                VALUES (?, ?, ?, 'booked')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $passengerId, $shuttleId, $seatNumber);
        return $stmt->execute();
    }
}
