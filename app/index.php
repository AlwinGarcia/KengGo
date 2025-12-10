<?php
session_start();

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/controller/LoginController.php";

// Initialize controller
$controller = new LoginController($conn);

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
        echo "<h2>Welcome to Dashboard, " . htmlspecialchars($_SESSION['passenger_name'] ?? 'Guest') . "</h2>";
        echo "<p><a href='index.php?page=logout'>Logout</a></p>";
        break;
    default:
        echo "<h2>404 Page Not Found</h2>";
}
