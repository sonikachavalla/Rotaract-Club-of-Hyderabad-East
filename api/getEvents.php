<?php

require "db.php";

$sql = "SELECT * FROM events ORDER BY event_date ASC";
$result = $conn->query($sql);

$events = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
    $events[] = [
        "id" => $row["id"],
        "title" => $row["title"],
        "desc" => $row["description"],
        "category" => $row["category"],
        "date" => $row["event_date"],
        "time" => $row["event_time"],
        "location" => $row["location"],
        "status" => strtolower($row["status"]),
        "poster" => $row["poster"]
    ];
}
}

header("Content-Type: application/json");
echo json_encode($events);

$conn->close();

?>