<?php
$host = 'localhost';
$username = 'root';  // Default for local servers
$password = '';      // Default is empty
$database = 'Lab_5b';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session for authentication
session_start();
?>