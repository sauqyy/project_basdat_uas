# Panduan Lengkap Manual - Semua Dim dan Fact Tables

Panduan step-by-step untuk membuat semua transformation di Pentaho Spoon secara manual.

---

## Setup Awal

### 1. Setup Database Connections

1. Buka Pentaho Spoon
2. **View** → **Database Connections** (atau tekan `Ctrl+Shift+D`)
3. Klik kanan → **New**

#### Connection 1: Operational_DB
- **Name**: `Operational_DB` (harus sama persis!)
- **Type**: MySQL
- **Access**: `Native (JDBC)` ← **PILIH INI, TIDAK PERLU UBAH**
- **Host**: localhost
- **Database**: sistem_akademik (atau nama database Anda)
- **Port**: 3306
- **Username**: root (atau username Anda)
- **Password**: password database Anda
- Klik **Test** → **OK**

#### Connection 2: DW_Connection
- **Name**: `DW_Connection` (harus sama persis!)
- **Type**: MySQL
- **Access**: `Native (JDBC)` ← **PILIH INI JUGA, TIDAK PERLU UBAH**
- **Host**: localhost
- **Database**: sistem_akademik (atau nama database Anda)
- **Port**: 3306
- **Username**: root (atau username Anda)
- **Password**: password database Anda
- Klik **Test** → **OK**

**⚠️ PENTING TENTANG ACCESS:**
- ✅ **Gunakan Native (JDBC) untuk SEMUA koneksi** (Operational_DB dan DW_Connection)
- ✅ **TIDAK PERLU diubah** ke ODBC atau JNDI
- ✅ Native (JDBC) adalah pilihan terbaik dan recommended
- ✅ ODBC hanya digunakan jika Native (JDBC) tidak berfungsi
- ✅ JNDI hanya untuk environment server, tidak untuk development lokal

---

## DIMENSION TABLES

---

### 01. DimDosen

**File**: `01_Populate_Dim_Dosen.ktr`

#### Step 1: Table Input
1. **File** → **New** → **Transformation**
2. Drag **Input** → **Table Input** ke canvas
3. Double-click step
4. **Step name**: `Table Input - Users`
5. **Connection**: `Operational_DB`
6. **SQL**:
```sql
SELECT 
    id,
    nip,
    name as nama_dosen,
    email,
    prodi,
    role,
    profile_picture,
    judul_skripsi,
    created_at,
    updated_at
FROM users
WHERE role = 'dosen' OR role = 'admin_prodi'
ORDER BY nip;
```
7. Klik **OK**

#### Step 2: Calculator
1. Drag **Transform** → **Calculator** ke canvas
2. Double-click step
3. **Step name**: `Calculator - Generate Keys`
4. Klik **New** untuk setiap field:

**Field 1: dosen_key**
- **New field**: `dosen_key`
- **Calculation**: `A + B`
- **Field A**: `id` (String)
- **Type**: String
- **Value**: `CONCAT('DOSEN_', LTRIM(STR(id)))`

**Field 2: is_active**
- **New field**: `is_active`
- **Type**: Integer
- **Value**: `1`

**Field 3: valid_from**
- **New field**: `valid_from`
- **Type**: Date
- **Value**: `created_at`

**Field 4: valid_to**
- **New field**: `valid_to`
- **Type**: Date
- **Value**: `NULL`

5. Klik **OK**

#### Step 3: Table Output
1. Drag **Output** → **Table Output** ke canvas
2. Double-click step
3. **Step name**: `Table Output - DimDosen`
4. **Connection**: `DW_Connection`
5. **Target table**: `dim_dosen`
6. Klik **Get Fields** atau map manual:
   - `dosen_key` → `dosen_key`
   - `nip` → `nip`
   - `nama_dosen` → `nama_dosen`
   - `email` → `email`
   - `prodi` → `prodi`
   - `role` → `role`
   - `profile_picture` → `profile_picture`
   - `judul_skripsi` → `judul_skripsi`
   - `is_active` → `is_active`
   - `valid_from` → `valid_from`
   - `valid_to` → `valid_to`
7. Klik **OK**

