<?php
$token = $_GET['token'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reset Password</title>

<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;

        /* Correct background path */
        background: url('Bengal.png') no-repeat center center fixed;
        background-size: contain;

        /* dark overlay */
        background-color: #333;
    }

    .overlay {
        background: rgba(0,0,0,0.65);
        position: fixed;
        inset: 0;
    }

    .card {
        width: 420px;
        padding: 30px;
        margin: 80px auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        position: relative;
        z-index: 2;
        text-align: center;
    }

    h2 {
        margin-top: 0;
        color: #d35400; /* ISU orange */
        font-size: 24px;
    }

    input {
        width: 100%;
        padding: 12px;
        margin: 10px 0 18px;
        border: 1px solid #bbb;
        border-radius: 6px;
        font-size: 15px;
        box-sizing: border-box;
    }

    button {
        width: 100%;
        padding: 12px;
        background: #d35400;
        border: none;
        color: white;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 5px;
    }

    button:hover {
        background: #b84200;
    }

    a {
        display: block;
        margin-top: 12px;
        text-decoration: none;
        color: #333;
    }

    .strength {
        font-size: 14px;
        margin-top: -12px;
        margin-bottom: 10px;
        text-align: left;
    }
</style>

</head>
<body>

<div class="overlay"></div>

<div class="card">
    <h2>Reset Password</h2>

    <form action="reset_password.php" method="POST" onsubmit="return validatePasswords();">

        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <input type="password" id="pass1" name="password" placeholder="New Password" required>

        <input type="password" id="pass2" name="password_confirm" placeholder="Confirm Password" required>

        <div id="strength" class="strength"></div>

        <button type="submit">Update Password</button>
    </form>

    <a href="login_page.php">Back to Login</a>
</div>

<script>
function validatePasswords() {
    let p1 = document.getElementById('pass1').value;
    let p2 = document.getElementById('pass2').value;

    if (p1 !== p2) {
        alert("Passwords do not match!");
        return false;
    }
    return true;
}

document.getElementById("pass1").addEventListener("input", function () {
    let p = this.value;
    let strength = document.getElementById("strength");

    let strong =
        p.length >= 8 &&
        /[A-Z]/.test(p) &&
        /[a-z]/.test(p) &&
        /[0-9]/.test(p) &&
        /[^A-Za-z0-9]/.test(p);

    if (strong) {
        strength.innerHTML = "<span style='color:green;'>Strong password ✔</span>";
    } else {
        strength.innerHTML = "<span style='color:red;'>Weak password</span>";
    }
});
</script>

</body>
</html>
