<?php
header("Content-Type: application/json");
require __DIR__ . '/../db_connect.php';

$search = $_GET['search'] ?? '';
$search = trim($search);

try {
    if ($search !== "") {
        // Search filtered
        $like = "%" . $search . "%";
        $stmt = $conn->prepare("
            SELECT items.id, items.status, items.title, items.location, items.image, items.created_at,
                   users.name AS poster
            FROM items 
            JOIN users ON users.id = items.user_id
            WHERE items.title LIKE ? OR items.location LIKE ?
            ORDER BY items.created_at DESC
        ");
        $stmt->bind_param("ss", $like, $like);
    } else {
        // Return all items
        $stmt = $conn->prepare("
            SELECT items.id, items.status, items.title, items.location, items.image, items.created_at,
                   users.name AS poster
            FROM items 
            JOIN users ON users.id = items.user_id
            ORDER BY items.created_at DESC
        ");
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    echo json_encode(["results" => $items], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error."]);
}
?>