#### Connect Steps:
- Table Input → Calculator → Table Output

#### Save:
**File** → **Save As** → `01_Populate_Dim_Dosen.ktr`

---

### 02. DimMataKuliah

**File**: `02_Populate_Dim_MataKuliah.ktr`

#### Step 1: Table Input
- **Step name**: `Table Input - MataKuliah`
- **Connection**: `Operational_DB`
- **SQL**:
```sql
SELECT 
    id,
    kode_mk,
    nama_mk,
    sks,
    semester,
    prodi,
    kapasitas,
    deskripsi,
    tipe_kelas,
    menit_per_sks,
    ada_praktikum,
    sks_praktikum,
    sks_materi,
    created_at,
    updated_at
FROM mata_kuliahs
ORDER BY kode_mk;
```

#### Step 2: Calculator
- **Step name**: `Calculator - Generate Keys`
- **Fields**:
  - `mata_kuliah_key`: `CONCAT('MK_', kode_mk)`
  - `is_active`: `1`
  - `valid_from`: `created_at`
  - `valid_to`: `NULL`

#### Step 3: Table Output
- **Step name**: `Table Output - DimMataKuliah`
- **Connection**: `DW_Connection`
- **Target table**: `dim_mata_kuliah`
- **Fields**: Map semua field dari Calculator

**Save**: `02_Populate_Dim_MataKuliah.ktr`

---

### 03. DimRuangan

**File**: `03_Populate_Dim_Ruangan.ktr`

#### Step 1: Table Input
- **Step name**: `Table Input - Ruangan`
- **Connection**: `Operational_DB`
- **SQL**:
```sql
SELECT 
    id,
    kode_ruangan,
    nama_ruangan,
    kapasitas,
    tipe_ruangan,
    fasilitas,
    prodi,
    status,
    created_at,
    updated_at
FROM ruangans
ORDER BY kode_ruangan;
```

#### Step 2: Calculator
- **Step name**: `Calculator - Generate Keys`
- **Fields**:
  - `ruangan_key`: `CONCAT('R_', kode_ruangan)`
  - `is_active`: `1`
  - `valid_from`: `created_at`
  - `valid_to`: `NULL`

#### Step 3: Table Output
- **Step name**: `Table Output - DimRuangan`
- **Connection**: `DW_Connection`
- **Target table**: `dim_ruangan`
- **Fields**: Map semua field

**Save**: `03_Populate_Dim_Ruangan.ktr`

---

### 04. DimWaktu

**File**: `04_Populate_Dim_Waktu.ktr`

#### Step 1: Table Input
- **Step name**: `Table Input - Jadwal`
- **Connection**: `Operational_DB`
- **SQL**:
```sql
SELECT DISTINCT
    hari,
    jam_mulai,
    jam_selesai,
    semester,
    tahun_akademik
FROM jadwals
WHERE status = 1
ORDER BY hari, jam_mulai;
```

#### Step 2: Group By
1. Drag **Statistics** → **Group By** ke canvas
2. Double-click step
3. **Step name**: `Group By - Unique Time`
4. **Group**:
   - `hari` (Group)
   - `jam_mulai` (Group)
   - `jam_selesai` (Group)
   - `semester` (Group)
   - `tahun_akademik` (Group)
5. Klik **OK**

#### Step 3: Table Output (LANGSUNG - TANPA CALCULATOR)

**⚠️ PENTING**: Jika Calculator tidak tersedia, gunakan SQL lengkap di Table Input!

**Opsi A: SQL Langsung (RECOMMENDED)**

Ubah SQL di Step 1 menjadi:

