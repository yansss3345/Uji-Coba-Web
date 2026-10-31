PANDUAN INSTALASI SISTEM PENGGAJIAN MULTI-USER

Fitur:
- Akun masing-masing untuk staf HRD.
- Role admin dan hrd.
- Hanya admin yang dapat membuat/menghapus akun.
- HRD ditolak oleh server jika membuka users.php secara langsung.
- Admin tidak dapat menghapus dirinya sendiri atau administrator terakhir.
- Data karyawan dan akun tersimpan di MySQL.
- Password di-hash, PDO prepared statements, token CSRF, session cookie HttpOnly.

Instalasi XAMPP:
1. Ekstrak folder penggajian_multiuser ke C:\xampp\htdocs\
2. Jalankan Apache dan MySQL.
3. Buka http://localhost/phpmyadmin, lalu impor database.sql.
4. Periksa config.php. XAMPP standar biasanya DB_USER=root dan DB_PASS kosong.
5. Buka http://localhost/penggajian_multiuser/setup.php.
6. Buat akun administrator pertama dengan password kuat minimal 10 karakter.
7. Setelah admin dibuat, hapus setup.php dari folder server.
8. Login di http://localhost/penggajian_multiuser/login.php.
9. Admin klik Kelola Pengguna untuk membuat akun HRD.

Data contoh karyawan: Fian (Tetap), Ayan (Training), Ade (Tetap), Gilang (Training), Roni (Tetap).
Gaji contoh: Tetap Rp3.500.000; Training Rp2.000.000. Sesuaikan sebelum digunakan nyata.

Penting untuk deployment:
- Gunakan HTTPS.
- Gunakan password unik dan kuat, backup database secara rutin.
- Batasi percobaan login dan lindungi phpMyAdmin/server.
- Tambahkan audit log, reset password, dan kebijakan nonaktif akun bila dibutuhkan.
- Ini starter internal, belum merupakan audit keamanan atau sistem payroll produksi.
