<?php
class DashboardModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getPassengerTrips($passengerId, $limit = 5) {
        $sql = "SELECT b.id, s.plate_number, s.route, s.price,
                       s.trip_date AS destination
                FROM bookings b
                JOIN shuttles s ON b.shuttle_id = s.id
                WHERE b.passenger_id = ?
                ORDER BY b.booked_at DESC
                LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $passengerId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
