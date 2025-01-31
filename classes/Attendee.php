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

    // insert attendee
    public function create($eventId, $name, $email)
    {
        try {
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
        } catch (Exception $e) {
            return false;
        }
    }


    public function getAllAttendees()
    {
        try {
            // echo $_SESSION['user_id'];exit;
            $userId = $_SESSION['user_id'];
            $stmt = $this->conn->prepare("
            SELECT 
            attendees.id AS attendee_id, 
            attendees.name AS attendee_name, 
            attendees.email, 
            attendees.event_id, 
            events.name AS event_name
            FROM attendees
            JOIN events ON attendees.event_id = events.id
            WHERE events.user_id = ?
        ");

            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    // get attendees for an event
    public function getAttendees($eventId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM attendees WHERE event_id = ?");
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("s", $eventId);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    // delete an attendee
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

    // Get event name from event id
    public function getEventName($eventId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT name FROM events WHERE id = ?");
            $stmt->bind_param("s", $eventId);
            $stmt->execute();
            $result = $stmt->get_result();
            $event = $result->fetch_assoc();
            return $event['name'];
        } catch (Exception $e) {
            return false;
        }
    }

    // total attendees
    public function totalAttendees()
    {
        try {
            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user') {
                $userId = $_SESSION['user_id'];
                $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM attendees WHERE event_id IN (SELECT id FROM events WHERE user_id = ?)");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                return $row['total'];
            }
            if ($_SESSION['role'] === 'admin') {
                // return $_SESSION['role'];exit;
                $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM attendees");
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                return $row['total'];
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
