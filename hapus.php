<?php
session_start();
include "koneksi.php";

// Cek login admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah ada ID
if (!isset($_GET['id'])) {
    header("Location: data_buku.php");
    exit;
}

$id = $_GET['id'];

// Hapus data berdasarkan ID
$query = mysqli_query($conn, "DELETE FROM books WHERE id='$id'");

// Jika berhasil
if ($query) {
    header("Location: data_buku.php");
    exit;
} else {
    echo "Data gagal dihapus: " . mysqli_error($conn);
}
?>