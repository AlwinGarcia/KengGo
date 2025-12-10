<?php
require_once __DIR__ . "/../model/SeatModel.php";

class SeatController {
    private $model;

    public function __construct($db) {
        $this->model = new SeatModel($db);
    }

    public function showSeatManagement() {
        if (!isset($_SESSION['passenger_id'])) {
            header("Location: index.php?page=login");
            exit();
        }

        $shuttleId = $_GET['shuttle_id'] ?? 1; // default shuttle
        $passengerId = $_SESSION['passenger_id'];

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
            $seatNumber = intval($_POST['selected_seat']);
            $this->model->bookSeat($passengerId, $shuttleId, $seatNumber);
        }

        $seats = $this->model->getBookedSeats($shuttleId);

        include __DIR__ . "/../view/php/seat_management.php";
    }
}
