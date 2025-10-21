# User Management Features

## Fitur yang Telah Ditambahkan

### 1. Sistem Login Berbasis Database
- **File**: `login.php`
- **Perubahan**: Menghapus hardcode login, sekarang menggunakan database `admin_users`
- **Fitur**: 
  - Validasi username dan password dari database
  - Password hashing dengan `password_verify()`
  - Session management dengan data user lengkap

### 2. User Management (Admin Only)
- **File**: `admin/user_management.php`
- **Fitur**:
  - Tambah user baru dengan role (admin/user)
  - Edit data user (username, full_name, email, role, status)
  - Hapus user (tidak bisa hapus akun sendiri)
  - Tampilan tabel dengan status dan role
  - Hanya admin yang bisa akses

### 3. Change Password
- **File**: `admin/change_password.php`
- **Fitur**:
  - Ganti password dengan validasi password lama
  - Konfirmasi password baru
  - Validasi minimal 6 karakter
  - Semua user bisa akses

### 4. Dashboard Updates
- **File**: `dashboard.php`
- **Perubahan**:
  - Menu User Management (hanya untuk admin)
  - Menu Change Password di sidebar
  - Display nama user di navbar atas
  - Dropdown menu dengan opsi: Change Password, View Website, Logout

## Database Structure

Tabel `admin_users` dengan struktur:
- `id` (INTEGER) - Primary key
- `username` (TEXT) - Username unik
- `password` (TEXT) - Password ter-hash
- `email` (TEXT) - Email user
- `full_name` (TEXT) - Nama lengkap
- `role` (TEXT) - 'admin' atau 'user'
- `status` (INTEGER) - 1 = Active, 0 = Inactive
- `created_at` (DATETIME) - Tanggal dibuat
- `updated_at` (DATETIME) - Tanggal diupdate

## Default Admin User
- **Username**: admin
- **Password**: admin123
- **Role**: admin
- **Status**: Active

## Security Features
- Password hashing dengan `password_hash()` dan `password_verify()`
- Input sanitization dengan `sanitizeInput()`
- Session management yang aman
- Role-based access control
- Prevention dari self-deletion

## Cara Penggunaan

1. **Login**: Gunakan username/password dari database
2. **User Management**: Hanya admin yang bisa akses menu ini
3. **Change Password**: Semua user bisa ganti password sendiri
4. **Dashboard**: Menampilkan nama user dan menu sesuai role
