# Fix: TOP 5 BEBAN DOSEN Setelah Hapus Akun

## ✅ Masalah yang Diperbaiki

Setelah menghapus akun dosen, TOP 5 BEBAN DOSEN masih menampilkan dosen yang sudah dihapus.

## 🔧 Solusi yang Diterapkan

Query di `AdminProdiController` dan `SuperAdminController` sudah diperbaiki untuk **hanya menampilkan dosen yang masih aktif** di tabel `users`.

### Perubahan di AdminProdiController:

```php
// SEBELUM: Mengambil semua dosen dari FactJadwal
$topDosenBeban = FactJadwal::select(...)
    ->where('prodi_key', $prodiKey)
    ->where('status_aktif', true)
    ->get();

// SESUDAH: Hanya mengambil dosen yang masih ada di tabel users
$dosenAktifNIPs = User::where('role', 'dosen')
    ->where('prodi', $prodi)
    ->pluck('nip')
    ->toArray();

$dosenKeysAktif = DimDosen::where('is_active', true)
    ->whereIn('nip', $dosenAktifNIPs)
    ->pluck('dosen_key')
    ->toArray();

$topDosenBeban = FactJadwal::select(...)
    ->where('prodi_key', $prodiKey)
    ->where('status_aktif', true)
    ->whereIn('dosen_key', $dosenKeysAktif) // ✅ Filter dosen aktif
    ->get();
```

### Perubahan di SuperAdminController:

```php
// SEBELUM: Tidak ada join ke users
$dosenBeban = Jadwal::select(...)
    ->join('mata_kuliahs', ...)
    ->where('jadwals.status', true)
    ->get();

// SESUDAH: Join ke users untuk filter dosen aktif
$dosenBeban = Jadwal::select(...)
    ->join('mata_kuliahs', ...)
    ->join('users', 'mata_kuliahs.dosen_id', '=', 'users.id') // ✅ Join users
    ->where('jadwals.status', true)
    ->where('users.role', 'dosen') // ✅ Filter role
    ->get();
```

## ✅ Hasil

Setelah perubahan ini:
- ✅ TOP 5 BEBAN DOSEN hanya menampilkan dosen yang **masih aktif** di tabel `users`
- ✅ Dosen yang sudah dihapus **tidak akan muncul** di dashboard
- ✅ Tidak perlu re-populate fact tables setelah menghapus akun

## 🔄 Cara Test

1. Hapus satu akun dosen untuk prodi TSD
2. Refresh dashboard Admin Prodi
3. TOP 5 BEBAN DOSEN seharusnya hanya menampilkan 3 dosen (bukan 4)

## 📝 Catatan

- Perubahan ini **langsung berlaku** tanpa perlu re-populate fact tables
- Query akan otomatis filter berdasarkan data di tabel `users`
- Jika masih ada masalah, pastikan:
  1. Akun dosen sudah benar-benar dihapus dari tabel `users`
  2. Refresh dashboard (Ctrl+F5 untuk hard refresh)
  3. Clear cache browser jika perlu

---

**Perubahan sudah diterapkan! Silakan refresh dashboard untuk melihat hasilnya.** 🎉

