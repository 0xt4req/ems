<?php
session_start();
class Event {
    private $conn;

    public function __construct($db) {
        if (!isset($_SESSION['username'])) {
            http_response_code(403); 
            echo json_encode(["success" => false,"message" => "Unauthorized"]);
            exit;
        }
        $this->conn = $db->getConnection();

    }

    // Create a new event
    public function create($userId, $name, $description, $date, $time, $location, $maxCapacity) {
        $stmt = $this->conn->prepare("INSERT INTO events (uuid, user_id, name, description, date, time, location, max_capacity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $uuid = bin2hex(random_bytes(8));
        $stmt->bind_param("sisssssi", $uuid, $userId, $name, $description, $date, $time, $location, $maxCapacity);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get all events
    public function getAll() {
        $result = $this->conn->query("SELECT * FROM events");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Delete an event
    public function delete($eventId) {
        $userId = $_SESSION['user_id'];
        $stmt = $this->conn->prepare("DELETE FROM events WHERE uuid = ? AND user_id = ?");
        $stmt->bind_param("ss", $eventId, $userId);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // check if event exists
    public function checkEventExists($eventId) {
        $stmt = $this->conn->prepare("SELECT id FROM events WHERE id = ?");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }
}
?>