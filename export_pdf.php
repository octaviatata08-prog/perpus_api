<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php';

// ===== CEK APAKAH ADA ID YANG DIPILIH =====
$ids = isset($_GET['ids']) ? mysqli_real_escape_string($koneksi, $_GET['ids']) : '';

$where = '';
if ($ids !== '') {
    $where = "WHERE id IN ($ids)";
}

// ===== PAGINATION =====
$limit = 5;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, trim($_GET['search'])) : '';

// Build WHERE (gabung ids + search)
$where_parts = [];
if ($ids !== '') $where_parts[] = "id IN ($ids)";
if ($search !== '') $where_parts[] = "(judul LIKE '%$search%' OR penulis LIKE '%$search%' OR kode_buku LIKE '%$search%' OR kategori LIKE '%$search%' OR penerbit LIKE '%$search%')";
$where = count($where_parts) > 0 ? 'WHERE ' . implode(' AND ', $where_parts) : '';

$total_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM books $where"))['total'];
$total_page = ceil($total_data / $limit);

$query = mysqli_query($koneksi, "SELECT * FROM books $where ORDER BY id ASC LIMIT $limit OFFSET $offset");
if (!$query) die("Query error: " . mysqli_error($koneksi));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Buku - PDF</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background: linear-gradient(135deg, #f5f0ff 0%, #ffe8f0 100%); min-height: 100vh; }

        .navbar {
            background: #fff; padding: 15px 40px; display: flex;
            align-items: center; justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky; top: 0; z-index: 100;
        }
        .navbar .brand { font-size: 20px; font-weight: bold; color: #6c5ce7; }
        .navbar .menu { display: flex; gap: 25px; align-items: center; }
        .navbar .menu a { text-decoration: none; color: #555; font-size: 14px; font-weight: 500; }
        .navbar .menu a:hover, .navbar .menu a.active { color: #6c5ce7; }
        .navbar .logout { background: #ff6b9d; color: #fff !important; padding: 8px 20px; border-radius: 20px; font-weight: 600; }

        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .page-title { font-size: 22px; color: #333; margin-bottom: 5px; text-align: center; }
        .page-subtitle { font-size: 13px; color: #888; margin-bottom: 20px; text-align: center; }

        .card { background: #fff; border-radius: 16px; padding: 20px 25px; box-shadow: 0 4px 20px rgba(108, 92, 231, 0.08); margin-bottom: 20px; }

        .search-box { display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; }
        .search-box input { flex: 1; min-width: 200px; padding: 10px 14px; border: 1px solid #e0d7f5; border-radius: 10px; font-size: 13px; background: #faf8ff; outline: none; }
        .search-box input:focus { border-color: #6c5ce7; background: #fff; }

        .btn { padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.3s ease; }
        .btn-cari { background: #6c5ce7; color: #fff; }
        .btn-cari:hover { background: #5a4bd1; transform: translateY(-3px); }
        .btn-reset { background: #f3efff; color: #6c5ce7; }
        .btn-reset:hover { background: #e5dcff; }
        .btn-print { display: block; margin: 0 auto 20px auto; padding: 10px 25px; background: #6c5ce7; color: #fff; border: none; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer; transition: 0.3s; }
        .btn-print:hover { background: #5a4bd1; transform: translateY(-3px); }

        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th { background: #6c5ce7; color: #fff; padding: 12px 10px; text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; }
        tbody td { padding: 12px 10px; border-bottom: 1px solid #f0ecfa; color: #444; }
        tbody tr:nth-child(even) { background: #f9f7ff; }
        tbody tr:hover { background: #faf8ff; }
        .empty { text-align: center; padding: 30px; color: #aaa; font-style: italic; }

        .pagination { display: flex; justify-content: center; gap: 6px; margin-top: 20px; }
        .pagination a, .pagination span { padding: 8px 14px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; color: #6c5ce7; background: #f3efff; }
        .pagination a:hover { background: #6c5ce7; color: #fff; }
        .pagination .active { background: #6c5ce7; color: #fff; }
        .pagination .disabled { color: #ccc; background: #f7f7f7; }

        .data-info { font-size: 12px; color: #888; text-align: right; margin-bottom: 5px; }
        .filter-info { font-size: 12px; color: #6c5ce7; font-weight: 600; margin-bottom: 10px; text-align: center; }

        @media print {
            .navbar, .btn-print, .pagination, .data-info, .search-box, .no-print { display: none !important; }
            body { background: #fff !important; }
            .card { box-shadow: none; padding: 0; }
            .container { margin: 0; max-width: 100%; padding: 0; }
            thead th { background: #6c5ce7 !important; color: #fff !important; -webkit-print-color-adjust: exact; }
        }

        /* ============================================
           RESPONSIVE TAMBAHAN — TIDAK MENGUBAH DESKTOP
           ============================================ */

        @media (max-width: 1024px) {
            .container { max-width: 95%; }
        }

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

            /* Tombol print */
            .btn-print {
                width: 100% !important;
                font-size: 13px !important;
                padding: 10px 20px !important;
            }

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

            /* Pagination */
            .pagination {
                flex-wrap: wrap !important;
                gap: 4px !important;
                margin-top: 15px !important;
            }
            .pagination a, .pagination span {
                padding: 6px 10px !important;
                font-size: 12px !important;
            }

            .data-info { font-size: 11px !important; }
        }

        @media (max-width: 480px) {
            .navbar .brand { font-size: 14px !important; }
            .navbar .menu a { font-size: 10px !important; padding: 5px 8px !important; }

            .page-title { font-size: 16px !important; }

            table thead th,
            table tbody td {
                padding: 8px 6px !important;
                font-size: 11px !important;
            }

            .pagination a, .pagination span {
                padding: 5px 8px !important;
                font-size: 11px !important;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="brand">📚 PERPUSTAKAAN</div>
    <div class="menu">
        <a href="dashboard.php">Dashboard</a>
        <a href="data_buku.php">Data Buku</a>
        <a href="tambah_buku.php">Tambah Buku</a>
        <a href="laporan_kategori.php">Laporan Kategori</a>
        <a href="export_excel.php">Export Excel</a>
        <a href="export_pdf.php" class="active">Export PDF</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="container">

    <h1 class="page-title">📕 Laporan Data Buku</h1>
    <p class="page-subtitle">Dicetak pada: <?= date('d-m-Y H:i'); ?></p>

    <?php if ($ids !== ''): ?>
        <div class="filter-info">📌 Menampilkan <?= $total_data; ?> data terpilih</div>
    <?php endif; ?>

    <div class="card no-print">
        <form method="GET" action="">
            <?php if ($ids !== ''): ?>
                <input type="hidden" name="ids" value="<?= htmlspecialchars($ids); ?>">
            <?php endif; ?>
            <div class="search-box">
                <input type="text" name="search" placeholder="🔍 Cari judul, penulis, kode..." value="<?= htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-cari">🔍 Cari</button>
                <?php if ($search): ?>
                    <a href="export_pdf.php<?= $ids ? '?ids=' . urlencode($ids) : ''; ?>" class="btn btn-reset">✖ Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <button class="btn-print no-print" onclick="window.print()">🖨️ Cetak / Simpan sebagai PDF</button>

    <div class="data-info no-print">Menampilkan <?= mysqli_num_rows($query); ?> dari <?= $total_data; ?> data</div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Kategori</th>
                    <th>Tahun</th>
                    <th>Penerbit</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($query) > 0): ?>
                    <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['kode_buku']; ?></td>
                            <td><?= $row['judul']; ?></td>
                            <td><?= $row['penulis']; ?></td>
                            <td><?= $row['kategori']; ?></td>
                            <td><?= $row['tahun_terbit']; ?></td>
                            <td><?= $row['penerbit']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="empty">📭 Data tidak ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($total_page > 1): ?>
            <div class="pagination no-print">
                <?php if ($page > 1): ?>
                    <a href="?ids=<?= urlencode($ids); ?>&search=<?= urlencode($search); ?>&page=<?= $page - 1; ?>">« Prev</a>
                <?php else: ?>
                    <span class="disabled">« Prev</span>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_page; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="active"><?= $i; ?></span>
                    <?php else: ?>
                        <a href="?ids=<?= urlencode($ids); ?>&search=<?= urlencode($search); ?>&page=<?= $i; ?>"><?= $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $total_page): ?>
                    <a href="?ids=<?= urlencode($ids); ?>&search=<?= urlencode($search); ?>&page=<?= $page + 1; ?>">Next »</a>
                <?php else: ?>
                    <span class="disabled">Next »</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>