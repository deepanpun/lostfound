<?php
session_start();
require __DIR__ . '/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_page.php");
    exit;
}

$current_user_id = (int)$_SESSION['user_id'];

$sql = "
    SELECT c.*, 
           s.name AS sender_name,
           i.title AS item_title,
           i.status AS item_status
    FROM contacts c
    JOIN users s ON s.id = c.sender_id
    JOIN items i ON i.id = c.item_id
    WHERE c.receiver_id = ?
    ORDER BY c.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$messages = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Messages</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0;}
        .container {
            width:90%; max-width:900px; margin:40px auto;
            background:#fff; padding:22px; border-radius:10px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }
        h2 { margin-top:0; color:#f36f21; }
        .message-card {
            border-bottom:1px solid #eee;
            padding:10px 0;
        }
        .message-card:last-child {
            border-bottom:none;
        }
        .message-card small { color:#666; font-size:12px;}
        .item-label { font-size:13px; color:#333; margin-bottom:4px;}
        pre {
            white-space:pre-wrap;
            font-family:inherit;
            margin:4px 0 0 0;
        }
        a.back { display:inline-block; margin-top:15px; text-decoration:none; color:#0066ff;}
    </style>
</head>
<body>
<div class="container">
    <h2>My Messages</h2>

    <?php if ($messages->num_rows === 0): ?>
        <p>You have no messages yet.</p>
    <?php else: ?>
        <?php while ($m = $messages->fetch_assoc()): ?>
            <div class="message-card">
                <div class="item-label">
                    About: [<?= htmlspecialchars($m['item_status']) ?>]
                    <?= htmlspecialchars($m['item_title']) ?>
                </div>
                <small>
                    From: <?= htmlspecialchars($m['sender_name']) ?> |
                    Sent: <?= htmlspecialchars($m['created_at']) ?>
                </small>
                <pre><?= htmlspecialchars($m['message']) ?></pre>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <a href="index.php" class="back">&laquo; Back to Dashboard</a>
</div>
</body>
</html>
