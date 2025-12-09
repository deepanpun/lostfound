<?php
require __DIR__ . '/db_connect.php';

$email = trim($_POST['email'] ?? '');

if (!$email) {
    header("Location: forgot_password_page.php?err=" . urlencode("Email is required"));
    exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: forgot_password_page.php?err=" . urlencode("No account found with that email"));
    exit;
}

$user = $result->fetch_assoc();

// Generate token
$token = bin2hex(random_bytes(16));

// Save token to DB
$stmt = $conn->prepare("UPDATE users SET reset_token=? WHERE email=?");
$stmt->bind_param("ss", $token, $email);
$stmt->execute();

// Redirect user to reset_password_page.php
header("Location: reset_password_page.php?token=" . urlencode($token));
exit;
?>
