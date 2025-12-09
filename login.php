<?php
session_start();
require __DIR__ . '/db_connect.php';

// Settings
$MAX_ATTEMPTS = 5;
$LOCK_TIME = 300; // 5 minutes

// Initialize session attempts
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['lockout_time'])) {
    $_SESSION['lockout_time'] = 0;
}

// Check lockout status
if ($_SESSION['login_attempts'] >= $MAX_ATTEMPTS) {

    $elapsed = time() - $_SESSION['lockout_time'];

    if ($elapsed < $LOCK_TIME) {

        $remaining = $LOCK_TIME - $elapsed;
        $min = floor($remaining / 60);
        $sec = $remaining % 60;

        header("Location: login_page.php?err=" .
            urlencode("Too many attempts. Try again in {$min}m {$sec}s."));
        exit;
    } else {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['lockout_time'] = 0;
    }
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login_page.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';

// Validate
if (!$email || !$pass) {
    header("Location: login_page.php?err=" .
        urlencode("All fields are required."));
    exit;
}

// Lookup user
$stmt = $conn->prepare("SELECT id, name, password_hash FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($u = $res->fetch_assoc()) {

    if (password_verify($pass, $u['password_hash'])) {

        // Success login
        $_SESSION['login_attempts'] = 0;
        $_SESSION['lockout_time'] = 0;

        // Remember me
        if (!empty($_POST['remember'])) {
            setcookie("remember_email", $email, time() + 7*24*60*60);
        }

        $_SESSION['user_id'] = $u['id'];
        $_SESSION['name']    = $u['name'];

        header("Location: index.php");
        exit;
    }
}

// Failure - increment attempts
$_SESSION['login_attempts']++;
$attempt = $_SESSION['login_attempts'];

if ($attempt >= $MAX_ATTEMPTS) {
    $_SESSION['lockout_time'] = time();
    header("Location: login_page.php?err=" .
        urlencode("Too many wrong attempts. Locked for 5 minutes."));
    exit;
}

header("Location: login_page.php?err=" .
    urlencode("Invalid password. Attempt {$attempt} of {$MAX_ATTEMPTS}."));
exit;
