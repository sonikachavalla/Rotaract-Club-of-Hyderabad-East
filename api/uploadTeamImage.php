<?php

header("Content-Type: application/json");

$uploadDir = "../uploads/team/";

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!isset($_FILES["image"])) {
    echo json_encode([
        "success" => false,
        "message" => "No image uploaded"
    ]);
    exit;
}

$file = $_FILES["image"];

$extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

$allowed = ["jpg", "jpeg", "png", "webp"];

if (!in_array($extension, $allowed)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid image type"
    ]);
    exit;
}

$filename = uniqid("member_") . "." . $extension;
$target = $uploadDir . $filename;

if (!move_uploaded_file($file["tmp_name"], $target)) {
    echo json_encode([
        "success" => false,
        "message" => "Upload failed"
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "url" => "uploads/team/" . $filename
]);

?>