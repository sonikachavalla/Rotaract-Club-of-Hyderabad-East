<?php

require "db.php";

header("Content-Type: application/json");

$result = $conn->query("
SELECT *
FROM event_years
ORDER BY year_name DESC
");

$years = [];

while ($row = $result->fetch_assoc()) {

    $years[] = [
        "id" => (int)$row["id"],
        "year_name" => $row["year_name"],
        "is_current" => (int)$row["is_current"]
    ];

}

echo json_encode([
    "success" => true,
    "years" => $years
]);

$conn->close();

?>