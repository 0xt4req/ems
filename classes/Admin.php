<?php

class Admin
{

    private $conn;

    public function __construct($db)
    {
        if (!isset($_SESSION['username']) && !isset($_SESSION['user_role'])) {
            http_response_code(302);
            header('Location: http://localhost/ems/public/views/admin');
            exit;
        }
        if ($_SESSION['user_role'] !== 'admin') {
            http_response_code(302);
            header('Location: http://localhost/ems/public/views/admin');
            exit;
        }
        $this->conn = $db->getConnection();
    }

    // get all admins
    public function admins()
    {
        try {
            $stmt = $this->conn->prepare("SELECT id, username, name, email, role FROM admins");
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    // check if username already exists
    public function checkAdminUsername($username)
    {
        try {
            $stmt = $this->conn->prepare("SELECT id FROM admins WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // check if email already exists
    public function checkAdminEmail($email)
    {
        try {
            $stmt = $this->conn->prepare("SELECT id FROM admins WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // add admin
    public function createAdmin($username, $name, $email, $password)
    {
        try {
            $password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare("INSERT INTO admins (uuid, username, name, email, password) VALUES (?, ?, ?, ?, ?)");
            $uuid = bin2hex(random_bytes(8));
            $stmt->bind_param("sssss", $uuid, $username, $name, $email, $password);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // delete admin
    public function deleteAdmin($adminId)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM admins WHERE id = ?");
            $stmt->bind_param("s", $adminId);
            $stmt->execute();
            if ($stmt->error) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // get all users
    public function getAllUsers()
    {
        try {
            $stmt = $this->conn->prepare("SELECT id, username, name, email, role FROM users");
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    // delete user
    public function deleteUser($userId)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            if ($stmt->error) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // get all events
    public function getAllEvents()
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM events");
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    // delete event
    public function deleteEvent($eventId)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM events WHERE id = ?");
            $stmt->bind_param("s", $eventId);
            $stmt->execute();
            if ($stmt->error) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // get all attendees
    public function getAllAttendees()
    {
        try {
            $stmt = $this->conn->prepare("SELECT attendees.id AS id, attendees.name AS name, attendees.email AS email, events.id AS event_id, events.name AS event_name FROM attendees JOIN events ON attendees.event_id = events.id");
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    // delete attendee
    public function deleteAttendee($attendeeId)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM attendees WHERE id = ?");
            $stmt->bind_param("s", $attendeeId);
            $stmt->execute();
            if ($stmt->error) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
