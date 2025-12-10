<?php
session_start();
header("Content-Type: application/json");
require __DIR__ . '/../db_connect.php';

$item_id = intval($_GET['id'] ?? 0);

$sql = $conn->prepare("
    SELECT h.*, u.name AS user_name
    FROM item_history h
    JOIN users u ON u.id = h.user_id
    WHERE h.item_id = ?
    ORDER BY h.changed_at DESC
");
$sql->bind_param("i", $item_id);
$sql->execute();
$res = $sql->get_result();

$history = [];
while ($row = $res->fetch_assoc()) {
    $history[] = $row;
}

echo json_encode(["history" => $history]);
