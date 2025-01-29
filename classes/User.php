<?php
class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db->getConnection();
    }

    // Register a new user
    public function register($username, $name, $email, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $uuid = bin2hex(random_bytes(8));
        $stmt = $this->conn->prepare("INSERT INTO users (uuid, username, name, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $uuid, $username, $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Check if a username already exists
    public function checkUsernameExists($username)
    {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    // Check if an email already exists
    public function checkEmailExists($email)
    {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    // destructor to close the database connection
    public function __destruct()
    {
        $this->conn->close();
    }
}
