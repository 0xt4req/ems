<?php
class Attendee
{
    private $conn;

    public function __construct($db)
    {
        // echo $_SESSION['username'];
        if (!isset($_SESSION['username'])) {
            http_response_code(302);
            header('Location: http://localhost/ems/public/views/login.php');
            // echo json_encode(["success" => false, "message" => "Unauthorized"]);
            exit;
        }
        $this->conn = $db->getConnection();
    }

    public function create($eventId, $name, $email)
    {

        // insert attendee
        $stmt = $this->conn->prepare("INSERT INTO attendees (uuid, event_id, name, email) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            return false;
        }
        $uuid = bin2hex(random_bytes(8));
        $stmt->bind_param("siss", $uuid, $eventId, $name, $email);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    public function getAllAttendees()
    {
        // echo $_SESSION['user_id'];exit;
        $userId = $_SESSION['user_id'];
        $stmt = $this->conn->prepare("SELECT * FROM attendees WHERE event_id IN (SELECT id FROM events WHERE user_id = ?)");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // get attendees for an event
    public function getAttendees($eventId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM attendees WHERE event_id = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("s", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get event name from event id
    public function getEventName($eventId)
    {
        $stmt = $this->conn->prepare("SELECT name FROM events WHERE id = ?");
        $stmt->bind_param("s", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        $event = $result->fetch_assoc();
        return $event['name'];
    }

    // total attendees
    public function totalAttendees()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM attendees");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // check if the logged in user is the owner of the event
    public function checkEventOwner($eventId)
    {
        $userId = $_SESSION['user_id'];
        $stmt = $this->conn->prepare("SELECT id FROM events WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $eventId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }
}
