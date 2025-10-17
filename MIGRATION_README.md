# Migrasi dari Python Flask ke PHP Native

## Overview
Proyek ini telah berhasil dimigrasikan dari Python Flask ke PHP native untuk kompatibilitas dengan shared hosting yang tidak mendukung Python.

## Struktur File Baru

```
public_html/
├── index.php                 # Halaman utama website
├── login.php                 # Halaman login admin
├── dashboard.php             # Dashboard admin
├── config.php                # Konfigurasi database dan helper functions
├── .htaccess                 # Apache rewrite rules
├── api/                      # API endpoints (menggantikan Flask routes)
│   ├── login.php
│   ├── logout.php
│   ├── check_auth.php
│   ├── get_all.php
│   ├── health.php
│   ├── home_section.php
│   ├── about_section.php
│   ├── services.php
│   ├── vision.php
│   ├── mission.php
│   ├── contact.php
│   ├── projects.php
│   ├── projects_add.php
│   ├── projects_edit.php
│   ├── projects_delete.php
│   ├── dashboard.php
│   └── upload_image.php
├── admin/                    # Halaman admin (menggantikan Flask templates)
│   ├── home.php
│   ├── about.php
│   ├── contact.php
│   ├── projects.php
│   ├── projects_add.php
│   └── projects_edit.php
├── static/                   # Assets statis (CSS, JS)
│   ├── css/
│   └── js/
│       └── dashboard.js      # JavaScript untuk dashboard
├── templates/                # Template HTML (tetap sama)
├── pictures/                 # Folder untuk gambar
└── website.db               # Database SQLite (tetap sama)
```

## Perubahan Utama

### 1. Database Connection
- **Sebelum**: SQLite3 dengan Python
- **Sesudah**: PDO SQLite dengan PHP
- File: `config.php`

### 2. Authentication
- **Sebelum**: Flask sessions
- **Sesudah**: PHP sessions dengan `session_start()`
- File: `api/login.php`, `api/logout.php`, `api/check_auth.php`

### 3. API Endpoints
- **Sebelum**: Flask routes (`@app.route`)
- **Sesudah**: PHP files di folder `api/`
- Semua endpoint mengembalikan JSON dengan CORS headers

### 4. Image Upload
- **Sebelum**: Flask `request.files`
- **Sesudah**: PHP `$_FILES` dengan validasi file type
- File: `api/upload_image.php`

### 5. Template Rendering
- **Sebelum**: Flask `render_template()`
- **Sesudah**: PHP dengan `include` dan `echo`
- File: `index.php`, `dashboard.php`, `login.php`

## Fitur yang Dipertahankan

✅ **Semua fitur Flask tetap berfungsi:**
- Login/logout admin
- Dashboard dengan statistik
- CRUD untuk projects
- Edit konten (home, about, services, vision, mission, contact)
- Upload dan management gambar
- Responsive design
- API endpoints untuk frontend

## Keamanan

### Implemented Security Features:
- Input sanitization dengan `htmlspecialchars()`
- File type validation untuk upload
- SQL injection protection dengan prepared statements
- Session management yang aman
- CORS headers untuk API
- File access restrictions di `.htaccess`

## Deployment

### Requirements:
- PHP 7.4+ dengan PDO SQLite support
- Apache dengan mod_rewrite
- Shared hosting yang mendukung PHP

### Setup:
1. Upload semua file ke `public_html/`
2. Pastikan folder `pictures/` writable (chmod 755)
3. Database `website.db` harus accessible
4. Test dengan mengakses `index.php`

## API Endpoints

### Authentication
- `POST api/login.php` - Login admin
- `POST api/logout.php` - Logout
- `GET api/check_auth.php` - Check authentication status

### Content Management
- `GET api/get_all.php` - Get all website content
- `POST api/home_section.php` - Update home content
- `POST api/about_section.php` - Update about content
- `POST api/services.php` - Update services content
- `POST api/vision.php` - Update vision content
- `POST api/mission.php` - Update mission content
- `POST api/contact.php` - Update contact content

### Project Management
- `GET api/projects.php` - Get all projects
- `POST api/projects_add.php` - Add new project
- `POST api/projects_edit.php` - Edit project
- `POST api/projects_delete.php` - Delete project

### Utilities
- `GET api/health.php` - Health check
- `POST api/upload_image.php` - Upload image

## JavaScript Integration

File `static/js/dashboard.js` menyediakan:
- API helper functions
- Authentication management
- Form handling utilities
- Live preview functionality
- Error handling dan notifications

## Troubleshooting

### Common Issues:

1. **Database connection error**
   - Pastikan `website.db` ada dan accessible
   - Check permissions pada file database

2. **Image upload tidak berfungsi**
   - Pastikan folder `pictures/` writable
   - Check file size limits di PHP

3. **Session tidak berfungsi**
   - Pastikan PHP session support enabled
   - Check session save path permissions

4. **API endpoints tidak accessible**
   - Check `.htaccess` configuration
   - Pastikan mod_rewrite enabled

## Performance

### Optimizations:
- Database queries menggunakan prepared statements
- Image compression dan optimization
- Caching headers di `.htaccess`
- Gzip compression enabled
- Static file caching

## Maintenance

### Regular Tasks:
- Backup database `website.db`
- Monitor folder `pictures/` untuk file yang tidak terpakai
- Update PHP version jika tersedia
- Monitor error logs

## Support

Untuk pertanyaan atau masalah:
1. Check error logs di hosting panel
2. Test API endpoints dengan Postman/curl
3. Verify file permissions
4. Check PHP error reporting

---

**Migration completed successfully!** 🎉

Website sekarang fully compatible dengan shared hosting PHP dan semua fitur Flask telah dipertahankan.
