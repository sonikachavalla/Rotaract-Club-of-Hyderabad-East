<?php

header("Content-Type: application/json");

$targetDir = "../uploads/events/";

if (!file_exists($targetDir)) {
    mkdir($targetDir, 0755, true);
}

if (!isset($_FILES["image"])) {
    echo json_encode([
        "success" => false,
        "message" => "No image received."
    ]);
    exit;
}

$file = $_FILES["image"];

$allowed = [
    "image/jpeg",
    "image/png",
    "image/webp"
];

if (!in_array($file["type"], $allowed)) {
    echo json_encode([
        "success" => false,
        "message" => "Only JPG, PNG and WEBP allowed."
    ]);
    exit;
}

$extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

$filename = uniqid("event_") . "." . $extension;

$destination = $targetDir . $filename;

if (!move_uploaded_file($file["tmp_name"], $destination)) {
    echo json_encode([
        "success" => false,
        "message" => "Upload failed."
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "url" => "uploads/events/" . $filename
]);

?>