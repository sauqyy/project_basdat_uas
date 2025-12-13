# Cara Reset Database dengan Preferensi Lengkap
AOHWDAHWODHAOHDAWHOd
## ✅ Yang Sudah Diperbaiki

Seeder `FreshDataSeeder` sekarang sudah membuat:
- ✅ **Preferensi Global** untuk setiap dosen dengan **minimal 3 hari** dan **minimal 3 pilihan jam**
- ✅ **Preferensi Spesifik** untuk setiap mata kuliah

## 🚀 Cara Reset

### Cara 1: Menggunakan Script Batch
```bash
reset_and_seed.bat
```

### Cara 2: Menggunakan Command
```bash
php artisan migrate:fresh --seed --seeder=FreshDataSeeder --force
```

## 📊 Data yang Dibuat

- **26 Akun** (1 Super Admin + 5 Admin Prodi + 20 Dosen)
- **27 Ruangan** (15 kelas + 10 lab + 2 auditorium)
- **~26 Mata Kuliah**
- **Preferensi untuk semua dosen** (minimal 3 hari + 3 jam)

## ✅ Verifikasi

Setelah seeding selesai, cek dengan:
```bash
php artisan tinker
```

```php
\App\Models\User::count(); // Harusnya 26
\App\Models\PreferensiDosen::count(); // Harusnya > 20
```

---

**Database sudah siap dengan preferensi lengkap!** 🎉

