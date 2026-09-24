<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php';

$notif = '';
$notif_type = '';

if (isset($_POST['submit'])) {
    $kode_buku    = mysqli_real_escape_string($koneksi, $_POST['kode_buku']);
    $judul        = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $penulis      = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $kategori     = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $tahun_terbit = mysqli_real_escape_string($koneksi, $_POST['tahun_terbit']);
    $penerbit     = mysqli_real_escape_string($koneksi, $_POST['penerbit']);

    $query = "INSERT INTO books (kode_buku, judul, penulis, kategori, tahun_terbit, penerbit)
              VALUES ('$kode_buku', '$judul', '$penulis', '$kategori', '$tahun_terbit', '$penerbit')";

    if (mysqli_query($koneksi, $query)) {
        $notif = 'Buku berhasil ditambahkan!';
        $notif_type = 'success';
    } else {
        $notif = 'Gagal: ' . mysqli_error($koneksi);
        $notif_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - Perpustakaan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body {
            background: linear-gradient(135deg, #f5f0ff 0%, #ffe8f0 100%);
            min-height: 100vh;
            perspective: 1000px;
        }

        /* ================= NAVBAR ================= */
        .navbar {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 15px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(108, 92, 231, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
            transition: all 0.3s ease;
        }
        .navbar:hover { box-shadow: 0 8px 25px rgba(108, 92, 231, 0.15); }
        .navbar .brand {
            font-size: 20px; font-weight: bold; color: #6c5ce7;
            transition: all 0.3s ease;
        }
        .navbar .brand:hover {
            transform: scale(1.05) translateY(-2px);
            text-shadow: 0 4px 12px rgba(108, 92, 231, 0.4);
        }
        .navbar .menu { display: flex; gap: 25px; align-items: center; }
        .navbar .menu a {
            text-decoration: none; color: #555; font-size: 14px;
            font-weight: 500; padding: 8px 14px; border-radius: 10px;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .navbar .menu a:hover, .navbar .menu a.active {
            color: #6c5ce7;
            background: #f3efff;
            transform: translateY(-3px) scale(1.08);
            box-shadow: 0 8px 20px rgba(108, 92, 231, 0.25),
                        0 0 0 4px rgba(108, 92, 231, 0.08);
        }
        .navbar .logout {
            background: linear-gradient(135deg, #ff6b9d, #ff8fb8);
            color: #fff !important;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(255, 107, 157, 0.3);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .navbar .logout:hover {
            transform: translateY(-3px) scale(1.08);
            box-shadow: 0 10px 25px rgba(255, 107, 157, 0.5);
        }

        /* ================= CONTAINER ================= */
        .container {
            max-width: 700px;
            margin: 30px auto;
            padding: 0 20px;
            transform-style: preserve-3d;
        }

        /* ================= PAGE TITLE ================= */
        .page-title { font-size: 22px; color: #333; margin-bottom: 5px; }
        .page-subtitle { font-size: 13px; color: #888; margin-bottom: 20px; }

        /* ================= CARD ================= */
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 25px 30px;
            box-shadow: 0 4px 20px rgba(108, 92, 231, 0.08),
                        0 1px 2px rgba(0,0,0,0.02);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at center, rgba(108, 92, 231, 0.06) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }
        .card:hover {
            transform: translateY(-6px) scale(1.01);
            box-shadow: 0 20px 40px rgba(108, 92, 231, 0.18),
                        0 8px 16px rgba(108, 92, 231, 0.1),
                        0 0 0 4px rgba(108, 92, 231, 0.05);
        }
        .card:hover::before { opacity: 1; }

        /* ================= FORM ================= */
        .form-group { margin-bottom: 15px; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-bottom: 6px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e0d7f5;
            border-radius: 10px;
            font-size: 13px;
            background: #faf8ff;
            outline: none;
            transition: all 0.3s ease;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: #6c5ce7;
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.12),
                        0 4px 12px rgba(108, 92, 231, 0.1);
        }

        /* ================= TOMBOL ================= */
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        .btn {
            display: inline-block;
            padding: 10px 22px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .btn-primary {
            background: #6c5ce7;
            color: #fff;
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3);
        }
        .btn-primary:hover {
            background: #5a4bd1;
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 12px 24px rgba(108, 92, 231, 0.5);
        }
        .btn-batal {
            background: #f3efff;
            color: #6c5ce7;
        }
        .btn-batal:hover {
            background: #e5dcff;
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 8px 16px rgba(108, 92, 231, 0.2);
        }

        /* ================= RIPPLE ================= */
        .ripple { position: relative; overflow: hidden; }
        .ripple-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.65);
            transform: scale(0);
            animation: rippleAnim 0.6s ease-out;
            pointer-events: none;
            z-index: 1;
        }
        @keyframes rippleAnim {
            to { transform: scale(4); opacity: 0; }
        }

        /* ================= NOTIF CUSTOM ================= */
        .notif-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .notif-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        .notif-box {
            background: #fff;
            border-radius: 20px;
            padding: 35px 45px;
            text-align: center;
            box-shadow: 0 25px 60px rgba(108, 92, 231, 0.35);
            transform: scale(0.7) translateY(30px);
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            max-width: 400px;
            width: 90%;
        }
        .notif-overlay.show .notif-box {
            transform: scale(1) translateY(0);
        }
        .notif-icon {
            font-size: 55px;
            margin-bottom: 15px;
            display: inline-block;
            animation: bounceIcon 0.8s ease infinite alternate;
        }
        @keyframes bounceIcon {
            from { transform: translateY(0) scale(1); }
            to   { transform: translateY(-8px) scale(1.08); }
        }
        .notif-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .notif-title.success { color: #6c5ce7; }
        .notif-title.error { color: #d63031; }
        .notif-message {
            font-size: 14px;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        .notif-btn {
            background: linear-gradient(135deg, #6c5ce7, #8a7bff);
            color: #fff;
            border: none;
            padding: 12px 40px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(108, 92, 231, 0.4);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
        }
        .notif-btn:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 12px 28px rgba(108, 92, 231, 0.6);
        }
        .notif-btn:active {
            transform: translateY(-1px) scale(1.02);
        }

        /* ================= RESPONSIVE LAMA ================= */
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .navbar .menu { gap: 10px; flex-wrap: wrap; }
            .navbar .menu a { font-size: 12px; padding: 6px 10px; }
        }

        /* ============================================
           RESPONSIVE TAMBAHAN — TIDAK MENGUBAH DESKTOP
           ============================================ */

        /* Tablet */
        @media (max-width: 1024px) {
            .container { max-width: 95%; }
        }

        /* HP */
        @media (max-width: 768px) {
            .navbar {
                padding: 12px 16px !important;
                flex-wrap: wrap !important;
                gap: 10px !important;
            }
            .navbar .brand { font-size: 16px !important; }
            .navbar .menu {
                gap: 6px !important;
                flex-wrap: wrap !important;
                justify-content: flex-end !important;
                width: 100% !important;
            }
            .navbar .menu a {
                font-size: 11px !important;
                padding: 6px 10px !important;
            }
            .navbar .logout {
                padding: 6px 14px !important;
                font-size: 11px !important;
            }

            .container {
                margin: 15px auto !important;
                padding: 0 12px !important;
            }
            .page-title { font-size: 18px !important; }
            .page-subtitle { font-size: 12px !important; }

            .card {
                padding: 18px 16px !important;
                border-radius: 14px !important;
            }

            /* Form */
            .form-group input,
            .form-group select {
                font-size: 12px !important;
                padding: 9px 12px !important;
            }
            .form-group label { font-size: 12px !important; }

            /* Tombol */
            .btn {
                font-size: 12px !important;
                padding: 9px 16px !important;
            }
            .btn-group {
                flex-direction: column !important;
                gap: 8px !important;
            }
            .btn-group .btn {
                width: 100% !important;
                text-align: center !important;
                justify-content: center !important;
            }

            /* Notif */
            .notif-box { padding: 25px 20px !important; }
            .notif-icon { font-size: 42px !important; }
            .notif-title { font-size: 17px !important; }
            .notif-message { font-size: 13px !important; }
            .notif-btn { padding: 10px 30px !important; font-size: 13px !important; }
        }

        /* HP Kecil */
        @media (max-width: 480px) {
            .navbar .brand { font-size: 14px !important; }
            .navbar .menu a { font-size: 10px !important; padding: 5px 8px !important; }

            .page-title { font-size: 16px !important; }

            .card { padding: 15px 14px !important; }

            .btn { font-size: 11px !important; padding: 8px 14px !important; }
        }
    </style>
</head>
<body>

<!-- ================= NAVBAR ================= -->
<div class="navbar">
    <div class="brand">📚 PERPUSTAKAAN</div>
    <div class="menu">
        <a href="dashboard.php">Dashboard</a>
        <a href="data_buku.php">Data Buku</a>
        <a href="tambah_buku.php" class="active">Tambah Buku</a>
        <a href="laporan_kategori.php">Laporan Kategori</a>
        <a href="export_excel.php">Export Excel</a>
        <a href="export_pdf.php">Export PDF</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<!-- ================= CONTAINER ================= -->
<div class="container">

    <h1 class="page-title">➕ Tambah Buku</h1>
    <p class="page-subtitle">Isi form di bawah untuk menambahkan buku baru.</p>

    <div class="card">
        <form method="POST" action="" id="formTambah">
            <div class="form-group">
                <label>Kode Buku</label>
                <input type="text" name="kode_buku" placeholder="Contoh: BK021" required>
            </div>
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="judul" placeholder="Judul buku" required>
            </div>
            <div class="form-group">
                <label>Penulis</label>
                <input type="text" name="penulis" placeholder="Nama penulis" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Novel">Novel</option>
                    <option value="Teknologi">Teknologi</option>
                    <option value="Pendidikan">Pendidikan</option>
                    <option value="Database">Database</option>
                    <option value="Fiksi">Fiksi</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tahun Terbit</label>
                <input type="number" name="tahun_terbit" min="1900" max="2100" placeholder="Contoh: 2024" required>
            </div>
            <div class="form-group">
                <label>Penerbit</label>
                <input type="text" name="penerbit" placeholder="Nama penerbit" required>
            </div>

            <div class="btn-group">
                <button type="submit" name="submit" class="btn btn-primary">💾 Simpan</button>
                <a href="data_buku.php" class="btn btn-batal">↩️ Batal</a>
            </div>
        </form>
    </div>

</div>

<!-- ================= NOTIF CUSTOM ================= -->
<?php if ($notif): ?>
<div class="notif-overlay show" id="notifOverlay">
    <div class="notif-box">
        <div class="notif-icon"><?= $notif_type === 'success' ? '🎉' : '⚠️'; ?></div>
        <div class="notif-title <?= $notif_type; ?>">
            <?= $notif_type === 'success' ? 'Berhasil!' : 'Gagal!'; ?>
        </div>
        <div class="notif-message"><?= htmlspecialchars($notif); ?></div>
        <button class="notif-btn" onclick="tutupNotif()">Oke</button>
    </div>
</div>
<script>
    function tutupNotif() {
        document.getElementById('notifOverlay').classList.remove('show');
        <?php if ($notif_type === 'success'): ?>
            setTimeout(() => window.location = 'data_buku.php', 300);
        <?php endif; ?>
    }
    // Auto close setelah 2.5 detik (opsional)
    // setTimeout(tutupNotif, 2500);
</script>
<?php endif; ?>

<!-- ================= SCRIPT: RIPPLE ================= -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selector = [
        '.btn', '.btn-primary', '.btn-batal', '.notif-btn',
        '.navbar .menu a', '.navbar .logout',
        '.card'
    ].join(',');

    document.querySelectorAll(selector).forEach(el => {
        el.classList.add('ripple');

        el.addEventListener('click', function (e) {
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            const circle = document.createElement('span');
            circle.classList.add('ripple-circle');
            circle.style.width = circle.style.height = size + 'px';
            circle.style.left = x + 'px';
            circle.style.top = y + 'px';

            const old = this.querySelector('.ripple-circle');
            if (old) old.remove();

            this.appendChild(circle);
            setTimeout(() => circle.remove(), 600);
        });
    });
});
</script>

</body>
</html>