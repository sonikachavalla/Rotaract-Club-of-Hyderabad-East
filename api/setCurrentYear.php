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

// Remove current flag from all years
$conn->query("UPDATE event_years SET is_current = 0");

// Set selected year as current
$stmt = $conn->prepare("
UPDATE event_years
SET is_current = 1
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