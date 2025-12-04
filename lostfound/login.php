<?php
session_start();
require __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: login_page.php'); exit;
}

$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT id, name, password_hash FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($u = $res->fetch_assoc()) {
  if (password_verify($pass, $u['password_hash'])) {
    $_SESSION['user_id'] = $u['id'];
    $_SESSION['name']    = $u['name'];
    header("Location: index.php"); exit;
  }
}
header("Location: login_page.php?err=" . urlencode("Invalid email or password"));
