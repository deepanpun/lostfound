<?php
session_start();
require __DIR__ . '/../db_connect.php';

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

$status = $_POST['status'] ?? '';
$title  = trim($_POST['title'] ?? '');
$loc    = trim($_POST['location'] ?? '');
$desc   = trim($_POST['description'] ?? '');
$category_id = $_POST['category_id'] ?? '';

if (!$status || !$title || !$category_id) {
    echo json_encode(["status"=>"error", "message"=>"Status, title, and category are required"]);
    exit;
}

/* ---- Handle Image Upload ---- */
$imagePath = null;

if (!empty($_FILES['image']['name'])) {
    $dir = __DIR__ . '/../uploads/';
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $safeName = time() . "_" . preg_replace("/[^A-Za-z0-9._-]/", "_", $_FILES['image']['name']);
    $target = $dir . $safeName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $imagePath = "uploads/" . $safeName;
    } else {
        echo json_encode(["status"=>"error", "message"=>"Image upload failed"]);
        exit;
    }
}

/* ---- Insert into database ---- */
$stmt = $conn->prepare("
    INSERT INTO items (user_id, status, title, description, location, image, category_id)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isssssi",
    $_SESSION['user_id'],
    $status,
    $title,
    $desc,
    $loc,
    $imagePath,
    $category_id
);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success", "message"=>"Item reported successfully!"]);
} else {
    echo json_encode(["status"=>"error", "message"=>"Database error"]);
}

$stmt->close();
