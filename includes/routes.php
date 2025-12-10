<?php
require_once __DIR__ . "/db_connect.php";
require_once __DIR__ . "/../app/passenger/controller/LoginController.php";

$loginController = new LoginController($conn);

$page = $_GET['page'] ?? 'login';

switch ($page) {
    case 'login':
        $loginController->login();
        break;
    case 'logout':
        $loginController->logout();
        break;
    case 'dashboard':
        session_start();
        echo "<h2>Welcome to Dashboard, " . ($_SESSION['passenger_name'] ?? 'Guest') . "</h2>";
        break;
    default:
        echo "<h2>404 Page Not Found</h2>";
}
