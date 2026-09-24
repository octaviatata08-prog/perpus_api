<?php
// ============================================
// SCRIPT AUTO-INJECT DARK MODE (LANGSUNG AKTIF)
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

$link_css = '    <link rel="stylesheet" href="darkmode.css">';
$log = [];

foreach ($files as $file) {
    if (!file_exists($file)) {
        $log[] = "❌ <b>$file</b> — tidak ditemukan";
        continue;
    }

    $content = file_get_contents($file);
    $changed = false;
    $notes = [];

    // 1. Tambah <link darkmode.css> sebelum </head>
    if (strpos($content, 'darkmode.css') === false) {
        if (strpos($content, '</head>') !== false) {
            $content = str_replace('</head>', $link_css . "\n</head>", $content);
            $notes[] = "link CSS";
            $changed = true;
        }
    }

    // 2. Ganti <body> jadi <body class="dark">
    if (strpos($content, '<body class="dark">') === false) {
        if (strpos($content, '<body>') !== false) {
            $content = str_replace('<body>', '<body class="dark">', $content);
            $notes[] = "body.dark";
            $changed = true;
        }
    }

    if ($changed) {
        file_put_contents($file, $content);
        $log[] = "✅ <b>$file</b> — " . implode(', ', $notes);
    } else {
        $log[] = "⚠️ <b>$file</b> — sudah ada, skip";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Auto Dark Mode</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #1a1030, #2a1035);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #e0d7f5;
        }
        .box {
            background: rgba(40, 28, 70, 0.95);
            border-radius: 16px;
            padding: 30px 40px;
            box-shadow: 0 20px 40px rgba(108, 92, 231, 0.4);
            max-width: 600px;
            width: 100%;
            border: 1px solid rgba(108, 92, 231, 0.3);
        }
        h1 { color: #b8a8ff; font-size: 20px; margin-bottom: 20px; }
        ul { list-style: none; padding: 0; }
        li {
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 8px;
            background: rgba(108, 92, 231, 0.15);
            font-size: 14px;
            color: #d0c5f0;
        }
        .footer {
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 200, 60, 0.15);
            border-left: 4px solid #ffc107;
            border-radius: 8px;
            font-size: 13px;
            color: #ffd966;
        }
        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 25px;
            background: linear-gradient(135deg, #6c5ce7, #8a7bff);
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
        <h1>🌙 Auto Dark Mode — Aktif Otomatis</h1>
        <ul>
            <?php foreach ($log as $l) echo "<li>$l</li>"; ?>
        </ul>
        <div class="footer">
            ⚠️ <b>PENTING:</b> Hapus file <code>auto_darkmode.php</code> setelah selesai!
        </div>
        <a href="dashboard.php" class="btn">🏠 Cek Dashboard</a>
    </div>
</body>
</html>