<?php
require_once __DIR__ . '/../model/TripModel.php';

class TripsController {
    private $model;

    public function __construct($db) {
        $this->model = new TripModel($db);
    }

    public function showPastTrips() {


        $trips = $this->model->getPastTrips();
        include __DIR__ . '/../view/php/past_trips.php';
    }
}
