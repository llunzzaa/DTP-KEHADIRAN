
<?php

require_once '../koneksi.php';

// Pastikan request menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Ambil dan validasi ID absensi
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// Hapus data jika ID valid
if ($id) {
    $stmt = $pdo->prepare(
        "DELETE FROM absensi WHERE id_absensi = ?"
    );

    $stmt->execute([$id]);
}

// Kembali ke halaman data absensi
header('Location: index.php?pesan=hapus');
exit;

?>