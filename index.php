<?php

session_start();

// Jika admin sudah login,
// langsung masuk ke dashboard
if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}

// Jika belum login,
// arahkan ke halaman login
header("Location: login.php");
exit;

?>