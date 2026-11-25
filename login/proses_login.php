
<?php

require_once '../koneksi.php';
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Periksa token keamanan
$token = $_POST['csrf_token'] ?? '';

if (
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    exit('Permintaan tidak valid.');
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header('Location: login.php?error=1');
    exit;
}

// Cari username di database
$sql = "SELECT * FROM users WHERE username = :username";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':username' => $username
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Periksa password
if ($user && password_verify($password, $user['password'])) {

    session_regenerate_id(true);

    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    header('Location: ../dashboard.php');
    exit;

} else {
    header('Location: login.php?error=1');
    exit;
}