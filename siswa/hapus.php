
<?php

require_once '../koneksi.php';

// Pastikan request menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Ambil dan validasi ID siswa
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// Hapus data siswa jika ID valid
if ($id) {

    $stmt = $pdo->prepare(
        "DELETE FROM siswa WHERE id_siswa = ?"
    );

    $stmt->execute([$id]);
}

// Kembali ke halaman data siswa
header('Location: index.php?pesan=hapus');
exit;

?>