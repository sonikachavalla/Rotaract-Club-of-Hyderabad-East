<?php

require "db.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request"
    ]);
    exit;
}

$id          = intval($data["id"]);
$title       = $data["title"] ?? "";
$description = $data["description"] ?? "";
$category    = $data["category"] ?? "";
$date        = $data["event_date"] ?? "";
$time        = $data["event_time"] ?? "";
$location    = $data["location"] ?? "";
$status      = $data["status"] ?? "Upcoming";
$poster      = $data["poster"] ?? "";
$ri_year     = $data["ri_year"] ?? "";

$sql = "UPDATE events SET
title=?,
description=?,
category=?,
event_date=?,
event_time=?,
location=?,
status=?,
poster=?,
ri_year=?
WHERE id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssssssssi",
    $title,
    $description,
    $category,
    $date,
    $time,
    $location,
    $status,
    $poster,
    $ri_year,
    $id
);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true
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