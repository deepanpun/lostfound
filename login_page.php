<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;

            /* === Local ISU Bengal background === */
            background: url("Bengal/bengal.png") no-repeat center center fixed;
            background-size: contain; /* prevents blur */
            background-color: #808080; /* fallback gray */
        }

        .login-box {
            width: 380px;
            margin: 60px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.92);
            border-radius: 12px;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.25);
            backdrop-filter: blur(3px);
            text-align: center;
        }

        .login-logo {
            width: 140px;
            margin-bottom: 15px;
        }

        h2 {
            margin-bottom: 15px;
            font-size: 28px;
            font-weight: bold;
        }

        .error-box {
            background: #ffe6e6;
            color: #cc0000;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ffb3b3;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
            text-align: left;
        }

        input {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            background: #eef3ff;
            margin-bottom: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #e65c3a; /* ISU orange */
            border: none;
            color: white;
            border-radius: 6px;
            font-size: 17px;
            cursor: pointer;
        }

        button:hover {
            background: #cc4f32;
        }

        .forgot, .register-box {
            text-align: center;
            margin-top: 12px;
        }

        a {
            color: #e65c3a;
            text-decoration: none;
        }
    </style>
</head>

<body>

<?php include __DIR__ . "/templates/header.html"; ?>

<div class="login-box">

    <!-- 🔥 Added Logo Image Here -->
    <img src="Idaho State University.jpg" alt="ISU Logo" class="login-logo">

    <h2>Login</h2>

    <?php if (!empty($_GET['err'])): ?>
        <div class="error-box">
            <?= htmlspecialchars($_GET['err']) ?>
        </div>
    <?php endif; ?>

    <?php $savedEmail = $_COOKIE['remember_email'] ?? ''; ?>

    <form action="login.php" method="POST">

        <label>Email:</label>
        <input type="email"
               name="email"
               placeholder="Enter your email"
               value="<?= htmlspecialchars($savedEmail) ?>"
               autocomplete="new-email"
               required>

        <label>Password:</label>
        <input type="password"
               name="password"
               placeholder="Enter your password"
               autocomplete="new-password"
               required>

        <div>
            <input type="checkbox" name="remember"> Remember Me (7 days)
        </div>

        <button type="submit">Login</button>
    </form>

    <a class="forgot" href="forgot_password_page.php">Forgot password?</a>

    <div class="register-box">
        Don’t have an account?
        <a href="register_page.php">Register here</a>
    </div>

</div>

<?php include __DIR__ . "/templates/footer.html"; ?>

</body>
</html>
