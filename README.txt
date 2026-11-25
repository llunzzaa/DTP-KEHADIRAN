HADIRIN - SISTEM MANAJEMEN KEHADIRAN SISWA

KEBUTUHAN:
- XAMPP (Apache dan MySQL)
- PHP dengan PDO MySQL
- Browser

CARA MENJALANKAN:

1. Ekstrak folder HADIRIN ke C:\xampp\htdocs\
2. Buka XAMPP Control Panel.
3. Jalankan Apache dan MySQL.
4. Buka http://localhost/phpmyadmin
5. Pilih tab Import.
6. Impor file database/hadirindb.sql.
7. Pastikan konfigurasi database di koneksi.php sesuai:
   Host: localhost
   Database: hadirindb
   Username: root
   Password: kosong (default XAMPP)
8. Buka http://localhost/HADIRIN/

FITUR:

- Dashboard ringkasan jumlah siswa dan absensi hari ini.
- CRUD Data Siswa.
- CRUD Absensi.
- Pencarian dan filter data.
- Statistik jumlah dan persentase status absensi.

STRUKTUR:

HADIRIN/
  assets/style.css
  database/hadirindb.sql
  siswa/index.php
  siswa/tambah.php
  siswa/edit.php
  siswa/hapus.php
  absensi/index.php
  absensi/tambah.php
  absensi/edit.php
  absensi/hapus.php
  koneksi.php
  fungsi.php
  index.php
  dashboard.php
  statistik.php
  README.txt

CATATAN:

- Status absensi: Hadir, Izin, Sakit, Alpa.
- Satu siswa hanya dapat memiliki satu catatan absensi
  pada tanggal yang sama.
- Penghapusan data siswa akan menghapus absensi milik
  siswa tersebut karena ON DELETE CASCADE.