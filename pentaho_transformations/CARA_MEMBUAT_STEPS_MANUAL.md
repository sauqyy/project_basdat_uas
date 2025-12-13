Cara Membuat Steps Secara Manual di Pentaho Spoon

Jika file KTR tidak menampilkan steps di canvas, ikuti langkah berikut untuk membuat transformation secara manual:

## Langkah 1: Buat Transformation Baru

1. Buka Pentaho Spoon
2. **File** → **New** → **Transformation** (atau tekan `Ctrl+N`)
3. Canvas kosong akan muncul

## Langkah 2: Setup Database Connections

1. **View** → **Database Connections** (atau tekan `Ctrl+Shift+D`)
2. Klik kanan → **New**
3. Buat 2 connections:
   - **Operational_DB**: Database operasional (users, jadwals, dll)
   - **DW_Connection**: Database data warehouse (dim_*, fact_*)
4. Test kedua connections

## Langkah 3: Tambahkan Steps

### Step 1: Table Input
1. Di palette kiri, cari **Input** → **Table Input**
2. Drag ke canvas
3. Double-click step tersebut
4. **Step name**: `Table Input - Users`
5. **Connection**: Pilih `Operational_DB`
6. Klik **SQL Editor** atau **Get SQL select statement**
7. Masukkan query:
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
8. Klik **OK**

### Step 2: Calculator
1. Di palette, cari **Transform** → **Calculator**
2. Drag ke canvas (di sebelah kanan Table Input)
3. Double-click step tersebut
4. **Step name**: `Calculator - Generate Keys`
5. Klik **New** untuk menambahkan field calculation:

**Field 1: dosen_key**
- **New field**: `dosen_key`
- **Calculation**: `A + B`
- **Field A**: `id` (String)
- **Type**: String
- **Value**: `CONCAT('DOSEN_', LTRIM(STR(id)))`

**Field 2: is_active**
- **New field**: `is_active`
- **Calculation**: `A + B`
- **Field A**: `1` (Integer)
- **Type**: Integer
- **Value**: `1`

**Field 3: valid_from**
- **New field**: `valid_from`
- **Calculation**: `A + B`
- **Field A**: `created_at` (Date)
- **Type**: Date
- **Value**: `created_at`

**Field 4: valid_to**
- **New field**: `valid_to`
- **Calculation**: `A + B`
- **Field A**: `NULL` (Date)
- **Type**: Date
- **Value**: `NULL`

6. Klik **OK**

### Step 3: Table Output
1. Di palette, cari **Output** → **Table Output**
2. Drag ke canvas (di sebelah kanan Calculator)
3. Double-click step tersebut
4. **Step name**: `Table Output - DimDosen`
5. **Connection**: Pilih `DW_Connection`
6. **Target table**: `dim_dosen`
7. Klik **Get Fields** untuk auto-map fields
8. Atau map secara manual:
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
9. Klik **OK**

## Langkah 4: Connect Steps (Hops)

1. Hover mouse di **Table Input - Users**
2. Klik dan drag dari output arrow (panah keluar) ke **Calculator - Generate Keys**
3. Hover mouse di **Calculator - Generate Keys**
4. Klik dan drag dari output arrow ke **Table Output - DimDosen**
5. Sekarang 3 steps sudah terhubung

## Langkah 5: Test Transformation

1. Klik **Run** (F9) atau tombol play
2. Lihat **Execution Results** di bawah
3. Jika ada error, perbaiki sesuai error message
4. Jika berhasil, data akan masuk ke `dim_dosen`

## Langkah 6: Save

1. **File** → **Save As**
2. Simpan sebagai: `01_Populate_Dim_Dosen.ktr`
3. File siap digunakan!

## Tips

- Jika Calculator step terlalu kompleks, gunakan **Select Values** step untuk rename fields
- Gunakan **Preview** untuk melihat data sebelum Table Output
- Gunakan **Get Fields** di Table Output untuk auto-mapping
- Pastikan database connections sudah di-test sebelum run

## Troubleshooting

**Error: Connection not found**
- Pastikan connection name sama persis dengan yang dipilih di step
- Test connection terlebih dahulu

**Error: Table doesn't exist**
- Pastikan migration sudah dijalankan
- Periksa nama database di connection

**Error: Column not found**
- Periksa nama kolom di SQL query
- Pastikan kolom ada di tabel

**Steps tidak muncul setelah save dan reload**
- Ini normal jika file dibuat manual di Spoon
- File KTR yang dibuat manual akan tetap berfungsi saat di-run
- Steps akan muncul saat file dibuka kembali di Spoon

