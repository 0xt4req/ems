<?php 
session_start();
class Attendee {
    private $conn;

    public function __construct($db) {
        echo $_SESSION['username'];
        if (!isset($_SESSION['username'])) {
            http_response_code(403); 
            echo json_encode(["success" => false,"message" => "Unauthorized"]);
            exit;
        }
        $this->conn = $db->getConnection();
    }

    public function create($eventId, $name, $email) {

        // insert attendee
        $stmt = $this->conn->prepare("INSERT INTO attendees (uuid, event_id, name, email) VALUES (?, ?, ?, ?)");
        if(!$stmt) {
            return false;
        }
        $uuid = bin2hex(random_bytes(8));
        $stmt->bind_param("siss",$uuid, $eventId, $name, $email);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getAllAttendees(){
        $stmt = $this->conn->prepare("SELECT * FROM attendees");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // get attendees for an event
    public function getAttendees($eventId){
        $stmt = $this->conn->prepare("SELECT * FROM attendees WHERE event_id = ?");
        if(!$stmt) {
            return false;
        }
        $stmt->bind_param("s", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // check if attendee exists
    public function checkAttendeeExists($eventId, $email) {
        $stmt = $this->conn->prepare("SELECT id FROM attendees WHERE event_id = ? AND email = ?");
        $stmt->bind_param("ss", $eventId, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }

}


?>