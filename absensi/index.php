
<?php

require_once '../koneksi.php';
require_once '../fungsi.php';

// Ambil data filter
$cari = trim($_GET['cari'] ?? '');
$status = trim($_GET['status'] ?? '');
$tanggal = trim($_GET['tanggal'] ?? '');

// Query utama: JOIN 3 tabel
$sql = "SELECT
            a.*,
            s.nis,
            s.nama_siswa,
            k.nama_kelas
        FROM absensi a
        JOIN siswa s
            ON s.id_siswa = a.id_siswa
        JOIN kelas k
            ON k.id_kelas = s.id_kelas
        WHERE (
            s.nama_siswa LIKE :cari_nama
            OR s.nis LIKE :cari_nis
        )";

$params = [
    ':cari_nama' => "%$cari%",
    ':cari_nis' => "%$cari%"
];

// Filter status
if (in_array($status, ['Hadir', 'Izin', 'Sakit', 'Alpa'], true)) {
    $sql .= " AND a.status = :status";
    $params[':status'] = $status;
}

// Filter tanggal
if (
    $tanggal !== '' &&
    preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)
) {
    $sql .= " AND a.tanggal = :tanggal";
    $params[':tanggal'] = $tanggal;
}

// Urutkan data
$sql .= " ORDER BY a.tanggal DESC, a.id_absensi DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll();

$pesan = $_GET['pesan'] ?? '';

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi | HADIRIN</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>

<div class="app-layout">

    <aside class="sidebar">

        <a class="brand" href="../dashboard.php">
            <span class="brand-icon">H</span>
            <span>
                HADIRIN
                <small>Student Attendance</small>
            </span>
        </a>

        <p class="nav-caption">MENU UTAMA</p>

        <nav class="nav-menu">
            <a href="../dashboard.php">▦ <span>Dashboard</span></a>
            <a href="../siswa/index.php">♙ <span>Data Siswa</span></a>
            <a class="active" href="index.php">▣ <span>Absensi</span></a>
            <a href="../statistik.php">▥ <span>Statistik</span></a>
        </nav>

        <div class="sidebar-note">
            <strong>HADIRIN</strong><br>
            Kelola kehadiran dengan lebih mudah.
        </div>

    </aside>

    <main class="main-content">

        <header class="topbar">

            <div>
                <p class="eyebrow">MANAJEMEN KEHADIRAN</p>
                <h1>Absensi Siswa</h1>
                <p class="muted">
                    Catat dan kelola kehadiran siswa.
                </p>
            </div>

            <div class="admin-chip">
                <span class="avatar">A</span>
                Administrator
            </div>

        </header>

        <?php if ($pesan === 'tambah'): ?>
            <div class="alert success">
                Data absensi berhasil ditambahkan.
            </div>

        <?php elseif ($pesan === 'ubah'): ?>
            <div class="alert success">
                Data absensi berhasil diperbarui.
            </div>

        <?php elseif ($pesan === 'hapus'): ?>
            <div class="alert success">
                Data absensi berhasil dihapus.
            </div>
        <?php endif; ?>

        <section class="panel">

            <div class="panel-heading">

                <div>
                    <h2>Riwayat Absensi</h2>
                    <p class="muted">
                        <?= count($data) ?> data absensi ditampilkan.
                    </p>
                </div>

                <a class="btn btn-primary" href="tambah.php">
                    ＋ Tambah Absensi
                </a>

            </div>

            <form class="filter-form" method="GET">

                <input
                    type="text"
                    name="cari"
                    placeholder="Cari nama atau NIS..."
                    value="<?= e($cari) ?>"
                >

                <select name="status">
                    <option value="">Semua Status</option>

                    <?php foreach (['Hadir', 'Izin', 'Sakit', 'Alpa'] as $st): ?>
                        <option
                            value="<?= e($st) ?>"
                            <?= $status === $st ? 'selected' : '' ?>
                        >
                            <?= e($st) ?>
                        </option>
                    <?php endforeach; ?>

                </select>

                <input
                    type="date"
                    name="tanggal"
                    value="<?= e($tanggal) ?>"
                >

                <button class="btn btn-light" type="submit">
                    Filter
                </button>

                <a class="btn btn-plain" href="index.php">
                    Reset
                </a>

            </form>

            <div class="table-wrap">

                <table>

                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (!$data): ?>

                        <tr>
                            <td colspan="8" class="empty">
                                Belum ada data absensi.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($data as $i => $a): ?>

                            <tr>
                                <td><?= $i + 1 ?></td>

                                <td class="name-cell">
                                    <?= e($a['nama_siswa']) ?>
                                </td>

                                <td><?= e($a['nis']) ?></td>

                                <td><?= e($a['nama_kelas']) ?></td>

                                <td>
                                    <?= e(format_tanggal($a['tanggal'])) ?>
                                </td>

                                <td>
                                    <span class="status status-<?= strtolower(e($a['status'])) ?>">
                                        <?= e($a['status']) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= e($a['keterangan'] ?? '') ?: '-' ?>
                                </td>

                                <td class="actions">

                                    <a
                                        class="btn btn-small btn-light"
                                        href="edit.php?id=<?= (int) $a['id_absensi'] ?>"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="hapus.php"
                                        onsubmit="return confirm('Yakin ingin menghapus absensi ini?')"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $a['id_absensi'] ?>"
                                        >

                                        <button
                                            class="btn btn-small btn-danger"
                                            type="submit"
                                        >
                                            Hapus
                                        </button>

                                    </form>

                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

        <footer class="footer">
            © <?= date('Y') ?> HADIRIN · Sistem Manajemen Kehadiran Siswa
        </footer>

    </main>

</div>

</body>
</html>