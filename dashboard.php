<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php';

// ===== STATISTIK =====
$total_buku     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS t FROM books"))['t'];
$total_kategori = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(DISTINCT kategori) AS t FROM books"))['t'];
$total_penulis  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(DISTINCT penulis) AS t FROM books"))['t'];
$tahun_terbaru  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT MAX(tahun_terbit) AS t FROM books"))['t'];

// ===== DATA CHART KATEGORI =====
$q_kategori = mysqli_query($koneksi, "SELECT kategori, COUNT(*) AS jumlah FROM books GROUP BY kategori ORDER BY jumlah DESC");
$kategori_labels = [];
$kategori_data   = [];
while ($k = mysqli_fetch_assoc($q_kategori)) {
    $kategori_labels[] = $k['kategori'];
    $kategori_data[]   = $k['jumlah'];
}

// ===== BUKU TERBARU =====
$buku_terbaru = mysqli_query($koneksi, "SELECT * FROM books ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Perpustakaan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .navbar:hover {
            box-shadow: 0 8px 25px rgba(108, 92, 231, 0.15);
        }
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
            position: relative;
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
            box-shadow: 0 10px 25px rgba(255, 107, 157, 0.5),
                        0 0 0 4px rgba(255, 107, 157, 0.15);
        }

        /* ================= CONTAINER ================= */
        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
            transform-style: preserve-3d;
        }

        /* ================= WELCOME ================= */
        .welcome { margin-bottom: 20px; transition: all 0.3s ease; }
        .welcome:hover { transform: translateX(8px); }
        .welcome h1 { font-size: 22px; color: #333; margin-bottom: 4px; }
        .welcome p { font-size: 13px; color: #888; }

        /* ================= STATISTIK ================= */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(108, 92, 231, 0.08),
                        0 1px 2px rgba(0,0,0,0.02);
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at center, rgba(108, 92, 231, 0.08) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }
        .stat-card:hover {
            transform: translateY(-8px) scale(1.04) rotateX(4deg);
            box-shadow: 0 20px 40px rgba(108, 92, 231, 0.25),
                        0 8px 16px rgba(108, 92, 231, 0.15),
                        0 0 0 4px rgba(108, 92, 231, 0.06);
        }
        .stat-card:hover::before { opacity: 1; }
        .stat-card .icon { font-size: 22px; margin-bottom: 8px; transition: transform 0.4s ease; }
        .stat-card:hover .icon { transform: scale(1.2) rotate(-8deg); }
        .stat-card .label { font-size: 12px; color: #888; margin-bottom: 4px; }
        .stat-card .value {
            font-size: 24px; font-weight: bold; color: #6c5ce7;
            transition: all 0.4s ease;
        }
        .stat-card:hover .value {
            text-shadow: 0 4px 12px rgba(108, 92, 231, 0.4);
            transform: scale(1.05);
        }

        /* ================= GRID UTAMA ================= */
        .main-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
            align-items: start;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 20px 25px;
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
        .card h3 {
            font-size: 15px; color: #333; margin-bottom: 15px;
            display: flex; align-items: center; gap: 8px;
            transition: transform 0.3s ease;
        }
        .card:hover h3 { transform: translateX(4px); }

        /* CHART */
        .chart-wrapper {
            position: relative;
            height: 220px;
            max-width: 320px;
            margin: 0 auto;
            transition: transform 0.4s ease;
        }
        .card:hover .chart-wrapper { transform: scale(1.03); }

        /* ================= MENU CEPAT ================= */
        .menu-cepat { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .menu-cepat a {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            padding: 12px;
            background: linear-gradient(135deg, #f3efff, #ede4ff);
            color: #6c5ce7;
            text-decoration: none;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 2px 6px rgba(108, 92, 231, 0.08);
        }
        .menu-cepat a::before {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            width: 0; height: 0;
            background: radial-gradient(circle, rgba(255,255,255,0.5), transparent);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.5s ease, height 0.5s ease;
        }
        .menu-cepat a:hover::before {
            width: 300px; height: 300px;
        }
        .menu-cepat a:hover {
            background: linear-gradient(135deg, #6c5ce7, #8a7bff);
            color: #fff;
            transform: translateY(-5px) scale(1.06);
            box-shadow: 0 12px 24px rgba(108, 92, 231, 0.4),
                        0 0 0 4px rgba(108, 92, 231, 0.1);
        }

        /* ================= TABEL ================= */
        table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; }
        thead th {
            background: #f3efff; color: #6c5ce7;
            padding: 12px 10px; text-align: left;
            font-weight: 600; font-size: 11px;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }
        tbody td {
            padding: 12px 10px;
            border-bottom: 1px solid #f0ecfa;
            color: #444;
            transition: all 0.3s ease;
            position: relative;
        }
        tbody tr {
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: pointer;
        }
        tbody tr:hover {
            background: linear-gradient(90deg, #faf8ff, #f3efff, #faf8ff);
            transform: translateX(6px) scale(1.01);
            box-shadow: -4px 4px 16px rgba(108, 92, 231, 0.12),
                        0 4px 12px rgba(108, 92, 231, 0.08);
        }
        tbody tr:hover td { color: #6c5ce7; font-weight: 500; }

        .badge {
            display: inline-block; padding: 3px 10px; border-radius: 20px;
            background: #e5dcff; color: #6c5ce7; font-size: 11px; font-weight: 600;
            transition: all 0.3s ease;
        }
        tbody tr:hover .badge {
            background: #6c5ce7; color: #fff;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.4);
        }

        /* ================= RESPONSIVE LAMA ================= */
        @media (max-width: 768px) {
            .stats { grid-template-columns: repeat(2, 1fr); }
            .main-grid { grid-template-columns: 1fr; }
            .navbar { padding: 15px 20px; }
            .navbar .menu { gap: 10px; }
            .navbar .menu a { font-size: 12px; padding: 6px 10px; }
        }

        /* ============================================
           RESPONSIVE TAMBAHAN — TIDAK MENGUBAH DESKTOP
           ============================================ */

        /* Tablet */
        @media (max-width: 1024px) {
            .container { max-width: 95%; }
            .stats { grid-template-columns: repeat(2, 1fr); }
            .main-grid { grid-template-columns: 1fr; }
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

            .welcome h1 { font-size: 17px !important; }
            .welcome p { font-size: 12px !important; }

            .stats {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 12px !important;
            }
            .stat-card { padding: 15px !important; }
            .stat-card .icon { font-size: 18px !important; }
            .stat-card .label { font-size: 11px !important; }
            .stat-card .value { font-size: 20px !important; }

            .card {
                padding: 15px 16px !important;
                border-radius: 14px !important;
                margin-bottom: 15px !important;
            }
            .card h3 { font-size: 14px !important; }

            .chart-wrapper { height: 200px !important; max-width: 260px !important; }
            .main-grid { gap: 15px !important; }

            .menu-cepat { grid-template-columns: 1fr 1fr !important; gap: 8px !important; }
            .menu-cepat a {
                font-size: 11px !important;
                padding: 10px 8px !important;
            }

            /* Tabel — scroll horizontal */
            .card table {
                display: block !important;
                overflow-x: auto !important;
                white-space: nowrap !important;
                -webkit-overflow-scrolling: touch !important;
            }
            table thead th,
            table tbody td {
                padding: 10px 8px !important;
                font-size: 12px !important;
            }
        }

        /* HP Kecil */
        @media (max-width: 480px) {
            .navbar .brand { font-size: 14px !important; }
            .navbar .menu a { font-size: 10px !important; padding: 5px 8px !important; }

            .stats { grid-template-columns: 1fr !important; }
            .main-grid { grid-template-columns: 1fr !important; }

            .welcome h1 { font-size: 15px !important; }

            .stat-card .value { font-size: 18px !important; }

            table thead th,
            table tbody td {
                padding: 8px 6px !important;
                font-size: 11px !important;
            }

            .menu-cepat { grid-template-columns: 1fr !important; }
            .chart-wrapper { height: 180px !important; max-width: 220px !important; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="brand">📚 PERPUSTAKAAN</div>
    <div class="menu">
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="data_buku.php">Data Buku</a>
        <a href="tambah_buku.php">Tambah Buku</a>
        <a href="laporan_kategori.php">Laporan Kategori</a>
        <a href="export_excel.php">Export Excel</a>
        <a href="export_pdf.php">Export PDF</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="container">

    <!-- WELCOME -->
    <div class="welcome">
        <h1>👋 Selamat Datang, admin!</h1>
        <p>Berikut ringkasan data perpustakaan hari ini.</p>
    </div>

    <!-- STATISTIK -->
    <div class="stats">
        <div class="stat-card">
            <div class="icon">📚</div>
            <div class="label">Total Buku</div>
            <div class="value"><?= $total_buku; ?></div>
        </div>
        <div class="stat-card">
            <div class="icon">📂</div>
            <div class="label">Jumlah Kategori</div>
            <div class="value"><?= $total_kategori; ?></div>
        </div>
        <div class="stat-card">
            <div class="icon">✍️</div>
            <div class="label">Jumlah Penulis</div>
            <div class="value"><?= $total_penulis; ?></div>
        </div>
        <div class="stat-card">
            <div class="icon">📅</div>
            <div class="label">Tahun Terbit Terbaru</div>
            <div class="value"><?= $tahun_terbaru; ?></div>
        </div>
    </div>

    <!-- CHART + MENU CEPAT -->
    <div class="main-grid">
        <div class="card">
            <h3>📊 Buku Berdasarkan Kategori</h3>
            <div class="chart-wrapper">
                <canvas id="chartKategori"></canvas>
            </div>
        </div>
        <div class="card">
            <h3>⚡ Menu Cepat</h3>
            <div class="menu-cepat">
                <a href="data_buku.php">📖 Data Buku</a>
                <a href="tambah_buku.php">➕ Tambah Buku</a>
                <a href="laporan_kategori.php">📊 Laporan Kategori</a>
                <a href="export_excel.php">📗 Export Excel</a>
                <a href="export_pdf.php">📕 Export PDF</a>
                <a href="api_buku.php">🔗 API JSON</a>
            </div>
        </div>
    </div>

    <!-- BUKU TERBARU -->
    <div class="card">
        <h3>🆕 Buku Terbaru</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Kategori</th>
                    <th>Tahun</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($b = mysqli_fetch_assoc($buku_terbaru)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $b['kode_buku']; ?></td>
                        <td><?= $b['judul']; ?></td>
                        <td><?= $b['penulis']; ?></td>
                        <td><span class="badge"><?= $b['kategori']; ?></span></td>
                        <td><?= $b['tahun_terbit']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
    const ctx = document.getElementById('chartKategori').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($kategori_labels); ?>,
            datasets: [{
                data: <?= json_encode($kategori_data); ?>,
                backgroundColor: ['#6c5ce7', '#ff6b9d', '#ffa94d', '#ffd43b', '#20c997', '#4dabf7'],
                borderWidth: 0,
                hoverOffset: 18,
                hoverBorderColor: '#fff',
                hoverBorderWidth: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1200,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 11 },
                        padding: 12,
                        boxWidth: 12,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(108, 92, 231, 0.95)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: false,
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 }
                }
            },
            cutout: '62%'
        }
    });
</script>

</body>
</html>