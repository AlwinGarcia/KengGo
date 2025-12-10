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

        // Get all trips first to count them
        $allTrips   = $this->model->getAllBookedTrips($passengerId);
        $totalTrips = count($allTrips);

        // If ?all=1 is set, show all bookings
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
