<?php
session_start();
header("Content-Type: application/json");
// error_reporting(E_ALL);
// ini_set('display_errors', 1);


require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Attendee.php';
require_once __DIR__ . '/../classes/BaseEvent.php';
require_once __DIR__ . '/../classes/PublicEvents.php';
require_once __DIR__ . '/../classes/PrivateEvent.php';
require_once __DIR__ . '/../classes/PublicAttendee.php';
require_once __DIR__ . '/../classes/Admin.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/EventApi.php';

$db = new Database();
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// Extract the endpoint from the request URI
$uriSegments = explode('/', trim($requestUri, '/'));
$endpoint = $uriSegments[2] ?? ''; 

if (count($uriSegments) > 3) {
    $endpoint = $uriSegments[2] . '/' . $uriSegments[3];
}
if (count($uriSegments) > 4) {
    $endpoint = $uriSegments[2] . '/' . $uriSegments[3] . '/' . $uriSegments[4];
}
// echo "Endpoint: " . $endpoint . "\n";exit;

$baseUrl = "http://localhost/ems";

// Handle API endpoints
switch ($endpoint) {
    case 'register':
        $register = new EventApi();
        $register->register($requestMethod, $db);
        break;

    case 'login':
        $login = new EventApi();
        $login->login($requestMethod, $db, $baseUrl);
        break;

    case 'admin/login':
        $adminLogin = new EventApi();
        $adminLogin->AdminLogin($requestMethod, $db, $baseUrl);
        break;

    case 'admin/events':
        $adminEvents = new EventApi();
        $adminEvents->AdminEvents($requestMethod, $db);
        break;

    case 'admin/event/delete':
        $adminEventDelete = new EventApi();
        $adminEventDelete->AdminEventDelete($requestMethod, $db);
        break;

    case 'admin/attendees':
        $adminAttendees = new EventApi();
        $adminAttendees->AdminAttendees($requestMethod, $db);
        break;

    case 'admin/attendee/delete':
        $adminAttendeeDelete = new EventApi();
        $adminAttendeeDelete->AdminAttendeeDelete($requestMethod, $db);
        break;

    case 'admin/users':
        $adminUsers = new EventApi();
        $adminUsers->AdminUsers($requestMethod, $db);
        break;

    case 'admin/user/delete':
        $adminUserDelete = new EventApi();
        $adminUserDelete->AdminUserDelete($requestMethod, $db);
        break;

    case 'logout':
        $logout = new EventApi();
        $logout->logout($requestMethod, $db);
        break;

    case 'events':
        $events = new EventApi();
        $events->events($requestMethod, $db);
        break;

    case 'totalEvents':
        $totalEvents = new EventApi();
        $totalEvents->totalEvents($requestMethod, $db);
        break;

    case 'events/public':
        $publicEvents = new EventApi();
        $publicEvents->publicEvents($requestMethod, $db);
        break;

    case 'event/create':
        $createEvent = new EventApi();
        $createEvent->createEvent($requestMethod, $db);
        break;

    case 'event/update':
        $updateEvent = new EventApi();
        $updateEvent->updateEvent($requestMethod, $db);
        break;
    case 'event/delete':
        $deleteEvent = new EventApi();
        $deleteEvent->deleteEvent($requestMethod, $db);
        break;

    case 'attendees':
        $attendees = new EventApi();
        $attendees->attendees($requestMethod, $db);
        break;

    case 'totalAttendees':
        $totalAttendees = new EventApi();
        $totalAttendees->totalAttendees($requestMethod, $db);
        break;

    case 'attendees/create':
        $createAttendees = new EventApi();
        $createAttendees->createAttendees($requestMethod, $db);
        break;

    default:
        http_response_code(404); // Not Found
        echo json_encode(["message" => "Invalid endpoint"]);
        break;
}
