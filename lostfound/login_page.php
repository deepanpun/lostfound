<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login Campus Lost & Found</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/templates/header.html'; ?>
<div class="container">
  <h2>Login to Campus Lost & Found</h2>
  <?php if(!empty($_GET['err'])): ?>
    <p class="error"><?= htmlspecialchars($_GET['err']) ?></p>
  <?php endif; ?>
  <form action="login.php" method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>
  <p>Don’t have an account? <a href="register_page.php">Register here</a></p>
</div>
<?php include __DIR__ . '/templates/footer.html'; ?>
</body>
</html>
