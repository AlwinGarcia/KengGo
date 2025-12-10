<?php
require_once __DIR__ . '/../model/BookedTripsModel.php';

class SeatController {
    private $model;

    public function __construct($db) {
        $this->model = new BookedTripsModel($db);
    }

    // Show seat management page
    public function showSeatManagement() {
        if (!isset($_SESSION['passenger_id'])) {
            header("Location: index.php?page=login");
            exit();
        }

        $shuttleId = $_GET['shuttle_id'] ?? 1;

        // Get booked seats
        $seats = $this->model->getBookedSeats($shuttleId); // array of seat numbers

        // Get shuttle capacity
        $capacity = $this->model->getShuttleCapacity($shuttleId); // new method in BookedTripsModel

        // Get optional message
        $message = $_SESSION['seat_message'] ?? null;
        unset($_SESSION['seat_message']);

        // Pass all to view
        include __DIR__ . '/../view/php/seat_management.php';
    }

    // Confirm seat booking
    public function confirmSeat() {
        if (!isset($_SESSION['passenger_id'])) {
            header("Location: index.php?page=login");
            exit();
        }

        $passengerId = $_SESSION['passenger_id'];
        $shuttleId   = $_GET['shuttle_id'] ?? 1;
        $seatNumber  = trim($_POST['selected_seat'] ?? '');

        if (!$seatNumber) {
            $_SESSION['seat_message'] = "No seat selected.";
            header("Location: index.php?page=seat-management&shuttle_id={$shuttleId}");
            exit();
        }

        // Check if seat is already booked
        if ($this->model->isSeatBooked($shuttleId, $seatNumber)) {
            $_SESSION['seat_message'] = "Seat {$seatNumber} is already booked. Please choose another.";
            header("Location: index.php?page=seat-management&shuttle_id={$shuttleId}");
            exit();
        }

        // Insert or reuse booking
        $result = $this->model->insertBooking($passengerId, $shuttleId, $seatNumber);

        switch ($result) {
            case "duplicate":
                $_SESSION['seat_message'] = "You already have a booking for this shuttle.";
                header("Location: index.php?page=seat-management&shuttle_id={$shuttleId}");
                break;

            case "rebooked":
                $_SESSION['seat_message'] = "Your cancelled booking has been reactivated with seat {$seatNumber}.";
                header("Location: index.php?page=booked-trips");
                break;

            case "success":
                $_SESSION['seat_message'] = "Seat {$seatNumber} booked successfully!";
                header("Location: index.php?page=booked-trips");
                break;

            default:
                $_SESSION['seat_message'] = "Unable to book seat. Please try again.";
                header("Location: index.php?page=seat-management&shuttle_id={$shuttleId}");
                break;
        }
        exit();
    }
}
