<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM books ORDER BY id ASC");
$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

// Format JSON yang cantik
$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>API JSON - Perpustakaan</title>
<style>
* { box-sizing: border-box; }
body {
    margin: 0;
    font-family: 'Courier New', monospace;
    background: linear-gradient(135deg, #fff0f6, #f5f1ff, #edf8ff);
    color: #5b5573;
    min-height: 100vh;
}

.navbar {
    background: rgba(255,255,255,0.78);
    backdrop-filter: blur(15px);
    display: flex;
    align-items: center;
    padding: 18px 35px;
    box-shadow: 0 5px 25px rgba(120,110,160,0.08);
    flex-wrap: wrap;
    gap: 15px;
    position: sticky;
    top: 0;
    z-index: 100;
    font-family: Arial, sans-serif;
}
.logo { font-size: 21px; font-weight: bold; color: #7664ad; margin-right: 30px; }
.navbar a { text-decoration: none; color: #68627e; font-size: 14px; font-weight: 600; margin-right: 20px; transition: 0.2s; white-space: nowrap; }
.navbar a:hover { color: #8b79c6; }
.logout-btn { background: #ff7aa8 !important; color: white !important; padding: 10px 20px !important; border-radius: 20px !important; margin-left: auto !important; }
.logout-btn:hover { background: #ff5c93 !important; color: white !important; }

.container { width: 92%; max-width: 1250px; margin: auto; padding: 30px 0 60px; }
h1 { color: #514a70; margin-bottom: 8px; font-size: 26px; font-family: Arial, sans-serif; }
.subtitle { color: #817a94; margin-bottom: 25px; font-size: 14px; font-family: Arial, sans-serif; }

.json-box {
    background: #1e1b2e;
    border-radius: 22px;
    padding: 25px;
    box-shadow: 0 15px 40px rgba(80, 60, 130, 0.25);
    overflow-x: auto;
    position: relative;
}

.copy-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: linear-gradient(135deg, #a99bd8, #8f7dc4);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 10px;
    font-weight: bold;
    font-size: 12px;
    cursor: pointer;
    font-family: Arial, sans-serif;
    transition: 0.3s;
}
.copy-btn:hover { transform: translateY(-2px); }

pre {
    margin: 0;
    color: #c4b5fd;
    font-size: 13px;
    line-height: 1.7;
    white-space: pre-wrap;
    word-wrap: break-word;
}
pre .key { color: #ff9ac7; }
pre .string { color: #a5f3c4; }
pre .number { color: #fcd34d; }
pre .boolean { color: #93c5fd; }
pre .null { color: #f87171; }

/* Syntax highlighting manual (simple) */
.token-key { color: #ff9ac7; }
.token-string { color: #a5f3fc; }
.token-number { color: #fcd34d; }

@media(max-width: 768px) {
    .navbar { padding: 15px 20px; flex-direction: column; align-items: flex-start; gap: 12px; }
    .logo { margin-right: 0; margin-bottom: 5px; font-size: 18px; }
    .nav-links { display: flex; flex-wrap: wrap; gap: 8px; width: 100%; }
    .navbar a { margin-right: 0; font-size: 12px; padding: 6px 10px; background: #f5f1ff; border-radius: 10px; }
    .logout-btn { margin-left: 0 !important; width: 100%; text-align: center; margin-top: 5px; }
    h1 { font-size: 20px; }
    .json-box { padding: 18px; border-radius: 18px; }
    pre { font-size: 11px; }
    .copy-btn { top: 10px; right: 10px; padding: 6px 12px; font-size: 11px; }
}
</style>
</head>
<body>

<nav class="navbar">
    <div class="logo">📚 PERPUSTAKAAN</div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="data_buku.php">Data Buku</a>
        <a href="tambah.php">Tambah Buku</a>
        <a href="laporan_kategori.php">Laporan Kategori</a>
        <a href="export_excel.php">Export Excel</a>
        <a href="export_pdf.php">Export PDF</a>
        <a href="data_json_view.php">API JSON</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</nav>

<div class="container">
    <h1>🔗 API JSON - Data Buku</h1>
    <p class="subtitle">Kode JSON mentah dari data buku perpustakaan. Endpoint: <strong>data_json.php</strong></p>

    <div class="json-box">
        <button class="copy-btn" onclick="copyJSON()">📋 Copy</button>
        <pre id="jsonContent"><?= htmlspecialchars($json); ?></pre>
    </div>
</div>

<script>
function copyJSON() {
    const text = document.getElementById("jsonContent").innerText;
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.querySelector(".copy-btn");
        btn.innerHTML = "✅ Tersalin!";
        setTimeout(() => btn.innerHTML = "📋 Copy", 2000);
    });
}
</script>
</body>
</html>