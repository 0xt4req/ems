<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class PublicAttendee extends PublicEvents
{
    private $conn;
    public function __construct($db)
    {
        $this->conn = $db->getConnection();
        Parent::__construct($db);
    }

    // insert attendee
    public function createAttendee($eventId, $name, $email)
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

    // check if attendee exists
    public function checkAttendeeExists($eventId, $email)
    {
        try {
            $stmt = $this->conn->prepare("SELECT id FROM attendees WHERE event_id = ? AND email = ?");
            $stmt->bind_param("ss", $eventId, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    // check if the event is full
    public function checkEventFull($eventId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT id FROM events WHERE id = ? AND max_capacity = (SELECT COUNT(*) FROM attendees WHERE event_id = ?)");
            $stmt->bind_param("ii", $eventId, $eventId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
