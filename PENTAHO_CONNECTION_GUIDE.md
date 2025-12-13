# Panduan Koneksi Pentaho dengan Data Warehouse

## Overview

Data warehouse dengan tabel dimensi (Dim) dan fakta (Fact) dapat diakses langsung dari Pentaho Data Integration (PDI) atau Pentaho Business Analytics melalui koneksi database.

---

## Metode 1: Koneksi Langsung ke Database (Recommended)

### A. Untuk MySQL/MariaDB

#### 1. Setup Database Connection di Pentaho

**Di Pentaho Data Integration (Spoon):**

1. Buka **View** → **Database Connections** (atau tekan `Ctrl+Shift+D`)
2. Klik kanan → **New**
3. Isi konfigurasi:
   - **Connection Name**: `Laravel Data Warehouse`
   - **Connection Type**: `MySQL`
   - **Access**: `Native (JDBC)`
   - **Host Name**: `localhost` (atau IP server database)
   - **Database Name**: `nama_database_laravel` (cek di `.env` file)
   - **Port Number**: `3306` (default MySQL)
   - **User Name**: `username_database`
   - **Password**: `password_database`

4. Klik **Test** untuk memastikan koneksi berhasil
5. Klik **OK**

#### 2. Menggunakan Connection di Transformation

1. Buat **Table Input** step
2. Pilih connection `Laravel Data Warehouse`
3. Tulis SQL query:
```sql
SELECT 
    f.*,
    d.nama_dosen,
    mk.nama_mk,
    r.nama_ruangan,
    w.hari,
    w.jam_mulai,
    w.jam_selesai,
    p.nama_prodi
FROM fact_jadwal f
LEFT JOIN dim_dosen d ON f.dosen_key = d.dosen_key
LEFT JOIN dim_mata_kuliah mk ON f.mata_kuliah_key = mk.mata_kuliah_key
LEFT JOIN dim_ruangan r ON f.ruangan_key = r.ruangan_key
LEFT JOIN dim_waktu w ON f.waktu_key = w.waktu_key
LEFT JOIN dim_prodi p ON f.prodi_key = p.prodi_key
WHERE f.status_aktif = 1;
```

### B. Untuk SQLite

**Di Pentaho Data Integration:**

1. **Connection Type**: `SQLite`
2. **Database Name/File**: Path ke file database SQLite
   - Contoh: `C:\Users\ASUS\Project_BASDAT_UTS (2)\Project_BASDAT_UTS\Project_BASDAT_UTSFIX\Project_BASDAT_UTS - Copy\database\database.sqlite`
3. **User Name**: (kosongkan untuk SQLite)
4. **Password**: (kosongkan untuk SQLite)

**Catatan**: Pastikan driver SQLite sudah terinstall di Pentaho.

---

## Metode 2: Export ke File (Alternatif)

Jika koneksi langsung tidak memungkinkan, export data ke file terlebih dahulu.

### A. Export ke CSV

Command sudah tersedia! Jalankan:

```bash
# Export semua tabel ke CSV
php artisan export:pentaho --format=csv

# Export tabel tertentu
php artisan export:pentaho --table=fact_jadwal --format=csv
php artisan export:pentaho --table=fact_utilisasi_ruangan --format=csv
php artisan export:pentaho --table=fact_kecocokan_jadwal --format=csv

# Export dimensi tertentu
php artisan export:pentaho --table=dim_dosen --format=csv
php artisan export:pentaho --table=dim_mata_kuliah --format=csv
```

File akan tersimpan di: `storage/exports/`

### B. Export ke JSON

```bash
php artisan export:pentaho --format=json
php artisan export:pentaho --table=fact_jadwal --format=json
```

### C. Custom Output Directory

```bash
php artisan export:pentaho --format=csv --output=storage/pentaho_exports
```

---

## Metode 3: Menggunakan REST API (Advanced)

Jika Pentaho mendukung REST API, buat endpoint di Laravel untuk mengakses data.

---

## Tabel yang Tersedia

### Dimensi Tables:
1. `dim_dosen` - Data dosen
2. `dim_mata_kuliah` - Data mata kuliah
3. `dim_ruangan` - Data ruangan
4. `dim_waktu` - Data waktu (hari, jam, semester)
5. `dim_prodi` - Data program studi
6. `dim_preferensi` - Data preferensi dosen

### Fact Tables:
1. `fact_jadwal` - Fakta utama jadwal
2. `fact_utilisasi_ruangan` - Fakta utilisasi ruangan
3. `fact_kecocokan_jadwal` - Fakta kecocokan jadwal dengan preferensi

---

## Contoh Query untuk Pentaho

### 1. Query FactJadwal dengan Dimensi

