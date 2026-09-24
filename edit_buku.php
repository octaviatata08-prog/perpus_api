<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data buku yang mau diedit
$query  = "SELECT * FROM books WHERE id = $id";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='data_buku.php';</script>";
    exit;
}

$row = mysqli_fetch_assoc($result);

// Proses update
if (isset($_POST['submit'])) {
    $kode_buku    = mysqli_real_escape_string($koneksi, $_POST['kode_buku']);
    $judul        = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $penulis      = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $kategori     = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $tahun_terbit = mysqli_real_escape_string($koneksi, $_POST['tahun_terbit']);
    $penerbit     = mysqli_real_escape_string($koneksi, $_POST['penerbit']);

    $update = "UPDATE books SET
                kode_buku    = '$kode_buku',
                judul        = '$judul',
                penulis      = '$penulis',
                kategori     = '$kategori',
                tahun_terbit = '$tahun_terbit',
                penerbit     = '$penerbit'
              WHERE id = $id";

    if (mysqli_query($koneksi, $update)) {
        echo "<script>alert('Buku berhasil diupdate!'); window.location='data_buku.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Buku</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background: linear-gradient(135deg, #f5f0ff 0%, #ffe8f0 100%); min-height: 100vh; }

        .navbar {
            background: #fff; padding: 15px 40px; display: flex;
            align-items: center; justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .navbar .brand { font-size: 20px; font-weight: bold; color: #6c5ce7; }
        .navbar .menu { display: flex; gap: 25px; align-items: center; }
        .navbar .menu a { text-decoration: none; color: #555; font-size: 14px; font-weight: 500; }
        .navbar .menu a:hover, .navbar .menu a.active { color: #6c5ce7; }
        .navbar .logout { background: #ff6b9d; color: #fff !important; padding: 8px 20px; border-radius: 20px; font-weight: 600; }

        .container { max-width: 700px; margin: 30px auto; padding: 0 20px; }
        .page-title { font-size: 22px; color: #333; margin-bottom: 5px; }
        .page-subtitle { font-size: 13px; color: #888; margin-bottom: 20px; }

        .card {
            background: #fff; border-radius: 16px; padding: 25px 30px;
            box-shadow: 0 4px 20px rgba(108, 92, 231, 0.08);
        }

        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px; }
        .form-group input, .form-group select {
            width: 100%; padding: 10px 14px; border: 1px solid #e0d7f5;
            border-radius: 10px; font-size: 13px; background: #faf8ff; outline: none;
        }
        .form-group input:focus, .form-group select:focus { border-color: #6c5ce7; background: #fff; }

        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        .btn { padding: 10px 22px; border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; }
        .btn-update { background: #6c5ce7; color: #fff; }
        .btn-update:hover { background: #5a4bd1; }
        .btn-batal { background: #f3efff; color: #6c5ce7; }
        .btn-batal:hover { background: #e5dcff; }
    </style>
</head>
<body>

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

<div class="container">
    <h1 class="page-title">✏️ Edit Buku</h1>
    <p class="page-subtitle">Ubah data buku di bawah ini.</p>

    <div class="card">
        <form method="POST" action="">
            <div class="form-group">
                <label>Kode Buku</label>
                <input type="text" name="kode_buku" value="<?= $row['kode_buku']; ?>" required>
            </div>
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="judul" value="<?= $row['judul']; ?>" required>
            </div>
            <div class="form-group">
                <label>Penulis</label>
                <input type="text" name="penulis" value="<?= $row['penulis']; ?>" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" required>
                    <option value="Novel" <?= $row['kategori']=='Novel'?'selected':''; ?>>Novel</option>
                    <option value="Teknologi" <?= $row['kategori']=='Teknologi'?'selected':''; ?>>Teknologi</option>
                    <option value="Pendidikan" <?= $row['kategori']=='Pendidikan'?'selected':''; ?>>Pendidikan</option>
                    <option value="Database" <?= $row['kategori']=='Database'?'selected':''; ?>>Database</option>
                    <option value="Fiksi" <?= $row['kategori']=='Fiksi'?'selected':''; ?>>Fiksi</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tahun Terbit</label>
                <input type="number" name="tahun_terbit" value="<?= $row['tahun_terbit']; ?>" min="1900" max="2100" required>
            </div>
            <div class="form-group">
                <label>Penerbit</label>
                <input type="text" name="penerbit" value="<?= $row['penerbit']; ?>" required>
            </div>

            <div class="btn-group">
                <button type="submit" name="submit" class="btn btn-update">💾 Update</button>
                <a href="data_buku.php" class="btn btn-batal">↩️ Batal</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>