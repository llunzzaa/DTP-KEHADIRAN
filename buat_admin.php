
<?php

require_once 'koneksi.php';

$nama = 'Administrator';
$username = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$role = 'admin';

$sql = "INSERT INTO users
        (nama, username, password, role)
        VALUES
        (:nama, :username, :password, :role)";

$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([
        ':nama' => $nama,
        ':username' => $username,
        ':password' => $password,
        ':role' => $role
    ]);

    echo "Akun admin berhasil dibuat.";

} catch (PDOException $e) {
    echo "Gagal membuat akun. Username mungkin sudah digunakan.";
}