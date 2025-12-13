# Quick Start Guide - Pentaho Data Integration

Panduan cepat untuk menggunakan file-file SQL dan export data untuk Pentaho Data Integration.

## 📁 File yang Tersedia

### Folder: `pentaho_queries/`
Berisi file SQL siap pakai untuk semua tabel dimensi dan fakta:

**Dimensi:**
- `dim_dosen.sql`
- `dim_mata_kuliah.sql`
- `dim_ruangan.sql`
- `dim_waktu.sql`
- `dim_prodi.sql`
- `dim_preferensi.sql`

**Fakta dengan Dimensi:**
- `fact_jadwal_with_dimensions.sql`
- `fact_utilisasi_ruangan_with_dimensions.sql`
- `fact_kecocokan_jadwal_with_dimensions.sql`

**Utility:**
- `all_dimensions.sql` - Summary semua dimensi
- `all_facts.sql` - Summary semua fakta
- `data_quality_check.sql` - Data quality validation

## 🚀 Cara Cepat

### Opsi 1: Export ke CSV (Paling Mudah)

```bash
# Export semua dimensi ke CSV
php artisan export:pentaho --table=all_dim --format=csv

# Export semua fakta ke CSV
php artisan export:pentaho --table=all_fact --format=csv

# Export semua (dimensi + fakta)
php artisan export:pentaho --table=all --format=csv
```

File akan tersimpan di: `storage/exports/`

Kemudian di Pentaho:
1. Buka **Text File Input** step
2. Pilih file CSV yang sudah di-export
3. Setup delimiter (koma) dan encoding (UTF-8)
4. Preview dan lanjutkan transformation

### Opsi 2: Koneksi Langsung ke Database

1. **Setup Connection di PDI:**
   - View → Database Connections → New
   - Pilih MySQL/SQLite
   - Isi host, database, username, password
   - Test connection

2. **Gunakan SQL File:**
   - Buat **Table Input** step
   - Pilih connection
   - Copy-paste query dari file `.sql` di folder `pentaho_queries/`
   - Preview data

## 📋 Contoh Workflow

### Workflow 1: Load DimDosen ke Staging

```
Start
  ↓
Table Input (dim_dosen.sql)
  ↓
Select Values (pilih kolom)
  ↓
Table Output (staging_dim_dosen)
  ↓
Success
```

### Workflow 2: Analisis Utilisasi Ruangan

```
Start
  ↓
Table Input (fact_utilisasi_ruangan_with_dimensions.sql)
  ↓
Filter Rows (persentase_utilisasi > 80)
  ↓
Sort Rows (persentase_utilisasi DESC)
  ↓
Excel Output
  ↓
Success
```

### Workflow 3: Data Quality Check

```
Start
  ↓
Table Input (data_quality_check.sql)
  ↓
Filter Rows (error_count > 0)
  ↓
Excel Output (errors_report.xlsx)
  ↓
Success
```

## 🔧 Command Export Lengkap

```bash
# Export tabel tertentu
php artisan export:pentaho --table=dim_dosen --format=csv
php artisan export:pentaho --table=fact_jadwal --format=csv

# Export ke JSON
php artisan export:pentaho --table=all_dim --format=json

# Custom output directory
php artisan export:pentaho --table=all --format=csv --output=storage/pentaho_data
```

## 📊 Struktur Data

### DimDosen
- dosen_key, nip, nama_dosen, email, prodi, role, dll

### DimMataKuliah
- mata_kuliah_key, kode_mk, nama_mk, sks, semester, dll

### DimRuangan
- ruangan_key, kode_ruangan, nama_ruangan, kapasitas, tipe_ruangan, dll

### DimWaktu
- waktu_key, hari, jam_mulai, jam_selesai, semester, tahun_akademik, dll

### DimProdi
- prodi_key, kode_prodi, nama_prodi, fakultas, akreditasi, dll

### DimPreferensi
- preferensi_key, dosen_id, mata_kuliah_id, preferensi_hari, preferensi_jam, dll

### FactJadwal
- Semua foreign keys + measures (jumlah_sks, utilisasi_ruangan, konflik_jadwal, dll)

### FactUtilisasiRuangan
- ruangan_key, waktu_key, prodi_key + measures (persentase_utilisasi, jumlah_kelas, dll)

### FactKecocokanJadwal
- dosen_key, preferensi_key, waktu_key + measures (skor_kecocokan, persentase_kecocokan, dll)

## 💡 Tips

1. **Gunakan SQL files** untuk koneksi langsung (lebih cepat, real-time)
2. **Gunakan CSV export** jika koneksi database tidak memungkinkan
3. **Preview data** sebelum melanjutkan transformation
4. **Setup error handling** untuk menangani data yang tidak valid
5. **Gunakan filter** untuk membatasi data yang di-load

## 📝 Next Steps

1. Pilih metode (CSV export atau koneksi langsung)
2. Setup connection di PDI (jika koneksi langsung)
3. Test dengan query sederhana
4. Buat transformation sesuai kebutuhan
5. Schedule job jika diperlukan (untuk ETL otomatis)

## 🆘 Troubleshooting

**File tidak ditemukan?**
- Pastikan command sudah dijalankan: `php artisan export:pentaho`
- Cek folder `storage/exports/`

**Query error di PDI?**
- Pastikan migration sudah dijalankan: `php artisan migrate`
- Periksa nama tabel dan kolom
- Test query langsung di database dulu

**Data tidak muncul?**
- Pastikan data sudah di-populate: `php artisan fact:populate`
- Cek filter WHERE di query
- Pastikan status_aktif = 1 untuk data aktif




