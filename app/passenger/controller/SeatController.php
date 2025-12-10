<?php
require_once __DIR__ . "/../model/SeatModel.php";

class SeatController {
    private $model;

    public function __construct($db) {
        $this->model = new SeatModel($db);
    }

    public function showSeatManagement() {
        // not logged in
        if (!isset($_SESSION['passenger_id'])) {
            $_SESSION['error'] = "Please log in to book a seat.";
            header("Location: index.php?page=login");
            exit();
        }

        $shuttleId   = $_GET['shuttle_id'] ?? 1;
        $passengerId = $_SESSION['passenger_id'];
        $message     = null;

        // Shuttle not found
        $shuttle = $this->model->getShuttle($shuttleId);
        if (!$shuttle || $shuttle['status'] !== 'active') {
            $message = " Shuttle not available for booking.";
            $seats   = [];
            include __DIR__ . "/../view/php/seat_management.php";
            return;
        }

        $shuttleCapacity = $shuttle['capacity'];

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // cancel button handling
            if (isset($_POST['cancel'])) {
                header("Location: index.php?page=dashboard");
                exit();
            }

            if (isset($_POST['confirm'])) {
                $seatNumber = intval($_POST['selected_seat']);

                // invalid seat number
                if ($seatNumber < 1 || $seatNumber > $shuttleCapacity) {
                    $message = " Invalid seat number selected.";
                } else {
                    try {
                        $success = $this->model->bookSeat($passengerId, $shuttleId, $seatNumber);
                        if ($success) {
                            $message = " Seat $seatNumber booked successfully!";
                        } else {
                            $message = "Could not book seat $seatNumber.";
                        }
                    } catch (mysqli_sql_exception $e) {
                        //  duplicate booking error
                        if ($e->getCode() == 1062) {
                            $message = " Seat $seatNumber is already booked. Please choose another.";
                            error_log("Duplicate booking attempt: Passenger $passengerId tried seat $seatNumber on shuttle $shuttleId");
                        } else {
                            //  database error
                            $message = " System error occurred. Please try again later.";
                            error_log("Booking error: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        // Get booked seats for rendering
        $seats = $this->model->getBookedSeats($shuttleId);

        include __DIR__ . "/../view/php/seat_management.php";
    }
}
