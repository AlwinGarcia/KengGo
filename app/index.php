<?php
session_start();

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/controller/LoginController.php";
require_once __DIR__ . "/passenger/controller/DashboardController.php";
require_once __DIR__ . "/passenger/controller/SeatController.php";
require_once __DIR__ . "/passenger/controller/BookedTripsController.php";
require_once __DIR__ . "/passenger/controller/TripsController.php"; // ✅ add TripsController

// Initialize controllers
$controller            = new LoginController($conn);
$dashboardController   = new DashboardController($conn);
$seatController        = new SeatController($conn);
$bookedTripsController = new BookedTripsController($conn);
$tripsController       = new TripsController($conn); // ✅ initialize

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

    case 'seat-management': // ✅ loads seat_management.php via SeatController
        $seatController->showSeatManagement();
        break;

    case 'seat-confirm': // ✅ handles POST from seat_management.php
        $seatController->confirmSeat();
        break;

    case 'booked-trips':
        $bookedTripsController->showBookedTrips();
        break;

    case 'trips': // ✅ new route for Past Trips
        $tripsController->showPastTrips();
        break;

    default:
        echo "<h2>404 Page Not Found</h2>";
}
