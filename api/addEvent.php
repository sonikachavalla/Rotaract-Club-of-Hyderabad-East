<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require "db.php";

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
$poster      = $data["poster"] ?? "";
$ri_year     = trim($data["ri_year"] ?? "");
$year_name   = trim($data["year_name"] ?? "");
$event_link  = trim($data["event_link"] ?? "");
if (
    empty($title) ||
    empty($category) ||
    empty($date)
) {
    echo json_encode([
        "success" => false,
        "message" => "Please fill all required fields."
    ]);
    exit;
}

$sql = "INSERT INTO events
(
title,
description,
category,
event_date,
event_time,
location,
status,
poster,
event_link,
ri_year,
year_name
)
VALUES
(
?,
?,
?,
?,
?,
?,
?,
?,
?,
?,
?
)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);
    exit;
}

$stmt->bind_param(
    "sssssssssss",
    $title,
$description,
$category,
$date,
$time,
$location,
$status,
$poster,
$event_link,
$ri_year,
$year_name
);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Event added successfully.",
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