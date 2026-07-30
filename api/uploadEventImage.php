<?php

header("Content-Type: application/json");

$targetDir = "../uploads/events/";

if (!file_exists($targetDir)) {
    mkdir($targetDir, 0755, true);
}

if (!isset($_FILES["images"])) {
    echo json_encode([
        "success" => false,
        "message" => "No images received."
    ]);
    exit;
}

$allowed = [
    "image/jpeg",
    "image/png",
    "image/webp",
    "image/jpg"
];

$uploadedImages = [];

foreach ($_FILES["images"]["tmp_name"] as $index => $tmpName) {

    if ($_FILES["images"]["error"][$index] !== UPLOAD_ERR_OK) {
        continue;
    }

    $type = $_FILES["images"]["type"][$index];

    if (!in_array($type, $allowed)) {
        continue;
    }

    $extension = strtolower(
        pathinfo($_FILES["images"]["name"][$index], PATHINFO_EXTENSION)
    );

    $filename = uniqid("event_") . "." . $extension;

    $destination = $targetDir . $filename;

    if (move_uploaded_file($tmpName, $destination)) {

        $uploadedImages[] = [
            "path" => "uploads/events/" . $filename,
            "original_name" => $_FILES["images"]["name"][$index]
        ];

    }

}

if (empty($uploadedImages)) {

    echo json_encode([
        "success" => false,
        "message" => "No valid images were uploaded."
    ]);
    exit;

}

echo json_encode([
    "success" => true,
    "images" => $uploadedImages
]);

?>