```sql
SELECT 
    f.id,
    f.jumlah_sks,
    f.durasi_menit,
    f.utilisasi_ruangan,
    f.konflik_jadwal,
    d.nama_dosen,
    d.nip,
    mk.nama_mk,
    mk.kode_mk,
    r.nama_ruangan,
    r.tipe_ruangan,
    w.hari,
    w.jam_mulai,
    w.jam_selesai,
    w.semester,
    w.tahun_akademik,
    p.nama_prodi,
    p.fakultas
FROM fact_jadwal f
LEFT JOIN dim_dosen d ON f.dosen_key = d.dosen_key
LEFT JOIN dim_mata_kuliah mk ON f.mata_kuliah_key = mk.mata_kuliah_key
LEFT JOIN dim_ruangan r ON f.ruangan_key = r.ruangan_key
LEFT JOIN dim_waktu w ON f.waktu_key = w.waktu_key
LEFT JOIN dim_prodi p ON f.prodi_key = p.prodi_key
WHERE f.status_aktif = 1
ORDER BY w.hari, w.jam_mulai;
```

### 2. Query FactUtilisasiRuangan

```sql
SELECT 
    f.*,
    r.nama_ruangan,
    r.tipe_ruangan,
    r.kapasitas,
    w.hari,
    w.slot_waktu,
    p.nama_prodi
FROM fact_utilisasi_ruangan f
LEFT JOIN dim_ruangan r ON f.ruangan_key = r.ruangan_key
LEFT JOIN dim_waktu w ON f.waktu_key = w.waktu_key
LEFT JOIN dim_prodi p ON f.prodi_key = p.prodi_key
ORDER BY f.persentase_utilisasi DESC;
```

### 3. Query FactKecocokanJadwal

```sql
SELECT 
    f.*,
    d.nama_dosen,
    d.nip,
    w.hari,
    w.jam_mulai,
    w.jam_selesai,
    p.preferensi_hari,
    p.preferensi_jam,
    p.prioritas
FROM fact_kecocokan_jadwal f
LEFT JOIN dim_dosen d ON f.dosen_key = d.dosen_key
LEFT JOIN dim_waktu w ON f.waktu_key = w.waktu_key
LEFT JOIN dim_preferensi p ON f.preferensi_key = p.preferensi_key
ORDER BY f.skor_kecocokan DESC;
```

---

## Setup JDBC Driver (Jika Diperlukan)

### MySQL JDBC Driver:
1. Download MySQL Connector/J dari: https://dev.mysql.com/downloads/connector/j/
2. Extract file `mysql-connector-java-X.X.XX.jar`
3. Copy ke folder: `pentaho/data-integration/lib`
4. Restart Pentaho

### SQLite JDBC Driver:
1. Download dari: https://github.com/xerial/sqlite-jdbc
2. Copy `sqlite-jdbc-X.X.XX.jar` ke folder `pentaho/data-integration/lib`
3. Restart Pentaho

---

## Tips dan Best Practices

1. **Gunakan Connection Pooling**: Untuk performa yang lebih baik
2. **Index Optimization**: Pastikan index sudah dibuat di database untuk query yang cepat
3. **Filter Data**: Gunakan WHERE clause untuk membatasi data yang di-load
4. **Incremental Load**: Untuk data besar, gunakan incremental loading berdasarkan timestamp
5. **Error Handling**: Setup proper error handling di Pentaho transformation

---

## Troubleshooting

### Error: "Driver not found"
- Pastikan JDBC driver sudah di-copy ke folder `lib`
- Restart Pentaho setelah menambahkan driver

### Error: "Access denied"
- Periksa username dan password database
- Pastikan user memiliki akses ke database dan tabel

### Error: "Table doesn't exist"
- Pastikan migration sudah dijalankan
- Periksa nama tabel (case sensitive untuk beberapa database)

### Connection Timeout
- Periksa firewall settings
- Pastikan database server dapat diakses dari komputer Pentaho
- Periksa network connection

---

## Contoh Transformation di Pentaho

### Transformation untuk Load FactJadwal:

1. **Table Input** → Query fact_jadwal dengan join dimensi
2. **Select Values** → Pilih kolom yang diperlukan
3. **Filter Rows** → Filter status_aktif = 1
4. **Sort Rows** → Sort berdasarkan waktu
5. **Output** → Excel/CSV/Another Database

---

## Contoh Job di Pentaho

1. **Start** → Start job
2. **SQL** → Truncate staging table
3. **Transformation** → Load data dari fact tables
4. **SQL** → Update/Insert ke target table
5. **Success** → Job berhasil

---

## Informasi Database Connection

Untuk mengetahui konfigurasi database Laravel, cek file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=username
DB_PASSWORD=password
```

Gunakan informasi ini untuk setup connection di Pentaho.

---

## Next Steps

1. Setup database connection di Pentaho
2. Test connection dengan query sederhana
3. Buat transformation untuk load data
4. Setup schedule job jika diperlukan (untuk ETL otomatis)
5. Buat dashboard di Pentaho BA jika diperlukan

