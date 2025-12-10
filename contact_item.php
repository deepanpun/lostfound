<?php
session_start();
require __DIR__ . '/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_page.php");
    exit;
}

$item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;
if ($item_id <= 0) {
    die("Invalid item id.");
}

/* Fetch item + owner info */
$stmt = $conn->prepare("
    SELECT items.*, users.name AS owner_name, users.id AS owner_id
    FROM items
    JOIN users ON users.id = items.user_id
    WHERE items.id = ?
");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$item) {
    die("Item not found.");
}

$owner_id = (int)$item['owner_id'];
$current_user_id = (int)$_SESSION['user_id'];

if ($owner_id === $current_user_id) {
    die("You cannot contact yourself about your own item.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Owner</title>

    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; }
        .container {
            width: 90%; max-width: 700px; margin: 40px auto;
            background:#fff; padding:25px; border-radius:10px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }
        h2 { margin-top:0; color:#f36f21; }
        .item-summary {
            background:#fafafa; padding:10px 12px; border-radius:6px; margin-bottom:15px;
        }
        textarea {
            width:100%; min-height:140px; padding:10px;
            border-radius:6px; border:1px solid #ccc;
            box-sizing:border-box;
        }
        .btn-primary {
            margin-top:10px; padding:10px 18px; border:none;
            border-radius:6px; background:#f36f21; color:#fff;
            cursor:pointer; font-weight:bold;
        }
        a.back { display:inline-block; margin-top:12px; text-decoration:none; color:#0066ff; }
    </style>

    <script>
        function sendMessage(event) {
            event.preventDefault();

            let form = new FormData(document.getElementById("contactForm"));
            form.append("item_id", <?= $item_id ?>);

            fetch("api/contact_item.php", {
                method: "POST",
                body: form
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);

                if (data.status === "success") {
                    document.getElementById("message").value = "";
                }
            })
            .catch(err => console.error("API Error:", err));
        }
    </script>

</head>
<body>

<div class="container">
    <h2>Contact Owner</h2>

    <div class="item-summary">
        <strong>Item:</strong> [<?= htmlspecialchars($item['status']) ?>] <?= htmlspecialchars($item['title']) ?><br>
        <strong>Owner:</strong> <?= htmlspecialchars($item['owner_name'] ?? "Unknown") ?><br>
        <?php if (!empty($item['location'])): ?>
            <strong>Location:</strong> <?= htmlspecialchars($item['location']) ?><br>
        <?php endif; ?>
    </div>

    <!-- FIXED FORM HERE -->
    <form id="contactForm" onsubmit="sendMessage(event)">
        
        <!-- 🔥 ADD THIS HIDDEN FIELD (the missing piece!) -->
        <input type="hidden" name="item_id" value="<?= $item_id ?>">

        <label><strong>Your message to the owner:</strong></label>
        <textarea id="message" name="message" placeholder="Describe who you are, where you saw the item, or how they can contact you."></textarea>

        <button type="submit" class="btn-primary">Send Message</button>
    </form>

    <a class="back" href="index.php">&laquo; Back to Dashboard</a>
</div>

</body>
</html>
