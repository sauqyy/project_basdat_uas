# Data Warehouse Documentation - Sistem Generate Jadwal

## Overview
Data warehouse berbentuk snowflake untuk sistem generate jadwal yang memungkinkan analisis mendalam terhadap data jadwal, dosen, mata kuliah, ruangan, dan preferensi.

## Arsitektur Snowflake

### Tabel Fakta (Fact Table)
- **FactJadwal** - Tabel fakta utama yang berisi data jadwal dengan foreign key ke semua dimensi

### Tabel Dimensi (Dimension Tables)
1. **DimDosen** - Dimensi dosen
2. **DimMataKuliah** - Dimensi mata kuliah  
3. **DimRuangan** - Dimensi ruangan
4. **DimWaktu** - Dimensi waktu (hari, jam, semester, tahun akademik)
5. **DimProdi** - Dimensi program studi
6. **DimPreferensi** - Dimensi preferensi dosen

## Struktur Tabel

### 1. FactJadwal (Tabel Fakta)
```sql
- id (Primary Key)
- dosen_key (FK ke DimDosen)
- mata_kuliah_key (FK ke DimMataKuliah)
- ruangan_key (FK ke DimRuangan)
- waktu_key (FK ke DimWaktu)
- prodi_key (FK ke DimProdi)
- preferensi_key (FK ke DimPreferensi, nullable)
- jumlah_sks (Measure)
- durasi_menit (Measure)
- kapasitas_kelas (Measure)
- jumlah_mahasiswa (Measure)
- utilisasi_ruangan (Measure - persentase)
- prioritas_preferensi (Measure)
- konflik_jadwal (Measure - boolean)
- tingkat_konflik (Measure - 0-5)
- status_aktif (Status)
- created_at_jadwal (Timestamp)
- updated_at_jadwal (Timestamp)
```

### 2. DimDosen (Dimensi Dosen)
```sql
- id (Primary Key)
- dosen_key (Surrogate Key)
- nip (Natural Key)
- nama_dosen
- email
- prodi
- role
- profile_picture
- judul_skripsi
- is_active
- valid_from (SCD Type 2)
- valid_to (SCD Type 2)
```

### 3. DimMataKuliah (Dimensi Mata Kuliah)
```sql
- id (Primary Key)
- mata_kuliah_key (Surrogate Key)
- kode_mk (Natural Key)
- nama_mk
- sks
- semester
- prodi
- kapasitas
- deskripsi
- tipe_kelas
- menit_per_sks
- ada_praktikum
- sks_praktikum
- sks_materi
- is_active
- valid_from (SCD Type 2)
- valid_to (SCD Type 2)
```

### 4. DimRuangan (Dimensi Ruangan)
```sql
- id (Primary Key)
- ruangan_key (Surrogate Key)
- kode_ruangan (Natural Key)
- nama_ruangan
- kapasitas
- tipe_ruangan
- fasilitas
- prodi
- status
- is_active
- valid_from (SCD Type 2)
- valid_to (SCD Type 2)
```

### 5. DimWaktu (Dimensi Waktu)
```sql
- id (Primary Key)
- waktu_key (Surrogate Key)
- hari
- jam_mulai
- jam_selesai
- semester
- tahun_akademik
- periode (Ganjil/Genap)
- hari_ke (1-5 untuk Senin-Jumat)
- slot_waktu (Pagi/Siang/Sore)
- durasi_menit
- is_active
```

### 6. DimProdi (Dimensi Program Studi)
```sql
- id (Primary Key)
- prodi_key (Surrogate Key)
- kode_prodi (Natural Key)
- nama_prodi
- fakultas
- deskripsi
- akreditasi
- is_active
- valid_from (SCD Type 2)
- valid_to (SCD Type 2)
```

### 7. DimPreferensi (Dimensi Preferensi)
```sql
- id (Primary Key)
- preferensi_key (Surrogate Key)
- dosen_id
- mata_kuliah_id
- preferensi_hari (JSON Array)
- preferensi_jam (JSON Array)
- prioritas (1-5)
- catatan
- is_active
- valid_from (SCD Type 2)
- valid_to (SCD Type 2)
```

