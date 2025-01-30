<?php

class EventApi
{
    // ============================================================== Start Of User APIs ==============================================================

    public function register($requestMethod, $db)
    {
        if ($requestMethod === 'POST') {
            $user = new User($db);
            $data = json_decode(file_get_contents('php://input'), true);

            // Validate the username, name, email, and password
            if (empty($data['username']) || empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                echo json_encode(["success" => false, "message" => "All fields are required"]);
                exit;
            }

            $username = htmlspecialchars($data['username']);
            $name = htmlspecialchars($data['name']);
            $email = htmlspecialchars($data['email']);
            $password = htmlspecialchars($data['password']);

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

            // Validate the email and password
            if (empty($data['email']) || empty($data['password'])) {
                echo json_encode(["success" => false, "message" => "All fields are required"]);
                exit;
            }

            $email = htmlspecialchars($data['email']);
            $password = htmlspecialchars($data['password']);

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
            echo json_encode($event->getAllEventForUser());
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

            if (empty($data['name']) || empty($data['description']) || empty($data['date']) || empty($data['time']) || empty($data['location']) || empty($data['max_capacity'])) {
                http_response_code(400); // Bad Request
                echo json_encode(["success" => false, "message" => "All fields are required"]);
                exit;
            }

            $name = htmlspecialchars($data['name']);
            $description = htmlspecialchars($data['description']);
            $date = htmlspecialchars($data['date']);
            $time = htmlspecialchars($data['time']);
            $location = htmlspecialchars($data['location']);
            $maxCapacity = htmlspecialchars($data['max_capacity']);

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

            // check if the required fields are present
            if (empty($data['id']) || empty($data['name']) || empty($data['description']) || empty($data['date']) || empty($data['time']) || empty($data['location']) || empty($data['max_capacity'])) {
                http_response_code(400); // Bad Request
                echo json_encode(["success" => false, "message" => "All fields are required"]);
                exit;
            }

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

            // Check if 'id' is present in the JSON data
            if (empty($data['id'])) {
                http_response_code(400); // Bad Request
                echo json_encode(["success" => false, "message" => "Event ID is required"]);
                exit;
            }

            $eventId = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);

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
            if ($attendees) {
                echo json_encode($attendees);
            } else {
                echo json_encode(["success" => false, "message" => "No attendees found"]);
            }
        }
    }

    public function deleteAttendee($requestMethod, $db)
    {
        if ($requestMethod === 'DELETE') {
            // Read and decode the JSON input
            $data = json_decode(file_get_contents('php://input'), true);

            // Check if 'id' is present in the JSON data
            if (empty($data['eventId']) || empty($data['attendeeId'])) {
                http_response_code(400); // Bad Request
                echo json_encode(["success" => false, "message" => "Event ID and Attendee ID are required"]);
                exit;
            }

            $eventId = filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT);
            $attendeeId = filter_var($data['attendeeId'], FILTER_SANITIZE_NUMBER_INT);

            $attendee = new Attendee($db);
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
            if ($attendee->deleteAttendee($attendeeId)) {
                http_response_code(200); // OK
                echo json_encode(["success" => true, "message" => "Attendee deleted successfully"]);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(["success" => false, "message" => "Attendee deletion failed"]);
            }
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
            exit;
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

            // check if the required fields are present
            if (empty($data['eventId']) || empty($data['name']) || empty($data['email'])) {
                echo json_encode(["success" => false, "message" => "Event ID, Name and Email are required"]);
                exit;
            }

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

            // Validate the email and password
            if (empty($data['email']) || empty($data['password'])) {
                echo json_encode(["success" => false, "message" => "All fields are required"]);
                exit;
            }

            $email = htmlspecialchars($data['email']);
            $password = htmlspecialchars($data['password']);

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
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
            exit;
        }
    }

    // Admins
    public function admins($requestMethod, $db)
    {
        if ($requestMethod === 'GET') {
            $admin = new Admin($db);
            $admins = $admin->admins();
            echo json_encode($admins);
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
            exit;
        }
    }

    // create new admin
    public function AdminCreate($requestMethod, $db)
    {
        if ($requestMethod === 'POST') {
            $admin = new Admin($db);
            $data = json_decode(file_get_contents('php://input'), true);

            // Validate the username, name, email, and password
            if (empty($data['username']) || empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                echo json_encode(["success" => false, "message" => "All fields are required"]);
                exit;
            }

            $username = htmlspecialchars($data['username']);
            $name = htmlspecialchars($data['name']);
            $email = htmlspecialchars($data['email']);
            $password = htmlspecialchars($data['password']);

            // check if username already exists
            if (!$admin->checkAdminUsername($username)) {
                echo json_encode(["success" => false, "message" => "Username already exists"]);
                exit;
            }

            // check if email already exists
            if (!$admin->checkAdminEmail($email)) {
                echo json_encode(["success" => false, "message" => "Email already exists"]);
                exit;
            }

            if ($admin->createAdmin($username, $name, $email, $password)) {
                echo json_encode(["success" => true, "message" => "Admin created successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Admin creation failed"]);
            }
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
    }

    // Delete Admin
    public function AdminDelete($requestMethod, $db)
    {
        if ($requestMethod === 'DELETE') {
            $data = json_decode(file_get_contents('php://input'), true);

            // check if the adminId is empty
            if (empty($data['id'])) {
                echo json_encode(["success" => false, "message" => "Admin ID is required"]);
                exit;
            }

            $adminId = htmlspecialchars($data['id']);

            $admin = new Admin($db);
            if ($admin->deleteAdmin($adminId)) {
                echo json_encode(["success" => true, "message" => "Admin deleted successfully"]);
                exit;
            } else {
                echo json_encode(["success" => false, "message" => "Failed to delete admin"]);
                exit;
            }
        } else {
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

            if (empty($data['id'])) {
                echo json_encode(["success" => false, "message" => "Event ID is required"]);
                exit;
            }

            $eventId = htmlspecialchars($data['id']);

            // check if event exists
            $event = new PublicEvents($db);
            if (!$event->checkEventExists($eventId)) {
                echo json_encode(["success" => false, "message" => "Event does not exist"]);
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
        } else {
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

            // check if the eventId is empty
            if (empty($data['id'])) {
                echo json_encode(["success" => false, "message" => "Attendee ID is required"]);
                exit;
            }

            $attendeeId = htmlspecialchars($data['id']);

            $attendee = new Admin($db);
            if ($attendee->deleteAttendee($attendeeId)) {
                echo json_encode(["success" => true, "message" => "Attendee deleted successfully"]);
                exit;
            } else {
                echo json_encode(["success" => false, "message" => "Failed to delete attendee"]);
                exit;
            }
        } else {
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

            // check if the eventId is empty
            if (empty($data['id'])) {
                echo json_encode(["success" => false, "message" => "User ID is required"]);
                exit;
            }

            $userId = htmlspecialchars($data['id']);

            $user = new Admin($db);
            if ($user->deleteUser($userId)) {
                echo json_encode(["success" => true, "message" => "User deleted successfully"]);
                exit;
            } else {
                echo json_encode(["success" => false, "message" => "Failed to delete user"]);
                exit;
            }
        } else {
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
