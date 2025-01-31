<?php

class BaseEvent
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db->getConnection();
    }

    // Create a new event
    public function create($name, $description, $date, $time, $location, $maxCapacity)
    {
        try {
            $userId = $_SESSION['user_id'];
            $stmt = $this->conn->prepare("INSERT INTO events (uuid, user_id, name, description, date, time, location, max_capacity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $uuid = bin2hex(random_bytes(8));
            $stmt->bind_param("sisssssi", $uuid, $userId, $name, $description, $date, $time, $location, $maxCapacity);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    // Get all events
    public function getAll()
    {
        try {
            $stmt = $this->conn->prepare("
            SELECT 
            events.id AS id, 
            events.name AS name, 
            events.description, 
            events.date, 
            events.time, 
            events.location, 
            events.max_capacity,
            users.username,
            COUNT(attendees.id) AS total_attendees
            FROM events 
            JOIN users ON events.user_id = users.id
            LEFT JOIN attendees ON events.id = attendees.event_id
            GROUP BY events.id
        ");
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    // Get event for logged in user
    public function getAllEventForUser()
    {
        try {
            if (isset($_SESSION['username'])) {
                $userId = $_SESSION['user_id'];

                $stmt = $this->conn->prepare("
                    SELECT 
                    events.id AS id, 
                    events.name AS name, 
                    events.description, 
                    events.date, 
                    events.time, 
                    events.location, 
                    events.max_capacity,
                    users.username,
                    COUNT(attendees.id) AS total_attendees
                    FROM events 
                    JOIN users ON events.user_id = users.id
                    LEFT JOIN attendees ON events.id = attendees.event_id
                    WHERE events.user_id = ?
                    GROUP BY events.id
                ");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // update an event
    public function update($eventId, $name, $description, $date, $time, $location, $maxCapacity)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE events SET name = ?, description = ?, date = ?, time = ?, location = ?, max_capacity = ? WHERE id = ?");
            $stmt->bind_param("ssssssi", $name, $description, $date, $time, $location, $maxCapacity, $eventId);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    // Delete an event
    public function delete($eventId)
    {
        try {
            $userId = $_SESSION['user_id'];
            $stmt = $this->conn->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ss", $eventId, $userId);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    // check if event exists
    public function checkEventExists($eventId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT id FROM events WHERE id = ?");
            $stmt->bind_param("i", $eventId);
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

    // check if the user is the owner of the event
    public function checkEventOwner($eventId)
    {
        try {
            $userId = $_SESSION['user_id'];
            $stmt = $this->conn->prepare("SELECT id FROM events WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $eventId, $userId);
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

    // total events
    public function totalEvents()
    {
        try {
            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user') {
                $userId = $_SESSION['user_id'];
                $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM events WHERE user_id = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                return $row['total'];
            }
            if ($_SESSION['role'] === 'admin') {
                $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM events");
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
