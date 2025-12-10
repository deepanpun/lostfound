<?php
session_start();
header("Content-Type: application/json");
require __DIR__ . '/../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$item_id = intval($_POST['id'] ?? 0);

// Fetch item
$stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$item) {
    echo json_encode(["status" => "error", "message" => "Item not found"]);
    exit;
}

$new_title = $_POST['title'] ?? '';
$new_description = $_POST['description'] ?? '';
$new_location = $_POST['location'] ?? '';
$new_status = $_POST['status'] ?? '';

// Update
$update = $conn->prepare("
    UPDATE items SET title=?, description=?, location=?, status=?
    WHERE id=?
");
$update->bind_param(
    "ssssi",
    $new_title,
    $new_description,
    $new_location,
    $new_status,
    $item_id
);
$update->execute();
$update->close();

// History log function
function log_history($conn, $item_id, $user_id, $type, $old, $new) {
    if ($old === $new) return;

    $sql = $conn->prepare("
        INSERT INTO item_history (item_id, user_id, change_type, old_value, new_value)
        VALUES (?, ?, ?, ?, ?)
    ");
    $sql->bind_param("iisss", $item_id, $user_id, $type, $old, $new);
    $sql->execute();
    $sql->close();
}

log_history($conn, $item_id, $user_id, "title", $item['title'], $new_title);
log_history($conn, $item_id, $user_id, "description", $item['description'], $new_description);
log_history($conn, $item_id, $user_id, "location", $item['location'], $new_location);
log_history($conn, $item_id, $user_id, "status", $item['status'], $new_status);

echo json_encode(["status" => "success", "message" => "Item updated"]);
exit;
