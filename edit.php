<?php

session_start();
include "koneksi.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}


if (!isset($_GET['id'])) {

    header("Location: data_buku.php");
    exit;

}


$id = (int) $_GET['id'];


$q = mysqli_query(
    $conn,
    "SELECT *
     FROM books
     WHERE id = $id"
);


if (mysqli_num_rows($q) === 0) {

    header("Location: data_buku.php");
    exit;

}


$buku = mysqli_fetch_assoc($q);

$error = "";


/* =========================
   UPDATE
========================= */

if (isset($_POST['update'])) {

    $kode_buku =
        mysqli_real_escape_string(
            $conn,
            $_POST['kode_buku']
        );

    $judul =
        mysqli_real_escape_string(
            $conn,
            $_POST['judul']
        );

    $penulis =
        mysqli_real_escape_string(
            $conn,
            $_POST['penulis']
        );

    $kategori =
        mysqli_real_escape_string(
            $conn,
            $_POST['kategori']
        );

    $tahun_terbit =
        (int) $_POST['tahun_terbit'];

    $penerbit =
        mysqli_real_escape_string(
            $conn,
            $_POST['penerbit']
        );


    if (
        $kode_buku === "" ||
        $judul === "" ||
        $penulis === "" ||
        $kategori === "" ||
        $tahun_terbit === 0 ||
        $penerbit === ""
    ) {

        $error =
            "Semua data wajib diisi!";

    } else {

        $query = mysqli_query(
            $conn,
            "UPDATE books SET

                kode_buku = '$kode_buku',
                judul = '$judul',
                penulis = '$penulis',
                kategori = '$kategori',
                tahun_terbit = '$tahun_terbit',
                penerbit = '$penerbit'

             WHERE id = $id"
        );


        if ($query) {

            $_SESSION['pesan'] =
                "Buku berhasil diperbarui!";

            header(
                "Location: data_buku.php"
            );

            exit;

        } else {

            $error =
                "Gagal memperbarui data: "
                . mysqli_error($conn);

        }

    }

}

?>
<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Edit Buku</title>


<script>
(function () {

    if (localStorage.getItem("darkMode") === "true") {
        document.documentElement.classList.add("dark-mode");
    }

})();
</script>


<style>

/* =====================================================
   GLOBAL
===================================================== */

* {
    box-sizing: border-box;
}

html,
body {
    margin: 0;
    padding: 0;
}

body {

    min-height: 100vh;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background:
        linear-gradient(
            135deg,
            #ffe4ef,
            #f3ecff,
            #e5f7ff
        );

    color: #51496b;

    transition: 0.3s;
}


/* =====================================================
   NAVBAR
===================================================== */

.navbar {

    width: 100%;

    height: 74px;

    min-height: 74px;

    padding: 0 32px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    position: sticky;

    top: 0;

    z-index: 9999;

    background:
        rgba(255,255,255,0.92);

    backdrop-filter:
        blur(15px);

    box-shadow:
        0 4px 20px
        rgba(0,0,0,0.08);
}

.logo {

    flex-shrink: 0;

    white-space: nowrap;

    font-size: 21px;

    font-weight: 800;

    color: #6953a6;
}

.nav-menu {

    display: flex;

    align-items: center;

    gap: 7px;

    flex-wrap: nowrap;

    overflow-x: auto;

    white-space: nowrap;

    scrollbar-width: none;
}

.nav-menu::-webkit-scrollbar {
    display: none;
}

.nav-menu a {

    height: 40px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    padding: 0 13px;

    border-radius: 10px;

    text-decoration: none;

    color: #625b78;

    font-size: 13px;

    font-weight: 600;

    white-space: nowrap;
}

.nav-menu a:hover {

    background: #f1e9ff;

    color: #6b4dc0;
}

.logout {

    background: #ff70a5 !important;

    color: white !important;
}


/* =====================================================
   FORM
===================================================== */

.page {

    width: 92%;

    max-width: 650px;

    margin: 45px auto;
}

.page-title {

    text-align: center;

    margin-bottom: 22px;
}

.page-title h1 {

    margin: 0 0 8px;

    font-size: 30px;

    color: #59468e;
}

.page-title p {

    margin: 0;

    color: #888;
}

.form-card {

    background:
        rgba(255,255,255,0.86);

    padding: 30px;

    border-radius: 24px;

    box-shadow:
        0 12px 35px
        rgba(0,0,0,0.08);
}

.form-group {

    margin-bottom: 17px;
}

.form-group label {

    display: block;

    margin-bottom: 7px;

    font-size: 13px;

    font-weight: 700;

    color: #625876;
}

