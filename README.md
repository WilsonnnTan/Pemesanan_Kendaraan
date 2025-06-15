# Sistem Manajemen Kendaraan Sekawan Media

Sistem manajemen kendaraan untuk mengelola penggunaan kendaraan perusahaan dengan fitur approval multi-level.

## Spesifikasi Teknis

- **Framework**: CodeIgniter 4
- **PHP Version**: 8.1 atau lebih tinggi
- **Database**: MySQL 8.0.42 (Railway)
- **Web Server**: Apache
- **Container**: Docker

### Prasyarat
- Akun Railway
- Database MySQL dari Railway

### Catatan Penting
- Pastikan semua environment variables sudah diatur di Railway
- Railway akan otomatis menggunakan port yang disediakan
- Database harus sudah dibuat dan dikonfigurasi di Railway

## Daftar Akun Default

### Admin
- Username: `admin`
- Password: `admin123`
- Role: Administrator
- Akses: Manajemen pengguna, kendaraan, dan laporan

### Approver Level 1
- Username: `approver1`
- Password: `approver123`
- Role: Approver
- Level: 1
- Akses: Approval level pertama

### Approver Level 2
- Username: `approver2`
- Password: `approver123`
- Role: Approver
- Level: 2
- Akses: Approval level kedua

## Panduan Penggunaan

### 1. Login
- Buka aplikasi di browser
- Masukkan username dan password sesuai role
- Klik tombol "Login"

### 2. Admin
#### Manajemen Pengguna
- Lihat daftar pengguna

#### Manajemen Kendaraan
- Tambah kendaraan
- Atur driver untuk kendaraan
- Lihat status kendaraan

#### Laporan
- Lihat laporan penggunaan kendaraan
- Export laporan ke Excel/PDF

### 3. Approver
#### Approval Level 1
- Lihat daftar permintaan yang menunggu approval
- Setujui/tolak permintaan
- Berikan catatan jika diperlukan

#### Approval Level 2
- Setujui/tolak permintaan
- Berikan catatan jika diperlukan

### 4. Penggunaan Kendaraan
1. Admin membuat permintaan penggunaan kendaraan
2. Approver Level 1 melakukan approval
3. Approver Level 2 melakukan approval
4. Kendaraan dapat digunakan setelah kedua level disetujui

## Fitur Utama

1. **Manajemen Pengguna**
   - Multi-role (Admin, Approver)
   - Multi-level approval
   - Manajemen akses

2. **Manajemen Kendaraan**
   - Data kendaraan
   - Penugasan driver
   - Status kendaraan

3. **Sistem Approval**
   - Multi-level approval
   - Tracking status

4. **Laporan**
   - Export Excel
   - Export PDF

## Keamanan

- Password di-hash menggunakan `password_hash()`
- Validasi input
- Proteksi CSRF
- Session management
- Role-based access control