```sql
SELECT DISTINCT
    -- Data dasar
    hari,
    jam_mulai,
    jam_selesai,
    semester,
    tahun_akademik,
    
    -- Generate waktu_key
    CONCAT(
        hari, '_',
        REPLACE(CAST(jam_mulai AS CHAR), ':', ''), '_',
        REPLACE(CAST(jam_selesai AS CHAR), ':', ''), '_',
        CAST(semester AS CHAR), '_',
        CAST(tahun_akademik AS CHAR)
    ) AS waktu_key,
    
    -- hari_ke (1-5 untuk Senin-Jumat)
    CASE 
        WHEN hari = 'Senin' THEN 1
        WHEN hari = 'Selasa' THEN 2
        WHEN hari = 'Rabu' THEN 3
        WHEN hari = 'Kamis' THEN 4
        WHEN hari = 'Jumat' THEN 5
        ELSE 0
    END AS hari_ke,
    
    -- slot_waktu (Pagi/Siang/Sore)
    CASE 
        WHEN HOUR(jam_mulai) < 12 THEN 'Pagi'
        WHEN HOUR(jam_mulai) < 15 THEN 'Siang'
        ELSE 'Sore'
    END AS slot_waktu,
    
    -- durasi_menit
    TIMESTAMPDIFF(MINUTE, jam_mulai, jam_selesai) AS durasi_menit,
    
    -- periode (Ganjil/Genap)
    CASE 
        WHEN MOD(CAST(semester AS UNSIGNED), 2) = 1 THEN 'Ganjil'
        ELSE 'Genap'
    END AS periode,
    
    -- is_active
    1 AS is_active
    
FROM jadwals
WHERE status = 1
ORDER BY hari, jam_mulai;
```

Lalu langsung ke **Table Output** (skip Calculator step).

**Opsi B: Menggunakan JavaScript Step**

Jika SQL tidak support semua fungsi:
1. Gunakan Table Input sederhana (hanya ambil data dasar)
2. Tambahkan **Scripting** → **Modified JavaScript Value**
3. Gunakan JavaScript untuk kalkulasi (lihat panduan lengkap)
4. Lanjut ke Table Output

#### Step 4: Table Output
- **Step name**: `Table Output - DimWaktu`
- **Connection**: `DW_Connection`
- **Target table**: `dim_waktu`
- **Fields**: Map semua field

**Connect**: Table Input → Group By → Calculator → Table Output

**Save**: `04_Populate_Dim_Waktu.ktr`

---

### 05. DimProdi

**File**: `05_Populate_Dim_Prodi.ktr`

#### Step 1: Table Input - Users
- **Step name**: `Table Input - Prodi from Users`
- **Connection**: `Operational_DB`
- **SQL**:
```sql
SELECT DISTINCT prodi as nama_prodi 
FROM users 
WHERE prodi IS NOT NULL AND prodi != '';
```

#### Step 2: Table Input - MataKuliah
- **Step name**: `Table Input - Prodi from MataKuliah`
- **Connection**: `Operational_DB`
- **SQL**:
```sql
SELECT DISTINCT prodi as nama_prodi 
FROM mata_kuliahs 
WHERE prodi IS NOT NULL AND prodi != '';
```

#### Step 3: Table Input - Jadwal
- **Step name**: `Table Input - Prodi from Jadwal`
- **Connection**: `Operational_DB`
- **SQL**:
```sql
SELECT DISTINCT prodi as nama_prodi 
FROM jadwals 
WHERE prodi IS NOT NULL AND prodi != '';
```

#### Step 4: Union Rows
1. Drag **Flow** → **Union Rows** ke canvas
2. Double-click step
3. **Step name**: `Union Rows - Combine Prodi`
4. Klik **OK**

#### Step 5: Group By
- **Step name**: `Group By - Unique Prodi`
- **Group**: `nama_prodi` (Group)

#### Step 6: Calculator
- **Step name**: `Calculator - Generate Keys`
- **Fields**:
  - `kode_prodi`: `UPPER(SUBSTRING(REPLACE(nama_prodi, ' ', ''), 1, 10))`
  - `prodi_key`: `CONCAT('PRODI_', kode_prodi)`
  - `fakultas`:
    ```
    CASE 
        WHEN nama_prodi LIKE '%Teknik%' THEN 'Fakultas Teknik'
        WHEN nama_prodi LIKE '%Ekonomi%' THEN 'Fakultas Ekonomi'
        WHEN nama_prodi LIKE '%Hukum%' THEN 'Fakultas Hukum'
        ELSE 'Fakultas Lainnya'
    END
    ```
  - `is_active`: `1`
  - `valid_from`: `NOW()`
  - `valid_to`: `NULL`

