<?php

class Auth
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db->getConnection();
    }

    // Login a user
    public function login($email, $password)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = $user['role'];

                    session_regenerate_id(true);

                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    // Login an admin

    public function Adminlogin($email, $password)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM admins WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = $user['role'];
                    session_regenerate_id(true);
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    // Logout
    public function logout()
    {
        try {
            session_destroy();
            unset($_SESSION['user_id']);
            unset($_SESSION['username']);
            unset($_SESSION['user_role']);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
