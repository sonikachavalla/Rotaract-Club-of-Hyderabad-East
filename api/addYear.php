<?php

require "db.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data["year_name"])) {

    echo json_encode([
        "success" => false,
        "message" => "Year name is required."
    ]);
    exit;
}

$year_name = trim($data["year_name"]);

$check = $conn->prepare("SELECT id FROM event_years WHERE year_name=?");
$check->bind_param("s", $year_name);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {

    echo json_encode([
        "success" => false,
        "message" => "This Rotary year already exists."
    ]);

    exit;
}

$check->close();

$stmt = $conn->prepare("
INSERT INTO event_years (year_name, is_current)
VALUES (?, 0)
");

$stmt->bind_param("s", $year_name);

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