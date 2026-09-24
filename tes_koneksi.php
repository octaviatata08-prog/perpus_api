<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$koneksi = mysqli_connect("localhost", "root", "", "perpus_api");

if (!$koneksi) {
    die("❌ Gagal konek: " . mysqli_connect_error());
}
echo "✅ Koneksi berhasil!<br>";

$result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM books");
if (!$result) {
    die("❌ Query error: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($result);
echo "📚 Total buku: " . $data['total'];
?>