# Pentaho Data Integration (PDI) Transformations

Folder ini berisi file-file KTR (Kettle Transformation) untuk ETL (Extract, Transform, Load) proses data warehouse.

## Struktur File

### Dimension Tables (Dim Tables)

1. **01_Populate_Dim_Dosen.ktr**
   - Populate `dim_dosen` dari tabel `users` (operational database)
   - Generate surrogate key: `DOSEN_{id}`
   - SCD Type 2 support dengan `valid_from` dan `valid_to`

2. **02_Populate_Dim_MataKuliah.ktr**
   - Populate `dim_mata_kuliah` dari tabel `mata_kuliahs`
   - Generate surrogate key: `MK_{kode_mk}`
   - Include semua atribut mata kuliah

3. **03_Populate_Dim_Ruangan.ktr**
   - Populate `dim_ruangan` dari tabel `ruangans`
   - Generate surrogate key: `R_{kode_ruangan}`
   - Include kapasitas, tipe, dan fasilitas

4. **04_Populate_Dim_Waktu.ktr**
   - Populate `dim_waktu` dari kombinasi unik `hari`, `jam_mulai`, `jam_selesai`, `semester`, `tahun_akademik`
   - Generate surrogate key: `{hari}_{jam_mulai}_{jam_selesai}_{semester}_{tahun_akademik}`
   - Calculate: `hari_ke`, `slot_waktu`, `durasi_menit`, `periode`

5. **05_Populate_Dim_Prodi.ktr**
   - Populate `dim_prodi` dari nilai unik `prodi` di berbagai tabel
   - Menggabungkan data dari `users`, `mata_kuliahs`, dan `jadwals`
   - Generate `kode_prodi` dan `fakultas` otomatis

6. **06_Populate_Dim_Preferensi.ktr**
   - Populate `dim_preferensi` dari tabel `preferensi_dosens`
   - Generate surrogate key: `PREF_{id}`
   - Include preferensi hari, jam, dan prioritas

### Fact Tables

7. **07_Populate_Fact_Jadwal.ktr**
   - Populate `fact_jadwal` dari tabel `jadwals` (operational)
   - Melakukan lookup ke semua dimension tables
   - Calculate measures:
     - `jumlah_sks`
     - `durasi_menit`
     - `kapasitas_kelas`
     - `jumlah_mahasiswa`
     - `utilisasi_ruangan`
     - `konflik_jadwal` (default 0)
     - `tingkat_konflik` (default 0)

8. **08_Populate_Fact_UtilisasiRuangan.ktr**
   - Populate `fact_utilisasi_ruangan` dari aggregated `fact_jadwal`
   - Group by: `ruangan_key`, `waktu_key`, `prodi_key`
   - Calculate:
     - `total_jam_penggunaan` (sum of `durasi_menit`)
     - `total_jam_tersedia` (default 480 menit = 8 jam)
     - `persentase_utilisasi`

9. **09_Populate_Fact_KecocokanJadwal.ktr**
   - Populate `fact_kecocokan_jadwal` dari `fact_jadwal` dan `dim_preferensi`
   - Calculate match antara jadwal dengan preferensi:
     - `preferensi_hari_terpenuhi` (1 jika hari sesuai, 0 jika tidak)
     - `preferensi_jam_terpenuhi` (1 jika jam sesuai, 0 jika tidak)
     - `persentase_kecocokan` (0%, 50%, atau 100%)
     - `skor_kecocokan` (berdasarkan prioritas)

## Cara Menggunakan

### 1. Setup Database Connections

**PENTING**: Setelah membuka file KTR di Pentaho Spoon, Anda HARUS mengatur database connections terlebih dahulu!

#### a. Operational_DB
1. Di Spoon, klik **View** → **Database Connections** (atau tekan `Ctrl+Shift+D`)
2. Klik kanan → **New**
3. **Name**: `Operational_DB` (harus sama persis!)
4. **Type**: MySQL
5. **Access**: `Native (JDBC)` ← **PILIH INI, TIDAK PERLU UBAH**
6. **Host**: localhost (atau server database Anda)
7. **Database**: nama database operasional (misal: `sistem_akademik`)
8. **Port**: 3306
9. **Username**: root (atau username database Anda)
10. **Password**: password database Anda
11. Klik **Test** untuk memastikan koneksi berhasil
12. Klik **OK**

