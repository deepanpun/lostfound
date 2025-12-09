<?php
session_start();
require __DIR__ . '/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_page.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Invalid delete request.");
}

$id = intval($_GET['id']);

// Delete only if user owns the item
$stmt = $conn->prepare("DELETE FROM items WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);

if ($stmt->execute()) {
    header("Location: index.php?msg=" . urlencode("Item deleted successfully."));
} else {
    header("Location: index.php?err=" . urlencode("Failed to delete item."));
}
exit;
