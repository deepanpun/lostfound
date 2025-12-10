<?php
session_start();
require __DIR__ . '/../db_connect.php';

header("Content-Type: application/json");

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

// Must receive id
if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "Invalid delete request"]);
    exit;
}

$id = intval($_GET['id']);
$user_id = intval($_SESSION['user_id']);

// Delete only if user owns the item
$stmt = $conn->prepare("DELETE FROM items WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Item deleted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete item"]);
}
exit;
