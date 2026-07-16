<?php

$host = "localhost";
$dbname = "rotaract_db";
$username = "rotaract_website";
$password = "Rache@2025-2027";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character encoding
$conn->set_charset("utf8mb4");

?>