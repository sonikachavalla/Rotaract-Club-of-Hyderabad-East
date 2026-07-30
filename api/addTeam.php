<?php

require "db.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "No data received"
    ]);
    exit;
}

$name          = trim($data["name"] ?? "");
$role          = trim($data["role"] ?? "");
$member_type   = $data["member_type"] ?? "member";
$linkedin      = trim($data["linkedin"] ?? "");
$email         = trim($data["email"] ?? "");
$photo         = $data["photo"] ?? "";
$display_order = intval($data["display_order"] ?? 0);

if ($name === "" || $role === "") {
    echo json_encode([
        "success" => false,
        "message" => "Name and Role are required."
    ]);
    exit;
}

/* Check if this member already exists */
$check = $conn->prepare("SELECT id FROM team WHERE name = ? AND role = ? LIMIT 1");
$check->bind_param("ss", $name, $role);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $existing = $result->fetch_assoc();

    echo json_encode([
        "success" => true,
        "id" => $existing["id"],
        "message" => "Already exists"
    ]);

    $check->close();
    $conn->close();
    exit;
}

$check->close();

/* Insert new member */
$sql = "INSERT INTO team
(name, role, member_type, linkedin, email, photo, display_order)
VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssssssi",
    $name,
    $role,
    $member_type,
    $linkedin,
    $email,
    $photo,
    $display_order
);

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