
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/db.php";

function respond($success, $message) {
    echo json_encode([
        "success" => $success,
        "message" => $message
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    respond(false, "POST request required.");
}

/*
 * SECURITY:
 * Add server-side admin authentication here before
 * enabling this endpoint on the public website.
 */

$data = json_decode(file_get_contents("php://input"), true);
$id = filter_var($data["id"] ?? null, FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    respond(false, "A valid magazine ID is required.");
}

$stmt = $conn->prepare(
    "SELECT pdf_path FROM magazines WHERE id = ?"
);

if (!$stmt) {
    http_response_code(500);
    respond(false, "Could not prepare database query.");
}

$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($pdfPath);

if (!$stmt->fetch()) {
    $stmt->close();
    $conn->close();
    http_response_code(404);
    respond(false, "Magazine not found.");
}

$stmt->close();

/* Only permit deletion from uploads/magazines/. */
$expectedPrefix = "uploads/magazines/";

if (
    strpos($pdfPath, $expectedPrefix) !== 0 ||
    basename($pdfPath) !== substr($pdfPath, strlen($expectedPrefix))
) {
    http_response_code(400);
    respond(false, "The stored PDF path is invalid.");
}

$projectRoot = dirname(__DIR__);
$absolutePath = $projectRoot . "/" . $pdfPath;
$realUploadDir = realpath($projectRoot . "/uploads/magazines/");
$realFilePath = realpath($absolutePath);

/*
 * Refuse to delete the database record if a PDF exists
 * but cannot safely be removed.
 */
if ($realFilePath !== false) {
    if (
        $realUploadDir === false ||
        strpos(
            $realFilePath,
            $realUploadDir . DIRECTORY_SEPARATOR
        ) !== 0
    ) {
        http_response_code(400);
        respond(false, "The PDF is outside the magazine folder.");
    }

    if (!is_file($realFilePath) || !is_writable($realFilePath)) {
        http_response_code(500);
        respond(false, "The PDF cannot be deleted. Check file permissions.");
    }

    if (!unlink($realFilePath)) {
        http_response_code(500);
        respond(false, "Could not delete the PDF file.");
    }
}

$stmt = $conn->prepare("DELETE FROM magazines WHERE id = ?");

if (!$stmt) {
    http_response_code(500);
    respond(false, "Could not prepare the delete operation.");
}

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    $stmt->close();
    $conn->close();
    http_response_code(500);
    respond(false, "Could not delete the magazine record.");
}

$stmt->close();
$conn->close();

respond(true, "Magazine deleted successfully.");
?>
