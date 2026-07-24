<?php

require "db.php";

header("Content-Type: application/json");

$year = $_GET["year_name"] ?? "";

if (!empty($year)) {

    $stmt = $conn->prepare("
        SELECT *
        FROM events
        WHERE year_name = ?
        ORDER BY event_date DESC
    ");

    $stmt->bind_param("s", $year);
    $stmt->execute();
    $result = $stmt->get_result();

} else {

    $result = $conn->query("
        SELECT *
        FROM events
        ORDER BY event_date DESC
    ");

}

$events = [];

while ($row = $result->fetch_assoc()) {

    $events[] = [

        "id" => (int)$row["id"],

        "title" => $row["title"],

        "desc" => $row["description"],

        "date" => $row["event_date"],

        "time" => $row["event_time"],

        "location" => $row["location"],

        "category" => $row["category"],

        "status" => ($row["status"] === "Completed")
            ? "past"
            : "upcoming",

        "poster" => $row["poster"],

        "event_link" => $row["event_link"],

        "link" => "",

        "ri_year" => $row["ri_year"],

        "year_name" => $row["year_name"]

    ];

}

echo json_encode([
    "success" => true,
    "events" => $events
]);

$conn->close();

?>