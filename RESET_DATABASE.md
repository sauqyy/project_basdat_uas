# Panduan Reset Database dan Seed Data Baru

## Cara Menggunakan

### Opsi 1: Menggunakan Script Batch (Windows)

Jalankan file `reset_and_seed.bat`:

```bash
reset_and_seed.bat
```

Script ini akan:
1. Menghapus semua tabel dan membuat ulang
2. Menjalankan seeder untuk mengisi data baru
3. Menampilkan informasi akun login

### Opsi 2: Menggunakan Command Manual

**Reset database dan seed ulang:**
```bash
php artisan migrate:fresh --force
php artisan db:seed --class=FreshDataSeeder --force
```

**Atau dalam satu command:**
```bash
php artisan migrate:fresh --seed --seeder=FreshDataSeeder --force
```

## Data yang Akan Dibuat

### 1. Akun Super Admin
- **Email:** `superadmin@kampusmerdeka.ac.id`
- **Password:** `password`
- **Role:** `admin_super`

### 2. Akun Admin Prodi (5 akun)

| Prodi | Email | Password |
|-------|-------|----------|
| Teknologi Sains Data | `admin.tsd@kampusmerdeka.ac.id` | `password` |
| Rekayasa Nanoteknologi | `admin.rn@kampusmerdeka.ac.id` | `password` |
| Teknik Industri | `admin.ti@kampusmerdeka.ac.id` | `password` |
| Teknik Elektro | `admin.te@kampusmerdeka.ac.id` | `password` |
| Teknik Robotika dan Kecerdasan Buatan | `admin.trkb@kampusmerdeka.ac.id` | `password` |

### 3. Akun Dosen (20 akun - 4 per prodi)

**Teknologi Sains Data:**
- `tsd.dosen1@kampusmerdeka.ac.id` - Password: `password`
- `tsd.dosen2@kampusmerdeka.ac.id` - Password: `password`
- `tsd.dosen3@kampusmerdeka.ac.id` - Password: `password`
- `tsd.dosen4@kampusmerdeka.ac.id` - Password: `password`

**Rekayasa Nanoteknologi:**
- `rn.dosen1@kampusmerdeka.ac.id` - Password: `password`
- `rn.dosen2@kampusmerdeka.ac.id` - Password: `password`
- `rn.dosen3@kampusmerdeka.ac.id` - Password: `password`
- `rn.dosen4@kampusmerdeka.ac.id` - Password: `password`

**Teknik Industri:**
- `ti.dosen1@kampusmerdeka.ac.id` - Password: `password`
- `ti.dosen2@kampusmerdeka.ac.id` - Password: `password`
- `ti.dosen3@kampusmerdeka.ac.id` - Password: `password`
- `ti.dosen4@kampusmerdeka.ac.id` - Password: `password`

**Teknik Elektro:**
- `te.dosen1@kampusmerdeka.ac.id` - Password: `password`
- `te.dosen2@kampusmerdeka.ac.id` - Password: `password`
- `te.dosen3@kampusmerdeka.ac.id` - Password: `password`
- `te.dosen4@kampusmerdeka.ac.id` - Password: `password`

**Teknik Robotika dan Kecerdasan Buatan:**
- `trkb.dosen1@kampusmerdeka.ac.id` - Password: `password`
- `trkb.dosen2@kampusmerdeka.ac.id` - Password: `password`
- `trkb.dosen3@kampusmerdeka.ac.id` - Password: `password`
- `trkb.dosen4@kampusmerdeka.ac.id` - Password: `password`

### 4. Ruangan

**Setiap prodi memiliki:**
- 3 ruangan kelas (kapasitas 30-50)
- 2 ruangan lab (kapasitas 25-40)

**Plus:**
- 2 auditorium umum (kapasitas 80-150)

**Total: ~27 ruangan**

### 5. Mata Kuliah

**Teknologi Sains Data:** 6 mata kuliah
- Pemrograman Dasar (3 SKS)
- Struktur Data (3 SKS)
- Basis Data (4 SKS - Praktikum)
- Data Mining (3 SKS)
- Machine Learning (4 SKS - Praktikum)
- Big Data Analytics (3 SKS)

**Rekayasa Nanoteknologi:** 5 mata kuliah
- Kimia Dasar (3 SKS)
- Fisika Material (3 SKS)
- Sintesis Nanomaterial (4 SKS - Praktikum)
- Karakterisasi Material (3 SKS)
- Nanoteknologi Terapan (4 SKS - Praktikum)

**Teknik Industri:** 5 mata kuliah
- Pengantar Teknik Industri (3 SKS)
- Statistika Industri (3 SKS)
- Sistem Produksi (4 SKS - Praktikum)
- Manajemen Operasi (3 SKS)
- Optimasi Sistem (3 SKS)

**Teknik Elektro:** 5 mata kuliah
- Rangkaian Listrik (3 SKS)
- Elektronika Dasar (3 SKS)
- Praktikum Elektronika (4 SKS - Praktikum)
- Sistem Tenaga Listrik (3 SKS)
- Kontrol Sistem (3 SKS)

**Teknik Robotika dan Kecerdasan Buatan:** 5 mata kuliah
- Pengantar Robotika (3 SKS)
- Pemrograman Robot (3 SKS)
- Praktikum Robotika (4 SKS - Praktikum)
- Kecerdasan Buatan (3 SKS)
- Sistem Kendali Robot (4 SKS - Praktikum)

**Total: ~26 mata kuliah**

## Catatan Penting

1. **Semua password adalah:** `password`
2. **Semua data akan dihapus** sebelum diisi ulang
3. **Jadwal tidak dibuat otomatis** - Anda perlu generate jadwal setelah seeding
4. **Preferensi dosen tidak dibuat** - Dosen perlu set preferensi sendiri atau admin bisa set untuk mereka
5. **Fact tables tidak di-populate** - Jalankan `php artisan fact:populate` setelah generate jadwal

## Langkah Setelah Reset

1. **Login sebagai Super Admin atau Admin Prodi**
2. **Set Preferensi Dosen** (opsional, tapi disarankan untuk hasil yang lebih baik)
3. **Generate Jadwal** menggunakan tombol "Generate Jadwal AI"
4. **Populate Fact Tables** (untuk dashboard data warehouse):
   ```bash
   php artisan fact:populate
   ```

## Troubleshooting

### Error: Foreign key constraint

Jika terjadi error foreign key constraint, pastikan menggunakan `migrate:fresh` yang akan menghapus semua tabel terlebih dahulu.

### Error: Table already exists

Jalankan `php artisan migrate:fresh` terlebih dahulu untuk menghapus semua tabel.

### Data tidak muncul

Pastikan seeder berjalan tanpa error. Cek log dengan:
```bash
tail -f storage/logs/laravel.log
```

## File yang Dibuat

- `database/seeders/FreshDataSeeder.php` - Seeder utama
- `reset_and_seed.bat` - Script untuk Windows
- `reset_database.php` - Script PHP alternatif

