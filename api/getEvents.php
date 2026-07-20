<?php

require "db.php";

header("Content-Type: application/json");

$sql = "SELECT * FROM events ORDER BY event_date DESC";

$result = $conn->query($sql);

$events = [];

while ($row = $result->fetch_assoc()) {

    $row["status"] = ($row["status"] == "Completed") ? "past" : "upcoming";

    $events[] = [
        "id" => $row["id"],
        "title" => $row["title"],
        "desc" => $row["description"],
        "date" => $row["event_date"],
        "time" => $row["event_time"],
        "location" => $row["location"],
        "category" => $row["category"],
        "status" => $row["status"],
        "poster" => $row["poster"],
        "link" => "",
        "ri_year" => $row["ri_year"]
    ];
}

echo json_encode([
    "success" => true,
    "events" => $events
]);

$conn->close();

?>