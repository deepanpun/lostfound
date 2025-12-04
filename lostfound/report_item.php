<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login_page.php");
    exit;
}

$result = $conn->query("SELECT items.*, users.name FROM items JOIN users ON items.user_id = users.id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Campus Lost & Found</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'templates/header.html'; ?>
<div class="dashboard">
  <div class="topbar">
    <h1>Campus Lost & Found</h1>
    <nav>
      <a href="report_item.php">Report Item</a>
      <a href="logout.php">Logout</a>
    </nav>
  </div>
  <div class="items-grid">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="item-card">
        <?php if ($row['image']): ?>
          <img src="<?= htmlspecialchars($row['image']) ?>" alt="Item image">
        <?php endif; ?>
        <h3>[<?= $row['status'] ?>] <?= htmlspecialchars($row['title']) ?></h3>
        <p><?= htmlspecialchars($row['location']) ?></p>
        <p>Posted <?= htmlspecialchars($row['name']) ?></p>
      </div>
    <?php endwhile; ?>
    <?php if ($result->num_rows == 0): ?>
      <p>No items yet.</p>
    <?php endif; ?>
  </div>
</div>
<?php include 'templates/footer.html'; ?>
</body>
</html>