#### b. DW_Connection
1. Ulangi langkah di atas dengan:
   - **Name**: `DW_Connection` (harus sama persis!)
   - **Access**: `Native (JDBC)` ← **PILIH INI JUGA, TIDAK PERLU UBAH**
   - **Database**: bisa sama dengan Operational_DB atau berbeda
2. Klik **Test** dan **OK**

**⚠️ PENTING TENTANG ACCESS:**
- ✅ **Gunakan Native (JDBC) untuk SEMUA koneksi** (Operational_DB dan DW_Connection)
- ✅ **TIDAK PERLU diubah** ke ODBC atau JNDI
- ✅ Native (JDBC) adalah pilihan terbaik dan recommended untuk semua use case

**Catatan**: 
- Nama connection HARUS sama persis dengan yang ada di file KTR (`Operational_DB` dan `DW_Connection`)
- Jika password di file KTR ter-encrypt, Anda perlu mengatur ulang password di connection settings
- Pastikan kedua database connection sudah di-test dan berhasil sebelum menjalankan transformation

### 2. Membuka File KTR

1. Buka Pentaho Data Integration (Spoon)
2. **File** → **Open** → Pilih file KTR (misal: `01_Populate_Dim_Dosen.ktr`)
3. File akan terbuka di canvas
4. **PENTING**: Setelah file terbuka, pastikan database connections sudah di-setup (lihat langkah 1)
5. Jika connection belum ada atau salah, double-click pada step yang menggunakan connection dan pilih connection yang benar

### 3. Menjalankan Transformations

#### Urutan Eksekusi:

**Step 1: Populate Dimension Tables (Harus dijalankan terlebih dahulu)**
1. Buka `01_Populate_Dim_Dosen.ktr`
2. **Pastikan database connections sudah di-setup** (lihat langkah 1)
3. Klik **Run** (F9) atau klik tombol play di toolbar
4. Tunggu sampai selesai (lihat Execution Results)
5. Ulangi untuk semua file dim (02-06):
   - `02_Populate_Dim_MataKuliah.ktr`
   - `03_Populate_Dim_Ruangan.ktr`
   - `04_Populate_Dim_Waktu.ktr`
   - `05_Populate_Dim_Prodi.ktr`
   - `06_Populate_Dim_Preferensi.ktr`

**Step 2: Populate Fact Tables (Setelah dim tables terisi)**
1. Buka `07_Populate_Fact_Jadwal.ktr`
2. **Pastikan database connections sudah di-setup**
3. Klik **Run** (F9)
4. Tunggu sampai selesai
5. Buka `08_Populate_Fact_UtilisasiRuangan.ktr`
6. Klik **Run** (F9)
7. Buka `09_Populate_Fact_KecocokanJadwal.ktr`
8. Klik **Run** (F9)

### 4. Membuat Job untuk Otomasi

Untuk menjalankan semua transformations secara berurutan, buat file **Job** (.kjb):

1. **File** → **New** → **Job**
2. Drag **Start** step
3. Drag **Transformation** step untuk setiap file KTR
4. Connect dengan urutan:
   - Start → 01_Populate_Dim_Dosen
   - 01 → 02_Populate_Dim_MataKuliah
   - 02 → 03_Populate_Dim_Ruangan
   - 03 → 04_Populate_Dim_Waktu
   - 04 → 05_Populate_Dim_Prodi
   - 05 → 06_Populate_Dim_Preferensi
   - 06 → 07_Populate_Fact_Jadwal
   - 07 → 08_Populate_Fact_UtilisasiRuangan
   - 08 → 09_Populate_Fact_KecocokanJadwal
   - 09 → Success
5. Save sebagai `ETL_Complete_Load.kjb`

