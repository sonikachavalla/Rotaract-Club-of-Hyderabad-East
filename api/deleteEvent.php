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

$id = intval($data["id"]);

$stmt = $conn->prepare("DELETE FROM events WHERE id=?");
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