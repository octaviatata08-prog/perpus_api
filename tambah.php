<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
$sukses = "";
if (isset($_SESSION['sukses'])) { $sukses = $_SESSION['sukses']; unset($_SESSION['sukses']); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Buku - Perpustakaan</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: Arial, sans-serif; background: linear-gradient(135deg, #fff0f6, #f5f1ff, #edf8ff); color: #5b5573; min-height: 100vh; }

/* NAVBAR */
.navbar { background: rgba(255,255,255,0.78); backdrop-filter: blur(15px); display: flex; align-items: center; padding: 18px 35px; box-shadow: 0 5px 25px rgba(120,110,160,0.08); flex-wrap: wrap; gap: 15px; position: sticky; top: 0; z-index: 100; }
.logo { font-size: 21px; font-weight: bold; color: #7664ad; margin-right: 30px; }
.navbar a { text-decoration: none; color: #68627e; font-size: 14px; font-weight: 600; margin-right: 20px; transition: 0.2s; white-space: nowrap; }
.navbar a:hover { color: #8b79c6; }
.logout-btn { background: #ff7aa8 !important; color: white !important; padding: 10px 20px !important; border-radius: 20px !important; margin-left: auto !important; transition: 0.3s; }
.logout-btn:hover { background: #ff5c93 !important; color: white !important; transform: translateY(-2px); }

/* CONTAINER */
.container { width: 92%; max-width: 800px; margin: auto; padding: 30px 0 60px; }
h1 { color: #514a70; text-align: center; margin-bottom: 8px; font-size: 26px; }
.subtitle { color: #817a94; text-align: center; margin-bottom: 25px; font-size: 14px; }

/* FORM */
.form-box { background: rgba(255,255,255,0.82); backdrop-filter: blur(15px); border-radius: 22px; padding: 30px; box-shadow: 0 10px 30px rgba(100,90,140,0.10); }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #665b83; font-size: 14px; }
.form-group input, .form-group select { width: 100%; padding: 13px 15px; border-radius: 13px; border: 1px solid #e2ddef; background: white; color: #5b5573; outline: none; font-size: 14px; }
.form-group input:focus, .form-group select:focus { border-color: #a99bd8; box-shadow: 0 0 0 3px rgba(169,155,216,0.15); }
.btn-submit { width: 100%; padding: 15px; border: none; border-radius: 14px; background: linear-gradient(135deg, #a99bd8, #8f7dc4); color: white; font-weight: bold; font-size: 15px; cursor: pointer; transition: 0.3s; box-shadow: 0 8px 20px rgba(130,110,190,0.2); }
.btn-submit:hover { transform: translateY(-2px); }

/* NOTIF */
.notif-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(80, 70, 110, 0.25); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; z-index: 9999; opacity: 0; visibility: hidden; transition: 0.3s ease; }
.notif-overlay.show { opacity: 1; visibility: visible; }
.notif-box { background: rgba(255, 255, 255, 0.92); backdrop-filter: blur(20px); padding: 35px 45px; border-radius: 26px; box-shadow: 0 20px 50px rgba(120, 100, 170, 0.25); text-align: center; max-width: 380px; transform: scale(0.85); transition: 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); border: 1px solid rgba(255,255,255,0.6); }
.notif-overlay.show .notif-box { transform: scale(1); }
.notif-icon { font-size: 55px; margin-bottom: 12px; display: block; }
.notif-title { font-size: 20px; font-weight: bold; color: #7664ad; margin-bottom: 8px; }
.notif-message { font-size: 14px; color: #817a94; margin-bottom: 22px; line-height: 1.5; }
.notif-btn { background: linear-gradient(135deg, #a99bd8, #8f7dc4); color: white; border: none; padding: 12px 35px; border-radius: 14px; font-weight: bold; font-size: 14px; cursor: pointer; }
.notif-btn:hover { transform: translateY(-2px); }

/* RESPONSIVE */
@media(max-width: 768px) {
    .navbar { padding: 15px 20px; flex-direction: column; align-items: flex-start; gap: 12px; }
    .logo { margin-right: 0; margin-bottom: 5px; font-size: 18px; }
    .nav-links { display: flex; flex-wrap: wrap; gap: 8px; width: 100%; }
    .navbar a { margin-right: 0; font-size: 12px; padding: 6px 10px; background: #f5f1ff; border-radius: 10px; }
    .logout-btn { margin-left: 0 !important; width: 100%; text-align: center; margin-top: 5px; }
    h1 { font-size: 22px; }
    .subtitle { font-size: 12px; }
    .form-box { padding: 20px; border-radius: 18px; }
    .notif-box { padding: 25px 20px; margin: 20px; }
    .notif-icon { font-size: 45px; }
    .notif-title { font-size: 18px; }
    .notif-message { font-size: 13px; }
}
@media(max-width: 480px) {
    .container { width: 94%; padding: 20px 0 40px; }
    h1 { font-size: 20px; }
    .form-group label { font-size: 13px; }
    .form-group input, .form-group select { font-size: 13px; padding: 11px 13px; }
    .btn-submit { font-size: 14px; padding: 13px; }
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
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</nav>

<div class="container">
    <h1>➕ Tambah Buku</h1>
    <p class="subtitle">Tambahkan data buku baru ke perpustakaan 📚</p>

    <div class="form-box">
        <form action="proses_tambah.php" method="POST">
            <div class="form-group"><label>📕 Kode Buku</label><input type="text" name="kode_buku" placeholder="Contoh: BK006" required></div>
            <div class="form-group"><label>📖 Judul Buku</label><input type="text" name="judul" placeholder="Masukkan judul buku" required></div>
            <div class="form-group"><label>✍️ Penulis</label><input type="text" name="penulis" placeholder="Nama penulis" required></div>
            <div class="form-group">
                <label>📁 Kategori</label>
                <select name="kategori" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Teknologi">Teknologi</option>
                    <option value="Novel">Novel</option>
                    <option value="Pendidikan">Pendidikan</option>
                    <option value="Database">Database</option>
                    <option value="Fiksi">Fiksi</option>
                </select>
            </div>
            <div class="form-group"><label>📅 Tahun Terbit</label><input type="number" name="tahun_terbit" placeholder="Contoh: 2024" required></div>
            <div class="form-group"><label>🏢 Penerbit</label><input type="text" name="penerbit" placeholder="Nama penerbit" required></div>
            <button type="submit" class="btn-submit">💾 Simpan Buku</button>
        </form>
    </div>
</div>

<?php if ($sukses != ""): ?>
<div class="notif-overlay" id="notifOverlay">
    <div class="notif-box">
        <span class="notif-icon"><?= stripos($sukses,'gagal') !== false ? '❌' : '🎉' ?></span>
        <div class="notif-title"><?= stripos($sukses,'gagal') !== false ? 'Oops!' : 'Berhasil!' ?></div>
        <div class="notif-message"><?= htmlspecialchars($sukses); ?></div>
        <button class="notif-btn" onclick="tutupNotif()">Oke, Mengerti</button>
    </div>
</div>
<script>
window.addEventListener('load', () => setTimeout(() => document.getElementById('notifOverlay').classList.add('show'), 150));
function tutupNotif() { const o = document.getElementById('notifOverlay'); o.classList.remove('show'); setTimeout(() => o.style.display = 'none', 300); }
document.getElementById('notifOverlay').addEventListener('click', e => { if (e.target === e.currentTarget) tutupNotif(); });
</script>
<?php endif; ?>
</body>
</html>