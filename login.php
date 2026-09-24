<?php
session_start();

// Kalau sudah login, langsung ke dashboard
if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

// ============================
// LOGIN ADMIN (HARDCODED)
// Username: admin
// Password: admin123
// ============================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ganti sesuai keinginan kamu
    $admin_username = "admin";
    $admin_password = "admin123";

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin'] = $username;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin - Perpustakaan</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #fff0f6, #f5f1ff, #edf8ff);
    color: #5b5573;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

/* BUBBLES */
.bubble { position: fixed; border-radius: 50%; filter: blur(2px); opacity: 0.5; z-index: 0; }
.bubble.one { width: 300px; height: 300px; background: #ffdce9; top: -80px; left: -100px; }
.bubble.two { width: 350px; height: 350px; background: #dceeff; bottom: -120px; right: -120px; }
.bubble.three { width: 200px; height: 200px; background: #e9ddff; top: 40%; left: 60%; }

/* CARD */
.login-wrapper { position: relative; z-index: 1; width: 100%; max-width: 420px; padding: 20px; }

.login-card {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(20px);
    border-radius: 28px;
    padding: 45px 40px;
    box-shadow: 0 20px 60px rgba(120, 100, 170, 0.18);
    border: 1px solid rgba(255,255,255,0.6);
    text-align: center;
    animation: fadeUp 0.6s ease;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.login-logo { font-size: 42px; margin-bottom: 10px; display: block; animation: pop 0.6s ease; }
@keyframes pop {
    0% { transform: scale(0); }
    70% { transform: scale(1.15); }
    100% { transform: scale(1); }
}

.login-title { font-size: 26px; font-weight: bold; color: #7664ad; margin-bottom: 6px; }
.login-subtitle { font-size: 13px; color: #817a94; margin-bottom: 30px; }

/* FORM */
.form-group { text-align: left; margin-bottom: 18px; }
.form-group label { display: block; font-size: 13px; font-weight: bold; color: #665b83; margin-bottom: 8px; }
.form-group input {
    width: 100%;
    padding: 14px 16px;
    border-radius: 14px;
    border: 1px solid #e2ddef;
    background: rgba(255,255,255,0.9);
    color: #5b5573;
    font-size: 14px;
    outline: none;
    transition: 0.2s;
}
.form-group input:focus {
    border-color: #a99bd8;
    box-shadow: 0 0 0 4px rgba(169, 155, 216, 0.18);
    background: #ffffff;
}

/* BUTTON */
.btn-login {
    width: 100%;
    padding: 15px;
    border: none;
    border-radius: 14px;
    background: linear-gradient(135deg, #a99bd8, #8f7dc4);
    color: white;
    font-weight: bold;
    font-size: 15px;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0 10px 25px rgba(130, 110, 190, 0.25);
    margin-top: 5px;
}
.btn-login:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(130, 110, 190, 0.35); }

/* ERROR */
.error-box {
    background: #ffe4ec;
    color: #c15d78;
    padding: 12px 15px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: bold;
    margin-bottom: 20px;
    text-align: center;
    border: 1px solid #ffc8d8;
    animation: shake 0.4s ease;
}
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.login-footer { margin-top: 25px; font-size: 12px; color: #a098b5; text-align: center; }

@media(max-width: 480px) {
    .login-card { padding: 35px 25px; }
    .login-title { font-size: 22px; }
}
</style>
</head>
<body>

<div class="bubble one"></div>
<div class="bubble two"></div>
<div class="bubble three"></div>

<div class="login-wrapper">
    <div class="login-card">

        <span class="login-logo">📚</span>

        <div class="login-title">Perpustakaan</div>
        <div class="login-subtitle">Login sebagai Admin</div>

        <?php if ($error != ""): ?>
            <div class="error-box">❌ <?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>👤 Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required autofocus>
            </div>
            <div class="form-group">
                <label>🔒 Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn-login">🚀 Login Admin</button>
        </form>

        <div class="login-footer">© 2026 Aplikasi Data Buku Perpustakaan</div>

    </div>
</div>

</body>
</html>