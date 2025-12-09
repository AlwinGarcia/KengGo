<?php
// controllers/LoginController.php
require_once "models/Passenger.php";

class LoginController {
    private $model;

    public function __construct($db) {
        $this->model = new Passenger($db);
    }

    public function login() {
        session_start();

        // Handle POST request (form submission)
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email    = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Fetch passenger by email
            $user = $this->model->findByEmail($email);

            if ($user && $user['password'] === $password) {
                // ✅ For testing: plain text "pass"
                // 🔒 In production: use password_verify($password, $user['password'])
                $_SESSION['passenger_id'] = $user['id'];
                $_SESSION['passenger_name'] = $user['name'];

                // Redirect to dashboard
                header("Location: index.php?page=dashboard");
                exit();
            } else {
                // Invalid credentials → reload login view with error
                $error = "Invalid email or password.";
                include "views/login.php";
            }
        } else {
            // GET request → show login form
            include "views/login.php";
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php?page=login");
        exit();
    }
}
