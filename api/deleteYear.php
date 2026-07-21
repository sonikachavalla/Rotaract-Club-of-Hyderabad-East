<?php

require "db.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data["id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Year ID is required."
    ]);
    exit;
}

$id = (int)$data["id"];

// Don't allow deleting the current year
$checkCurrent = $conn->prepare("
SELECT is_current
FROM event_years
WHERE id = ?
");

$checkCurrent->bind_param("i", $id);
$checkCurrent->execute();
$result = $checkCurrent->get_result()->fetch_assoc();

if (!$result) {

    echo json_encode([
        "success" => false,
        "message" => "Year not found."
    ]);
    exit;
}

if ($result["is_current"] == 1) {

    echo json_encode([
        "success" => false,
        "message" => "Current Rotary year cannot be deleted."
    ]);
    exit;
}

// Check if events exist
$checkEvents = $conn->prepare("
SELECT COUNT(*) AS total
FROM events
WHERE year_id = ?
");

$checkEvents->bind_param("i", $id);
$checkEvents->execute();
$count = $checkEvents->get_result()->fetch_assoc();

if ($count["total"] > 0) {

    echo json_encode([
        "success" => false,
        "message" => "Delete all events in this Rotary year before deleting it."
    ]);
    exit;
}

// Delete year
$stmt = $conn->prepare("
DELETE FROM event_years
WHERE id = ?
");

$stmt->bind_param("i", $id);

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