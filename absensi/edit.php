
<?php

require_once '../koneksi.php';
require_once '../fungsi.php';

// Mengambil ID absensi dari URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

// Mengambil data absensi berdasarkan ID
$query = $pdo->prepare("
    SELECT *
    FROM absensi
    WHERE id_absensi = ?
");

$query->execute([$id]);
$absensi = $query->fetch();

// Jika data tidak ditemukan
if (!$absensi) {
    header('Location: index.php');
    exit;
}

// Mengambil seluruh data siswa
$siswaList = $pdo->query("
    SELECT id_siswa, nis, nama_siswa, kelas
    FROM siswa
    ORDER BY nama_siswa ASC
")->fetchAll();

$error = '';

// Proses ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_siswa = $_POST['id_siswa'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $status = $_POST['status'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');

    // Validasi input
    if (
        $id_siswa === '' ||
        $tanggal === '' ||
        !in_array($status, ['Hadir', 'Izin', 'Sakit', 'Alpa'], true)
    ) {

        $error = 'Lengkapi data absensi dengan benar.';

    } else {

        // Memeriksa apakah absensi sudah tersedia
        $cek = $pdo->prepare("
            SELECT COUNT(*)
            FROM absensi
            WHERE id_siswa = ?
            AND tanggal = ?
            AND id_absensi <> ?
        ");

        $cek->execute([
            $id_siswa,
            $tanggal,
            $id
        ]);

        if ($cek->fetchColumn() > 0) {

            $error = 'Absensi siswa tersebut pada tanggal itu sudah ada.';

        } else {

            // Memperbarui data absensi
            $update = $pdo->prepare("
                UPDATE absensi
                SET id_siswa = ?,
                    tanggal = ?,
                    status = ?,
                    keterangan = ?
                WHERE id_absensi = ?
            ");

            $update->execute([
                $id_siswa,
                $tanggal,
                $status,
                $keterangan,
                $id
            ]);

            header('Location: index.php?pesan=ubah');
            exit;
        }
    }

    // Menyimpan input agar tidak hilang ketika validasi gagal
    $absensi = array_merge($absensi, [
        'id_siswa' => $id_siswa,
        'tanggal' => $tanggal,
        'status' => $status,
        'keterangan' => $keterangan
    ]);
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Absensi | HADIRIN</title>

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

            <a href="../siswa/index.php">
                ♙ <span>Data Siswa</span>
            </a>

            <a class="active" href="index.php">
                ▣ <span>Absensi</span>
            </a>

            <a href="../statistik.php">
                ▥ <span>Statistik</span>
            </a>

        </nav>

    </aside>

    <!-- KONTEN UTAMA -->
    <main class="main-content">

        <header class="topbar">

            <div>

                <p class="eyebrow">
                    MANAJEMEN KEHADIRAN
                </p>

                <h1>Edit Absensi</h1>

                <p class="muted">
                    Perbarui catatan kehadiran siswa.
                </p>

            </div>

        </header>

        <!-- FORM EDIT -->
        <section class="form-panel">

            <div class="panel-heading">

                <div>

                    <h2>Formulir Absensi</h2>

                    <p class="muted">
                        Ubah data yang diperlukan.
                    </p>

                </div>

            </div>

            <!-- Pesan Error -->
            <?php if ($error !== ''): ?>

                <div class="alert error">
                    <?= e($error) ?>
                </div>

            <?php endif; ?>

            <form method="POST" class="form-grid">

                <!-- Nama Siswa -->
                <label>

                    Nama Siswa

                    <select name="id_siswa" required>

                        <?php foreach ($siswaList as $s): ?>

                            <option
                                value="<?= (int)$s['id_siswa'] ?>"
                                <?= (string)$absensi['id_siswa'] === (string)$s['id_siswa'] ? 'selected' : '' ?>
                            >

                                <?= e($s['nama_siswa']) ?>
                                — <?= e($s['kelas']) ?>
                                (<?= e($s['nis']) ?>)

                            </option>

                        <?php endforeach; ?>

                    </select>

                </label>

                <!-- Tanggal -->
                <label>

                    Tanggal

                    <input
                        type="date"
                        name="tanggal"
                        value="<?= e($absensi['tanggal']) ?>"
                        required
                    >

                </label>

                <!-- Status Kehadiran -->
                <label>

                    Status Kehadiran

                    <select name="status" required>

                        <?php foreach (['Hadir', 'Izin', 'Sakit', 'Alpa'] as $st): ?>

                            <option
                                value="<?= $st ?>"
                                <?= $absensi['status'] === $st ? 'selected' : '' ?>
                            >

                                <?= $st ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </label>

                <!-- Keterangan -->
                <label class="full-width">

                    Keterangan

                    <textarea
                        name="keterangan"
                        rows="4"
                        placeholder="Masukkan keterangan (opsional)"
                    ><?= e($absensi['keterangan']) ?></textarea>

                </label>

                <!-- Tombol -->
                <div class="form-actions">

                    <a
                        class="btn btn-plain"
                        href="index.php"
                    >
                        Batal
                    </a>

                    <button
                        class="btn btn-primary"
                        type="submit"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </section>

    </main>

</div>

</body>
</html>