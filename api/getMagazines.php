
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "GET request required."
    ]);
    exit;
}

$sql = "
    SELECT id, display_name, year_name, pdf_path, created_at
    FROM magazines
    ORDER BY year_name DESC, created_at DESC, id DESC
";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Could not retrieve magazines."
    ]);
    $conn->close();
    exit;
}

$magazines = [];

while ($row = $result->fetch_assoc()) {
    $magazines[] = [
        "id" => (int) $row["id"],
        "display_name" => $row["display_name"],
        "year_name" => $row["year_name"],
        "pdf_path" => $row["pdf_path"],
        "created_at" => $row["created_at"]
    ];
}

echo json_encode([
    "success" => true,
    "magazines" => $magazines
]);

$result->free();
$conn->close();
?>
