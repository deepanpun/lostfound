<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Create Account | Lost & Found</title>

<style>
/* ===== PAGE BACKGROUND ===== */
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;

    background: url("Bengal.png") no-repeat center center fixed;
    background-size: cover;

    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;

    backdrop-filter: brightness(0.45); /* Darken overlay for readability */
}

/* ===== WHITE CARD ===== */
.card {
    width: 450px;
    background: white;
    padding: 35px;
    border-radius: 12px;
    box-shadow: 0 5px 35px rgba(0,0,0,0.35);
    text-align: center;
}

/* ===== TITLE ===== */
.card h2 {
    color: #d35400; /* ISU Orange */
    font-size: 28px;
    margin-bottom: 20px;
}

/* ===== INPUTS ===== */
input {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #bbb;
    box-sizing: border-box;
    font-size: 15px;
}

/* ===== BUTTON ===== */
button {
    width: 100%;
    padding: 12px;
    background: #d35400; /* Strong ISU orange */
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.2s;
}
button:hover {
    background: #b34700;
}

/* Back to login link */
a {
    color: black;
    text-decoration: none;
    font-size: 14px;
}
a:hover {
    text-decoration: underline;
}

#strength {
    font-size: 13px;
    margin-bottom: 10px;
}
</style>

</head>
<body>

<div class="card">
    <h2>Create Account</h2>

    <form action="register.php" method="POST" onsubmit="return validatePasswords();">

        <input type="text" name="name" placeholder="Full name" required>
        <input type="email" name="email" placeholder="Email" required>

        <input type="password" id="pass" name="password" placeholder="Password (8+ chars)" required>
        <input type="password" id="pass2" placeholder="Confirm password" required>

        <div id="strength"></div>

        <button type="submit">Sign Up</button>
    </form>

    <br>
    <a href="login_page.php">Back to login</a>
</div>

<script>
function validatePasswords() {
    let p1 = document.getElementById("pass").value;
    let p2 = document.getElementById("pass2").value;

    if (p1 !== p2) {
        alert("Passwords do not match.");
        return false;
    }
    return true;
}

document.getElementById("pass").addEventListener("input", function () {
    let p = this.value;
    let strength = document.getElementById("strength");

    let strong = p.length >= 8 &&
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