## Relasi Antar Tabel

```
FactJadwal
├── DimDosen (dosen_key)
├── DimMataKuliah (mata_kuliah_key)
├── DimRuangan (ruangan_key)
├── DimWaktu (waktu_key)
├── DimProdi (prodi_key)
└── DimPreferensi (preferensi_key) [nullable]
```

## Contoh Query Analisis

### 1. Analisis Utilisasi Ruangan
```sql
SELECT 
    r.nama_ruangan,
    r.tipe_ruangan,
    SUM(f.jumlah_mahasiswa) as total_mahasiswa,
    AVG(f.utilisasi_ruangan) as avg_utilisasi
FROM fact_jadwal f
JOIN dim_ruangan r ON f.ruangan_key = r.ruangan_key
WHERE f.status_aktif = true
GROUP BY r.nama_ruangan, r.tipe_ruangan
ORDER BY avg_utilisasi DESC;
```

### 2. Analisis Beban Kerja Dosen
```sql
SELECT 
    d.nama_dosen,
    d.prodi,
    COUNT(f.id) as total_jadwal,
    SUM(f.jumlah_sks) as total_sks,
    SUM(f.durasi_menit) as total_menit
FROM fact_jadwal f
JOIN dim_dosen d ON f.dosen_key = d.dosen_key
WHERE f.status_aktif = true
GROUP BY d.nama_dosen, d.prodi
ORDER BY total_sks DESC;
```

### 3. Analisis Konflik Jadwal
```sql
SELECT 
    w.hari,
    w.slot_waktu,
    COUNT(f.id) as total_jadwal,
    SUM(CASE WHEN f.konflik_jadwal = true THEN 1 ELSE 0 END) as jumlah_konflik,
    AVG(f.tingkat_konflik) as avg_tingkat_konflik
FROM fact_jadwal f
JOIN dim_waktu w ON f.waktu_key = w.waktu_key
WHERE f.status_aktif = true
GROUP BY w.hari, w.slot_waktu
ORDER BY jumlah_konflik DESC;
```

### 4. Analisis Preferensi Dosen
```sql
SELECT 
    d.nama_dosen,
    mk.nama_mk,
    p.prioritas,
    COUNT(f.id) as jumlah_jadwal_terpenuhi
FROM fact_jadwal f
JOIN dim_dosen d ON f.dosen_key = d.dosen_key
JOIN dim_mata_kuliah mk ON f.mata_kuliah_key = mk.mata_kuliah_key
JOIN dim_preferensi p ON f.preferensi_key = p.preferensi_key
WHERE f.status_aktif = true
GROUP BY d.nama_dosen, mk.nama_mk, p.prioritas
ORDER BY p.prioritas, jumlah_jadwal_terpenuhi DESC;
```

## Keunggulan Arsitektur Snowflake

1. **Normalisasi Tinggi**: Dimensi terpisah memungkinkan analisis yang lebih fleksibel
2. **SCD Type 2**: Mendukung perubahan historis data
3. **Surrogate Keys**: Memungkinkan tracking perubahan data
4. **Measures Terpusat**: Semua metrik bisnis di tabel fakta
5. **Relasi Jelas**: Foreign key yang jelas antar tabel
6. **Indexing Optimal**: Index pada kombinasi key untuk performa query

## Cara Menjalankan Migration

```bash
php artisan migrate
```

## Model Eloquent

Semua model sudah dibuat dengan relasi yang sesuai:
- `DimDosen`
- `DimMataKuliah`
- `DimRuangan`
- `DimWaktu`
- `DimProdi`
- `DimPreferensi`
- `FactJadwal`

## Penggunaan

```php
// Contoh query menggunakan Eloquent
$jadwalWithDetails = FactJadwal::with([
    'dimDosen',
    'dimMataKuliah',
    'dimRuangan',
    'dimWaktu',
    'dimProdi',
    'dimPreferensi'
])->where('status_aktif', true)->get();
```

Data warehouse ini siap digunakan untuk analisis mendalam sistem generate jadwal dengan performa yang optimal.






