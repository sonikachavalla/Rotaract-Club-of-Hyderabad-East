<?php

header("Content-Type: application/json");

require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["events"])) {
    echo json_encode([
        "success" => false,
        "message" => "No events received."
    ]);
    exit;
}

$events = $data["events"];

$imported = 0;
$skipped = [];

foreach ($events as $index => $row) {

    $title = trim($row["Title"] ?? "");
    $description = trim($row["Description"] ?? "");
    $category = trim($row["Category"] ?? "");
    $date = trim($row["Date"] ?? "");

/*
|--------------------------------------------------------------------------
| Convert Excel date to YYYY-MM-DD
|--------------------------------------------------------------------------
*/

if (is_numeric($date)) {

    // Excel serial number
    $date = gmdate("Y-m-d", ($date - 25569) * 86400);

} else {

    $timestamp = strtotime($date);

    if ($timestamp !== false) {
        $date = date("Y-m-d", $timestamp);
    }

}
    $time = trim($row["Time"] ?? "");
    $location = trim($row["Location"] ?? "");
    $status = trim($row["Status"] ?? "Upcoming");
    $registration_link = trim($row["Registration Link"] ?? "");
    $poster = trim($row["Poster Filename"] ?? "");

    if ($title == "" || $date == "") {
        $skipped[] = "Row " . ($index + 2) . " : Missing Title or Date";
        continue;
    }

    if ($poster != "") {

        $posterPath = "uploads/events/" . $poster;

        if (!file_exists("../" . $posterPath)) {
            $skipped[] = "Row " . ($index + 2) . " : Poster '$poster' not found";
            continue;
        }

    } else {

        $posterPath = "";

    }

    $stmt = $conn->prepare("
        INSERT INTO events
        (
            title,
            description,
            category,
            event_date,
            event_time,
            location,
            status,
            registration_link,
            poster
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssssssss",
        $title,
        $description,
        $category,
        $date,
        $time,
        $location,
        $status,
        $registration_link,
        $posterPath
    );

    if ($stmt->execute()) {
        $imported++;
    } else {
        $skipped[] = "Row " . ($index + 2) . " : Database error";
    }

    $stmt->close();
}

echo json_encode([
    "success" => true,
    "message" => "$imported events imported.",
    "imported" => $imported,
    "skipped" => $skipped
]);