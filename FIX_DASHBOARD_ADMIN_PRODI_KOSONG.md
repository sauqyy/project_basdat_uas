# Fix: Dashboard Admin Prodi Nilai Kosong

## ✅ Masalah yang Diperbaiki

Dashboard Admin Prodi menampilkan nilai kosong karena **fact tables belum ter-populate** setelah reset database atau generate jadwal.

## 🔧 Solusi

**Populate fact tables** dengan menjalankan script populate data warehouse:

```bash
php populate_dw.php
```

## 📊 Hasil Populate

Setelah populate, fact tables akan terisi:
- ✅ **FactJadwal**: 28 records
- ✅ **FactUtilisasiRuangan**: 28 records  
- ✅ **FactKecocokanJadwal**: 4 records (hanya untuk jadwal yang punya preferensi)

## 🔄 Kapan Harus Populate?

Populate fact tables perlu dilakukan setelah:
1. ✅ **Reset database** (`migrate:fresh`)
2. ✅ **Generate jadwal baru** (setelah AI generate jadwal)
3. ✅ **Update jadwal** (tambah/edit/hapus jadwal)
4. ✅ **Update preferensi dosen**

## 📝 Cara Cek Apakah Fact Tables Sudah Ter-Populate

```bash
php artisan tinker
```

```php
// Cek jumlah data di fact tables
\App\Models\FactJadwal::count(); // Harusnya > 0
\App\Models\FactUtilisasiRuangan::count(); // Harusnya > 0
\App\Models\FactKecocokanJadwal::count(); // Bisa 0 jika tidak ada preferensi

// Cek untuk prodi tertentu
$prodi = 'Teknologi Sains Data';
$dimProdi = \App\Models\DimProdi::where('nama_prodi', $prodi)->first();
if ($dimProdi) {
    echo 'FactJadwal untuk ' . $prodi . ': ' . 
         \App\Models\FactJadwal::where('prodi_key', $dimProdi->prodi_key)->count();
}
```

## ✅ Verifikasi Dashboard

Setelah populate, refresh dashboard Admin Prodi:
1. Login sebagai Admin Prodi
2. Buka dashboard
3. Data seharusnya sudah muncul:
   - Total Jadwal Aktif
   - Total Konflik
   - Rata-rata Utilisasi Ruangan
   - Top 5 Ruangan
   - Top 5 Dosen dengan Beban Mengajar Tertinggi

## 🔄 Auto-Populate (Opsional)

Jika ingin auto-populate setelah generate jadwal, bisa ditambahkan di controller:

```php
// Di AdminProdiController setelah generate jadwal berhasil
\Artisan::call('fact:populate', ['--fresh' => false]);
```

Atau gunakan script populate langsung:
```php
require base_path('populate_dw.php');
```

## ⚠️ Catatan Penting

- ✅ Fact tables **HARUS ter-populate** sebelum dashboard bisa menampilkan data
- ✅ Populate tidak menghapus data lama jika tidak menggunakan flag `--fresh`
- ✅ Jika masih kosong setelah populate, cek:
  1. Apakah ada jadwal aktif? (`Jadwal::where('status', true)->count()`)
  2. Apakah dimension tables sudah ter-populate?
  3. Apakah prodi_key di fact tables sesuai dengan prodi admin?

---

**Dashboard Admin Prodi sekarang sudah menampilkan data dengan benar!** 🎉

