
<?php

// Memeriksa apakah pengguna sudah login
require_once __DIR__ . '/login/auth.php';
cekLogin();

require_once 'koneksi.php';
require_once 'fungsi.php';

// Menghitung total siswa
$totalSiswa = (int) $pdo->query(
    "SELECT COUNT(*) FROM siswa"
)->fetchColumn();

// Menghitung jumlah siswa berdasarkan status hari ini
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM absensi
    WHERE tanggal = CURDATE()
    AND status = ?
");

$stmt->execute(['Hadir']);
$hadir = (int) $stmt->fetchColumn();

$stmt->execute(['Izin']);
$izin = (int) $stmt->fetchColumn();

$stmt->execute(['Sakit']);
$sakit = (int) $stmt->fetchColumn();

$stmt->execute(['Alpa']);
$alpa = (int) $stmt->fetchColumn();

// Mengambil absensi terbaru menggunakan JOIN 3 tabel
$terbaru = $pdo->query("
    SELECT
        a.id_absensi,
        a.tanggal,
        a.status,
        a.keterangan,
        s.nis,
        s.nama_siswa,
        k.nama_kelas
    FROM absensi a
    INNER JOIN siswa s
        ON s.id_siswa = a.id_siswa
    INNER JOIN kelas k
        ON s.id_kelas = k.id_kelas
    ORDER BY a.tanggal DESC, a.id_absensi DESC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | HADIRIN</title>

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

            <a class="<?= aktif('dashboard') ?>" href="dashboard.php">
                ▦ <span>Dashboard</span>
            </a>

            <a href="siswa/index.php">
                ♙ <span>Data Siswa</span>
            </a>

            <a href="absensi/index.php">
                ▣ <span>Absensi</span>
            </a>

            <a href="statistik.php">
                ▥ <span>Statistik</span>
            </a>

        </nav>

        <div class="sidebar-note">
            <strong>HADIRIN</strong><br>
            Kelola kehadiran dengan lebih mudah.
        </div>

    </aside>


    <!-- KONTEN UTAMA -->
    <main class="main-content">

        <header class="topbar">

            <div>
                <p class="eyebrow">SISTEM INFORMASI SEKOLAH</p>

                <h1>Dashboard</h1>

                <p class="muted">
                    Selamat datang di Sistem Manajemen Kehadiran.
                </p>
            </div>

            <!-- INFORMASI ADMIN DAN LOGOUT -->
            <div class="admin-chip">

                <span class="avatar">A</span>

                <span>
                    <?= e($_SESSION['nama'] ?? 'Administrator') ?>
                </span>

                <a href="login/logout.php" class="btn btn-primary">
                    Logout
                </a>

            </div>

        </header>


        <!-- WELCOME CARD -->
        <section class="welcome-card">

            <div>
                <p class="welcome-label">
                    HADIRIN · STUDENT ATTENDANCE
                </p>

                <h2>
                    Kelola kehadiran siswa<br>
                    dengan lebih praktis.
                </h2>

                <p>
                    Pantau data siswa dan catatan absensi dalam satu tempat.
                </p>
            </div>

            <div class="welcome-art">✓</div>

        </section>


        <!-- RINGKASAN KEHADIRAN -->
        <div class="section-heading">

            <div>
                <h2>Ringkasan Kehadiran Hari Ini</h2>

                <p class="muted">
                    <?= e(date('d F Y')) ?>
                </p>
            </div>

        </div>


        <!-- STATISTIK -->
        <section class="stats-grid">

            <article class="stat-card">
                <div class="stat-top">
                    <span>Total Siswa</span>
                    <span class="stat-icon blue">♙</span>
                </div>

                <strong><?= $totalSiswa ?></strong>
                <small>Data siswa terdaftar</small>
            </article>


            <article class="stat-card">
                <div class="stat-top">
                    <span>Hadir</span>
                    <span class="stat-icon green">✓</span>
                </div>

                <strong><?= $hadir ?></strong>
                <small>Siswa hadir hari ini</small>
            </article>


            <article class="stat-card">
                <div class="stat-top">
                    <span>Izin</span>
                    <span class="stat-icon orange">✉</span>
                </div>

                <strong><?= $izin ?></strong>
                <small>Siswa izin hari ini</small>
            </article>


            <article class="stat-card">
                <div class="stat-top">
                    <span>Sakit</span>
                    <span class="stat-icon purple">＋</span>
                </div>

                <strong><?= $sakit ?></strong>
                <small>Siswa sakit hari ini</small>
            </article>


            <article class="stat-card">
                <div class="stat-top">
                    <span>Alpa</span>
                    <span class="stat-icon red">!</span>
                </div>

                <strong><?= $alpa ?></strong>
                <small>Tanpa keterangan hari ini</small>
            </article>

        </section>


        <!-- ABSENSI TERBARU -->
        <section class="panel">

            <div class="panel-heading">

                <div>
                    <h2>Absensi Terbaru</h2>

                    <p class="muted">
                        Catatan kehadiran terakhir yang dimasukkan.
                    </p>
                </div>

                <a class="btn btn-primary" href="absensi/index.php">
                    Lihat Semua
                </a>

            </div>


            <div class="table-wrap">

                <table>

                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (!$terbaru): ?>

                            <tr>
                                <td colspan="5" class="empty">
                                    Belum ada data absensi.
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($terbaru as $row): ?>

                                <tr>

                                    <td class="name-cell">
                                        <?= e($row['nama_siswa']) ?>
                                    </td>

                                    <td>
                                        <?= e($row['nis']) ?>
                                    </td>

                                    <td>
                                        <?= e($row['nama_kelas']) ?>
                                    </td>

                                    <td>
                                        <?= e(format_tanggal($row['tanggal'])) ?>
                                    </td>

                                    <td>
                                        <span class="status status-<?= strtolower(e($row['status'])) ?>">
                                            <?= e($row['status']) ?>
                                        </span>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>


        <!-- FOOTER -->
        <footer class="footer">
            © <?= date('Y') ?> HADIRIN · Sistem Manajemen Kehadiran Siswa
        </footer>

    </main>

</div>

</body>
</html>