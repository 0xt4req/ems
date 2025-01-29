<?php

class EventApi
{
    // ============================================================== Start Of User APIs ==============================================================

    public function register($requestMethod, $db)
    {
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
    }

    public function login($requestMethod, $db, $baseUrl)
    {
        if ($requestMethod === 'POST') {
            $user = new Auth($db);
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
                http_response_code(302);
                echo json_encode(["success" => true, "message" => "User logged in successfully", "location" => "$baseUrl/public/views/user/dashboard.php"]);
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
    }

    public function events($requestMethod, $db)
    {
        if ($requestMethod === 'GET') {
            $event = new PrivateEvent($db);
            echo json_encode($event->getAll());
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
    }

    public function totalEvents($requestMethod, $db)
    {
        if ($requestMethod === 'GET') {
            $event = new PublicEvents($db);
            echo json_encode($event->totalEvents());
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
    }

    public function publicEvents($requestMethod, $db)
    {
        if ($requestMethod === 'GET') {
            $event = new PublicEvents($db);
            echo json_encode($event->getAll());
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
    }

    public function createEvent($requestMethod, $db)
    {
        if ($requestMethod === 'POST') {
            // echo "hi";
            $event = new PrivateEvent($db);
            $data = json_decode(file_get_contents('php://input'), true);

            // print_r($data);exit();

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
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
            exit;
        }
    }

    public function updateEvent($requestMethod, $db)
    {
        if ($requestMethod === 'PUT') {
            $event = new PrivateEvent($db);
            $data = json_decode(file_get_contents('php://input'), true);
            // Get the event ID and new values from the request body
            $eventId = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
            $name = htmlspecialchars($data['name']);
            $description = htmlspecialchars($data['description']);
            $date = htmlspecialchars($data['date']);
            $time = htmlspecialchars($data['time']);
            $location = htmlspecialchars($data['location']);
            $maxCapacity = filter_var($data['max_capacity'], FILTER_SANITIZE_NUMBER_INT);

            // Validate the event ID
            if (!$event->checkEventExists($eventId)) {
                http_response_code(404); // Not Found
                echo json_encode(["success" => false, "message" => "Event not found"]);
                exit;
            }

            // Check if the user is the owner of the event
            if (!$event->checkEventOwner($eventId)) {
                http_response_code(403); // Forbidden
                echo json_encode(["success" => false, "message" => "You are not authorized to update this event"]);
                exit;
            }

            // Validate the event ID and new values
            if (empty($eventId) || empty($name) || empty($description) || empty($date) || empty($time) || empty($location) || empty($maxCapacity)) {
                http_response_code(400); // Bad Request
                echo json_encode(["success" => false, "message" => "All fields are required"]);
                exit;
            }

            // Update the event
            if ($event->update($eventId, $name, $description, $date, $time, $location, $maxCapacity)) {
                echo json_encode(["success" => true, "message" => "Event updated successfully"]);
                exit;
            } else {
                echo json_encode(["success" => false, "message" => "Event update failed"]);
                exit;
            }
        } else {
                http_response_code(405); // Method Not Allowed
                echo json_encode(["message" => "Method not allowed"]);
                exit;
            }
        
    }

    public function deleteEvent($requestMethod, $db)
    {
        if ($requestMethod === 'DELETE') {
            // Read and decode the JSON input
            $data = json_decode(file_get_contents('php://input'), true);

            $eventId = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);

            // Check if 'id' is present in the JSON data
            if (empty($eventId)) {
                http_response_code(400); // Bad Request
                echo json_encode(["success" => false, "message" => "Event ID is required"]);
                exit;
            }

            $event = new PrivateEvent($db);
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
    }

    public function attendees($requestMethod, $db)
    {
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
    }

    public function totalAttendees($requestMethod, $db)
    {
        if ($requestMethod === 'GET') {
            $attendee = new Attendee($db);
            $totalAttendees = $attendee->totalAttendees();
            echo json_encode($totalAttendees);
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
    }

    public function createAttendees($requestMethod, $db)
    {
        if ($requestMethod === 'POST') {
            $attendees = new PublicAttendee($db);

            $data = json_decode(file_get_contents('php://input'), true);
            $eventId = htmlspecialchars($data['eventId']);
            // echo $eventId;exit();
            $name = htmlspecialchars($data['name']);
            $email = htmlspecialchars($data['email']);

            // check if the event exists
            if (!$attendees->checkEventExists($eventId)) {
                echo json_encode(["success" => false, "message" => "Event does not exists"]);
                exit;
            }

            // check if the attendee exists
            if ($attendees->checkAttendeeExists($eventId, $email)) {
                echo json_encode(["success" => false, "message" => "Attendee already exists"]);
                exit;
            }

            // check if the event is full
            if ($attendees->checkEventFull($eventId)) {
                echo json_encode(["success" => false, "message" => "Event is full"]);
                exit;
            }

            // print_r($data);exit();

            if ($attendees->createAttendee($eventId, $name, $email)) {
                echo json_encode(["success" => true, "message" => "You have successfully registered for the event. The Host will send you confirmation email soon."]);
            } else {
                echo json_encode(["success" => false, "message" => "Attendee creation failed"]);
            }
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
    }

    // ============================================================== End Of User APIs ==============================================================

    // ============================================================== Start Of Admin APIs ==============================================================

    public function AdminLogin($requestMethod, $db, $baseUrl)
    {
        if ($requestMethod === 'POST') {
            $admin = new Auth($db);
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
            if ($admin->Adminlogin($email, $password)) {
                $baseUrl = "http://localhost/ems";
                http_response_code(302);
                echo json_encode(["success" => true, "message" => "Admin logged in successfully", "location" => "$baseUrl/public/views/admin/dashboard.php"]);
                exit;
            } else {
                echo json_encode(["success" => false, "message" => "Invalid Email or Password"]);
                exit;
            }
            
        }else{
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
            exit;
        }
    }

    public function AdminEvents($requestMethod, $db)
    {
        $event = new Admin($db);
        if ($requestMethod === 'GET') {
            echo json_encode($event->getAllEvents());
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
    }

    public function AdminEventDelete($requestMethod, $db)
    {
        if ($requestMethod === 'DELETE') {
            $data = json_decode(file_get_contents('php://input'), true);
            $eventId = htmlspecialchars($data['id']);
            // check if the eventId is empty
            if (empty($eventId)) {
                echo json_encode(["success" => false, "message" => "Event ID is required"]);
                exit;
            }
            $event = new Admin($db);
            if ($event->deleteEvent($eventId)) {
                echo json_encode(["success" => true, "message" => "Event deleted successfully"]);
                exit;
            } else {
                echo json_encode(["success" => false, "message" => "Failed to delete event"]);
                exit;
            }
        }else{
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
            exit;
        }
    }

    public function AdminAttendees($requestMethod, $db)
    {
        // echo $requestMethod;exit;
        if ($requestMethod === 'GET') {
            $attendee = new Admin($db);
            echo json_encode($attendee->getAllAttendees());
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
    }

    public function AdminAttendeeDelete($requestMethod, $db)
    {
        if ($requestMethod === 'DELETE') {
            $data = json_decode(file_get_contents('php://input'), true);
            $attendeeId = htmlspecialchars($data['id']);
            // check if the eventId is empty
            if (empty($attendeeId)) {
                echo json_encode(["success" => false, "message" => "Attendee ID is required"]);
                exit;
            }
            $attendee = new Admin($db);
            if ($attendee->deleteAttendee($attendeeId)) {
                echo json_encode(["success" => true, "message" => "Attendee deleted successfully"]);
                exit;
            } else {
                echo json_encode(["success" => false, "message" => "Failed to delete attendee"]);
                exit;
            }
        }else{
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
            exit;
        }
    }

    public function AdminUsers($requestMethod, $db)
    {
        if ($requestMethod === 'GET') {
            $user = new Admin($db);
            echo json_encode($user->getAllUsers());
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
    }

    public function AdminUserDelete($requestMethod, $db)
    {
        if ($requestMethod === 'DELETE') {
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = htmlspecialchars($data['id']);
            // check if the eventId is empty
            if (empty($userId)) {
                echo json_encode(["success" => false, "message" => "User ID is required"]);
                exit;
            }
            $user = new Admin($db);
            if ($user->deleteUser($userId)) {
                echo json_encode(["success" => true, "message" => "User deleted successfully"]);
                exit;
            } else {
                echo json_encode(["success" => false, "message" => "Failed to delete user"]);
                exit;
            }
        }else{
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
            exit;
        }
    }

    // ============================================================== End Of Admin APIs ==============================================================

    public function logout($requestMethod, $db)
    {
        if ($requestMethod === 'POST') {
            $user = new Auth($db);
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
    }
}
