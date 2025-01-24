<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Event.php';
require_once __DIR__ . '/../classes/Attendee.php';

$db = new Database();
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// Extract the endpoint from the request URI
$uriSegments = explode('/', trim($requestUri, '/'));
$endpoint = $uriSegments[2] ?? ''; // "register" is the 3rd segment in /ems/api/register

// multimple endpoint like /ems/api/events/create 
if (count($uriSegments) > 3) {
    $endpoint = $uriSegments[2] . '/' . $uriSegments[3];
}

// echo "Endpoint: " . $endpoint . "\n";exit;

// Handle API endpoints
switch ($endpoint) {
    case 'register':
        if ($requestMethod === 'POST') {
            $user = new User($db);
            $data = json_decode(file_get_contents('php://input'), true);

            // Get the username, name, email, and password from the request body
            $username = htmlspecialchars($data['username']);
            $name = htmlspecialchars($data['name']);
            $email = htmlspecialchars($data['email']);
            $password = $data['password'];

            // Validate the username, name, email, and password
            if (empty($username) || empty($name) || empty($email) || empty($password)) {
                echo json_encode(["success" => false,"message" => "All fields are required"]);
                exit;
            }

            // Check if the username already exists
            if ($user->checkUsernameExists($username)) {
                echo json_encode(["success" => false,"message" => "Username already exists"]);
                exit;
            }

            // Check if the email already exists
            if ($user->checkEmailExists($email)) {
                echo json_encode(["success" => false,"message" => "Email already exists"]);
                exit;
            }

            // Register a new user
            if ($user->register($username, $name, $email, $password)) {
                echo json_encode(["success" => true,"message" => "User registered successfully"]);
                exit;
            } else {
                echo json_encode(["success" => false,"message" => "Registration failed"]);
                exit;
            }
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["status" => "405","message" => "Method not allowed"]);
            exit;
        }
        break;

    case 'login':
        if ($requestMethod === 'POST') {
            $user = new User($db);
            $data = json_decode(file_get_contents('php://input'), true);

            // Get the email and password from the request body
            $email = htmlspecialchars($data['email']);
            $password = $data['password'];

            // Validate the email and password
            if (empty($email) || empty($password)) {
                echo json_encode(["success" => false,"message" => "All fields are required"]);
                exit;
            }

            // Login the user
            if ($user->login($email, $password)) {
                echo json_encode(["success" => true,"message" => "User logged in successfully"]);
                exit;
            } else {
                echo json_encode(["success" => false,"message" => "Invalid Email or Password"]);
                exit;
            }
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
            exit;
        }
        break;
    
    case 'logout':
        if ($requestMethod === 'POST') {
            $user = new User($db);
            if ($user->logout()) {
                echo json_encode(["success" => true,"message" => "User logged out successfully"]);
                exit;
            } else {
                echo json_encode(["success" => false,"message" => "Logout failed"]);
                exit;
            }
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
            exit;
        }
        break;

    case 'events':
        $event = new Event($db);
        if ($requestMethod === 'GET') {
            echo json_encode($event->getAll());
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
        break;

    case 'event/create':
        if ($requestMethod === 'POST') {
            $event = new Event($db);
            $data = json_decode(file_get_contents('php://input'), true);

            // Get the user_id, name, description, date, time, location, and max_capacity from the request body
            $userId = htmlspecialchars($data['user_id']);
            $name = htmlspecialchars($data['name']);
            $description = htmlspecialchars($data['description']);
            $date = htmlspecialchars($data['date']);
            $time = htmlspecialchars($data['time']);
            $location = htmlspecialchars($data['location']);
            $maxCapacity = htmlspecialchars($data['max_capacity']);

            // check if user_id exists
            $user = new User($db);
            if (!$user->checkUserIdExists($userId)) {
                echo json_encode(["success" => false,"message" => "User ID does not exist"]);
                exit;
            }

            // Validate the user_id, name, description, date, time, location, and max_capacity
            if (empty($userId) || empty($name) || empty($description) || empty($date) || empty($time) || empty($location) || empty($maxCapacity)) {
                echo json_encode(["success" => false,"message" => "All fields are required"]);
                exit;
            }

            // Create a new event
            if ($event->create($userId, $name, $description, $date, $time, $location, $maxCapacity)) {
                echo json_encode(["success" => true,"message" => "Event created successfully"]);
                exit;
            } else {
                echo json_encode(["success" => false,"message" => "Event creation failed"]);
                exit;
            }
            
        } 
        break;
    case 'event/delete':
        if ($requestMethod === 'DELETE') {
            $eventId = $_GET['id'];
            $event = new Event($db);
            if ($event->delete($eventId)) {
                echo json_encode(["success" => true,"message" => "Event deleted successfully"]);
            } else {
                echo json_encode(["success" => false,"message" => "Event deletion failed"]);
            }
        }
        break;

    case 'attendees':
        if ($requestMethod === 'GET') {
            $eventId = $_GET['id'];
            $attendee = new Attendee($db);
            echo json_encode($attendee->getAllAttendees($eventId));
        }
        break;

    case 'attendees/create':
        if ($requestMethod === 'POST') {
            $attendees = new Attendee($db);

            $data = json_decode(file_get_contents('php://input'), true);
            $eventId = htmlspecialchars($data['event_id']);
            $name = htmlspecialchars($data['name']);
            $email = htmlspecialchars($data['email']);

            $event = new Event($db);
            // check if the event exists
            if (!$event->checkEventExists($eventId)) {
                echo json_encode(["success" => false,"message" => "Event does not exists"]);
                exit;
            }

            // check if the attendee exists
            if ($attendees->checkAttendeeExists($eventId, $email)) {
                echo json_encode(["success" => false,"message" => "Attendee already exists"]);
                exit;
            }

            // print_r($data);exit();

            if ($attendees->create($eventId, $name, $email)) {
                echo json_encode(["success" => true,"message" => "Attendee created successfully"]);
            } else {
                echo json_encode(["success" => false,"message" => "Attendee creation failed"]);
            }
        }
        else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
        break;

    default:
        http_response_code(404); // Not Found
        echo json_encode(["message" => "Invalid endpoint"]);
        break;
}
?>