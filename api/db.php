<?php

$host = "localhost";
$dbname = "rotaract_db";
$username = "rotaract_website";
$password = "Rache@2025-2027";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    header("Content-Type: application/json");
    echo json_encode([
        "success" => false,
        "message" => $conn->connect_error
    ]);
    exit;
}

// Set character encoding
$conn->set_charset("utf8mb4");

?>