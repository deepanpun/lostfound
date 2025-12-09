<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password</title>

<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: url('Bengal.png') no-repeat center center fixed;
        background-size: cover;
        background-color: #222;
    }

    .overlay {
        background: rgba(0,0,0,0.65);
        position: fixed;
        inset: 0;
    }

    .card {
        width: 430px;
        padding: 30px;
        margin: 90px auto;
        background: white;
        border-radius: 14px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.35);
        position: relative;
        z-index: 2;
        text-align: center;
    }

    h2 {
        margin-top: 0;
        margin-bottom: 20px;
        color: #d35400;
        font-size: 26px;
        font-weight: bold;
    }

    input {
        width: 100%;
        padding: 13px;
        margin: 12px 0 18px;
        border: 1px solid #bbb;
        border-radius: 6px;
        font-size: 16px;
        box-sizing: border-box;
    }

    button {
        width: 100%;
        padding: 14px;
        background: #d35400;
        border: none;
        color: white;
        font-size: 17px;
        border-radius: 6px;
        cursor: pointer;
    }

    button:hover {
        background: #b84200;
    }

    a {
        display: block;
        margin-top: 15px;
        text-decoration: none;
        color: #333;
        font-size: 15px;
    }
</style>

</head>
<body>

<div class="overlay"></div>

<div class="card">
    <h2>Forgot Password</h2>

    <?php if (!empty($_GET['msg'])): ?>
        <p style="color:green;"><?= htmlspecialchars($_GET['msg']) ?></p>
    <?php endif; ?>

    <?php if (!empty($_GET['err'])): ?>
        <p style="color:red;"><?= htmlspecialchars($_GET['err']) ?></p>
    <?php endif; ?>

    <form action="forgot_password.php" method="POST">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Send Reset Link</button>
    </form>

    <a href="login_page.php">Back to Login</a>
</div>

</body>
</html>
