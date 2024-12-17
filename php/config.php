<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'edem.anagbah');
define('DB_PASSWORD', 'Ed21emk@2023');
define('DB_NAME', 'webtech_fall2024_edem_anagbah');

// Create database connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
