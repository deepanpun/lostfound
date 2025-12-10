<?php
session_start();
header("Content-Type: application/json");
require __DIR__ . '/../db_connect.php';

$item_id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

echo json_encode(["item" => $item]);
