# Reset Database dengan Preferensi Dosen

## ✅ Fitur Baru

Seeder `FreshDataSeeder` sekarang sudah diperbarui untuk membuat:
- ✅ **Preferensi Global** untuk setiap dosen dengan **minimal 3 hari** dan **minimal 3 pilihan jam**
- ✅ **Preferensi Spesifik** untuk setiap mata kuliah yang diampu dosen

## 🚀 Cara Reset Database

### Opsi 1: Menggunakan Script Batch (Windows)

Jalankan file:
```bash
reset_and_seed.bat
```

### Opsi 2: Menggunakan Command Manual

```bash
# Step 1: Reset database dan migration
php artisan migrate:fresh --force

# Step 2: Seed data baru
php artisan db:seed --class=FreshDataSeeder --force
```

### Opsi 3: Menggunakan Command Sekaligus

```bash
php artisan migrate:fresh --seed --seeder=FreshDataSeeder --force
```

## 📊 Data yang Dibuat

### 1. Akun Pengguna
- **1 Super Admin**
- **5 Admin Prodi** (1 per program studi)
- **20 Dosen** (4 per program studi)

### 2. Ruangan
- **3 Kelas** per prodi = 15 kelas
- **2 Lab** per prodi = 10 lab
- **2 Auditorium** umum
- **Total: 27 ruangan**

### 3. Mata Kuliah
- **5-6 mata kuliah** per prodi
- **Total: ~26 mata kuliah**

### 4. Preferensi Dosen

#### Preferensi Global (untuk semua mata kuliah)
- **Minimal 3 hari** dari: Senin, Selasa, Rabu, Kamis, Jumat
- **Minimal 3 pilihan jam** dari berbagai slot waktu
- Contoh kombinasi:
  - Hari: `['Senin', 'Selasa', 'Rabu']`
  - Jam: `['08:00-09:00', '09:00-10:00', '10:00-11:00', '13:00-14:00', '14:00-15:00']`

#### Preferensi Spesifik (per mata kuliah)
- Setiap mata kuliah memiliki preferensi sendiri
- Variasi hari dan jam untuk fleksibilitas

## 📝 Detail Preferensi

### Opsi Hari (Minimal 3):
1. `['Senin', 'Selasa', 'Rabu']`
2. `['Selasa', 'Rabu', 'Kamis']`
3. `['Rabu', 'Kamis', 'Jumat']`
4. `['Senin', 'Rabu', 'Jumat']`
5. `['Senin', 'Selasa', 'Kamis']`
6. `['Selasa', 'Kamis', 'Jumat']`
7. `['Senin', 'Rabu', 'Kamis']`
8. `['Selasa', 'Rabu', 'Jumat']`

### Opsi Jam (Minimal 3):
1. `['08:00-09:00', '09:00-10:00', '10:00-11:00', '13:00-14:00', '14:00-15:00']`
2. `['08:00-09:00', '10:00-11:00', '13:00-14:00', '14:00-15:00', '15:00-16:00']`
3. `['09:00-10:00', '10:00-11:00', '11:00-12:00', '13:00-14:00', '15:00-16:00']`
4. `['08:00-09:00', '09:00-10:00', '13:00-14:00', '14:00-15:00', '16:00-17:00']`
5. `['08:00-09:00', '10:00-11:00', '11:00-12:00', '14:00-15:00', '15:00-16:00']`

## ✅ Verifikasi Data

Setelah seeding, verifikasi dengan:

```bash
php artisan tinker
```

```php
// Cek jumlah data
\App\Models\User::count(); // Harusnya 26
\App\Models\User::where('role', 'dosen')->count(); // Harusnya 20
\App\Models\PreferensiDosen::count(); // Harusnya > 20 (global + spesifik)
\App\Models\MataKuliah::count(); // Harusnya ~26
\App\Models\Ruangan::count(); // Harusnya 27

// Cek preferensi global
$pref = \App\Models\PreferensiDosen::whereNull('mata_kuliah_id')->first();
$hari = is_array($pref->preferensi_hari) ? $pref->preferensi_hari : json_decode($pref->preferensi_hari, true);
$jam = is_array($pref->preferensi_jam) ? $pref->preferensi_jam : json_decode($pref->preferensi_jam, true);
echo 'Hari: ' . count($hari) . ' (minimal 3)' . PHP_EOL;
echo 'Jam: ' . count($jam) . ' (minimal 3)' . PHP_EOL;
```

## 🔄 Setelah Reset

1. **Populate Data Warehouse** (jika perlu):
   ```bash
   php populate_dw.php
   ```

2. **Login dengan akun baru**:
   - Super Admin: `superadmin@kampusmerdeka.ac.id` / `password`
   - Admin Prodi: `admin.tsd@kampusmerdeka.ac.id` / `password`
   - Dosen: `tsd.dosen1@kampusmerdeka.ac.id` / `password`

## ⚠️ Catatan Penting

- ✅ Semua data lama akan **dihapus** saat reset
- ✅ Preferensi dibuat untuk **semua dosen** (global + spesifik)
- ✅ Setiap preferensi memiliki **minimal 3 hari** dan **minimal 3 jam**
- ✅ Preferensi disimpan dalam format JSON di database

---

**Database sudah direset dengan preferensi lengkap!** 🎉

