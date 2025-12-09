<?php
require __DIR__ . '/db_connect.php';

$token = $_POST['token'] ?? '';
$pass1 = $_POST['password'] ?? '';
$pass2 = $_POST['password_confirm'] ?? '';

// -------- SERVER-SIDE VALIDATION --------

// 1. Check passwords match
if ($pass1 !== $pass2) {
    header("Location: reset_password_page.php?token=$token&err=" . urlencode("Passwords do not match."));
    exit;
}

// 2. Validate password strength
$strong =
    strlen($pass1) >= 8 &&
    preg_match('/[A-Z]/', $pass1) &&
    preg_match('/[a-z]/', $pass1) &&
    preg_match('/[0-9]/', $pass1) &&
    preg_match('/[^A-Za-z0-9]/', $pass1);

if (!$strong) {
    header("Location: reset_password_page.php?token=$token&err=" . urlencode("Weak password. Use upper, lower, number, symbol."));
    exit;
}

// -------- UPDATE PASSWORD --------
$newPassHash = password_hash($pass1, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
    UPDATE users 
    SET password_hash = ?, reset_token = NULL 
    WHERE reset_token = ?
");

$stmt->bind_param("ss", $newPassHash, $token);
$stmt->execute();

// If token was invalid (no row updated)
if ($stmt->affected_rows === 0) {
    header("Location: reset_password_page.php?err=" . urlencode("Invalid or expired reset link."));
    exit;
}

header("Location: login_page.php?msg=" . urlencode("Password updated successfully."));
exit;
