<?php 

class Admin {

    private $conn;

    public function __construct($db) {
        if(!isset($_SESSION['username']) && !isset($_SESSION['user_role'])) {
            http_response_code(302);
            header('Location: http://localhost/ems/public/views/admin');
            exit;
        }
        if($_SESSION['user_role'] !== 'admin') {
            http_response_code(302);
            header('Location: http://localhost/ems/public/views/admin');
            exit;
        }
        $this->conn = $db->getConnection();
    }

    // add admin
    public function insert($username, $name, $email, $password) {
        $stmt = $this->conn->prepare("INSERT INTO admins (uuid, username, name, email, password) VALUES (?, ?, ?, ?, ?)");
        $uuid = bin2hex(random_bytes(8));
        $stmt->bind_param("sssss", $uuid, $username, $name, $email, $password);
        $stmt->execute();
        return true;
    }

    // get all users
    public function getAllUsers() {
        $stmt = $this->conn->prepare("SELECT id, username, name, email, role FROM users");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // delete user
    public function deleteUser($userId) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        if ($stmt->error) {
            return false;
        }
        return true;
    }

    // get all events
    public function getAllEvents() {
        $stmt = $this->conn->prepare("SELECT * FROM events");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // delete event
    public function deleteEvent($eventId) {
        $stmt = $this->conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param("s", $eventId);
        $stmt->execute();
        if ($stmt->error) {
            return false;
        }
        return true;
    }

    // get all attendees
    public function getAllAttendees() {
        $stmt = $this->conn->prepare("SELECT attendees.id AS id, attendees.name AS name, attendees.email AS email, events.id AS event_id, events.name AS event_name FROM attendees JOIN events ON attendees.event_id = events.id");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // delete attendee
    public function deleteAttendee($attendeeId) {
        $stmt = $this->conn->prepare("DELETE FROM attendees WHERE id = ?");
        $stmt->bind_param("s", $attendeeId);
        $stmt->execute();
        if ($stmt->error) {
            return false;
        }
        return true;
    }
}


?>