#### Step 7: Table Output
- **Step name**: `Table Output - DimProdi`
- **Connection**: `DW_Connection`
- **Target table**: `dim_prodi`
- **Fields**: Map semua field

**Connect**: 
- 3 Table Input → Union Rows → Group By → Calculator → Table Output

**Save**: `05_Populate_Dim_Prodi.ktr`

---

### 06. DimPreferensi

**File**: `06_Populate_Dim_Preferensi.ktr`

#### Step 1: Table Input
- **Step name**: `Table Input - Preferensi`
- **Connection**: `Operational_DB`
- **SQL**:
```sql
SELECT 
    id,
    dosen_id,
    mata_kuliah_id,
    preferensi_hari,
    preferensi_jam,
    prioritas,
    catatan,
    created_at,
    updated_at
FROM preferensi_dosens
ORDER BY dosen_id, prioritas;
```

#### Step 2: Calculator
- **Step name**: `Calculator - Generate Keys`
- **Fields**:
  - `preferensi_key`: `CONCAT('PREF_', LTRIM(STR(id)))`
  - `is_active`: `1`
  - `valid_from`: `created_at`
  - `valid_to`: `NULL`

#### Step 3: Table Output
- **Step name**: `Table Output - DimPreferensi`
- **Connection**: `DW_Connection`
- **Target table**: `dim_preferensi`
- **Fields**: Map semua field

**Save**: `06_Populate_Dim_Preferensi.ktr`

---

## FACT TABLES

---

### 07. FactJadwal

**File**: `07_Populate_Fact_Jadwal.ktr`

#### Step 1: Table Input
- **Step name**: `Table Input - Jadwal`
- **Connection**: `Operational_DB`
- **SQL**:
```sql
SELECT 
    j.id,
    j.mata_kuliah_id,
    j.ruangan_id,
    j.hari,
    j.jam_mulai,
    j.jam_selesai,
    j.semester,
    j.tahun_akademik,
    j.prodi,
    j.status,
    j.created_at,
    j.updated_at,
    mk.dosen_id,
    mk.sks,
    mk.kapasitas,
    r.kode_ruangan,
    r.kapasitas as kapasitas_ruangan
FROM jadwals j
INNER JOIN mata_kuliahs mk ON j.mata_kuliah_id = mk.id
INNER JOIN ruangans r ON j.ruangan_id = r.id
WHERE j.status = 1;
```

#### Step 2: Database Lookup - DimDosen
1. Drag **Lookup** → **Database Lookup** ke canvas
2. Double-click step
3. **Step name**: `Database Lookup - DimDosen`
4. **Connection**: `DW_Connection`
5. **Table name**: `dim_dosen`
6. **Key fields**:
   - `nip` = `dosen_id` (dari Table Input, perlu join dulu dengan users)
7. **Retrieved fields**:
   - `dosen_key`
8. Klik **OK**

**Catatan**: Jika `dosen_id` tidak langsung match dengan `nip`, tambahkan step **Database Lookup** ke `users` dulu untuk mendapatkan `nip`.

**Alternatif**: Gunakan **Stream Lookup** jika sudah ada data dim_dosen di stream sebelumnya.

#### Step 3: Database Lookup - DimMataKuliah
- **Step name**: `Database Lookup - DimMataKuliah`
- **Connection**: `DW_Connection`
- **Table name**: `dim_mata_kuliah`
- **Key**: `kode_mk` = `mata_kuliah_id` (perlu lookup kode_mk dulu)
- **Retrieved**: `mata_kuliah_key`

#### Step 4: Database Lookup - DimRuangan
- **Step name**: `Database Lookup - DimRuangan`
- **Connection**: `DW_Connection`
- **Table name**: `dim_ruangan`
- **Key**: `kode_ruangan` = `kode_ruangan`
- **Retrieved**: `ruangan_key`

#### Step 5: Database Lookup - DimWaktu
- **Step name**: `Database Lookup - DimWaktu`
- **Connection**: `DW_Connection`
- **Table name**: `dim_waktu`
- **Keys**:
  - `hari` = `hari`
  - `jam_mulai` = `jam_mulai`
  - `jam_selesai` = `jam_selesai`
