<?php

require "db.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data["id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request"
    ]);
    exit;
}

$id          = (int)$data["id"];
$title       = trim($data["title"] ?? "");
$description = trim($data["description"] ?? "");
$category    = trim($data["category"] ?? "");
$date        = $data["event_date"] ?? "";
$time        = !empty($data["event_time"]) ? $data["event_time"] : null;
$location    = trim($data["location"] ?? "");
$status      = ($data["status"] ?? "upcoming") === "past"
    ? "Completed"
    : "Upcoming";

$poster      = $data["poster"] ?? "";

$ri_year     = trim($data["ri_year"] ?? "");

$year_name   = trim($data["year_name"] ?? "");

$sql = "UPDATE events SET
title=?,
description=?,
category=?,
event_date=?,
event_time=?,
location=?,
status=?,
poster=?,
ri_year=?,
year_name=?
WHERE id=?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);
    exit;
}

$stmt->bind_param(
    "ssssssssssi",
    $title,
    $description,
    $category,
    $date,
    $time,
    $location,
    $status,
    $poster,
    $ri_year,
    $year_name,
    $id
);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Event updated successfully."
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