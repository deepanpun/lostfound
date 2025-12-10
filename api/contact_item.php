<?php
session_start();
require __DIR__ . '/../db_connect.php';

header("Content-Type: application/json");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

$user_id  = (int)$_SESSION['user_id'];
$item_id  = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
$message  = trim($_POST['message'] ?? '');

// Validate item ID
if ($item_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid item ID"]);
    exit;
}

// Validate message
if ($message === "") {
    echo json_encode(["status" => "error", "message" => "Message cannot be empty"]);
    exit;
}

// Fetch item & owner
$stmt = $conn->prepare("
    SELECT items.title, items.status, items.location, items.user_id AS owner_id, users.name AS owner_name
    FROM items
    JOIN users ON users.id = items.user_id
    WHERE items.id = ?
");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Check if item exists
if (!$item) {
    echo json_encode(["status" => "error", "message" => "Item not found"]);
    exit;
}

$owner_id = (int)$item['owner_id'];

// Prevent user contacting themselves
if ($owner_id === $user_id) {
    echo json_encode(["status" => "error", "message" => "You cannot contact yourself about your own item"]);
    exit;
}

// Insert message into contacts table
$stmt = $conn->prepare("
    INSERT INTO contacts (item_id, sender_id, receiver_id, message)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("iiis", $item_id, $user_id, $owner_id, $message);

if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Failed to send message"]);
    exit;
}

$stmt->close();

// Success response
echo json_encode([
    "status"  => "success",
    "message" => "Your message has been sent to the owner",
    "item" => [
        "title"       => $item['title'],
        "status"      => $item['status'],
        "location"    => $item['location'],
        "owner_name"  => $item['owner_name']
    ]
]);