- **Retrieved**: `waktu_key`

#### Step 6: Database Lookup - DimProdi
- **Step name**: `Database Lookup - DimProdi`
- **Connection**: `DW_Connection`
- **Table name**: `dim_prodi`
- **Key**: `nama_prodi` = `prodi`
- **Retrieved**: `prodi_key`

#### Step 7: Database Lookup - DimPreferensi
- **Step name**: `Database Lookup - DimPreferensi`
- **Connection**: `DW_Connection`
- **Table name**: `dim_preferensi`
- **Keys**:
  - `dosen_id` = `dosen_id`
  - `mata_kuliah_id` = `mata_kuliah_id`
- **Retrieved**: `preferensi_key`
- **Fail on multiple**: No (bisa null)

#### Step 8: Calculator
- **Step name**: `Calculator - Calculate Measures`
- **Fields**:
  - `jumlah_sks`: `sks`
  - `durasi_menit`: `TIMESTAMPDIFF(MINUTE, jam_mulai, jam_selesai)`
  - `kapasitas_kelas`: `kapasitas`
  - `jumlah_mahasiswa`: `kapasitas`
  - `utilisasi_ruangan`: 
    ```
    CASE 
        WHEN kapasitas_ruangan > 0 THEN (kapasitas / kapasitas_ruangan) * 100
        ELSE 0
    END
    ```
  - `status_aktif`: `CASE WHEN status = 1 THEN 1 ELSE 0 END`
  - `konflik_jadwal`: `0`
  - `tingkat_konflik`: `0`

#### Step 9: Table Output
- **Step name**: `Table Output - FactJadwal`
- **Connection**: `DW_Connection`
- **Target table**: `fact_jadwal`
- **Fields**: Map semua field termasuk keys dan measures

**Connect**: Table Input → 6 Database Lookups → Calculator → Table Output

**Save**: `07_Populate_Fact_Jadwal.ktr`

---

### 08. FactUtilisasiRuangan

**File**: `08_Populate_Fact_UtilisasiRuangan.ktr`

#### Step 1: Table Input
- **Step name**: `Table Input - FactJadwal`
- **Connection**: `DW_Connection`
- **SQL**:
```sql
SELECT 
    ruangan_key,
    waktu_key,
    prodi_key,
    durasi_menit,
    jumlah_mahasiswa,
    kapasitas_kelas
FROM fact_jadwal
WHERE status_aktif = 1;
```

#### Step 2: Group By
- **Step name**: `Group By - Aggregate by Room`
- **Group**:
  - `ruangan_key` (Group)
  - `waktu_key` (Group)
  - `prodi_key` (Group)
- **Aggregate**:
  - `total_jam_penggunaan`: Sum of `durasi_menit`
  - `total_mahasiswa`: Sum of `jumlah_mahasiswa`
  - `max_kapasitas`: Maximum of `kapasitas_kelas`

#### Step 3: Calculator
- **Step name**: `Calculator - Calculate Utilization`
- **Fields**:
  - `total_jam_tersedia`: `480` (8 jam = 480 menit)
  - `persentase_utilisasi`:
    ```
    CASE 
        WHEN total_jam_tersedia > 0 THEN (total_jam_penggunaan / total_jam_tersedia) * 100
        ELSE 0
    END
    ```

#### Step 4: Table Output
- **Step name**: `Table Output - FactUtilisasiRuangan`
- **Connection**: `DW_Connection`
- **Target table**: `fact_utilisasi_ruangan`
- **Fields**: Map semua field

**Connect**: Table Input → Group By → Calculator → Table Output

**Save**: `08_Populate_Fact_UtilisasiRuangan.ktr`

---

### 09. FactKecocokanJadwal

**File**: `09_Populate_Fact_KecocokanJadwal.ktr`

