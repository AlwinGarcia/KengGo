<?php
require_once __DIR__ . "/../passenger/model/passenger.php";

class LoginController {
    private $model;

    public function __construct($db) {
        $this->model = new Passenger($db);
    }

   public function login() {


       if ($_SERVER["REQUEST_METHOD"] === "POST") {
           $email    = $_POST['email'] ?? '';
           $password = $_POST['password'] ?? '';

           $user = $this->model->findByEmail($email);

           if ($user && $user['password'] === $password) {
               $_SESSION['passenger_id']   = $user['id'];
               $_SESSION['passenger_name'] = $user['name'];
               $_SESSION['role']           = 'passenger'; // ✅ required for past trips

               header("Location: index.php?page=dashboard");
               exit();
           } else {
               $error = "Invalid email or password.";
               include __DIR__ . "/../passenger/view/php/login.php";
           }
       } else {
           include __DIR__ . "/../passenger/view/php/login.php";
       }
   }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php?page=login");
        exit();
    }
}
