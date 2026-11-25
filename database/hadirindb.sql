
-- =========================================
-- DATABASE HADIRIN
-- Sistem Manajemen Kehadiran Siswa
-- =========================================

CREATE DATABASE IF NOT EXISTS hadirindb
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE hadirindb;


-- =========================================
-- TABEL SISWA
-- =========================================

CREATE TABLE IF NOT EXISTS siswa (
    id_siswa INT AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(20) NOT NULL UNIQUE,
    nama_siswa VARCHAR(100) NOT NULL,
    kelas VARCHAR(30) NOT NULL,
    jenis_kelamin VARCHAR(15) NOT NULL
) ENGINE=InnoDB;


-- =========================================
-- TABEL ABSENSI
-- =========================================

CREATE TABLE IF NOT EXISTS absensi (
    id_absensi INT AUTO_INCREMENT PRIMARY KEY,
    id_siswa INT NOT NULL,
    tanggal DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    keterangan TEXT NULL,

    CONSTRAINT fk_absensi_siswa
        FOREIGN KEY (id_siswa)
        REFERENCES siswa(id_siswa)
        ON DELETE CASCADE,

    CONSTRAINT unik_absensi_harian
        UNIQUE (id_siswa, tanggal)

) ENGINE=InnoDB;


-- =========================================
-- DATA AWAL SISWA
-- =========================================

INSERT IGNORE INTO siswa
(nis, nama_siswa, kelas, jenis_kelamin)
VALUES
('2026001', 'Andi Pratama', 'X TJAT 1', 'Laki-laki'),
('2026002', 'Siti Aulia', 'X TJAT 1', 'Perempuan'),
('2026003', 'Budi Santoso', 'X TJAT 2', 'Laki-laki');