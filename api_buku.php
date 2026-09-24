<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php';

// Header biar output-nya JSON rapi
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Query ambil semua buku
$query  = mysqli_query($koneksi, "SELECT * FROM books ORDER BY id ASC");

if (!$query) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Query gagal: ' . mysqli_error($koneksi)
    ]);
    exit;
}

$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = [
        'id'           => (int)$row['id'],
        'kode_buku'    => $row['kode_buku'],
        'judul'        => $row['judul'],
        'penulis'      => $row['penulis'],
        'kategori'     => $row['kategori'],
        'tahun_terbit' => (int)$row['tahun_terbit'],
        'penerbit'     => $row['penerbit']
    ];
}

echo json_encode([
    'status' => 'success',
    'total'  => count($data),
    'data'   => $data
], JSON_PRETTY_PRINT);
?>