<?php
// edit_trip.php - Handle trip editing

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');

// Include database connection
require_once __DIR__ . '/../../../includes/db_connect.php';
require_once __DIR__ . '/../../model/Trip.php';

// Check request method
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get trip data by ID
    $trip_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($trip_id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid trip ID']);
        exit;
    }

    try {
        $trip = new Trip($conn);
        $trip_data = $trip->getTripById($trip_id);

        if (empty($trip_data)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Trip not found']);
            exit;
        }

        echo json_encode(['success' => true, 'data' => $trip_data]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update trip data
    $trip_id = isset($_POST['trip_id']) ? intval($_POST['trip_id']) : 0;

    if ($trip_id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid trip ID']);
        exit;
    }

    try {
        $trip = new Trip($conn);
        $result = $trip->editTrip($trip_id, $_POST);
        echo json_encode($result);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}