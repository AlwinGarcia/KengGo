<?php
require_once __DIR__ . "/../model/DashboardModel.php";

class DashboardController {
    private $model;

    public function __construct($db) {
        $this->model = new DashboardModel($db);
    }

    public function showDashboard() {
        if (!isset($_SESSION['passenger_id'])) {
            header("Location: index.php?page=login");
            exit();
        }

        // Fetch available trips instead of passenger bookings
        $trips = $this->model->getAvailableTrips();

        include __DIR__ . "/../view/php/dashboard.php";
    }
}
