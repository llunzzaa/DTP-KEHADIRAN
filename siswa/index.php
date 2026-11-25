
<?php
require_once '../koneksi.php';
require_once '../fungsi.php';

// Ambil kata pencarian
$cari = trim($_GET['cari'] ?? '');

// Query data siswa dengan JOIN tabel kelas
$sql = "SELECT
            s.id_siswa,
            s.nis,
            s.nama_siswa,
            s.jenis_kelamin,
            s.id_kelas,
            k.nama_kelas
        FROM siswa s
        INNER JOIN kelas k
            ON s.id_kelas = k.id_kelas
        WHERE (
            s.nis LIKE :nis
            OR s.nama_siswa LIKE :nama
        )
        ORDER BY s.nama_siswa ASC";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':nis' => "%$cari%",
    ':nama' => "%$cari%"
]);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Notifikasi
$pesan = $_GET['pesan'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Siswa | HADIRIN</title>

    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>

<div class="app-layout">

    <!-- SIDEBAR -->
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

            <a href="../dashboard.php">
                ▦ <span>Dashboard</span>
            </a>

            <a class="active" href="index.php">
                ♙ <span>Data Siswa</span>
            </a>

            <a href="../absensi/index.php">
                ▣ <span>Absensi</span>
            </a>

            <a href="../statistik.php">
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
                <p class="eyebrow">MANAJEMEN SISWA</p>
                <h1>Data Siswa</h1>

                <p class="muted">
                    Kelola data siswa dan informasi kelas.
                </p>
            </div>

            <div class="admin-chip">
                <span class="avatar">A</span>
                Administrator
            </div>

        </header>

        <!-- NOTIFIKASI -->
        <?php if ($pesan === 'tambah'): ?>

            <div class="alert success">
                Data siswa berhasil ditambahkan.
            </div>

        <?php elseif ($pesan === 'ubah'): ?>

            <div class="alert success">
                Data siswa berhasil diperbarui.
            </div>

        <?php elseif ($pesan === 'hapus'): ?>

            <div class="alert success">
                Data siswa berhasil dihapus.
            </div>

        <?php endif; ?>

        <!-- PANEL DATA SISWA -->
        <section class="panel">

            <div class="panel-heading">

                <div>
                    <h2>Daftar Siswa</h2>

                    <p class="muted">
                        <?= count($data) ?> data siswa ditampilkan.
                    </p>
                </div>

                <a class="btn btn-primary" href="tambah.php">
                    + Tambah Siswa
                </a>

            </div>

            <!-- FORM PENCARIAN -->
            <form class="filter-form" method="GET">

                <input
                    type="text"
                    name="cari"
                    placeholder="Cari nama siswa atau NIS..."
                    value="<?= e($cari) ?>"
                >

                <button class="btn btn-light" type="submit">
                    Cari
                </button>

                <a class="btn btn-plain" href="index.php">
                    Reset
                </a>

            </form>

            <!-- TABEL SISWA -->
            <div class="table-wrap">

                <table>

                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Jenis Kelamin</th>
                            <th>Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (empty($data)): ?>

                            <tr>
                                <td colspan="6" class="empty">
                                    Belum ada data siswa.
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($data as $i => $s): ?>

                                <tr>

                                    <td>
                                        <?= $i + 1 ?>
                                    </td>

                                    <td>
                                        <?= e($s['nis']) ?>
                                    </td>

                                    <td class="name-cell">
                                        <?= e($s['nama_siswa']) ?>
                                    </td>

                                    <td>
                                        <?= e($s['jenis_kelamin']) ?>
                                    </td>

                                    <td>
                                        <?= e($s['nama_kelas']) ?>
                                    </td>

                                    <td class="actions">

                                        <a
                                            class="btn btn-small btn-light"
                                            href="edit.php?id=<?= (int) $s['id_siswa'] ?>"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="hapus.php"
                                            onsubmit="return confirm('Yakin ingin menghapus data siswa ini?')"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $s['id_siswa'] ?>"
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