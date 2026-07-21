<?php

require "db.php";

header("Content-Type: application/json");

$result = $conn->query("
SELECT *
FROM team
ORDER BY display_order ASC, id ASC
");

$members = [];

while ($row = $result->fetch_assoc()) {

    $members[] = [
        "id" => (int)$row["id"],
        "name" => $row["name"],
        "role" => $row["role"],
        "member_type" => $row["member_type"],
        "linkedin" => $row["linkedin"],
        "email" => $row["email"],
        "photo" => $row["photo"],
        "display_order" => (int)$row["display_order"]
    ];

}

echo json_encode([
    "success" => true,
    "members" => $members
]);

$conn->close();

?>