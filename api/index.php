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
                echo json_encode(["success" => false, "message" => "All fields are required"]);
                exit;
            }

            // Check if the username already exists
            if ($user->checkUsernameExists($username)) {
                echo json_encode(["success" => false, "message" => "Username already exists"]);
                exit;
            }

            // Check if the email already exists
            if ($user->checkEmailExists($email)) {
                echo json_encode(["success" => false, "message" => "Email already exists"]);
                exit;
            }

            // Register a new user
            if ($user->register($username, $name, $email, $password)) {
                echo json_encode(["success" => true, "message" => "User registered successfully"]);
                exit;
            } else {
                echo json_encode(["success" => false, "message" => "Registration failed"]);
                exit;
            }
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["status" => "405", "message" => "Method not allowed"]);
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
                echo json_encode(["success" => false, "message" => "All fields are required"]);
                exit;
            }

            // Login the user
            if ($user->login($email, $password)) {
                echo json_encode(["success" => true, "message" => "User logged in successfully"]);
                exit;
            } else {
                echo json_encode(["success" => false, "message" => "Invalid Email or Password"]);
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
                http_response_code(302);
                header('Location: http://localhost/ems/public/views/login.php');
                // echo json_encode(["success" => true, "message" => "User logged out successfully"]);
                exit;
            } else {
                echo json_encode(["success" => false, "message" => "Logout failed"]);
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
            $name = htmlspecialchars($data['name']);
            $description = htmlspecialchars($data['description']);
            $date = htmlspecialchars($data['date']);
            $time = htmlspecialchars($data['time']);
            $location = htmlspecialchars($data['location']);
            $maxCapacity = htmlspecialchars($data['max_capacity']);

            // Validate the user_id, name, description, date, time, location, and max_capacity
            if (empty($name) || empty($description) || empty($date) || empty($time) || empty($location) || empty($maxCapacity)) {
                echo json_encode(["success" => false, "message" => "All fields are required"]);
                exit;
            }

            // Create a new event
            if ($event->create($name, $description, $date, $time, $location, $maxCapacity)) {
                echo json_encode(["success" => true, "message" => "Event created successfully"]);
                exit;
            } else {
                echo json_encode(["success" => false, "message" => "Event creation failed"]);
                exit;
            }
        }
        break;
    case 'event/delete':
    if ($requestMethod === 'DELETE') {
        // Read and decode the JSON input
        $data = json_decode(file_get_contents('php://input'), true);

        $eventId = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);

        // Check if 'id' is present in the JSON data
        if (!isset($eventId)) {
            http_response_code(400); // Bad Request
            echo json_encode(["success" => false, "message" => "Event ID is required"]);
            exit;
        }

        $event = new Event($db);
        // Check if the event exists
        if (!$event->checkEventExists($eventId)) {
            http_response_code(404); // Not Found
            echo json_encode(["success" => false, "message" => "Event does not exists"]);
            exit;
        }

        // Check if the user is authorized to delete the event
        if (!$event->checkEventOwner($eventId)) {
            http_response_code(403); // Forbidden
            echo json_encode(["success" => false, "message" => "You are not authorized to delete this event"]);
            exit;
        }

        // Attempt to delete the event
        if ($event->delete($eventId)) {
            http_response_code(200); // OK
            echo json_encode(["success" => true, "message" => "Event deleted successfully"]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["success" => false, "message" => "Event deletion failed"]);
        }
    } else {
        http_response_code(405); // Method Not Allowed
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
    }
    break;

    case 'attendees':
        if ($requestMethod === 'GET') {
            $attendee = new Attendee($db);
    
            // Fetch all attendees
            $attendees = $attendee->getAllAttendees();
    
            // Loop through attendees and add event names
            $attendeesWithEventNames = [];
            foreach ($attendees as $attendeeData) {
                $eventId = $attendeeData['event_id'];
                $eventName = $attendee->getEventName($eventId); // Fetch event name for each attendee
    
                // Combine attendee data with event name
                $attendeeData['event_name'] = $eventName;
                $attendeesWithEventNames[] = $attendeeData;
            }
    
            // Return the combined data as JSON
            echo json_encode($attendeesWithEventNames);
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
                echo json_encode(["success" => false, "message" => "Event does not exists"]);
                exit;
            }

            // check if the attendee exists
            if ($attendees->checkAttendeeExists($eventId, $email)) {
                echo json_encode(["success" => false, "message" => "Attendee already exists"]);
                exit;
            }

            // print_r($data);exit();

            if ($attendees->create($eventId, $name, $email)) {
                echo json_encode(["success" => true, "message" => "Attendee created successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Attendee creation failed"]);
            }
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
        break;

    default:
        http_response_code(404); // Not Found
        echo json_encode(["message" => "Invalid endpoint"]);
        break;
}
