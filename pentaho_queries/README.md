# File SQL untuk Pentaho Data Integration (PDI)

Folder ini berisi file-file SQL yang siap digunakan di Pentaho Data Integration untuk mengakses tabel dimensi dan fakta dari data warehouse.

## Cara Menggunakan di PDI

### 1. Setup Database Connection
1. Buka Pentaho Data Integration (Spoon)
2. Klik **View** → **Database Connections** (atau tekan `Ctrl+Shift+D`)
3. Klik kanan → **New**
4. Setup koneksi ke database Laravel (MySQL/SQLite)
5. Test connection dan klik **OK**

### 2. Menggunakan Query di Transformation

1. Buat transformation baru
2. Drag **Table Input** step ke canvas
3. Double-click **Table Input**
4. Pilih database connection yang sudah dibuat
5. Klik **SQL Editor** atau **Browse**
6. Copy-paste query dari file SQL di folder ini
7. Klik **OK**
8. Klik **Preview** untuk melihat hasil

## File yang Tersedia

### Dimensi Tables (Dim)

1. **dim_dosen.sql** - Query untuk tabel DimDosen
   - Berisi data dosen dengan semua atribut
   - Filter: hanya data aktif (is_active = 1)

2. **dim_mata_kuliah.sql** - Query untuk tabel DimMataKuliah
   - Berisi data mata kuliah lengkap
   - Filter: hanya data aktif

3. **dim_ruangan.sql** - Query untuk tabel DimRuangan
   - Berisi data ruangan dengan kapasitas dan fasilitas
   - Filter: hanya data aktif

4. **dim_waktu.sql** - Query untuk tabel DimWaktu
   - Berisi data waktu (hari, jam, semester, tahun akademik)
   - Filter: hanya data aktif
   - Sorted by: hari_ke, jam_mulai

5. **dim_prodi.sql** - Query untuk tabel DimProdi
   - Berisi data program studi dan fakultas
   - Filter: hanya data aktif

6. **dim_preferensi.sql** - Query untuk tabel DimPreferensi
   - Berisi preferensi dosen untuk mata kuliah
   - Filter: hanya data aktif
   - Sorted by: dosen_id, prioritas

### Fact Tables dengan Dimensi

1. **fact_jadwal_with_dimensions.sql** - FactJadwal dengan semua dimensi
   - Join dengan: DimDosen, DimMataKuliah, DimRuangan, DimWaktu, DimProdi
   - Berisi semua measures dari fact table
   - Filter: hanya jadwal aktif
   - Sorted by: hari, jam, dosen

2. **fact_utilisasi_ruangan_with_dimensions.sql** - FactUtilisasiRuangan dengan dimensi
   - Join dengan: DimRuangan, DimWaktu, DimProdi
   - Berisi data utilisasi ruangan lengkap
   - Sorted by: persentase utilisasi DESC

3. **fact_kecocokan_jadwal_with_dimensions.sql** - FactKecocokanJadwal dengan dimensi
   - Join dengan: DimDosen, DimPreferensi, DimWaktu
   - Berisi data kecocokan jadwal dengan preferensi
   - Sorted by: skor kecocokan DESC

## Contoh Workflow di PDI

### Contoh 1: Load DimDosen ke Staging
1. **Table Input** → Gunakan `dim_dosen.sql`
2. **Select Values** → Pilih kolom yang diperlukan
3. **Table Output** → Output ke staging table

### Contoh 2: Analisis Utilisasi Ruangan
1. **Table Input** → Gunakan `fact_utilisasi_ruangan_with_dimensions.sql`
2. **Filter Rows** → Filter persentase_utilisasi > 80
3. **Sort Rows** → Sort by persentase_utilisasi DESC
4. **Excel Output** → Export ke Excel

### Contoh 3: Dashboard Data
1. **Table Input** → Gunakan `fact_jadwal_with_dimensions.sql`
2. **Group By** → Group by prodi, hari
3. **Calculator** → Hitung total SKS, total mahasiswa
4. **Table Output** → Output ke dashboard table

## Tips

1. **Performance**: Gunakan filter WHERE untuk membatasi data yang di-load
2. **Index**: Pastikan index sudah dibuat di database untuk query yang cepat
3. **Incremental Load**: Untuk data besar, gunakan filter berdasarkan timestamp
4. **Error Handling**: Setup error handling di transformation untuk menangani error

## Modifikasi Query

Anda bisa memodifikasi query sesuai kebutuhan:
- Tambahkan WHERE clause untuk filter
- Tambahkan JOIN dengan tabel lain jika diperlukan
- Ubah ORDER BY untuk sorting berbeda
- Pilih kolom tertentu dengan SELECT

## Troubleshooting

### Error: Table doesn't exist
- Pastikan migration sudah dijalankan: `php artisan migrate`
- Periksa nama tabel (case sensitive untuk beberapa database)

### Error: Column not found
- Pastikan kolom ada di tabel
- Periksa nama kolom (case sensitive)

### Query terlalu lambat
- Tambahkan index di database
- Gunakan filter WHERE untuk membatasi data
- Pertimbangkan incremental load




