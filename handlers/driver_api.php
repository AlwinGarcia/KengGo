<?php
// handlers/driver_api.php - API endpoints for driver management
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once '../includes/db_connect.php';
require_once '../app/driver/model/Driver.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

try {
    $driver_model = new Driver($conn);

    switch ($action) {
        case 'list':
            $drivers = $driver_model->getAllDrivers();
            echo json_encode(['success' => true, 'data' => $drivers]);
            break;

        case 'create':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }
            $result = $driver_model->createDriver($_POST);
            echo json_encode($result);
            break;

        case 'view':
            $driver_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if ($driver_id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid driver ID']);
                exit;
            }
            $driver = $driver_model->getDriverById($driver_id);
            if (!$driver) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Driver not found']);
                exit;
            }
            echo json_encode(['success' => true, 'data' => $driver]);
            break;

        case 'edit':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $driver_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                if ($driver_id <= 0) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid driver ID']);
                    exit;
                }
                $driver = $driver_model->getDriverById($driver_id);
                if (!$driver) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Driver not found']);
                    exit;
                }
                echo json_encode(['success' => true, 'data' => $driver]);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $driver_id = isset($_POST['driver_id']) ? intval($_POST['driver_id']) : 0;
                if ($driver_id <= 0) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid driver ID']);
                    exit;
                }
                $result = $driver_model->updateDriver($driver_id, $_POST);
                echo json_encode($result);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            }
            break;

        case 'delete':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $drivers = $driver_model->getAllDrivers();
                echo json_encode(['success' => true, 'data' => $drivers]);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $driver_id = isset($_POST['driver_id']) ? intval($_POST['driver_id']) : 0;
                if ($driver_id <= 0) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid driver ID']);
                    exit;
                }
                $result = $driver_model->deleteDriver($driver_id);
                echo json_encode($result);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            }
            break;

        case 'search':
            $query = isset($_GET['q']) ? $_GET['q'] : '';
            if (empty($query)) {
                echo json_encode(['success' => false, 'message' => 'Search query required']);
                exit;
            }
            $results = $driver_model->searchDrivers($query);
            echo json_encode(['success' => true, 'data' => $results]);
            break;

        case 'stats':
            $stats = $driver_model->getStatistics();
            echo json_encode(['success' => true, 'data' => $stats]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
