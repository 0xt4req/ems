<?php 

class PublicEvents {
    private $conn;
    public function __construct($db) {
        $this->conn = $db->getConnection();
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM events");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}


?>