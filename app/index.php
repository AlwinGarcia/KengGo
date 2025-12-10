<?php
session_start();

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/controller/LoginController.php";
require_once __DIR__ . "/passenger/controller/DashboardController.php"; // ✅ add dashboard controller

// Initialize controllers
$controller = new LoginController($conn);
$dashboardController = new DashboardController($conn);

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

    default:
        echo "<h2>404 Page Not Found</h2>";
}
