<?php
require_once __DIR__ . '/../model/BookedTripsModel.php';

class BookedTripsController {
    private $model;

    public function __construct($db) {
        $this->model = new BookedTripsModel($db);
    }

    public function showBookedTrips() {
        if (!isset($_SESSION['passenger_id'])) {
            header("Location: index.php?page=login");
            exit();
        }

        $passengerId = $_SESSION['passenger_id'];

        // Get all active bookings to count
        $allTrips   = $this->model->getAllBookedTrips($passengerId);
        $totalTrips = count($allTrips);

        // Show latest or all
        if (isset($_GET['all']) && $_GET['all'] == 1) {
            $trips   = $allTrips;
            $showAll = true;
        } else {
            $trips   = $this->model->getLatestBookedTrips($passengerId);
            $showAll = false;
        }

        include __DIR__ . '/../view/php/booked_trips.php';
    }
}
