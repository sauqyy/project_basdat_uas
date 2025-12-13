# Panduan Menjalankan Aplikasi Laravel

## Prasyarat

Pastikan Anda sudah menginstall:
- **PHP 8.2 atau lebih tinggi**
- **Composer** (Package Manager untuk PHP)
- **SQLite** (sudah termasuk di PHP, atau gunakan MySQL/PostgreSQL)

## Langkah-langkah Menjalankan

### 1. Install Dependencies

Jalankan perintah berikut untuk menginstall semua package yang diperlukan:

```bash
composer install
```

Atau jika menggunakan composer yang sudah ada:

```bash
php composer.phar install
```

### 2. Setup Environment File

Buat file `.env` dari template (jika belum ada):

**Windows (PowerShell):**
```powershell
Copy-Item .env.example .env
```

**Linux/Mac:**
```bash
cp .env.example .env
```

Jika file `.env.example` tidak ada, buat file `.env` baru dengan konten berikut:

```env
APP_NAME="Sistem Generate Jadwal"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

APP_LOCALE=id
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=id_ID

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Generate Application Key

Generate application key untuk enkripsi:

```bash
php artisan key:generate
```

### 4. Setup Database

Aplikasi ini menggunakan **SQLite** sebagai database default. Pastikan file database ada:

**Windows (PowerShell):**
```powershell
New-Item -ItemType File -Path database/database.sqlite -Force
```

**Linux/Mac:**
```bash
touch database/database.sqlite
```

Atau jika file sudah ada, pastikan permission-nya benar.

### 5. Run Migrations

Jalankan migrasi untuk membuat tabel-tabel database:

```bash
php artisan migrate
```

### 6. (Opsional) Seed Database

Jika ingin mengisi database dengan data dummy:

```bash
php artisan db:seed
```

Atau seed specific seeder:

```bash
php artisan db:seed --class=UserAccountSeeder
```

### 7. Populate Fact Tables (Data Warehouse)

Untuk mengisi fact tables dengan data dari tabel operasional:

```bash
php artisan fact:populate
```

Atau dengan menghapus data lama terlebih dahulu:

```bash
php artisan fact:populate --fresh
```

### 8. Start Development Server

Jalankan server development Laravel:

```bash
php artisan serve
```

Server akan berjalan di `http://localhost:8000`

Untuk menjalankan di port tertentu:

```bash
php artisan serve --port=8080
```

### 9. Akses Aplikasi

Buka browser dan akses:
- **Landing Page**: http://localhost:8000
- **Login Admin**: http://localhost:8000/admin/login
- **Login Dosen**: http://localhost:8000/login

## Akun Default (jika sudah di-seed)

### Super Admin
- Email: `superadmin@kampusmerdeka.ac.id`
- Password: `password`

### Admin Prodi
- Email: `admin.tsd@kampusmerdeka.ac.id` (Teknologi Sains Data)
- Password: `password`

### Dosen
- Email: `ahmad@kampusmerdeka.ac.id`
- Password: `password`

## Troubleshooting

### Error: "SQLSTATE[HY000] [14] unable to open database file"

**Solusi:**
1. Pastikan file `database/database.sqlite` ada
2. Pastikan permission file database bisa diakses
3. Windows: Pastikan folder `database` memiliki permission write

### Error: "Class 'PDO' not found"

**Solusi:**
Install PHP SQLite extension:
- Windows: Edit `php.ini` dan uncomment `extension=pdo_sqlite`
- Linux: `sudo apt-get install php-sqlite3`
- Mac: `brew install php-sqlite`

### Error: "No application encryption key has been specified"

**Solusi:**
Jalankan: `php artisan key:generate`

### Error: "The stream or file could not be opened"

**Solusi:**
Pastikan folder `storage` dan `bootstrap/cache` memiliki permission write:
- Linux/Mac: `chmod -R 775 storage bootstrap/cache`
- Windows: Pastikan folder tidak read-only

### Port 8000 sudah digunakan

**Solusi:**
Gunakan port lain:
```bash
php artisan serve --port=8080
```

## Command Berguna Lainnya

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Optimize Application
```bash
php artisan optimize
```

### Tinker (Interactive Shell)
```bash
php artisan tinker
```

### List Routes
```bash
php artisan route:list
```

## Struktur Folder Penting

```
Project_BASDAT_UTS/
├── app/                    # Application code
│   ├── Http/
│   │   └── Controllers/    # Controllers
│   └── Models/             # Models
├── database/
│   ├── database.sqlite     # SQLite database
│   ├── migrations/         # Database migrations
│   └── seeders/            # Database seeders
├── public/                  # Public assets
├── resources/
│   └── views/              # Blade templates
├── routes/
│   └── web.php             # Web routes
├── storage/                 # Storage files
└── .env                     # Environment configuration
```

## Catatan Penting

1. **Jangan commit file `.env`** ke repository (sudah ada di `.gitignore`)
2. **Database SQLite** file (`database/database.sqlite`) juga tidak perlu di-commit
3. Pastikan **fact tables sudah ter-populate** sebelum menggunakan dashboard data warehouse
4. Untuk production, gunakan **MySQL** atau **PostgreSQL** dan ubah `DB_CONNECTION` di `.env`

## Next Steps

Setelah aplikasi berjalan:
1. Login sebagai Super Admin
2. Generate jadwal menggunakan AI
3. Populate fact tables untuk melihat dashboard data warehouse
4. Eksplorasi fitur-fitur yang tersedia

