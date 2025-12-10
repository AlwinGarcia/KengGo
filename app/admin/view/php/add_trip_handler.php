<?php
/**
 * Add Trip Handler
 * Handles the form submission for adding new trips
 */

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection and Trip model
require_once __DIR__ . '/../../../../includes/db_connect.php';
require_once __DIR__ . '/../../../model/Trip.php';

// Response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method. Only POST is allowed.');
    }
    
    // Get posted data
    $data = json_decode(file_get_contents("php://input"), true);
    
    // If no JSON data, try $_POST
    if (empty($data)) {
        $data = $_POST;
    }
    
    // Validate required fields
    $required_fields = ['shuttle_number', 'from_address', 'to_address', 'trip_date', 'depart_time', 'price'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        throw new Exception('Missing required fields: ' . implode(', ', $missing_fields));
    }
    
    // Validate data types
    if (!is_numeric($data['price']) || $data['price'] <= 0) {
        throw new Exception('Price must be a positive number');
    }
    
    if (isset($data['seats']) && (!is_numeric($data['seats']) || $data['seats'] <= 0)) {
        throw new Exception('Seats must be a positive number');
    }
    
    // Validate date format
    $date = DateTime::createFromFormat('Y-m-d', $data['trip_date']);
    if (!$date || $date->format('Y-m-d') !== $data['trip_date']) {
        throw new Exception('Invalid date format. Use YYYY-MM-DD');
    }
    
    // Create Trip object
    $trip = new Trip($conn);
    
    // Set trip properties
    $trip->shuttle_number = $data['shuttle_number'];
    $trip->driver_name = $data['driver_name'] ?? null;
    $trip->driver_id = !empty($data['driver_id']) ? (int)$data['driver_id'] : null;
    $trip->plate_number = $data['plate_number'] ?? null;
    $trip->seats_available = isset($data['seats']) ? (int)$data['seats'] : 20;
    $trip->from_address = $data['from_address'];
    $trip->to_address = $data['to_address'];
    $trip->trip_date = $data['trip_date'];
    $trip->depart_time = $data['depart_time'];
    $trip->arrive_time = $data['arrive_time'] ?? null;
    $trip->price = (float)$data['price'];
    $trip->status = $data['status'] ?? 'active';
    $trip->notes = $data['notes'] ?? null;
    
    // Create the trip
    if ($trip->create()) {
        $response['success'] = true;
        $response['message'] = 'Trip added successfully!';
        $response['data'] = [
            'trip_id' => $trip->id,
            'shuttle_number' => $trip->shuttle_number,
            'from' => $trip->from_address,
            'to' => $trip->to_address,
            'date' => $trip->trip_date,
            'time' => $trip->depart_time
        ];
        http_response_code(201); // Created
    } else {
        throw new Exception('Failed to create trip. Please try again.');
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400); // Bad Request
    error_log('Add Trip Error: ' . $e->getMessage());
} catch (Throwable $e) {
    $response['message'] = 'An unexpected error occurred. Please try again later.';
    http_response_code(500); // Internal Server Error
    error_log('Add Trip Fatal Error: ' . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
exit;
?>
