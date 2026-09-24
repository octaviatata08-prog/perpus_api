<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php';

// ===== PAGINATION =====
$limit = 5;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$total_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM books"))['total'];
$total_page = ceil($total_data / $limit);

$query = mysqli_query($koneksi, "SELECT * FROM books ORDER BY id ASC LIMIT $limit OFFSET $offset");
if (!$query) die("Query error: " . mysqli_error($koneksi));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku - Perpustakaan</title>
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
        .navbar .brand { font-size: 20px; font-weight: bold; color: #6c5ce7; transition: all 0.3s ease; }
        .navbar .brand:hover { transform: scale(1.05) translateY(-2px); text-shadow: 0 4px 12px rgba(108, 92, 231, 0.4); }
        .navbar .menu { display: flex; gap: 25px; align-items: center; }
        .navbar .menu a {
            text-decoration: none; color: #555; font-size: 14px;
            font-weight: 500; padding: 8px 14px; border-radius: 10px;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .navbar .menu a:hover, .navbar .menu a.active {
            color: #6c5ce7; background: #f3efff;
            transform: translateY(-3px) scale(1.08);
            box-shadow: 0 8px 20px rgba(108, 92, 231, 0.25), 0 0 0 4px rgba(108, 92, 231, 0.08);
        }
        .navbar .logout {
            background: linear-gradient(135deg, #ff6b9d, #ff8fb8);
            color: #fff !important;
            padding: 8px 20px; border-radius: 20px; font-weight: 600;
            box-shadow: 0 4px 12px rgba(255, 107, 157, 0.3);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .navbar .logout:hover { transform: translateY(-3px) scale(1.08); box-shadow: 0 10px 25px rgba(255, 107, 157, 0.5); }

        /* ================= CONTAINER ================= */
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; transform-style: preserve-3d; }
        .page-title { font-size: 22px; color: #333; margin-bottom: 5px; }
        .page-subtitle { font-size: 13px; color: #888; margin-bottom: 20px; }

        /* ================= CARD ================= */
        .card {
            background: #fff; border-radius: 16px; padding: 20px 25px;
            box-shadow: 0 4px 20px rgba(108, 92, 231, 0.08);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative; overflow: hidden; margin-bottom: 20px;
        }
        .card::before {
            content: ''; position: absolute; top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at center, rgba(108, 92, 231, 0.06) 0%, transparent 60%);
            opacity: 0; transition: opacity 0.4s ease; pointer-events: none;
        }
        .card:hover {
            transform: translateY(-6px) scale(1.01);
            box-shadow: 0 20px 40px rgba(108, 92, 231, 0.18), 0 8px 16px rgba(108, 92, 231, 0.1), 0 0 0 4px rgba(108, 92, 231, 0.05);
        }
        .card:hover::before { opacity: 1; }

        /* ================= FILTER ================= */
        .filter-title { font-size: 14px; font-weight: 600; color: #6c5ce7; margin-bottom: 12px; }
        .filter-row { display: flex; gap: 12px; flex-wrap: wrap; }
        .filter-row input, .filter-row select {
            flex: 1; min-width: 180px; padding: 10px 14px;
            border: 1px solid #e0d7f5; border-radius: 10px;
            font-size: 13px; background: #faf8ff; outline: none;
            transition: all 0.3s ease;
        }
        .filter-row input:focus, .filter-row select:focus {
            border-color: #6c5ce7; background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.12), 0 4px 12px rgba(108, 92, 231, 0.1);
        }

        /* ================= TOMBOL ================= */
        .btn {
            display: inline-block; padding: 9px 18px; border-radius: 10px;
            font-size: 13px; font-weight: 600; text-decoration: none;
            border: none; cursor: pointer; position: relative; overflow: hidden;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .btn-tambah {
            background: #6c5ce7; color: #fff; margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3);
        }
        .btn-tambah:hover {
            background: #5a4bd1; transform: translateY(-4px) scale(1.05);
            box-shadow: 0 12px 24px rgba(108, 92, 231, 0.5);
        }
        .btn-edit { background: #0984e3; color: #fff; padding: 6px 12px; font-size: 12px; border-radius: 8px; }
        .btn-edit:hover { transform: translateY(-4px) scale(1.1); box-shadow: 0 8px 20px rgba(9, 132, 227, 0.5); }
        .btn-hapus { background: #d63031; color: #fff; padding: 6px 12px; font-size: 12px; border-radius: 8px; }
        .btn-hapus:hover { transform: translateY(-4px) scale(1.1); box-shadow: 0 8px 20px rgba(214, 48, 49, 0.5); }

        /* ================= TOMBOL EXPORT TERPILIH ================= */
        .export-bar {
            display: flex; gap: 10px; align-items: center;
            flex-wrap: wrap;
            background: #fff;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(108, 92, 231, 0.08);
            margin-bottom: 15px;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .export-bar:hover {
            box-shadow: 0 8px 25px rgba(108, 92, 231, 0.15);
            transform: translateY(-2px);
        }
        .export-bar .label {
            font-size: 13px; font-weight: 600; color: #6c5ce7;
            margin-right: auto;
        }
        .export-bar .count {
            background: #f3efff; color: #6c5ce7;
            padding: 4px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 700;
        }
        .btn-export-excel {
            background: #1D6F42; color: #fff;
            padding: 9px 18px; border-radius: 10px;
            font-size: 13px; font-weight: 600; text-decoration: none; border: none;
            cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
            box-shadow: 0 4px 12px rgba(29, 111, 66, 0.3);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative; overflow: hidden;
        }
        .btn-export-excel:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 12px 24px rgba(29, 111, 66, 0.5);
        }
        .btn-export-pdf {
            background: #d63031; color: #fff;
            padding: 9px 18px; border-radius: 10px;
            font-size: 13px; font-weight: 600; text-decoration: none; border: none;
            cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
            box-shadow: 0 4px 12px rgba(214, 48, 49, 0.3);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative; overflow: hidden;
        }
        .btn-export-pdf:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 12px 24px rgba(214, 48, 49, 0.5);
        }
        .btn-export-all {
            background: #f3efff; color: #6c5ce7;
            padding: 9px 18px; border-radius: 10px;
            font-size: 13px; font-weight: 600; text-decoration: none;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .btn-export-all:hover {
            background: #e5dcff;
            transform: translateY(-4px) scale(1.05);
        }

        /* ================= CHECKBOX ================= */
        .checkbox-col { width: 40px; text-align: center; }
        input[type="checkbox"] {
            width: 18px; height: 18px;
            accent-color: #6c5ce7;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        input[type="checkbox"]:hover { transform: scale(1.2); }

        /* ================= TABEL ================= */
        table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; }
        thead th {
            background: #f3efff; color: #6c5ce7;
            padding: 12px 10px; text-align: left;
            font-weight: 600; font-size: 11px; text-transform: uppercase;
        }
        tbody td {
            padding: 12px 10px;
            border-bottom: 1px solid #f0ecfa;
            color: #444;
            transition: all 0.3s ease;
        }
        tbody tr { transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); cursor: pointer; }
        tbody tr:hover {
            background: linear-gradient(90deg, #faf8ff, #f3efff, #faf8ff);
            transform: translateX(6px) scale(1.01);
            box-shadow: -4px 4px 16px rgba(108, 92, 231, 0.12), 0 4px 12px rgba(108, 92, 231, 0.08);
        }
        tbody tr:hover td { color: #6c5ce7; font-weight: 500; }
        tbody tr.selected {
            background: linear-gradient(90deg, #f0ebff, #e5dcff, #f0ebff);
            box-shadow: inset 4px 0 0 #6c5ce7;
        }

        .empty { text-align: center; padding: 30px; color: #aaa; font-style: italic; }

        /* ================= PAGINATION ================= */
        .pagination { display: flex; justify-content: center; gap: 6px; margin-top: 20px; }
        .pagination a, .pagination span {
            padding: 8px 14px; border-radius: 8px; text-decoration: none;
            font-size: 13px; font-weight: 600;
            color: #6c5ce7; background: #f3efff;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .pagination a:hover {
            background: #6c5ce7; color: #fff;
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 8px 16px rgba(108, 92, 231, 0.4);
        }
        .pagination .active { background: #6c5ce7; color: #fff; box-shadow: 0 4px 12px rgba(108, 92, 231, 0.4); }
        .pagination .disabled { color: #ccc; background: #f7f7f7; cursor: not-allowed; }

        .data-info { font-size: 12px; color: #888; margin-bottom: 10px; text-align: right; }

        /* ================= RIPPLE ================= */
        .ripple { position: relative; overflow: hidden; }
        .ripple-circle {
            position: absolute; border-radius: 50%;
            background: rgba(255, 255, 255, 0.65);
            transform: scale(0);
            animation: rippleAnim 0.6s ease-out;
            pointer-events: none; z-index: 1;
        }
        @keyframes rippleAnim { to { transform: scale(4); opacity: 0; } }

        /* ================= RESPONSIVE LAMA ================= */
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .navbar .menu { gap: 10px; flex-wrap: wrap; }
            .navbar .menu a { font-size: 12px; padding: 6px 10px; }
            .filter-row input, .filter-row select { min-width: 100%; }
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
                padding: 15px 16px !important;
                border-radius: 14px !important;
                margin-bottom: 15px !important;
            }

            /* Filter */
            .filter-row {
                flex-direction: column !important;
                gap: 8px !important;
            }
            .filter-row input,
            .filter-row select {
                min-width: 100% !important;
                font-size: 12px !important;
                padding: 9px 12px !important;
            }

            /* Tombol */
            .btn { font-size: 12px !important; padding: 8px 14px !important; }
            .btn-tambah { margin-bottom: 12px !important; }
            .btn-edit, .btn-hapus {
                padding: 5px 10px !important;
                font-size: 11px !important;
            }

            /* Export Bar */
            .export-bar {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px !important;
                padding: 12px 15px !important;
            }
            .export-bar .label { margin-right: 0 !important; text-align: center !important; }
            .export-bar .count { text-align: center !important; }
            .export-bar button,
            .export-bar a {
                width: 100% !important;
                justify-content: center !important;
                font-size: 12px !important;
                padding: 9px 14px !important;
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

            /* Sembunyikan kolom yang kurang penting di HP */
            .hide-mobile { display: none !important; }

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

        /* HP Kecil */
        @media (max-width: 480px) {
            .navbar .brand { font-size: 14px !important; }
            .navbar .menu a { font-size: 10px !important; padding: 5px 8px !important; }

            .page-title { font-size: 16px !important; }

            .btn { font-size: 11px !important; padding: 7px 12px !important; }

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

<!-- ================= NAVBAR ================= -->
<div class="navbar">
    <div class="brand">📚 PERPUSTAKAAN</div>
    <div class="menu">
        <a href="dashboard.php">Dashboard</a>
        <a href="data_buku.php" class="active">Data Buku</a>
        <a href="tambah_buku.php">Tambah Buku</a>
        <a href="laporan_kategori.php">Laporan Kategori</a>
        <a href="export_excel.php">Export Excel</a>
        <a href="export_pdf.php">Export PDF</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<!-- ================= CONTAINER ================= -->
<div class="container">

    <h1 class="page-title">📖 Data Buku</h1>
    <p class="page-subtitle">Kelola, cari, filter, dan urutkan data buku perpustakaan.</p>

    <!-- Filter -->
    <div class="card">
        <div class="filter-title">🔍 Pencarian & Filter</div>
        <div class="filter-row">
            <input type="text" id="searchInput" placeholder="Cari kode, judul, atau penulis...">
            <select id="filterKategori">
                <option value="">Semua Kategori</option>
                <option value="Novel">Novel</option>
                <option value="Teknologi">Teknologi</option>
                <option value="Pendidikan">Pendidikan</option>
                <option value="Database">Database</option>
                <option value="Fiksi">Fiksi</option>
            </select>
            <select id="sortTahun">
                <option value="">Urutkan Tahun</option>
                <option value="asc">Tahun Terlama</option>
                <option value="desc">Tahun Terbaru</option>
            </select>
        </div>
    </div>

    <!-- Baris Export Terpilih -->
    <div class="export-bar">
        <span class="label">📤 Export Data:</span>
        <span class="count" id="selectedCount">0 terpilih</span>
        <button class="btn-export-excel" onclick="exportSelected('excel')">📗 Excel Terpilih</button>
        <button class="btn-export-pdf" onclick="exportSelected('pdf')">📕 PDF Terpilih</button>
        <a href="export_excel.php" class="btn-export-all">📦 Excel Semua</a>
        <a href="export_pdf.php" class="btn-export-all">📦 PDF Semua</a>
    </div>

    <!-- Tombol Tambah + Info -->
    <a href="tambah_buku.php" class="btn btn-tambah">+ Tambah Buku</a>
    <div class="data-info">Menampilkan <?= mysqli_num_rows($query); ?> dari <?= $total_data; ?> data</div>

    <!-- Tabel -->
    <div class="card">
        <table id="tabelBuku">
            <thead>
                <tr>
                    <th class="checkbox-col">
                        <input type="checkbox" id="checkAll" title="Pilih semua di halaman ini">
                    </th>
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Kategori</th>
                    <th>Tahun</th>
                    <th>Penerbit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($query) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($query)): ?>
                        <tr data-id="<?= $row['id']; ?>">
                            <td class="checkbox-col">
                                <input type="checkbox" class="row-check" value="<?= $row['id']; ?>">
                            </td>
                            <td><?= $row['id']; ?></td>
                            <td><?= $row['kode_buku']; ?></td>
                            <td><?= $row['judul']; ?></td>
                            <td><?= $row['penulis']; ?></td>
                            <td><?= $row['kategori']; ?></td>
                            <td><?= $row['tahun_terbit']; ?></td>
                            <td><?= $row['penerbit']; ?></td>
                            <td>
                                <a href="edit_buku.php?id=<?= $row['id']; ?>" class="btn btn-edit" onclick="event.stopPropagation()">Edit</a>
                                <a href="hapus_buku.php?id=<?= $row['id']; ?>" class="btn btn-hapus" onclick="event.stopPropagation(); return confirm('Yakin hapus?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="empty">📭 Data tidak ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($total_page > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1; ?>">« Prev</a>
                <?php else: ?>
                    <span class="disabled">« Prev</span>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_page; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="active"><?= $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i; ?>"><?= $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $total_page): ?>
                    <a href="?page=<?= $page + 1; ?>">Next »</a>
                <?php else: ?>
                    <span class="disabled">Next »</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- ================= SCRIPT ================= -->
<script>
    // ===== CHECKBOX LOGIC =====
    const checkAll = document.getElementById('checkAll');
    const rowChecks = document.querySelectorAll('.row-check');
    const selectedCount = document.getElementById('selectedCount');

    function updateCount() {
        const selected = document.querySelectorAll('.row-check:checked').length;
        selectedCount.textContent = selected + ' terpilih';
    }

    // Check all di halaman ini
    checkAll.addEventListener('change', function () {
        rowChecks.forEach(cb => {
            cb.checked = this.checked;
            cb.closest('tr').classList.toggle('selected', this.checked);
        });
        updateCount();
    });

    // Per checkbox
    rowChecks.forEach(cb => {
        cb.addEventListener('change', function () {
            this.closest('tr').classList.toggle('selected', this.checked);
            // Update checkAll state
            const totalChecked = document.querySelectorAll('.row-check:checked').length;
            checkAll.checked = totalChecked === rowChecks.length;
            checkAll.indeterminate = totalChecked > 0 && totalChecked < rowChecks.length;
            updateCount();
        });
    });

    // Klik baris untuk toggle checkbox
    document.querySelectorAll('#tabelBuku tbody tr').forEach(tr => {
        tr.addEventListener('click', function (e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'A' || e.target.tagName === 'BUTTON') return;
            const cb = this.querySelector('.row-check');
            if (cb) {
                cb.checked = !cb.checked;
                cb.dispatchEvent(new Event('change'));
            }
        });
    });

    // ===== EXPORT TERPILIH =====
    function exportSelected(type) {
        const selected = [];
        document.querySelectorAll('.row-check:checked').forEach(cb => {
            selected.push(cb.value);
        });

        if (selected.length === 0) {
            alert('Pilih minimal 1 buku untuk diexport!');
            return;
        }

        const ids = selected.join(',');
        if (type === 'excel') {
            window.location = 'export_excel.php?ids=' + ids;
        } else if (type === 'pdf') {
            window.location = 'export_pdf.php?ids=' + ids;
        }
    }

    // ===== FILTER TABLE =====
    const searchInput = document.getElementById('searchInput');
    const filterKategori = document.getElementById('filterKategori');
    const sortTahun = document.getElementById('sortTahun');
    const tbody = document.querySelector('#tabelBuku tbody');

    function filterTable() {
        const keyword = searchInput.value.toLowerCase();
        const kategori = filterKategori.value.toLowerCase();
        tbody.querySelectorAll('tr').forEach(row => {
            const text = row.innerText.toLowerCase();
            const matchKeyword = text.includes(keyword);
            const matchKategori = !kategori || text.includes(kategori);
            row.style.display = (matchKeyword && matchKategori) ? '' : 'none';
        });
    }
    searchInput.addEventListener('input', filterTable);
    filterKategori.addEventListener('change', filterTable);

    sortTahun.addEventListener('change', function() {
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const order = this.value;
        if (!order) return;
        rows.sort((a, b) => {
            const tahunA = parseInt(a.children[6].innerText);
            const tahunB = parseInt(b.children[6].innerText);
            return order === 'asc' ? tahunA - tahunB : tahunB - tahunA;
        });
        rows.forEach(row => tbody.appendChild(row));
    });

    // ===== RIPPLE =====
    document.querySelectorAll('.btn, .btn-tambah, .btn-edit, .btn-hapus, .btn-export-excel, .btn-export-pdf, .btn-export-all, .navbar .menu a, .navbar .logout, .pagination a, .card').forEach(el => {
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
</script>

</body>
</html>