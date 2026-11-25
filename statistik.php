
<?php

require_once 'koneksi.php';
require_once 'fungsi.php';

// Ambil ringkasan jumlah absensi berdasarkan status
$ringkasan = $pdo->query(
    "SELECT status, COUNT(*) AS jumlah
     FROM absensi
     GROUP BY status"
)->fetchAll();

// Hitung total seluruh catatan absensi
$total = (int) $pdo->query(
    "SELECT COUNT(*) FROM absensi"
)->fetchColumn();

// Nilai awal jumlah setiap status
$jumlah = [
    'Hadir' => 0,
    'Izin' => 0,
    'Sakit' => 0,
    'Alpa' => 0
];

// Masukkan hasil query ke dalam array
foreach ($ringkasan as $r) {

    if (isset($jumlah[$r['status']])) {
        $jumlah[$r['status']] = (int) $r['jumlah'];
    }

}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Statistik | HADIRIN</title>

    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<div class="app-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <a class="brand" href="dashboard.php">
            <span class="brand-icon">H</span>

            <span>
                HADIRIN
                <small>Student Attendance</small>
            </span>
        </a>

        <p class="nav-caption">MENU UTAMA</p>

        <nav class="nav-menu">

            <a href="dashboard.php">
                ▦ <span>Dashboard</span>
            </a>

            <a href="siswa/index.php">
                ♙ <span>Data Siswa</span>
            </a>

            <a href="absensi/index.php">
                ▣ <span>Absensi</span>
            </a>

            <a class="active" href="statistik.php">
                ▥ <span>Statistik</span>
            </a>

        </nav>

    </aside>

    <!-- KONTEN UTAMA -->
    <main class="main-content">

        <header class="topbar">

            <div>
                <p class="eyebrow">LAPORAN KEHADIRAN</p>

                <h1>Statistik Absensi</h1>

                <p class="muted">
                    Ringkasan seluruh data absensi yang tersimpan.
                </p>
            </div>

            <div class="admin-chip">
                <span class="avatar">A</span>
                Administrator
            </div>

        </header>

        <!-- KARTU STATISTIK -->
        <section class="stats-grid stats-four">

            <!-- TOTAL ABSENSI -->
            <article class="stat-card">

                <div class="stat-top">
                    <span>Total Catatan</span>
                    <span class="stat-icon blue">▤</span>
                </div>

                <strong><?= $total ?></strong>

                <small>Seluruh data absensi</small>

            </article>

            <!-- STATISTIK BERDASARKAN STATUS -->
            <?php foreach ($jumlah as $st => $j): ?>

                <article class="stat-card">

                    <div class="stat-top">

                        <span><?= e($st) ?></span>

                        <span class="stat-icon <?= strtolower($st) ?>">
                            <?= $st === 'Hadir'
                                ? '✓'
                                : ($st === 'Izin'
                                    ? '✉'
                                    : ($st === 'Sakit' ? '＋' : '!')) ?>
                        </span>

                    </div>

                    <strong><?= $j ?></strong>

                    <small>
                        Catatan <?= strtolower($st) ?>
                    </small>

                </article>

            <?php endforeach; ?>

        </section>

        <!-- PERSENTASE KEHADIRAN -->
        <section class="panel">

            <div class="panel-heading">

                <div>
                    <h2>Persentase Kehadiran</h2>

                    <p class="muted">
                        Perbandingan berdasarkan jumlah catatan absensi.
                    </p>
                </div>

            </div>

            <?php foreach ($jumlah as $st => $j): ?>

                <?php
                    $persen = $total > 0
                        ? round($j / $total * 100, 1)
                        : 0;
                ?>

                <div class="progress-row">

                    <div class="progress-label">

                        <strong><?= e($st) ?></strong>

                        <span>
                            <?= $j ?> data · <?= $persen ?>%
                        </span>

                    </div>

                    <div class="progress-track">

                        <div
                            class="progress-fill fill-<?= strtolower($st) ?>"
                            style="width: <?= $persen ?>%"
                        ></div>

                    </div>

                </div>

            <?php endforeach; ?>

            <?php if ($total === 0): ?>

                <p class="empty">
                    Belum ada data untuk dihitung.
                </p>

            <?php endif; ?>

        </section>

        <!-- FOOTER -->
        <footer class="footer">
            © <?= date('Y') ?> HADIRIN · Sistem Manajemen Kehadiran Siswa
        </footer>

    </main>

</div>

</body>
</html>