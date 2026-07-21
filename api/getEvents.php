<?php

require "db.php";

header("Content-Type: application/json");

$year_id = isset($_GET["year_id"]) ? (int)$_GET["year_id"] : 0;

if ($year_id > 0) {

    $stmt = $conn->prepare("
        SELECT *
        FROM events
        WHERE year_id = ?
        ORDER BY event_date DESC
    ");

    $stmt->bind_param("i", $year_id);
    $stmt->execute();
    $result = $stmt->get_result();

} else {

    $result = $conn->query("
        SELECT *
        FROM events
        ORDER BY event_date DESC
    ");

}

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
        "year_id" => $row["year_id"]
    ];
}

echo json_encode([
    "success" => true,
    "events" => $events
]);

$conn->close();

?>