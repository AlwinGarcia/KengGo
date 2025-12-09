<?php
require_once "config/db_connect.php";
require_once "controllers/LoginController.php";

$controller = new LoginController($conn);

$page = $_GET['page'] ?? 'login';

switch ($page) {
    case 'login':
        $controller->login();
        break;
    case 'dashboard':

        echo "Welcome to Dashboard!";
        break;
    default:
        echo "404 Page Not Found";
}
