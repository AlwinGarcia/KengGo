<?php
class DashboardModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch all available trips (active shuttles)
    public function getAvailableTrips() {
        $sql = "SELECT
                    s.id AS shuttle_id,
                    s.shuttle_number,
                    s.plate_number,
                    s.route,
                    s.trip_date,
                    s.departure_time,
                    s.arrival_time,
                    s.price,
                    s.capacity,
                    s.status
                FROM shuttles s
                WHERE s.status = 'active'
                ORDER BY s.trip_date ASC, s.departure_time ASC";

        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
