<?php

function e($value): string {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function aktif(string $bagian): string {
    $halaman = basename($_SERVER['PHP_SELF']);

    if ($bagian === 'dashboard' && $halaman === 'dashboard.php') {
        return 'active';
    }

    if ($bagian === 'siswa' &&
        strpos($_SERVER['PHP_SELF'], '/siswa/') !== false) {
        return 'active';
    }

    if ($bagian === 'absensi' &&
        strpos($_SERVER['PHP_SELF'], '/absensi/') !== false) {
        return 'active';
    }

    if ($bagian === 'statistik' && $halaman === 'statistik.php') {
        return 'active';
    }

    return '';
}

function format_tanggal(string $tanggal): string {
    return date('d-m-Y', strtotime($tanggal));
}

?>