
<?php

require_once '../koneksi.php';
require_once '../fungsi.php';

// Ambil ID siswa dari URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

// Ambil data siswa
$stmt = $pdo->prepare("
    SELECT *
    FROM siswa
    WHERE id_siswa = ?
");

$stmt->execute([$id]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

// Periksa apakah data siswa tersedia
if (!$siswa) {
    header('Location: index.php');
    exit;
}

// Ambil daftar kelas dari tabel kelas
$kelasList = $pdo->query("
    SELECT id_kelas, nama_kelas
    FROM kelas
    ORDER BY nama_kelas ASC
")->fetchAll(PDO::FETCH_ASSOC);

$error = '';

// Nilai awal id kelas
$id_kelas = $siswa['id_kelas'];

// Proses formulir edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nis = trim($_POST['nis'] ?? '');
    $nama = trim($_POST['nama_siswa'] ?? '');
    $id_kelas = $_POST['id_kelas'] ?? '';
    $jk = $_POST['jenis_kelamin'] ?? '';

    // Validasi input
    if (
        $nis === '' ||
        $nama === '' ||
        !filter_var($id_kelas, FILTER_VALIDATE_INT) ||
        (int)$id_kelas <= 0 ||
        $jk === ''
    ) {

        $error = 'Semua kolom wajib diisi.';

    } elseif (
        !in_array($jk, ['Laki-laki', 'Perempuan'], true)
    ) {

        $error = 'Jenis kelamin tidak valid.';

    } else {

        // Pastikan kelas tersedia
        $cekKelas = $pdo->prepare("
            SELECT COUNT(*)
            FROM kelas
            WHERE id_kelas = ?
        ");

        $cekKelas->execute([$id_kelas]);

        if ($cekKelas->fetchColumn() == 0) {

            $error = 'Kelas tidak ditemukan.';

        } else {

            // Periksa NIS agar tidak duplikat
            $cek = $pdo->prepare("
                SELECT COUNT(*)
                FROM siswa
                WHERE nis = ?
                AND id_siswa <> ?
            ");

            $cek->execute([$nis, $id]);

            if ($cek->fetchColumn() > 0) {

                $error = 'NIS sudah digunakan siswa lain.';

            } else {

                // Perbarui data siswa menggunakan id_kelas (FK)
                $update = $pdo->prepare("
                    UPDATE siswa
                    SET nis = ?,
                        nama_siswa = ?,
                        id_kelas = ?,
                        jenis_kelamin = ?
                    WHERE id_siswa = ?
                ");

                $update->execute([
                    $nis,
                    $nama,
                    $id_kelas,
                    $jk,
                    $id
                ]);

                header('Location: index.php?pesan=ubah');
                exit;
            }
        }
    }

    // Pertahankan input jika terjadi kesalahan
    $siswa = array_merge($siswa, [
        'nis' => $nis,
        'nama_siswa' => $nama,
        'jenis_kelamin' => $jk
    ]);
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Siswa | HADIRIN</title>

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

    </aside>

    <main class="main-content">

        <header class="topbar">

            <div>
                <p class="eyebrow">MANAJEMEN DATA</p>

                <h1>Edit Siswa</h1>

                <p class="muted">
                    Perbarui informasi siswa.
                </p>
            </div>

        </header>

        <section class="form-panel">

            <div class="panel-heading">

                <div>
                    <h2>Formulir Siswa</h2>

                    <p class="muted">
                        Ubah data yang diperlukan.
                    </p>
                </div>

            </div>

            <?php if ($error): ?>
                <div class="alert error">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="form-grid">

                <label>
                    NIS

                    <input
                        type="text"
                        name="nis"
                        maxlength="20"
                        value="<?= e($siswa['nis']) ?>"
                        required
                    >
                </label>

                <label>
                    Nama Siswa

                    <input
                        type="text"
                        name="nama_siswa"
                        maxlength="100"
                        value="<?= e($siswa['nama_siswa']) ?>"
                        required
                    >
                </label>

                <label>
                    Kelas

                    <select name="id_kelas" required>

                        <option value="">
                            Pilih kelas
                        </option>

                        <?php foreach ($kelasList as $k): ?>

                            <option
                                value="<?= (int)$k['id_kelas'] ?>"
                                <?= (string)$id_kelas === (string)$k['id_kelas'] ? 'selected' : '' ?>
                            >
                                <?= e($k['nama_kelas']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </label>

                <label>
                    Jenis Kelamin

                    <select name="jenis_kelamin" required>

                        <option value="">
                            Pilih jenis kelamin
                        </option>

                        <option
                            value="Laki-laki"
                            <?= $siswa['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>
                        >
                            Laki-laki
                        </option>

                        <option
                            value="Perempuan"
                            <?= $siswa['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>
                        >
                            Perempuan
                        </option>

                    </select>
                </label>

                <div class="form-actions">

                    <a class="btn btn-plain" href="index.php">
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