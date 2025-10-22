# Superior Admin Dashboard (PHP + SQLite)

## Deskripsi
Website company profile untuk PT. Superior Teknik Indonesia dengan admin panel untuk mengelola konten website. Dibangun dengan PHP, Bootstrap 5, dan SQLite database.

## Fitur Utama

### Website Frontend
- **Home Section**: Hero section dengan title, subtitle, dan call-to-action button
- **About Section**: Informasi perusahaan dengan gambar
- **Services Section**: Layanan Mechanical dan Electrical Engineering
- **Vision & Mission**: Visi dan misi perusahaan
- **Projects Section**: Portfolio proyek dengan gambar dan detail
- **Contact Section**: Informasi kontak dan WhatsApp integration

### Admin Panel
- **Dashboard**: Overview statistik dan quick actions
- **Content Management**: Edit semua section website
- **Project Management**: CRUD operations untuk proyek
- **User Management**: Manajemen user admin (role-based access)
- **Image Upload**: Upload dan manage gambar untuk website
- **Responsive Design**: Mobile-friendly interface

### Keamanan
- **Session Management**: Secure session handling
- **Password Hashing**: bcrypt password encryption
- **Input Sanitization**: XSS protection
- **Role-based Access**: Admin dan User roles
- **CSRF Protection**: Form token validation

## Setup

### Persyaratan
- PHP 7.4 atau lebih tinggi
- SQLite extension
- Web server (Apache/Nginx) atau PHP built-in server

### Instalasi
1. Clone repository:
   ```bash
   git clone <repository-url>
   cd superior
   ```

2. Set permissions untuk folder pictures:
   ```bash
   chmod 755 pictures/
   ```

3. Konfigurasi database:
   - Database SQLite akan dibuat otomatis di `website.db`
   - Tabel akan dibuat saat pertama kali diakses

4. Jalankan server:
   ```bash
   # Menggunakan PHP built-in server
   php -S localhost:8000
   
   # Atau menggunakan Apache/Nginx
   # Pastikan document root mengarah ke folder superior
   ```

5. Akses website:
   - Frontend: `http://localhost:8000/`
   - Admin Panel: `http://localhost:8000/login.php`

### Default Login
- **Username**: admin
- **Password**: admin123
- **Role**: Admin (full access)

## Struktur File

```
superior/
├── admin/                    # Admin panel files
│   ├── includes/            # Reusable components
│   │   ├── sidebar.php      # Sidebar component
│   │   ├── navbar.php       # Navbar component
│   │   ├── styles.php       # Common styles
│   │   └── scripts.php      # Common scripts
│   ├── about.php           # Edit About section
│   ├── contact.php         # Edit Contact section
│   ├── home.php            # Edit Home section
│   ├── mission.php         # Edit Mission section
│   ├── projects.php        # Manage Projects
│   ├── projects_add.php    # Add new project
│   ├── projects_edit.php   # Edit project
│   ├── services.php        # Edit Services section
│   ├── user_management.php # User management
│   ├── vision.php          # Edit Vision section
│   └── change_password.php # Change password
├── api/                     # API endpoints
├── pictures/               # Uploaded images
├── static/                  # CSS/JS files
├── templates/              # HTML templates
├── config.php              # Database configuration
├── dashboard.php           # Admin dashboard
├── index.php              # Main website
├── login.php              # Login page
└── website.db             # SQLite database
```

## API Endpoints

### Public API
- `GET /api/get_all` - Get all website content (JSON)
- `GET /api/health` - Health check

### Admin API (requires authentication)
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/dashboard` - Dashboard statistics
- `POST /api/projects_add` - Add new project
- `POST /api/projects_edit` - Edit project
- `POST /api/projects_delete` - Delete project
- `POST /api/upload_image` - Upload image

## Database Schema

### Tables
- `admin_users` - User accounts
- `home_content` - Home section content
- `about_content` - About section content
- `services` - Services content
- `vision_content` - Vision content
- `mission_content` - Mission content
- `contact_content` - Contact information
- `projects` - Project portfolio

## Deployment

### Production Setup
1. **Web Server**: Gunakan Apache/Nginx dengan PHP-FPM
2. **Database**: SQLite sudah included, atau migrate ke MySQL/PostgreSQL
3. **Security**: 
   - Ganti default password
   - Set proper file permissions
   - Enable HTTPS
   - Configure firewall
4. **Backup**: Regular backup database dan uploaded files

### Environment Variables
```php
// config.php
define('DB_PATH', 'website.db');
define('UPLOAD_FOLDER', 'pictures');
define('SECRET_KEY', 'your-secret-key-here');
```

## Features Update

### Latest Updates
- **Modular Admin Interface**: Sidebar dan navbar components untuk konsistensi
- **User Management**: Role-based access control
- **Responsive Design**: Mobile-optimized admin panel
- **Image Management**: Upload dan preview gambar
- **Project Portfolio**: Full CRUD untuk project management
- **Security Enhancements**: Input sanitization dan session management

### Admin Panel Features
- **Dashboard**: Statistics dan quick actions
- **Content Management**: Edit semua section website
- **Project Management**: Add, edit, delete projects
- **User Management**: Manage admin users (admin only)
- **Password Change**: Secure password update
- **Responsive Sidebar**: Collapsible navigation

## Troubleshooting

### Common Issues
1. **Database Error**: Pastikan SQLite extension enabled
2. **Upload Error**: Check folder permissions untuk `pictures/`
3. **Session Error**: Pastikan session directory writable
4. **Login Issues**: Check database dan user credentials

### Logs
- PHP errors: Check web server error logs
- Database: SQLite database di `website.db`
- Upload: Check `pictures/` folder permissions

## Support
Untuk bantuan teknis atau bug report, silakan buat issue di repository atau hubungi developer.

## License
Private project untuk PT. Superior Teknik Indonesia.
