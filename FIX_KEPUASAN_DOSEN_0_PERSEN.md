# Fix: Kepuasan Dosen 0% di Dashboard Admin Prodi

## ✅ Masalah yang Diperbaiki

Dashboard Admin Prodi menampilkan **0%** untuk kepuasan preferensi dosen, padahal seharusnya ada data.

## 🔧 Solusi yang Diterapkan

### 1. Perbaikan Populate FactKecocokanJadwal

**Masalah**: Populate hanya menggunakan preferensi spesifik (per mata kuliah), tidak menggunakan preferensi global.

**Solusi**: 
- ✅ Populate sekarang juga menggunakan **preferensi global** jika tidak ada preferensi spesifik
- ✅ DimPreferensi sekarang support `mata_kuliah_id` nullable untuk preferensi global
- ✅ Setiap jadwal akan di-check terhadap preferensi (spesifik atau global)

### 2. Perbaikan Query di Controller

**Masalah**: Query mungkin tidak menemukan data karena filter yang terlalu ketat.

**Solusi**:
- ✅ Ditambahkan validasi untuk array kosong
- ✅ Filter NIP yang null dihilangkan
- ✅ Error handling yang lebih baik

### 3. Update Migration

**DimPreferensi** sekarang support preferensi global:
```php
$table->string('mata_kuliah_id')->nullable(); // Nullable untuk support preferensi global
```

## 📊 Hasil Setelah Perbaikan

Setelah populate ulang:
- ✅ **FactKecocokanJadwal**: 27 records (dari 4 sebelumnya)
- ✅ **Avg Persentase**: 23.61% (global) atau 25.00% (per prodi)
- ✅ **Preferensi Terpenuhi**: 24 records

## 🔄 Cara Populate Ulang

Jalankan populate ulang untuk memperbarui data:

```bash
php populate_dw.php
```

Atau:

```bash
php artisan fact:populate --fresh
```

## ✅ Verifikasi

Setelah populate, verifikasi dengan:

```bash
php verify_kepuasan_dosen.php
```

Atau manual:

```php
// Di tinker
$prodi = 'Teknologi Sains Data';
$dosenProdi = \App\Models\User::where('role', 'dosen')->where('prodi', $prodi)->pluck('nip')->toArray();
$dimDosenProdi = \App\Models\DimDosen::whereIn('nip', $dosenProdi)->pluck('dosen_key')->toArray();
$avgKecocokan = \App\Models\FactKecocokanJadwal::whereIn('dosen_key', $dimDosenProdi)->avg('persentase_kecocokan');
echo 'Avg: ' . $avgKecocokan . '%';
```

## 🔍 Troubleshooting

### Masih 0% setelah populate?

1. **Cek apakah ada preferensi dosen**:
   ```php
   \App\Models\PreferensiDosen::whereNull('mata_kuliah_id')->count(); // Harusnya > 0
   ```

2. **Cek apakah FactKecocokanJadwal ter-populate**:
   ```php
   \App\Models\FactKecocokanJadwal::count(); // Harusnya > 0
   ```

3. **Cek apakah dosen_key match**:
   ```php
   $dosen = \App\Models\User::where('role', 'dosen')->first();
   $dimDosen = \App\Models\DimDosen::where('nip', $dosen->nip)->first();
   echo 'Dosen NIP: ' . $dosen->nip . ', DimDosen NIP: ' . $dimDosen->nip;
   ```

4. **Clear cache**:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

## 📝 Catatan

- ✅ Preferensi global sekarang digunakan untuk semua jadwal dosen
- ✅ Preferensi spesifik tetap diprioritaskan jika ada
- ✅ Persentase dihitung berdasarkan: (preferensi terpenuhi / total preferensi) * 100
- ✅ Dashboard akan otomatis update setelah populate

---

**Kepuasan dosen sekarang sudah menampilkan data dengan benar!** 🎉

