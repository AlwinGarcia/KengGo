<?php
class BookedTripsModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Latest 6 bookings
    public function getLatestBookedTrips($passengerId) {
        $sql = "SELECT
                    b.id AS booking_id,   -- ✅ booking id
                    s.id AS shuttle_id,
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
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // All bookings
    public function getAllBookedTrips($passengerId) {
        $sql = "SELECT
                    b.id AS booking_id,   -- ✅ booking id
                    s.id AS shuttle_id,
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
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
