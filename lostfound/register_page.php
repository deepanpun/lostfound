<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Register Campus Lost & Found</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/templates/header.html'; ?>
<div class="container">
  <h2>Create Account</h2>
  <?php if(!empty($_GET['err'])): ?>
    <p class="error"><?= htmlspecialchars($_GET['err']) ?></p>
  <?php endif; ?>
  <form action="register.php" method="POST">
    <input type="text" name="name" placeholder="Full name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password (min 6)" minlength="6" required>
    <button type="submit">Sign Up</button>
  </form>
  <p><a href="login_page.php">Back to login</a></p>
</div>
<?php include __DIR__ . '/templates/footer.html'; ?>
</body>
</html>