#### Step 1: Table Input
- **Step name**: `Table Input - FactJadwal with Preferensi`
- **Connection**: `DW_Connection`
- **SQL**:
```sql
SELECT 
    f.dosen_key,
    f.preferensi_key,
    f.waktu_key,
    w.hari,
    w.jam_mulai,
    w.jam_selesai,
    p.preferensi_hari,
    p.preferensi_jam,
    p.prioritas
FROM fact_jadwal f
INNER JOIN dim_waktu w ON f.waktu_key = w.waktu_key
LEFT JOIN dim_preferensi p ON f.preferensi_key = p.preferensi_key
WHERE f.status_aktif = 1
AND f.preferensi_key IS NOT NULL;
```

#### Step 2: Calculator
- **Step name**: `Calculator - Calculate Match`
- **Fields**:
  - `preferensi_hari_terpenuhi`:
    ```
    CASE 
        WHEN preferensi_hari LIKE CONCAT('%', hari, '%') THEN 1
        ELSE 0
    END
    ```
  - `preferensi_jam_terpenuhi`:
    ```
    CASE 
        WHEN preferensi_jam LIKE CONCAT('%', jam_mulai, '%') 
             OR preferensi_jam LIKE CONCAT('%', jam_selesai, '%') THEN 1
        ELSE 0
    END
    ```
  - `persentase_kecocokan`:
    ```
    CASE 
        WHEN preferensi_hari_terpenuhi = 1 AND preferensi_jam_terpenuhi = 1 THEN 100
        WHEN preferensi_hari_terpenuhi = 1 OR preferensi_jam_terpenuhi = 1 THEN 50
        ELSE 0
    END
    ```
  - `skor_kecocokan`:
    ```
    CASE 
        WHEN preferensi_hari_terpenuhi = 1 AND preferensi_jam_terpenuhi = 1 THEN prioritas * 2
        WHEN preferensi_hari_terpenuhi = 1 OR preferensi_jam_terpenuhi = 1 THEN prioritas
        ELSE 0
    END
    ```

#### Step 3: Table Output
- **Step name**: `Table Output - FactKecocokanJadwal`
- **Connection**: `DW_Connection`
- **Target table**: `fact_kecocokan_jadwal`
- **Fields**: Map semua field

**Connect**: Table Input → Calculator → Table Output

**Save**: `09_Populate_Fact_KecocokanJadwal.ktr`

---

## Tips Penting

1. **Urutan Eksekusi**: 
   - Jalankan Dimension Tables DULU (01-06)
   - Baru jalankan Fact Tables (07-09)

2. **Database Lookup**:
   - Pastikan dimension tables sudah ter-populate sebelum menjalankan fact tables
   - Test lookup dengan **Preview** sebelum Table Output

3. **Calculator Expressions**:
   - Gunakan **Preview** untuk test calculation
   - Pastikan tipe data sesuai (String, Integer, Date, Number)

4. **Error Handling**:
   - Jika lookup gagal, pastikan natural keys cocok
   - Gunakan **Filter Rows** untuk filter data yang tidak valid

5. **Performance**:
   - Gunakan **Get Fields** di Table Output untuk auto-mapping
   - Set **Commit** di Table Output (default 1000 rows)

---

## Testing

Setelah membuat semua transformations:

1. **Test setiap transformation**:
   - Klik **Preview** di Table Input untuk lihat data
   - Klik **Run** (F9) untuk execute
   - Cek **Execution Results** untuk error

2. **Verify data**:
   - Query database untuk cek jumlah rows
   - Pastikan semua keys ter-generate dengan benar

3. **Run berurutan**:
   - Dim tables: 01 → 02 → 03 → 04 → 05 → 06
   - Fact tables: 07 → 08 → 09

---

## Troubleshooting

**Error: Column not found**
- Periksa nama kolom di SQL query
- Pastikan kolom ada di tabel

**Error: Lookup failed**
- Pastikan dimension tables sudah ter-populate
- Periksa key matching (natural keys)

**Error: Calculation failed**
- Periksa tipe data di Calculator
- Test expression satu per satu

**Steps tidak muncul**
- File KTR tetap bisa di-run meskipun tidak terlihat
- Atau buat transformation baru dan drag-drop steps

---

**Selamat! Semua transformations sudah dibuat!** 🎉

