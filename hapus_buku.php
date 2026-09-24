<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo "<script>alert('ID tidak valid!'); window.location='data_buku.php';</script>";
    exit;
}

// Cek apakah data ada
$cek = mysqli_query($koneksi, "SELECT * FROM books WHERE id = $id");
if (mysqli_num_rows($cek) == 0) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='data_buku.php';</script>";
    exit;
}

// Hapus data
$hapus = "DELETE FROM books WHERE id = $id";

if (mysqli_query($koneksi, $hapus)) {
    echo "<script>alert('Buku berhasil dihapus!'); window.location='data_buku.php';</script>";
} else {
    echo "<script>alert('Gagal hapus: " . mysqli_error($koneksi) . "'); window.location='data_buku.php';</script>";
}
?>