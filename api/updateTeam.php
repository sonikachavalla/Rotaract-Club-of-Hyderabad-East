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

$id            = intval($data["id"]);
$name          = trim($data["name"] ?? "");
$role          = trim($data["role"] ?? "");
$member_type   = $data["member_type"] ?? "member";
$linkedin      = trim($data["linkedin"] ?? "");
$email         = trim($data["email"] ?? "");
$photo         = $data["photo"] ?? "";
$display_order = intval($data["display_order"] ?? 0);

$sql = "
UPDATE team
SET
    name=?,
    role=?,
    member_type=?,
    linkedin=?,
    email=?,
    photo=?,
    display_order=?
WHERE id=?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssssssii",
    $name,
    $role,
    $member_type,
    $linkedin,
    $email,
    $photo,
    $display_order,
    $id
);

if($stmt->execute()){
    echo json_encode([
        "success"=>true
    ]);
}else{
    echo json_encode([
        "success"=>false,
        "message"=>$stmt->error
    ]);
}

$stmt->close();
$conn->close();