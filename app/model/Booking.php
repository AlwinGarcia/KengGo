<?php
// app/model/Booking.php

class Booking {
    private $conn;
    private $table = "bookings"; // ensure this exists in kenggo.sql

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new booking for a passenger on a shuttle/trip
    public function createBooking($passengerId, $shuttleId, $seatCount = 1) {
        $passengerId = intval($passengerId);
        $shuttleId   = intval($shuttleId);
        $seatCount   = intval($seatCount);

        if ($passengerId <= 0 || $shuttleId <= 0 || $seatCount <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid booking data'
            ];
        }

        // Default status is Pending; payment is always Cash for now
        $status  = 'Pending';
        $payment = 'Cash';

        $query = "INSERT INTO {$this->table}
                  (passenger_id, shuttle_id, seats, status, payment_method, created_at)
                  VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $this->conn->error
            ];
        }

        $stmt->bind_param("iiiss", $passengerId, $shuttleId, $seatCount, $status, $payment);

        if ($stmt->execute()) {
            return [
                'success'   => true,
                'message'   => 'Booking created successfully',
                'booking_id'=> $this->conn->insert_id
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to create booking: ' . $stmt->error
        ];
    }

    // All bookings for one passenger, joined with trip/shuttle info
    public function getBookingsForPassenger($passengerId) {
        $passengerId = intval($passengerId);
        $query = "SELECT b.*, s.shuttle_number, s.route, s.departure_time,
                         s.arrival_time, s.trip_date
                  FROM {$this->table} b
                  JOIN shuttles s ON b.shuttle_id = s.id
                  WHERE b.passenger_id = ?
                  ORDER BY b.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $passengerId);
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
}
