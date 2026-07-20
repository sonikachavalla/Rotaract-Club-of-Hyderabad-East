<?php

require "db.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "No data received"
    ]);
    exit;
}

$title       = trim($data["title"] ?? "");
$description = trim($data["description"] ?? "");
$category    = trim($data["category"] ?? "");
$date        = $data["event_date"] ?? "";
$time        = !empty($data["event_time"]) ? $data["event_time"] : null;
$location    = trim($data["location"] ?? "");
$status      = ($data["status"] ?? "upcoming") === "past" ? "Completed" : "Upcoming";
$poster      = $data["poster"] ?? null;
$ri_year     = $data["ri_year"] ?? null;

$sql = "INSERT INTO events
(title, description, category, event_date, event_time, location, status, poster, ri_year)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssssssss",
    $title,
    $description,
    $category,
    $date,
    $time,
    $location,
    $status,
    $poster,
    $ri_year
);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "id" => $conn->insert_id
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();

?>