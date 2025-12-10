<?php
// delete_trip.php - Handle trip deletion

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
    // Get all trips for display
    try {
        $trip = new Trip($conn);
        $trips = $trip->getAllTrips();

        echo json_encode([
            'success' => true,
            'data' => $trips
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete a specific trip
    $trip_id = isset($_POST['trip_id']) ? intval($_POST['trip_id']) : 0;

    if ($trip_id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid trip ID']);
        exit;
    }

    try {
        $trip = new Trip($conn);
        $result = $trip->deleteTrip($trip_id);
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