.form-group input {

    width: 100%;

    height: 45px;

    border:
        1px solid #ded5ed;

    border-radius: 11px;

    padding: 0 13px;

    outline: none;

    font-size: 14px;

    background: white;

    color: #444;
}

.form-group input:focus {

    border-color: #a681dc;
}

.error {

    padding: 12px;

    border-radius: 10px;

    background: #ffe5eb;

    color: #c53d63;

    margin-bottom: 18px;

    font-size: 13px;
}

.buttons {

    display: flex;

    justify-content: center;

    gap: 10px;

    margin-top: 25px;
}

.btn {

    border: none;

    border-radius: 11px;

    padding: 12px 20px;

    text-decoration: none;

    font-weight: 700;

    cursor: pointer;
}

.update {

    background: #a783dc;

    color: white;
}

.back {

    background: #929ed4;

    color: white;
}


/* =====================================================
   DARK
===================================================== */

html.dark-mode body {

    background: #111;

    color: #eee;
}

html.dark-mode .navbar {

    background:
        rgba(25,25,25,0.97);

    box-shadow:
        0 4px 20px
        rgba(0,0,0,0.5);
}

html.dark-mode .logo {

    color: #ff91c3;
}

html.dark-mode .nav-menu a {

    color: #ddd;
}

html.dark-mode .nav-menu a:hover {

    background: #30242b;

    color: #ff91c3;
}

html.dark-mode .page-title h1 {

    color: #ff91c3;
}

html.dark-mode .page-title p {

    color: #999;
}

html.dark-mode .form-card {

    background: #1f1f1f;

    box-shadow:
        0 12px 35px
        rgba(0,0,0,.4);
}

html.dark-mode .form-group label {

    color: #ddd;
}

html.dark-mode .form-group input {

    background: #292929;

    color: #eee;

    border-color: #444;
}

html.dark-mode .error {

    background: #38242b;

    color: #ff9fbd;
}


@media(max-width:700px) {

    .navbar {

        padding: 0 12px;
    }

    .logo {

        font-size: 17px;
    }

    .page {

        width: 94%;
    }

    .form-card {

        padding: 20px;
    }

}

</style>

</head>


<body>


<nav class="navbar">

    <div class="logo">
        📚 PERPUSTAKAAN
    </div>

    <div class="nav-menu">

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="data_buku.php">
            Data Buku
        </a>

        <a href="tambah.php">
            Tambah Buku
        </a>

        <a href="laporan_kategori.php">
            Laporan Kategori
        </a>

        <a href="export_excel.php">
            Export Excel
        </a>

        <a href="export_pdf.php">
            Export PDF
        </a>

        <a
            href="logout.php"
            class="logout">
            Logout
        </a>

    </div>

</nav>


<div class="page">


    <div class="page-title">

        <h1>
            ✏️ Edit Buku
        </h1>

        <p>
            Perbarui informasi data buku.
        </p>

    </div>


    <div class="form-card">


        <?php if ($error != ""): ?>

            <div class="error">

                <?= htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            autocomplete="off">


            <div class="form-group">

                <label>
                    📌 Kode Buku
                </label>

                <input
                    type="text"
                    name="kode_buku"
                    value="<?= htmlspecialchars($buku['kode_buku']); ?>"
                    required>

            </div>


            <div class="form-group">

                <label>
                    📖 Judul Buku
                </label>

                <input
                    type="text"
                    name="judul"
                    value="<?= htmlspecialchars($buku['judul']); ?>"
                    required>

            </div>


            <div class="form-group">

                <label>
                    ✍️ Penulis
                </label>

                <input
                    type="text"
                    name="penulis"
                    value="<?= htmlspecialchars($buku['penulis']); ?>"
                    required>

            </div>


            <div class="form-group">

                <label>
                    🏷️ Kategori
                </label>

                <input
                    type="text"
                    name="kategori"
                    value="<?= htmlspecialchars($buku['kategori']); ?>"
                    required>

            </div>


            <div class="form-group">

                <label>
                    📅 Tahun Terbit
                </label>

                <input
                    type="number"
                    name="tahun_terbit"
                    value="<?= htmlspecialchars($buku['tahun_terbit']); ?>"
                    min="1000"
                    max="2100"
                    required>

            </div>


            <div class="form-group">

                <label>
                    🏢 Penerbit
                </label>

                <input
                    type="text"
                    name="penerbit"
                    value="<?= htmlspecialchars($buku['penerbit']); ?>"
                    required>

            </div>


            <div class="buttons">

                <button
                    type="submit"
                    name="update"
                    class="btn update">

                    💾 Simpan Perubahan

                </button>

                <a
                    href="data_buku.php"
                    class="btn back">

                    ← Kembali

                </a>

            </div>


        </form>

    </div>

</div>


</body>
</html>