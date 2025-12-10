<?php
session_start();
require __DIR__ . '/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_page.php");
    exit;
}

$msg = $err = "";

/* -----------------HERE HANDLE NEW ITEM SUBMISSION ----------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status      = $_POST['status'] ?? '';
    $title       = trim($_POST['title'] ?? '');
    $loc         = trim($_POST['location'] ?? '');
    $desc        = trim($_POST['description'] ?? '');
    $category_id = $_POST['category_id'] ?? '';

    if (!$status || !$title || !$category_id) {
        $err = "Status, Category, and Item Title are required.";
    } else {
        $imgPath = null;
        if (!empty($_FILES['image']['name'])) {
            $dirFs = __DIR__ . "/uploads/";
            if (!is_dir($dirFs)) mkdir($dirFs, 0775, true);

            $safeName = time() . "_" . preg_replace("/[^A-Za-z0-9._-]/", "_", $_FILES['image']['name']);
            $targetFs = $dirFs . $safeName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFs)) {
                $imgPath = "uploads/" . $safeName;
            } else {
                $err = "Image upload failed.";
            }
        }

        if (!$err) {
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
                $imgPath,
                $category_id
            );

            if ($stmt->execute()) $msg = "Item reported successfully!";
            else $err = "Database error while saving item.";

            $stmt->close();
        }
    }
}

/* ----------------- FETCH CATEGORIES ----------------- */
$categories = $conn->query("SELECT id, category_name FROM categories ORDER BY category_name ASC");

/* ----------------- FETCH ITEMS ----------------- */
$sql = "
    SELECT items.*, users.name AS poster, categories.category_name
    FROM items
    JOIN users ON users.id = items.user_id
    LEFT JOIN categories ON categories.id = items.category_id
    ORDER BY items.created_at DESC
";
$items = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Lost & Found | Dashboard</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        body { margin: 0; background-color: #f4f4f4; font-family: Arial, sans-serif; }
        .dashboard { width: 90%; max-width: 1100px; margin: 30px auto 50px auto; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .topbar h2 { margin: 0; color: #f36f21; }
        .topbar .user-info a { color: #0066ff; text-decoration: none; margin-left: 8px; }
        .card { background: #ffffff; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,0.08); padding: 20px 24px; margin-bottom: 25px; }
        .card h3 { margin-top: 0; border-left: 4px solid #f36f21; padding-left: 10px; }
        .form-row { display: flex; gap: 10px; flex-wrap: wrap; }
        .form-row > div { flex: 1; min-width: 220px; }
        label { display: block; font-weight: bold; margin-top: 10px; margin-bottom: 5px; }
        select, input, textarea { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; }
        textarea { min-height: 110px; resize: vertical; }
        .btn-primary { background: #f36f21; color: white; padding: 10px 18px; border: none; border-radius: 6px; margin-top: 15px; }
        .btn-sm { padding: 5px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; border: none; }
        .btn-edit { background: #0066ff; color: white; }
        .btn-delete { background: #b00020; color: white; }
        .btn-contact { background: #444; color: white; }
        .items-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 18px; }
        .item-card { background: white; border-radius: 10px; padding: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
        .item-card img { width: 100%; border-radius: 8px; max-height: 200px; object-fit: cover; }
    </style>

    <script>
    function deleteItem(id) {
        if (!confirm("Delete this item?")) return;

        fetch("api/delete_item.php?id=" + id)
            .then(r => r.json())
            .then(d => {
                alert(d.message);
                if (d.status === "success") location.reload();
            });
    }
    </script>
</head>

<body>

<?php include __DIR__ . '/templates/header.html'; ?>

<div class="dashboard">

    <div class="topbar">
        <h2>Campus Lost & Found</h2>
        <div class="user-info">
            Logged in as <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>
            <a href="messages.php">Messages</a> |
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- REPORT FORM -->
    <div class="card">
        <h3>Report Lost / Found Item</h3>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div>
                    <label>Status</label>
                    <select name="status" required>
                        <option value="">Select status</option>
                        <option value="LOST">LOST</option>
                        <option value="FOUND">FOUND</option>
                    </select>
                </div>
                <div>
                    <label>Item title</label>
                    <input type="text" name="title" required>
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label>Category</label>
                    <select name="category_id" required>
                        <option value="">Select category</option>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label>Location</label>
                    <input type="text" name="location">
                </div>
            </div>

            <label>Description</label>
            <textarea name="description"></textarea>

            <label>Upload image</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit" class="btn-primary">Submit Item</button>
        </form>
    </div>

    <!-- ITEMS GRID -->
    <div class="card">
        <h3>Recent Items</h3>

        <div class="items-grid">

            <?php while ($it = $items->fetch_assoc()): ?>
                <div class="item-card">

                    <?php if ($it['image']): ?>
                        <img src="<?= htmlspecialchars($it['image']) ?>">
                    <?php endif; ?>

                    <h4>[<?= htmlspecialchars($it['status']) ?>] <?= htmlspecialchars($it['title']) ?></h4>

                    <p><strong>Category:</strong> <?= htmlspecialchars($it['category_name']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($it['location']) ?></p>
                    <p><?= nl2br(htmlspecialchars($it['description'])) ?></p>

                    <small>Posted by <?= htmlspecialchars($it['poster']) ?> on <?= $it['created_at'] ?></small>

                    <div class="item-actions">
                        <?php if ($it['user_id'] == $_SESSION['user_id']): ?>

                            <a class="btn-sm btn-edit" href="edit_item.php?id=<?= $it['id'] ?>">Edit</a>

                            <button class="btn-sm btn-delete" onclick="deleteItem(<?= $it['id'] ?>)">
                                Delete
                            </button>

                        <?php else: ?>
                            <!-- ⭐ Contact Owner — UPDATED but same link (uses new API inside contact_item.php) -->
                            <a class="btn-sm btn-contact" href="contact_item.php?item_id=<?= $it['id'] ?>">
                                Contact Owner
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endwhile; ?>

        </div>
    </div>

</div>

<?php include __DIR__ . '/templates/footer.html'; ?>
</body>
</html>
