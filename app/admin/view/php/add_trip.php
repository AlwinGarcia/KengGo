<?php
// add_trip.php - Handle trip addition

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Include database connection
    require_once __DIR__ . '/../../../includes/db_connect.php';
    require_once __DIR__ . '/../../model/Trip.php';

    // Get POST data
    $data = $_POST;

    // Initialize Trip model
    $trip = new Trip($conn);

    // Add trip
    $result = $trip->addTrip($data);

    // Send JSON response
    echo json_encode($result);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}