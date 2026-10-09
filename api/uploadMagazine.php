
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/db.php";

function respond($success, $message, $extra = []) {
    echo json_encode(array_merge([
        "success" => $success,
        "message" => $message
    ], $extra));
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    respond(false, "POST request required.");
}

/*
 * SECURITY:
 * This endpoint must be connected to server-side admin
 * authentication before production use.
 * Your current Firebase-only login does not automatically
 * create a PHP session.
 */

$displayName = trim($_POST["display_name"] ?? "");
$yearName = trim($_POST["year_name"] ?? "");

if ($displayName === "" || strlen($displayName) > 150) {
    respond(false, "Enter a display name (maximum 150 characters).");
}

if (!preg_match('/^\d{4}-\d{4}$/', $yearName)) {
    respond(false, "Enter the year in YYYY-YYYY format, e.g. 2026-2027.");
}

if (!isset($_FILES["pdf"]) || !is_array($_FILES["pdf"])) {
    respond(false, "Please select a PDF file.");
}

$file = $_FILES["pdf"];

if ($file["error"] !== UPLOAD_ERR_OK) {
    respond(false, "Upload failed. Please check the file size and try again.");
}

$maxSize = 25 * 1024 * 1024; // 25 MB

if ($file["size"] <= 0 || $file["size"] > $maxSize) {
    respond(false, "The PDF must be smaller than 25 MB.");
}

$originalExtension = strtolower(
    pathinfo($file["name"], PATHINFO_EXTENSION)
);

if ($originalExtension !== "pdf") {
    respond(false, "Only PDF files are allowed.");
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file["tmp_name"]);

if ($mimeType !== "application/pdf") {
    respond(false, "The uploaded file is not a valid PDF.");
}

$projectRoot = dirname(__DIR__);
$uploadDir = $projectRoot . "/uploads/magazines/";

if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        respond(false, "Could not create the magazine upload folder.");
    }
}

if (!is_writable($uploadDir)) {
    respond(false, "The magazine upload folder is not writable.");
}

$fileName = "magazine_" . bin2hex(random_bytes(16)) . ".pdf";
$absolutePath = $uploadDir . $fileName;
$relativePath = "uploads/magazines/" . $fileName;

if (!move_uploaded_file($file["tmp_name"], $absolutePath)) {
    respond(false, "Could not save the uploaded PDF.");
}

$stmt = $conn->prepare(
    "INSERT INTO magazines (display_name, year_name, pdf_path)
     VALUES (?, ?, ?)"
);

if (!$stmt) {
    @unlink($absolutePath);
    respond(false, "Database operation could not be prepared.");
}

$stmt->bind_param("sss", $displayName, $yearName, $relativePath);

if (!$stmt->execute()) {
    $stmt->close();
    @unlink($absolutePath);
    respond(false, "Could not save the magazine details.");
}

$id = $conn->insert_id;
$stmt->close();
$conn->close();

respond(true, "Magazine uploaded successfully.", [
    "id" => $id,
    "magazine" => [
        "id" => $id,
        "display_name" => $displayName,
        "year_name" => $yearName,
        "pdf_path" => $relativePath
    ]
]);
