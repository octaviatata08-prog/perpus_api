<?php
if (!isset($pageTitle)) {
    $pageTitle = "Perpustakaan";
}

if (!isset($showThemeButton)) {
    $showThemeButton = false;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle); ?> - Perpustakaan</title>

    <link rel="stylesheet" href="style.css">

    <!--
        DARK MODE DIPASANG SEBELUM BODY TAMPIL
        supaya halaman tidak sempat berkedip putih.
    -->
    <script>
        (function () {
            const mode = localStorage.getItem("perpusDarkMode");

            if (mode === "true") {
                document.documentElement.classList.add("dark-loading");
            }
        })();
    </script>

</head>

<body>

<nav class="navbar">

    <!-- LOGO -->
    <a href="dashboard.php" class="nav-brand">
        <span class="nav-brand-icon">📚</span>
        <span>PERPUSTAKAAN</span>
    </a>

    <!-- MENU -->
    <div class="nav-menu">

        <a href="dashboard.php" class="nav-link">
            Dashboard
        </a>

        <a href="data_buku.php" class="nav-link">
            Data Buku
        </a>

        <a href="tambah.php" class="nav-link">
            Tambah Buku
        </a>

        <a href="laporan_kategori.php" class="nav-link">
            Laporan Kategori
        </a>

        <a href="export_excel.php" class="nav-link">
            Export Excel
        </a>

        <a href="export_pdf.php" class="nav-link">
            Export PDF
        </a>

    </div>

    <!-- BAGIAN KANAN -->
    <div class="nav-right">

        <div class="theme-slot">

            <?php if ($showThemeButton): ?>

                <button
                    type="button"
                    class="dark-mode-btn"
                    id="darkModeBtn"
                    onclick="togglePerpusDarkMode()"
                >
                    🌙 Dark Mode
                </button>

            <?php else: ?>

                <!--
                    SLOT KOSONG INI SENGAJA ADA
                    supaya posisi Logout TIDAK BERGESER
                -->
                <div class="theme-placeholder"></div>

            <?php endif; ?>

        </div>

        <a href="logout.php" class="logout-btn">
            Logout
        </a>

    </div>

</nav>

<script>
    /*
        Terapkan mode yang terakhir dipilih dari Dashboard.
    */

    (function () {

        const mode = localStorage.getItem("perpusDarkMode");

        if (mode === "true") {
            document.body.classList.add("dark-mode");
        }

    })();
</script>