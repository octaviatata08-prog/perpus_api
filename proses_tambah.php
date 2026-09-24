<?php
session_start();
include "koneksi.php";

// Cek apakah user sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Ambil data dari form
    $kode_buku    = mysqli_real_escape_string($conn, $_POST['kode_buku']);
    $judul        = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis      = mysqli_real_escape_string($conn, $_POST['penulis']);
    $kategori     = mysqli_real_escape_string($conn, $_POST['kategori']);
    $tahun_terbit = mysqli_real_escape_string($conn, $_POST['tahun_terbit']);
    $penerbit     = mysqli_real_escape_string($conn, $_POST['penerbit']);

    // Cek apakah kode buku sudah ada
    $cek = mysqli_query($conn, "SELECT * FROM books WHERE kode_buku = '$kode_buku'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['sukses'] = "Gagal! Kode buku $kode_buku sudah terdaftar.";
        header("Location: tambah.php");
        exit;
    }

    // Simpan data ke database
    $query = "INSERT INTO books (kode_buku, judul, penulis, kategori, tahun_terbit, penerbit) 
              VALUES ('$kode_buku', '$judul', '$penulis', '$kategori', '$tahun_terbit', '$penerbit')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['sukses'] = "Buku berhasil ditambahkan!";
        header("Location: data_buku.php");
        exit;
    } else {
        $_SESSION['sukses'] = "Gagal menambahkan buku: " . mysqli_error($conn);
        header("Location: tambah.php");
        exit;
    }

} else {
    // Kalau bukan POST, balikin ke halaman tambah
    header("Location: tambah.php");
    exit;
}
?>