### 5. Scheduling (Opsional)

Untuk menjalankan ETL secara otomatis:

1. Gunakan **Pentaho Data Integration Server** (PDI Server)
2. Atau gunakan **cron job** (Linux) / **Task Scheduler** (Windows) untuk menjalankan:
   ```bash
   pan.sh -file:/path/to/ETL_Complete_Load.kjb
   ```

## Setup Logging Tables (Opsional)

Jika Anda ingin menggunakan fitur logging Pentaho untuk tracking execution history:

1. Buka file `create_logging_tables.sql`
2. Jalankan script SQL tersebut di database yang digunakan untuk `DW_Connection`
3. Atau copy-paste query ke MySQL client dan execute
4. Setelah tabel dibuat, edit file KTR dan aktifkan kembali logging:
   - Buka file KTR di Spoon
   - **Edit** → **Settings** → **Logging**
   - Pilih connection dan table yang sesuai

**Catatan**: Logging adalah fitur opsional. Transformation tetap bisa berjalan tanpa tabel logging. Error "Table doesn't exist" untuk logging tidak mempengaruhi eksekusi transformation.

## Troubleshooting

### Error: Table 'log_trans' doesn't exist / Table 'perf_log' doesn't exist
- **Solusi 1 (Disarankan)**: Abaikan error ini. Ini hanya error logging yang tidak mempengaruhi eksekusi transformation. File KTR sudah di-set untuk tidak menggunakan logging.
- **Solusi 2**: Jika ingin menggunakan logging, jalankan script `create_logging_tables.sql` di database

### Error: Connection not found / Connection failed
- **Solusi**: Pastikan database connections sudah dibuat dengan nama yang sama persis (`Operational_DB` dan `DW_Connection`)
- Double-click pada step yang error, pilih connection yang benar
- Test connection terlebih dahulu di Database Connections window

### Error: Table doesn't exist
- Pastikan migration sudah dijalankan: `php artisan migrate`
- Periksa nama database di connection settings
- Pastikan Anda menggunakan database yang benar

### Error: Column not found
- Pastikan struktur tabel sesuai dengan yang diharapkan
- Periksa nama kolom (case sensitive di MySQL)
- Periksa SQL query di Table Input step

### Error: Lookup failed
- Pastikan dimension tables sudah ter-populate sebelum menjalankan fact tables
- Periksa key matching (natural keys harus cocok)
- Pastikan data di operational database sudah ada

### File KTR kosong / Tidak ada steps
- **Solusi**: File KTR yang saya buat sudah lengkap dengan steps. Jika tidak muncul:
  1. Pastikan Anda membuka file yang benar
  2. Coba refresh dengan **File** → **Reload**
  3. Jika masih kosong, buat transformation baru dan drag-drop steps secara manual:
     - **Table Input** dari palette
     - **Calculator** dari palette
     - **Table Output** dari palette
     - Connect dengan hops (drag dari output ke input)

### Performance Issues
- Gunakan batch commit (sudah diset di Table Output steps)
- Tambahkan index di database untuk kolom yang digunakan di lookup
- Pertimbangkan incremental load untuk data besar

## Catatan Penting

1. **Urutan Eksekusi**: Dimension tables HARUS di-populate terlebih dahulu sebelum fact tables
2. **Natural Keys**: Pastikan natural keys (NIP, kode_mk, kode_ruangan, dll) konsisten antara operational dan dimension tables
3. **SCD Type 2**: Untuk update data yang sudah ada, perlu logic tambahan untuk set `valid_to` dan insert record baru
4. **Incremental Load**: File KTR ini menggunakan full load. Untuk incremental load, tambahkan filter berdasarkan timestamp

## Modifikasi

Anda bisa memodifikasi transformations sesuai kebutuhan:
- Tambahkan filter WHERE untuk membatasi data
- Ubah calculation logic untuk measures
- Tambahkan error handling
- Tambahkan logging steps

## Support

Untuk pertanyaan atau masalah, silakan hubungi tim development atau buat issue di repository.

