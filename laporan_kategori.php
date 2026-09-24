<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php';

// ===== SEARCH =====
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, trim($_GET['search'])) : '';

// Build WHERE
$where = '';
if ($search !== '') {
    $where = "WHERE kategori LIKE '%$search%'";
}

// Query kategori + jumlah buku + daftar judul
$query = mysqli_query($koneksi, "
    SELECT kategori, COUNT(*) AS jumlah,
           GROUP_CONCAT(judul SEPARATOR ', ') AS daftar_judul
    FROM books
    $where
    GROUP BY kategori
    ORDER BY jumlah DESC
");
if (!$query) die("Query error: " . mysqli_error($koneksi));

$total_kategori = mysqli_num_rows($query);

// Total buku (kalau search, hitung yang sesuai filter)
$total_buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS t FROM books $where"))['t'];
$rata_rata  = round($total_buku / max($total_kategori, 1), 1);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kategori - Perpustakaan</title>
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
            max-width: 1100px;
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
            padding: 20px 25px;
            box-shadow: 0 4px 20px rgba(108, 92, 231, 0.08),
                        0 1px 2px rgba(0,0,0,0.02);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
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

        /* ================= SEARCH BOX ================= */
        .search-box { display: flex; gap: 10px; flex-wrap: wrap; }
        .search-box input {
            flex: 1;
            min-width: 200px;
            padding: 10px 14px;
            border: 1px solid #e0d7f5;
            border-radius: 10px;
            font-size: 13px;
            background: #faf8ff;
            outline: none;
            transition: all 0.3s ease;
        }
        .search-box input:focus {
            border-color: #6c5ce7;
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.12),
                        0 4px 12px rgba(108, 92, 231, 0.1);
        }

        /* ================= TOMBOL ================= */
        .btn {
            display: inline-block;
            padding: 10px 20px;
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
        .btn-cari {
            background: #6c5ce7;
            color: #fff;
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3);
        }
        .btn-cari:hover {
            background: #5a4bd1;
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 12px 24px rgba(108, 92, 231, 0.5);
        }
        .btn-reset {
            background: #f3efff;
            color: #6c5ce7;
        }
        .btn-reset:hover {
            background: #e5dcff;
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 8px 16px rgba(108, 92, 231, 0.2);
        }
        .btn-cetak {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 26px;
            background: linear-gradient(135deg, #6c5ce7, #8a7bff);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 20px;
            box-shadow: 0 6px 18px rgba(108, 92, 231, 0.35);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
        }
        .btn-cetak:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 14px 30px rgba(108, 92, 231, 0.55);
        }
        .btn-cetak:active { transform: translateY(-1px) scale(1.02); }

        /* ================= STATISTIK ================= */
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
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
            text-align: center;
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

        /* Chart wrapper */
        .chart-wrapper {
            position: relative;
            height: 260px;
            width: 100%;
        }

        /* ================= TABEL ================= */
        table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; }
        thead th {
            background: #f3efff;
            color: #6c5ce7;
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }
        tbody td {
            padding: 12px 10px;
            border-bottom: 1px solid #f0ecfa;
            color: #444;
            transition: all 0.3s ease;
            vertical-align: top;
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
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            background: #e5dcff;
            color: #6c5ce7;
            font-size: 11px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        tbody tr:hover .badge {
            background: #6c5ce7;
            color: #fff;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.4);
        }
        .judul-list {
            font-size: 12px;
            color: #777;
            line-height: 1.6;
            transition: color 0.3s ease;
        }
        tbody tr:hover .judul-list { color: #6c5ce7; }

        .empty {
            text-align: center;
            padding: 30px;
            color: #aaa;
            font-style: italic;
        }

        /* ================= FILTER INFO ================= */
        .filter-info {
            font-size: 12px;
            color: #6c5ce7;
            font-weight: 600;
            margin-bottom: 15px;
            text-align: center;
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

        /* ================= PRINT MODE ================= */
        @media print {
            .navbar, .btn-cetak, .search-box, .no-print { display: none !important; }
            body {
                background: #fff !important;
                perspective: none;
            }
            .container { margin: 0; max-width: 100%; padding: 0; }
            .card {
                box-shadow: none !important;
                padding: 0 !important;
                margin-bottom: 15px !important;
                transform: none !important;
            }
            .card::before { display: none !important; }
            .stats { grid-template-columns: repeat(3, 1fr); gap: 15px; }
            .stat-card {
                box-shadow: none !important;
                border: 1px solid #e0d7f5;
                transform: none !important;
                padding: 15px;
            }
            thead th {
                background: #6c5ce7 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
            }
            tbody tr { transform: none !important; box-shadow: none !important; }
            .chart-wrapper { height: 220px; }
            .filter-info { display: block !important; }
        }

        /* ================= RESPONSIVE LAMA ================= */
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .navbar .menu { gap: 10px; flex-wrap: wrap; }
            .navbar .menu a { font-size: 12px; padding: 6px 10px; }
            .stats { grid-template-columns: 1fr; }
        }

        /* ============================================
           RESPONSIVE TAMBAHAN — TIDAK MENGUBAH DESKTOP
           ============================================ */

        /* Tablet */
        @media (max-width: 1024px) {
            .container { max-width: 95%; }
            .stats { grid-template-columns: repeat(3, 1fr); }
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
                padding: 15px 16px !important;
                border-radius: 14px !important;
                margin-bottom: 15px !important;
            }
            .card h3 { font-size: 14px !important; }

            /* Search Box */
            .search-box {
                flex-direction: column !important;
                gap: 8px !important;
            }
            .search-box input,
            .search-box button,
            .search-box a {
                width: 100% !important;
                justify-content: center !important;
                font-size: 12px !important;
            }

            /* Tombol cetak */
            .btn-cetak {
                width: 100% !important;
                justify-content: center !important;
                font-size: 13px !important;
                padding: 10px 20px !important;
            }

            /* Stats */
            .stats { grid-template-columns: 1fr !important; gap: 12px !important; }
            .stat-card { padding: 15px !important; }
            .stat-card .icon { font-size: 18px !important; }
            .stat-card .label { font-size: 11px !important; }
            .stat-card .value { font-size: 20px !important; }

            /* Chart */
            .chart-wrapper { height: 200px !important; }

            /* Tabel scroll */
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

            .page-title { font-size: 16px !important; }

            .card { padding: 12px 14px !important; }

            table thead th,
            table tbody td {
                padding: 8px 6px !important;
                font-size: 11px !important;
            }

            .chart-wrapper { height: 180px !important; }
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
        <a href="tambah_buku.php">Tambah Buku</a>
        <a href="laporan_kategori.php" class="active">Laporan Kategori</a>
        <a href="export_excel.php">Export Excel</a>
        <a href="export_pdf.php">Export PDF</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<!-- ================= CONTAINER ================= -->
<div class="container">

    <h1 class="page-title">📊 Laporan Kategori</h1>
    <p class="page-subtitle">Rekap jumlah buku berdasarkan kategori.</p>

    <!-- Search Box -->
    <div class="card no-print">
        <form method="GET" action="">
            <div class="search-box">
                <input type="text" name="search" placeholder="🔍 Cari kategori..." value="<?= htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-cari">🔍 Cari</button>
                <?php if ($search): ?>
                    <a href="laporan_kategori.php" class="btn btn-reset">✖ Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Info filter aktif -->
    <?php if ($search): ?>
        <div class="filter-info">
            🔍 Hasil pencarian untuk: "<strong><?= htmlspecialchars($search); ?></strong>" — <?= $total_kategori; ?> kategori ditemukan
        </div>
    <?php endif; ?>

    <!-- Tombol Cetak -->
    <button class="btn-cetak no-print" onclick="window.print()">🖨️ Cetak Laporan</button>

    <!-- Statistik -->
    <div class="stats">
        <div class="stat-card">
            <div class="icon">📂</div>
            <div class="label">Total Kategori</div>
            <div class="value"><?= $total_kategori; ?></div>
        </div>
        <div class="stat-card">
            <div class="icon">📚</div>
            <div class="label">Total Buku</div>
            <div class="value"><?= $total_buku; ?></div>
        </div>
        <div class="stat-card">
            <div class="icon">📈</div>
            <div class="label">Rata-rata per Kategori</div>
            <div class="value"><?= $rata_rata; ?></div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card">
        <h3>📈 Grafik Jumlah Buku per Kategori</h3>
        <div class="chart-wrapper">
            <canvas id="chartKategori"></canvas>
        </div>
    </div>

    <!-- Tabel Detail -->
    <div class="card">
        <h3>📋 Detail per Kategori</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Jumlah Buku</th>
                    <th>Daftar Judul</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $labels = []; $data = [];
                mysqli_data_seek($query, 0);
                while ($row = mysqli_fetch_assoc($query)):
                    $labels[] = $row['kategori'];
                    $data[]   = $row['jumlah'];
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><span class="badge"><?= $row['kategori']; ?></span></td>
                        <td><?= $row['jumlah']; ?> buku</td>
                        <td class="judul-list"><?= $row['daftar_judul']; ?></td>
                    </tr>
                <?php endwhile; ?>

                <?php if (empty($labels)): ?>
                    <tr>
                        <td colspan="4" class="empty">📭 Tidak ada kategori yang cocok dengan pencarian.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- ================= SCRIPT: CHART ================= -->
<script>
    new Chart(document.getElementById('chartKategori'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels); ?>,
            datasets: [{
                label: 'Jumlah Buku',
                data: <?= json_encode($data); ?>,
                backgroundColor: ['#6c5ce7', '#ff6b9d', '#ffa94d', '#ffd43b', '#20c997', '#4dabf7'],
                borderRadius: 8,
                hoverBackgroundColor: '#5a4bd1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 1200, easing: 'easeOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(108, 92, 231, 0.95)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: false
                }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 2 } }
            }
        }
    });
</script>

<!-- ================= SCRIPT: RIPPLE ================= -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selector = [
        '.btn', '.btn-cari', '.btn-reset', '.btn-cetak',
        '.navbar .menu a', '.navbar .logout',
        '.card', '.stat-card', 'tbody tr'
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