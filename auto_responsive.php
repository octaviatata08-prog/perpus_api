<?php
// ============================================
// SCRIPT AUTO-INJECT responsive.css
// Jalankan SEKALI, lalu HAPUS file ini
// ============================================

$files = [
    'dashboard.php',
    'data_buku.php',
    'tambah_buku.php',
    'edit_buku.php',
    'laporan_kategori.php',
    'export_pdf.php',
    'export_excel.php',
];

$link = '    <link rel="stylesheet" href="responsive.css">';
$log = [];

foreach ($files as $file) {
    if (!file_exists($file)) {
        $log[] = "❌ <b>$file</b> — tidak ditemukan";
        continue;
    }

    $content = file_get_contents($file);

    // Cek apakah sudah ada link responsive.css
    if (strpos($content, 'responsive.css') !== false) {
        $log[] = "⚠️ <b>$file</b> — sudah ada, skip";
        continue;
    }

    // Cari </head> dan sisipkan sebelum itu
    if (strpos($content, '</head>') !== false) {
        $content = str_replace('</head>', $link . "\n</head>", $content);
        file_put_contents($file, $content);
        $log[] = "✅ <b>$file</b> — berhasil ditambahkan";
    } else {
        $log[] = "❌ <b>$file</b> — tidak ada tag </head>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Auto-Inject Responsive CSS</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f0ff, #ffe8f0);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .box {
            background: #fff;
            border-radius: 16px;
            padding: 30px 40px;
            box-shadow: 0 20px 40px rgba(108, 92, 231, 0.2);
            max-width: 600px;
            width: 100%;
        }
        h1 { color: #6c5ce7; font-size: 20px; margin-bottom: 20px; }
        ul { list-style: none; padding: 0; }
        li {
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 8px;
            background: #faf8ff;
            font-size: 14px;
            color: #444;
        }
        .footer {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 8px;
            font-size: 13px;
            color: #856404;
        }
        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 25px;
            background: #6c5ce7;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
        }
        .btn:hover { background: #5a4bd1; }
    </style>
</head>
<body>
    <div class="box">
        <h1>⚡ Auto-Inject responsive.css</h1>
        <ul>
            <?php foreach ($log as $l) echo "<li>$l</li>"; ?>
        </ul>
        <div class="footer">
            ⚠️ <b>PENTING:</b> Hapus file <code>auto_responsive.php</code> setelah selesai!
        </div>
        <a href="dashboard.php" class="btn">🏠 Cek Dashboard</a>
    </div>
</body>
</html>