# Petunjuk Teknis Pengguna — Project Support

## 1. Pendahuluan
Aplikasi **Project Support** digunakan untuk mengelola project, progress/histori, status, kanban progress, laporan, dan data pendukung (RKAP, katalog, direktorat). Akses melalui browser pada URL yang disediakan administrator (default `http://localhost:8000`).

## 2. Persyaratan & Akses
- Browser modern (Chrome/Edge/Firefox).
- Akun pengguna (ID pengguna & password).
- Jaringan ke server aplikasi.
- Login di halaman **Login**. Setelah berhasil, sistem menampilkan Dashboard.
- Ubah password melalui profil pengguna di sidebar (klik nama pengguna).

## 3. Peran & Hak Akses
- **Admin (level 0)**: akses penuh ke menu Referensi (Pengguna, Direktorat, Status Projects) dan Transaksi (RKAP, Katalog, Project, Kanban, Non Project, Backup).
- **Petugas/Developer (level ≠ 0)**: akses Laporan & Dashboard. Pada fitur donut context menu, hanya dapat mengubah status project yang **Lead By**-nya sama dengan ID petugas yang login. Project yang dipimpin orang lain tidak menampilkan menu.

## 4. Navigasi Umum
Sidebar kiri berisi menu:
- **Referensi** (admin): Pengguna, Direktorat, Status Projects.
- **Transaksi** (admin): RKAP, Katalog, Project, Kanban Progress, Non Project, Backup Database.
- **Laporan** (semua): Dashboard, Project, Non Project.
Klik nama pengguna untuk ubah password; tombol **Sign Out** untuk keluar.

## 5. Dashboard
Menampilkan ringkasan:
- Total project, project berjalan/QA, distribusi per direktorat.
- Daftar project Development aktif, project QA.
- Tabel detail project (dengan pagination) yang dapat difilter.
- Kalender aktivitas project.
Gunakan klik kanan pada baris/kartu project untuk membuka **Donut Context Menu** (jika berhak).

## 6. Modul Project
**Menu: Transaksi → Project**
- Daftar project dengan kolom: Nama Project, Tanggal, Direktorat, Status, RKAP, Aksi.
- Tambah project: tombol **Tambah Project** → isi form (nama, direktorat, bagian, PIC, no surat, tanggal, RKAP, lead by, support by, dll).
- Edit/Hapus project via tombol Aksi.
- Klik nama project untuk membuka histori progress.
- Klik kanan pada baris project untuk mengubah status cepat via Donut Context Menu.

## 7. Donut Context Menu (Ubah Status Cepat)
- Aktifkan dengan klik kanan pada project (baris/kartu).
- Pilih ikon status pada donut untuk mengubah status project.
- Bagian tengah menampilkan status saat ini; hint di bawah donut.
- Notifikasi toast menampilkan hasil.
- ESC / klik backdrop / tombol tutup untuk menutup.
- Pembatasan: petugas non-admin hanya dapat membuka menu pada project yang dipimpinnya.

## 8. Kanban Progress
**Menu: Transaksi → Kanban Progress**
- Papan kolom per status project.
- Kartu project dapat di-drag & drop antar kolom untuk memindah status.
- Klik kanan pada kartu untuk Donut Context Menu.
- Klik kartu untuk detail project.

## 9. Histori Project (Hproject)
**Halaman detail project (klik nama project)**
- Daftar histori progress per project (tanggal, status, progress, catatan).
- Tambah progress manual.
- Upload evidence (file) dan unduh/lihat evidence.
- Parse NDE (jika tersedia).
- Edit/hapus record progress.

## 10. Non Project
**Menu: Transaksi → Non Project / Laporan → Non Project**
- CRUD aktivitas non-project.
- Laporan non-project menampilkan data dengan filter.

## 11. Laporan Project
**Menu: Laporan → Project**
- Tabel laporan progress project.
- Filter berdasarkan PIC/lead (untuk level 3 hanya project yang ditugaskan).
- Klik kanan pada baris untuk Donut Context Menu (jika berhak).

## 12. Backup Database
**Menu: Transaksi → Backup Database (admin)**
- Jalankan backup database.
- Unduh file backup.
- Hapus file backup.

## 13. Master Referensi (admin)
- **Pengguna**: CRUD pengguna, atur level, status, bypass.
- **Direktorat**: CRUD direktorat.
- **Status Projects (Rproject)**: CRUD status project (deskripsi, ikon).
- **RKAP**: CRUD RKAP.
- **Katalog**: CRUD katalog & versi.

## 14. Glosarium
- **Lead By**: penanggung jawab project.
- **Support By**: pendukung project.
- **Rproject**: status project.
- **RKAP**: Rencana Kerja dan Anggaran Perusahaan.
- **Hproject**: histori progress project.

## 15. Troubleshooting
- **Menu tidak muncul saat klik kanan**: kemungkinan tidak berhak (project bukan milik Anda) atau bukan area project.
- **Gagal ubah status**: cek koneksi, hak akses, dan pastikan status target valid.
- **Evidence tidak terunggah**: cek ukuran/format file dan izin folder storage.
- **Lupa password**: hubungi administrator.
- **Halaman error/blank**: refresh, bersihkan cache, atau hubungi administrator.

---
*Dokumen ini dibuat berdasarkan struktur aplikasi Project Support.*
