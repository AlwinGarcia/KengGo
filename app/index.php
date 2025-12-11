<?php
session_start();

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/controller/LoginController.php";
require_once __DIR__ . "/passenger/controller/DashboardController.php";
require_once __DIR__ . "/passenger/controller/SeatController.php";
require_once __DIR__ . "/passenger/controller/BookedTripsController.php";
require_once __DIR__ . "/passenger/controller/TripsController.php";

// Initialize controllers
$controller            = new LoginController($conn);
$dashboardController   = new DashboardController($conn);
$seatController        = new SeatController($conn);
$bookedTripsController = new BookedTripsController($conn);
$tripsController       = new TripsController($conn);

// Route based on ?page=
$page = $_GET['page'] ?? 'login';

switch ($page) {
    case 'login':
        $controller->login();
        break;

    case 'logout':
        $controller->logout();
        break;

    case 'dashboard':
        $dashboardController->showDashboard();
        break;

    case 'seat-management':
        $seatController->showSeatManagement();
        break;

    case 'seat-confirm':
        $seatController->confirmSeat();
        break;

    case 'booked-trips':
        $bookedTripsController->showBookedTrips();
        break;

    case 'trips':
        $tripsController->showPastTrips();
        break;

    default:
        echo "<h2>404 Page Not Found</h2>";
}
