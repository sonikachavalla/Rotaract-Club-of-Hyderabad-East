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

$id = (int)$data["id"];

// Delete image if it exists
$result = $conn->query("SELECT poster FROM events WHERE id=$id");

if ($result && $row = $result->fetch_assoc()) {
    if (!empty($row["poster"])) {
        $file = "../" . $row["poster"];
        if (file_exists($file)) {
            unlink($file);
        }
    }
}

$stmt = $conn->prepare("DELETE FROM events WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode([
        "success" => false,
        "message" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();

?>