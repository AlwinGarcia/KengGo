<?php
class TripModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getPastTrips() {
        $today = date('Y-m-d');
        $sql = "SELECT s.*, d.name AS driver_name
                FROM shuttles s
                LEFT JOIN drivers d ON s.driver_id = d.id
                WHERE s.trip_date < ?
                ORDER BY s.trip_date DESC, s.departure_time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $today);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
