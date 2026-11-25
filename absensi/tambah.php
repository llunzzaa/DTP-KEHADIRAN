
<?php
require_once '../koneksi.php';
require_once '../fungsi.php';

$error = '';

$tanggalDefault = date('Y-m-d');

// Mengambil data siswa dan kelas
$sql = "SELECT
            s.id_siswa,
            s.nis,
            s.nama_siswa,
            k.nama_kelas
        FROM siswa s
        INNER JOIN kelas k
            ON s.id_kelas = k.id_kelas
        ORDER BY s.nama_siswa ASC";

$stmtSiswa = $pdo->query($sql);
$siswa = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);

// Proses penyimpanan absensi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_siswa = (int) ($_POST['id_siswa'] ?? 0);
    $tanggal = trim($_POST['tanggal'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if (
        $id_siswa <= 0 ||
        $tanggal === '' ||
        !in_array($status, ['Hadir', 'Izin', 'Sakit', 'Alpa'], true)
    ) {
        $error = 'Mohon lengkapi semua data dengan benar.';
    } else {

        try {
            // Pastikan siswa benar-benar tersedia
            $cek = $pdo->prepare(
                "SELECT COUNT(*) FROM siswa WHERE id_siswa = ?"
            );
            $cek->execute([$id_siswa]);

            if (!$cek->fetchColumn()) {
                $error = 'Data siswa tidak ditemukan.';
            } else {

                $sql = "INSERT INTO absensi
                        (id_siswa, tanggal, status, keterangan)
                        VALUES
                        (:id_siswa, :tanggal, :status, :keterangan)";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ':id_siswa' => $id_siswa,
                    ':tanggal' => $tanggal,
                    ':status' => $status,
                    ':keterangan' => $keterangan
                ]);

                header('Location: index.php?pesan=tambah');
                exit;
            }

        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $error = 'Siswa sudah memiliki absensi pada tanggal tersebut.';
            } else {
                $error = 'Gagal menyimpan absensi: ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Absensi | HADIRIN</title>

    <link rel="stylesheet" href="../assets/style.css">

    <style>
        .attendance-panel {
            max-width: 900px;
        }

        .attendance-form {
            padding: 28px;
        }

        .attendance-form .field {
            display: flex;
            flex-direction: column;
            gap: 9px;
            margin-bottom: 22px;
        }

        .attendance-form label {
            font-weight: 600;
            color: #34466b;
            font-size: 14px;
        }

        .attendance-form input,
        .attendance-form select,
        .attendance-form textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 13px 15px;
            border: 1px solid #dce3f0;
            border-radius: 10px;
            background: white;
            color: #34466b;
            font: inherit;
        }

        .attendance-form input,
        .attendance-form select {
            min-height: 48px;
        }

        .attendance-form textarea {
            min-height: 110px;
            resize: vertical;
        }

        .attendance-form input:focus,
        .attendance-form select:focus,
        .attendance-form textarea:focus {
            outline: none;
            border-color: #5479cc;
            box-shadow: 0 0 0 3px rgba(84,121,204,.12);
        }

        .attendance-form .actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-top: 1px solid #edf0f6;
            padding-top: 22px;
        }

        .attendance-form .actions a,
        .attendance-form .actions button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 45px;
            padding: 0 22px;
            border: 0;
            border-radius: 9px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .attendance-form .save-button {
            background: #5479cc;
            color: white;
        }

        .attendance-form .cancel-button {
            background: #edf2ff;
            color: #5479cc;
        }

        .attendance-error {
            margin: 20px 28px 0;
            padding: 14px;
            color: #b83232;
            background: #fff0f0;
            border-radius: 9px;
        }

        @media(max-width: 600px) {
            .attendance-form {
                padding: 18px;
            }
        }
    </style>
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

        <div class="sidebar-note">
            <strong>HADIRIN</strong><br>
            Kelola kehadiran dengan lebih mudah.
        </div>

    </aside>

    <!-- KONTEN UTAMA -->
    <main class="main-content">

        <header class="topbar">
            <div>
                <p class="eyebrow">MANAJEMEN KEHADIRAN</p>
                <h1>Tambah Absensi</h1>
                <p class="muted">
                    Masukkan data kehadiran siswa.
                </p>
            </div>

            <div class="admin-chip">
                <span class="avatar">A</span>
                Administrator
            </div>
        </header>

        <section class="panel attendance-panel">

            <div class="panel-heading">
                <div>
                    <h2>Form Absensi Siswa</h2>
                    <p class="muted">
                        Lengkapi informasi kehadiran berikut.
                    </p>
                </div>
            </div>

            <?php if ($error !== ''): ?>
                <div class="attendance-error">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="attendance-form">

                <div class="field">
                    <label for="id_siswa">Nama Siswa</label>

                    <select name="id_siswa" id="id_siswa" required>
                        <option value="">-- Pilih Siswa --</option>

                        <?php foreach ($siswa as $s): ?>
                            <option
                                value="<?= (int) $s['id_siswa'] ?>"
                                <?= (($_POST['id_siswa'] ?? '') == $s['id_siswa']) ? 'selected' : '' ?>
                            >
                                <?= e($s['nama_siswa']) ?>
                                - <?= e($s['nis']) ?>
                                (<?= e($s['nama_kelas']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (empty($siswa)): ?>
                        <small>Belum ada siswa yang terdaftar.</small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="tanggal">Tanggal Absensi</label>

                    <input
                        type="date"
                        name="tanggal"
                        id="tanggal"
                        value="<?= e($_POST['tanggal'] ?? $tanggalDefault) ?>"
                        required
                    >
                </div>

                <div class="field">
                    <label for="status">Status Kehadiran</label>

                    <select name="status" id="status" required>
                        <option value="">-- Pilih Status --</option>

                        <?php foreach (['Hadir', 'Izin', 'Sakit', 'Alpa'] as $st): ?>
                            <option
                                value="<?= e($st) ?>"
                                <?= (($_POST['status'] ?? '') === $st) ? 'selected' : '' ?>
                            >
                                <?= e($st) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="keterangan">Keterangan</label>

                    <textarea
                        name="keterangan"
                        id="keterangan"
                        placeholder="Masukkan keterangan jika ada..."
                    ><?= e($_POST['keterangan'] ?? '') ?></textarea>
                </div>

                <div class="actions">
                    <a href="index.php" class="cancel-button">
                        Kembali
                    </a>

                    <button
                        type="submit"
                        class="save-button"
                        <?= empty($siswa) ? 'disabled' : '' ?>
                    >
                        Simpan Absensi
                    </button>
                </div>

            </form>

        </section>

        <footer class="footer">
            © <?= date('Y') ?> HADIRIN · Sistem Manajemen Kehadiran Siswa
        </footer>

    </main>

</div>

</body>
</html>