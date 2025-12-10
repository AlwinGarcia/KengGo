<?php
// handlers/trip_api.php - API endpoints for trip operations
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Include database connection
require_once '../includes/db_connect.php';
require_once '../app/model/Trip.php';

// Get the action from query parameter or POST
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

try {
    $trip = new Trip($conn);

    switch ($action) {
        case 'add':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }
            $result = $trip->addTrip($_POST);
            echo json_encode($result);
            break;

        case 'edit':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                // Get trip data for editing
                $trip_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                if ($trip_id <= 0) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid trip ID']);
                    exit;
                }
                $trip_data = $trip->getTripById($trip_id);
                if (empty($trip_data)) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Trip not found']);
                    exit;
                }
                echo json_encode(['success' => true, 'data' => $trip_data]);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Update trip
                $trip_id = isset($_POST['trip_id']) ? intval($_POST['trip_id']) : 0;
                if ($trip_id <= 0) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid trip ID']);
                    exit;
                }
                $result = $trip->editTrip($trip_id, $_POST);
                echo json_encode($result);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            }
            break;

        case 'delete':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                // Get all trips
                $trips = $trip->getAllTrips();
                echo json_encode(['success' => true, 'data' => $trips]);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Delete specific trip
                $trip_id = isset($_POST['trip_id']) ? intval($_POST['trip_id']) : 0;
                if ($trip_id <= 0) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid trip ID']);
                    exit;
                }
                $result = $trip->deleteTrip($trip_id);
                echo json_encode($result);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            }
            break;

        case 'list':
            // Get all trips
            $trips = $trip->getAllTrips();
            echo json_encode(['success' => true, 'data' => $trips]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
