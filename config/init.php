<?php
// Database configuration
$host = 'localhost';
$dbname = 'event_management';
$username = 'root';
$password = '';

// Create a mysqli connection (without selecting a database)
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Drop the database if it exists
$dropDatabaseQuery = "DROP DATABASE IF EXISTS `$dbname`";
if ($conn->query($dropDatabaseQuery) === TRUE) {
    echo "Database dropped successfully!\n";
} else {
    die("Error dropping database: " . $conn->error);
}

// Create the database
$createDatabaseQuery = "CREATE DATABASE `$dbname`";
if ($conn->query($createDatabaseQuery) === TRUE) {
    echo "Database created successfully!\n";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// SQL statements to create tables
$sql = "
    CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uuid VARCHAR(36) NOT NULL,
        username VARCHAR(100) NOT NULL UNIQUE,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(20) DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uuid VARCHAR(36) NOT NULL,
        user_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        date DATE NOT NULL,
        time TIME NOT NULL,
        location VARCHAR(100) NOT NULL,
        max_capacity INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    CREATE TABLE attendees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uuid VARCHAR(36) NOT NULL,
        event_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    );

    CREATE TABLE admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uuid VARCHAR(36) NOT NULL,
        username VARCHAR(100) NOT NULL UNIQUE,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(20) DEFAULT 'admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
";

// Execute the SQL statements
if ($conn->multi_query($sql)) {
    do {
        // Loop through the results to ensure all queries execute
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    echo "Tables created successfully!\n";
} else {
    die("Error creating tables: " . $conn->error);
}

// Create the admin user (after tables are created)
$uuid = bin2hex(random_bytes(16)); // Generate a 16-byte (32-char) UUID
$adminUsername = 'admin';
$adminName = 'Admin';
$adminEmail = 'admin@example.com';
$adminPassword = password_hash('password', PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO admins (uuid, username, name, email, password) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $uuid, $adminUsername, $adminName, $adminEmail, $adminPassword);

if ($stmt->execute()) {
    echo "Admin user created successfully!\n";
} else {
    echo "Error creating admin user: " . $stmt->error;
}

// Close the connection
$conn->close();
