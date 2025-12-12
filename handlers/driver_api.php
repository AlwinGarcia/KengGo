<?php
// handlers/driver_api.php - API endpoints for driver management and driver portal data
// Session must start BEFORE any output
session_start();

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once '../includes/db_connect.php';
require_once '../app/driver/model/Driver.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

function send_json($payload, int $code = 200) {
    http_response_code($code);
    echo json_encode($payload);
    exit;
}

function require_driver_id(): int {
    if (!isset($_SESSION['driver_id'])) {
        send_json(['success' => false, 'message' => 'Unauthorized'], 401);
    }
    return (int) $_SESSION['driver_id'];
}

try {
    $driver_model = new Driver($conn);

    switch ($action) {
        /**
         * Driver portal: Assigned trips (current/active)
         */
        case 'assigned': {
            $driver_id = require_driver_id();

            $sql = "SELECT t.*, dta.status AS assignment_status, dta.assigned_at, dta.started_at, dta.completed_at
                    FROM driver_trip_assignments dta
                    JOIN trips t ON t.id = dta.trip_id
                    WHERE dta.driver_id = ?
                      AND dta.status IN ('assigned','accepted','en_route')
                    ORDER BY t.trip_date, t.depart_time";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $driver_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            send_json(['success' => true, 'data' => $rows]);
        }

        /**
         * Driver portal: My trips (history/upcoming)
         */
        case 'trips': {
            $driver_id = require_driver_id();
            $range = $_GET['range'] ?? 'all';

            $conditions = ["dta.driver_id = ?"];
            $order = "ORDER BY t.trip_date DESC, t.depart_time DESC";

            if ($range === 'history') {
                $conditions[] = "(dta.status IN ('completed','cancelled') OR t.trip_date < CURDATE())";
            } elseif ($range === 'upcoming') {
                $conditions[] = "(t.trip_date >= CURDATE() AND dta.status IN ('assigned','accepted','en_route','pending'))";
                $order = "ORDER BY t.trip_date ASC, t.depart_time ASC";
            }

            $where = 'WHERE ' . implode(' AND ', $conditions);
            $sql = "SELECT t.*, dta.status AS assignment_status, dta.assigned_at, dta.accepted_at, dta.started_at, dta.completed_at
                    FROM driver_trip_assignments dta
                    JOIN trips t ON t.id = dta.trip_id
                    $where
                    $order";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $driver_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            send_json(['success' => true, 'data' => $rows]);
        }

        /**
         * Driver portal: Profile data
         */
        case 'profile': {
            $driver_id = require_driver_id();

            $sql = "SELECT d.*,
                           (SELECT COUNT(*) FROM driver_trip_assignments a WHERE a.driver_id = d.id) AS assignments_total,
                           (SELECT COUNT(*) FROM driver_trip_assignments a WHERE a.driver_id = d.id AND a.status = 'completed') AS assignments_completed,
                           (SELECT COUNT(*) FROM driver_documents dd WHERE dd.driver_id = d.id AND dd.status = 'approved') AS documents_uploaded
                    FROM drivers d
                    WHERE d.id = ?
                    LIMIT 1";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $driver_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $driver = $result->fetch_assoc();
            $stmt->close();

            if (!$driver) {
                send_json(['success' => false, 'message' => 'Driver not found'], 404);
            }

            send_json(['success' => true, 'data' => $driver]);
        }

        /**
         * Driver portal: Notifications
         */
        case 'notifications': {
            $driver_id = require_driver_id();
            $status = $_GET['status'] ?? null;

            $where = 'WHERE driver_id = ?';
            $types = '';
            if ($status === 'unread') {
                $where .= " AND status = 'unread'";
            }

            $sql = "SELECT id, title, message, type, status, created_at, read_at
                    FROM driver_notifications
                    $where
                    ORDER BY created_at DESC
                    LIMIT 100";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $driver_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            send_json(['success' => true, 'data' => $rows]);
        }

        case 'mark_read': {
            $driver_id = require_driver_id();
            $notif_id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            if ($notif_id <= 0) send_json(['success' => false, 'message' => 'Invalid notification id'], 400);

            $stmt = $conn->prepare("UPDATE driver_notifications SET status='read', read_at = NOW() WHERE id = ? AND driver_id = ?");
            $stmt->bind_param('ii', $notif_id, $driver_id);
            $ok = $stmt->execute();
            $stmt->close();

            if (!$ok || $conn->affected_rows === 0) {
                send_json(['success' => false, 'message' => 'Nothing updated'], 404);
            }

            send_json(['success' => true]);
        }

        /**
         * Driver portal: Documents
         */
        case 'documents': {
            $driver_id = require_driver_id();
            $sql = "SELECT id, doc_type, file_name, file_url, issued_at, expires_at, status, verified_by, verified_at, created_at
                    FROM driver_documents
                    WHERE driver_id = ?
                    ORDER BY (expires_at IS NULL), expires_at ASC, created_at DESC";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $driver_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            send_json(['success' => true, 'data' => $rows]);
        }

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
