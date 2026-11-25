
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function cekLogin()
{
    if (!isset($_SESSION['id_user'])) {
        header('Location: /HADIRIN/login/login.php');
        exit;
    }
}

function cekAdmin()
{
    cekLogin();

    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        exit('Anda tidak memiliki hak akses.');
    }
}