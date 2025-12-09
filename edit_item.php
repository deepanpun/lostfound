<?php
session_start();
require __DIR__ . '/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_page.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$item_id = intval($_GET['id'] ?? 0);

// -------------------------------------------
// Fetch item
// -------------------------------------------
$stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$item) {
    die("Item not found.");
}

// -------------------------------------------
// SAVE CHANGES
// -------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $new_title = $_POST['title'];
    $new_description = $_POST['description'];
    $new_location = $_POST['location'];
    $new_status = $_POST['status'];

    // Update query
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

    // Helper to log history
    function log_history($conn, $item_id, $user_id, $type, $old, $new) {
        if ($old === $new) return; // No change
        
        $sql = $conn->prepare("
            INSERT INTO item_history (item_id, user_id, change_type, old_value, new_value)
            VALUES (?, ?, ?, ?, ?)
        ");
        $sql->bind_param("iisss", $item_id, $user_id, $type, $old, $new);
        $sql->execute();
        $sql->close();
    }

    // Log changes
    log_history($conn, $item_id, $user_id, "title", $item['title'], $new_title);
    log_history($conn, $item_id, $user_id, "description", $item['description'], $new_description);
    log_history($conn, $item_id, $user_id, "location", $item['location'], $new_location);
    log_history($conn, $item_id, $user_id, "status", $item['status'], $new_status);

    // Refresh item data
    header("Location: edit_item.php?id=$item_id&updated=1");
    exit;
}

// -------------------------------------------
// Fetch change history
// -------------------------------------------
$history_sql = $conn->prepare("
    SELECT h.*, u.name AS user_name
    FROM item_history h
    JOIN users u ON u.id = h.user_id
    WHERE h.item_id = ?
    ORDER BY h.changed_at DESC
");
$history_sql->bind_param("i", $item_id);
$history_sql->execute();
$history = $history_sql->get_result();
$history_sql->close();

?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Item</title>

<!-- ISU ORANGE + BLACK THEME -->
<style>
:root {
    --isu-orange: #F77F00;
    --isu-black: #1A1A1A;
    --isu-gray: #F7F7F7;
    --isu-border: #D9D9D9;
}

/* Container */
.edit-container {
    max-width: 900px;
    margin: 40px auto;
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
}

/* Title */
.edit-container h2 {
    color: var(--isu-orange);
    border-left: 6px solid var(--isu-orange);
    padding-left: 12px;
    font-size: 26px;
}

/* Labels & Inputs */
label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
    color: var(--isu-black);
}

input[type="text"], textarea, select {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--isu-border);
    border-radius: 6px;
    font-size: 15px;
    margin-top: 5px;
    background: white;
}

textarea {
    height: 90px;
}

/* Button */
button {
    background: var(--isu-orange);
    padding: 12px 22px;
    border-radius: 6px;
    border: none;
    color: white;
    font-size: 16px;
    margin-top: 18px;
    cursor: pointer;
    font-weight: bold;
}
button:hover {
    background: #d56c00;
}

/* History */
.history-title {
    margin-top: 40px;
    font-size: 22px;
    font-weight: bold;
    color: var(--isu-orange);
}

.history-card {
    background: var(--isu-gray);
    border-left: 5px solid var(--isu-orange);
    padding: 18px;
    margin-top: 18px;
    border-radius: 8px;
}

.history-card h4 {
    margin-bottom: 6px;
    color: var(--isu-black);
}

.history-meta {
    font-size: 13px;
    color: #555;
    margin-bottom: 10px;
}

.old span, .new span {
    font-weight: bold;
    color: var(--isu-black);
}
</style>

</head>
<body>

<div class="edit-container">

    <h2>Edit Item</h2>

    <?php if (isset($_GET['updated'])): ?>
        <p style="color:green;font-weight:bold;">Item updated successfully!</p>
    <?php endif; ?>

    <form method="POST">
        <label>Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>">

        <label>Description</label>
        <textarea name="description"><?= htmlspecialchars($item['description']) ?></textarea>

        <label>Location</label>
        <input type="text" name="location" value="<?= htmlspecialchars($item['location']) ?>">

        <label>Status</label>
        <select name="status">
            <option value="LOST" <?= $item['status']=="LOST"?"selected":"" ?>>LOST</option>
            <option value="FOUND" <?= $item['status']=="FOUND"?"selected":"" ?>>FOUND</option>
            <option value="CLAIMED" <?= $item['status']=="CLAIMED"?"selected":"" ?>>CLAIMED</option>
        </select>

        <button type="submit">Save Changes</button>
    </form>

    <!-- HISTORY -->
    <div class="history-title">Item Change History</div>

    <?php while ($h = $history->fetch_assoc()): ?>
        <div class="history-card">
            <h4><?= htmlspecialchars($h['change_type']) ?> changed</h4>
            <div class="history-meta">
                By <strong><?= htmlspecialchars($h['user_name']) ?></strong>
                on <?= htmlspecialchars($h['changed_at']) ?>
            </div>
            <div class="old"><span>Old:</span> <?= htmlspecialchars($h['old_value']) ?></div>
            <div class="new"><span>New:</span> <?= htmlspecialchars($h['new_value']) ?></div>
        </div>
    <?php endwhile; ?>

</div>

</body>
</html>
