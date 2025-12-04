<?php
session_start();
require __DIR__ . '/db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login_page.php"); exit; }

$msg = $err = "";

/* Handle “Report Item” submit on the same page */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $status = $_POST['status'] ?? '';
  $title  = trim($_POST['title'] ?? '');
  $desc   = trim($_POST['description'] ?? '');
  $loc    = trim($_POST['location'] ?? '');

  if (!$status || !$title) {
    $err = "Status and Title are required.";
  } else {
    // optional image upload
    $imgPath = NULL;
    if (!empty($_FILES['image']['name'])) {
      $dirFs = __DIR__ . "/uploads/";              // filesystem path
      if (!is_dir($dirFs)) { mkdir($dirFs, 0775, true); }
      $safe = time() . "_" . preg_replace("/[^A-Za-z0-9._-]/","_", $_FILES['image']['name']);
      $target = $dirFs . $safe;
      if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $imgPath = "uploads/" . $safe;             // web path
      } else {
        $err = "Could not upload image.";
      }
    }

    if (!$err) {
      $stmt = $conn->prepare(
        "INSERT INTO items (user_id, status, title, description, location, image)
         VALUES (?, ?, ?, ?, ?, ?)"
      );
      $stmt->bind_param("isssss", $_SESSION['user_id'], $status, $title, $desc, $loc, $imgPath);
      if ($stmt->execute()) $msg = "Item reported successfully!";
      else $err = "Database error while saving item.";
    }
  }
}

/* Fetch items to show */
$sql = "SELECT items.*, users.name AS poster
        FROM items JOIN users ON users.id = items.user_id
        ORDER BY items.created_at DESC";
$items = $conn->query($sql);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Campus Lost & Found  Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/templates/header.html'; ?>

<div class="dashboard">
  <div class="topbar">
    <div><strong>Logged in as:</strong> <?= htmlspecialchars($_SESSION['name']) ?></div>
    <nav><a class="btn" href="logout.php">Logout</a></nav>
  </div>

  <?php if ($msg): ?><p class="flash success"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
  <?php if ($err): ?><p class="flash error"><?= htmlspecialchars($err) ?></p><?php endif; ?>

  <!-- Report Item (same page) -->
  <div class="card">
    <h3>Report Lost/Found Item</h3>
    <form method="POST" enctype="multipart/form-data">
      <div class="form-row">
        <select name="status" required>
          <option value="">Status</option>
          <option value="LOST">LOST</option>
          <option value="FOUND">FOUND</option>
        </select>
        <input type="text" name="title" placeholder="Item title" required>
      </div>
      <input type="text" name="location" placeholder="Location (e.g., Library, Lot B)">
      <textarea name="description" placeholder="Description"></textarea>
      <input type="file" name="image" accept="image/*">
      <button type="submit">Submit Item</button>
    </form>
  </div>

  <!-- Items Grid -->
  <div class="items-grid">
    <?php if ($items && $items->num_rows): ?>
      <?php while ($it = $items->fetch_assoc()): ?>
        <div class="item-card">
          <?php if ($it['image']): ?>
            <img src="<?= htmlspecialchars($it['image']) ?>" alt="Item">
          <?php endif; ?>
          <h4>[<?= htmlspecialchars($it['status']) ?>] <?= htmlspecialchars($it['title']) ?></h4>
          <?php if ($it['location']): ?><p><?= htmlspecialchars($it['location']) ?></p><?php endif; ?>
          <small>Posted <?= htmlspecialchars($it['poster']) ?> · <?= htmlspecialchars($it['created_at']) ?></small>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="muted">No items yet.</p>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/templates/footer.html'; ?>
</body>
</html>
