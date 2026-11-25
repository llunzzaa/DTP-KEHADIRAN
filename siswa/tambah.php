
<?php
require_once '../koneksi.php';
require_once '../fungsi.php';

$error = '';

// Ambil daftar kelas
$stmtKelas = $pdo->query("
    SELECT id_kelas, nama_kelas
    FROM kelas
    ORDER BY nama_kelas ASC
");
$kelas = $stmtKelas->fetchAll(PDO::FETCH_ASSOC);

// Proses penyimpanan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nis = trim($_POST['nis'] ?? '');
    $nama = trim($_POST['nama_siswa'] ?? '');
    $jk = trim($_POST['jenis_kelamin'] ?? '');
    $id_kelas = (int) ($_POST['id_kelas'] ?? 0);

    if (
        $nis === '' ||
        $nama === '' ||
        !in_array($jk, ['Laki-laki', 'Perempuan'], true) ||
        $id_kelas <= 0
    ) {
        $error = 'Semua data wajib diisi dengan benar.';
    } else {
        try {
            $sql = "INSERT INTO siswa
                    (nis, nama_siswa, jenis_kelamin, id_kelas)
                    VALUES
                    (:nis, :nama, :jk, :id_kelas)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nis' => $nis,
                ':nama' => $nama,
                ':jk' => $jk,
                ':id_kelas' => $id_kelas
            ]);

            header('Location: index.php?pesan=tambah');
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $error = 'NIS sudah terdaftar atau kelas tidak valid.';
            } else {
                $error = 'Gagal menyimpan data: ' . $e->getMessage();
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
    <title>Tambah Siswa | HADIRIN</title>
    <link rel="stylesheet" href="../assets/style.css">

    <style>
        .student-form {
            max-width: 850px;
        }

        .student-form .form-body {
            padding: 28px;
        }

        .student-form .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .student-form .field {
            display: flex;
            flex-direction: column;
            gap: 9px;
            margin-bottom: 22px;
        }

        .student-form .field.full {
            grid-column: 1 / -1;
        }

        .student-form label {
            font-weight: 600;
            color: #34466b;
            font-size: 14px;
        }

        .student-form input,
        .student-form select {
            width: 100%;
            box-sizing: border-box;
            min-height: 48px;
            padding: 12px 15px;
            border: 1px solid #dce3f0;
            border-radius: 10px;
            background: #fff;
            color: #34466b;
            font: inherit;
            outline: none;
            transition: .2s;
        }

        .student-form input:focus,
        .student-form select:focus {
            border-color: #5479cc;
            box-shadow: 0 0 0 3px rgba(84, 121, 204, .12);
        }

        .student-form .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 20px;
            border-top: 1px solid #edf0f6;
        }

        .student-form .form-actions a,
        .student-form .form-actions button {
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 22px;
            border: 0;
            border-radius: 9px;
            text-decoration: none;
            cursor: pointer;
            font: inherit;
            font-weight: 600;
        }

        .student-form .save-button {
            background: #5479cc;
            color: white;
        }

        .student-form .cancel-button {
            background: #edf2ff;
            color: #5479cc;
        }

        .student-form .error-message {
            background: #fff0f0;
            color: #b83232;
            border: 1px solid #ffd6d6;
            border-radius: 10px;
            padding: 13px 16px;
            margin: 20px 28px 0;
        }

        @media (max-width: 650px) {
            .student-form .form-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .student-form .field.full {
                grid-column: auto;
            }

            .student-form .form-body {
                padding: 20px;
            }
        }
    </style>
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
            <a class="active" href="index.php">♙ <span>Data Siswa</span></a>
            <a href="../absensi/index.php">▣ <span>Absensi</span></a>
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
                <p class="eyebrow">MANAJEMEN SISWA</p>
                <h1>Tambah Siswa</h1>
                <p class="muted">Masukkan informasi siswa baru.</p>
            </div>

            <div class="admin-chip">
                <span class="avatar">A</span>
                Administrator
            </div>
        </header>

        <section class="panel student-form">

            <div class="panel-heading">
                <div>
                    <h2>Formulir Data Siswa</h2>
                    <p class="muted">
                        Lengkapi seluruh informasi di bawah ini.
                    </p>
                </div>
            </div>

            <?php if ($error !== ''): ?>
                <div class="error-message">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <div class="form-body">

                <form method="POST">

                    <div class="form-grid">

                        <div class="field">
                            <label for="nis">Nomor Induk Siswa (NIS)</label>
                            <input
                                type="text"
                                id="nis"
                                name="nis"
                                placeholder="Contoh: 2026001"
                                value="<?= e($_POST['nis'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="nama_siswa">Nama Lengkap</label>
                            <input
                                type="text"
                                id="nama_siswa"
                                name="nama_siswa"
                                placeholder="Masukkan nama siswa"
                                value="<?= e($_POST['nama_siswa'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select
                                name="jenis_kelamin"
                                id="jenis_kelamin"
                                required
                            >
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki"
                                    <?= (($_POST['jenis_kelamin'] ?? '') === 'Laki-laki') ? 'selected' : '' ?>>
                                    Laki-laki
                                </option>
                                <option value="Perempuan"
                                    <?= (($_POST['jenis_kelamin'] ?? '') === 'Perempuan') ? 'selected' : '' ?>>
                                    Perempuan
                                </option>
                            </select>
                        </div>

                        <div class="field">
                            <label for="id_kelas">Kelas</label>
                            <select name="id_kelas" id="id_kelas" required>
                                <option value="">-- Pilih Kelas --</option>

                                <?php foreach ($kelas as $k): ?>
                                    <option
                                        value="<?= (int) $k['id_kelas'] ?>"
                                        <?= (($_POST['id_kelas'] ?? '') == $k['id_kelas']) ? 'selected' : '' ?>
                                    >
                                        <?= e($k['nama_kelas']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="cancel-button">
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="save-button"
                            <?= empty($kelas) ? 'disabled' : '' ?>
                        >
                            Simpan Data Siswa
                        </button>
                    </div>

                </form>

            </div>

        </section>

        <footer class="footer">
            © <?= date('Y') ?> HADIRIN · Sistem Manajemen Kehadiran Siswa
        </footer>

    </main>

</div>

</body>
</html>