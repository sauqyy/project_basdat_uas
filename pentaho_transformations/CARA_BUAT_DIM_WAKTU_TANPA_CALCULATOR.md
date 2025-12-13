# Cara Membuat DimWaktu TANPA Calculator Step

## ✅ Solusi: Gunakan SQL Langsung

Karena Calculator step tidak tersedia, kita akan menghitung semua field langsung di SQL query.

---

## 🚀 Step-by-Step

### Step 1: Table Input dengan SQL Lengkap

1. **File** → **New** → **Transformation**
2. Drag **Input** → **Table Input** ke canvas
3. Double-click step
4. **Step name**: `Table Input - DimWaktu Lengkap`
5. **Connection**: `Operational_DB`
6. **SQL**: Copy query di bawah ini

```sql
SELECT DISTINCT
    -- Data dasar
    hari,
    jam_mulai,
    jam_selesai,
    semester,
    tahun_akademik,
    
    -- Generate waktu_key: Senin_0800_1000_Ganjil_2024
    CONCAT(
        hari, '_',
        REPLACE(CAST(jam_mulai AS CHAR), ':', ''), '_',
        REPLACE(CAST(jam_selesai AS CHAR), ':', ''), '_',
        CAST(semester AS CHAR), '_',
        CAST(tahun_akademik AS CHAR)
    ) AS waktu_key,
    
    -- hari_ke: 1=Senin, 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat
    CASE 
        WHEN hari = 'Senin' THEN 1
        WHEN hari = 'Selasa' THEN 2
        WHEN hari = 'Rabu' THEN 3
        WHEN hari = 'Kamis' THEN 4
        WHEN hari = 'Jumat' THEN 5
        ELSE 0
    END AS hari_ke,
    
    -- slot_waktu: Pagi (<12), Siang (12-15), Sore (>=15)
    CASE 
        WHEN HOUR(jam_mulai) < 12 THEN 'Pagi'
        WHEN HOUR(jam_mulai) < 15 THEN 'Siang'
        ELSE 'Sore'
    END AS slot_waktu,
    
    -- durasi_menit: selisih jam_mulai dan jam_selesai dalam menit
    TIMESTAMPDIFF(MINUTE, jam_mulai, jam_selesai) AS durasi_menit,
    
    -- periode: Ganjil (semester ganjil) atau Genap (semester genap)
    CASE 
        WHEN MOD(CAST(semester AS UNSIGNED), 2) = 1 THEN 'Ganjil'
        ELSE 'Genap'
    END AS periode,
    
    -- is_active: default 1 (aktif)
    1 AS is_active
    
FROM jadwals
WHERE status = 1
ORDER BY hari, jam_mulai;
```

7. Klik **Preview** untuk test query
8. Klik **OK**

---

### Step 2: Table Output

1. Drag **Output** → **Table Output** ke canvas
2. Connect dari **Table Input** ke **Table Output** (drag arrow)
3. Double-click **Table Output**
4. **Step name**: `Table Output - DimWaktu`
5. **Connection**: `DW_Connection`
6. **Target table**: `dim_waktu`
7. Klik **Get Fields** (untuk auto-map semua field)
8. Atau map manual:
   - `waktu_key` → `waktu_key`
   - `hari` → `hari`
   - `jam_mulai` → `jam_mulai`
   - `jam_selesai` → `jam_selesai`
   - `semester` → `semester`
   - `tahun_akademik` → `tahun_akademik`
   - `periode` → `periode`
   - `hari_ke` → `hari_ke`
   - `slot_waktu` → `slot_waktu`
   - `durasi_menit` → `durasi_menit`
   - `is_active` → `is_active`
9. Klik **OK**

---

### Step 3: Save dan Run

1. **File** → **Save As**
2. Nama file: `04_Populate_Dim_Waktu.ktr`
3. Klik **Save**
4. Klik **Run** (play button) untuk test
5. Cek hasil di database

---

## 🔄 Alternatif: Menggunakan JavaScript Step

Jika SQL tidak support semua fungsi, gunakan JavaScript:

### Step 1: Table Input (Sederhana)

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

### Step 2: Modified JavaScript Value

1. Drag **Scripting** → **Modified JavaScript Value** ke canvas
2. Connect dari **Table Input** ke **Modified JavaScript Value**
3. Double-click step
4. **Step name**: `JavaScript - Generate Keys`
5. Di tab **Script**, paste:

```javascript
// Generate waktu_key
var waktu_key = hari + '_' + 
    String(jam_mulai).replace(/:/g, '') + '_' + 
    String(jam_selesai).replace(/:/g, '') + '_' + 
    String(semester) + '_' + 
    String(tahun_akademik);

// Hitung hari_ke
var hari_ke = 0;
if (hari == 'Senin') hari_ke = 1;
else if (hari == 'Selasa') hari_ke = 2;
else if (hari == 'Rabu') hari_ke = 3;
else if (hari == 'Kamis') hari_ke = 4;
else if (hari == 'Jumat') hari_ke = 5;

// Hitung slot_waktu
var jamStr = String(jam_mulai);
var jam = parseInt(jamStr.substring(0, 2));
var slot_waktu = '';
if (jam < 12) slot_waktu = 'Pagi';
else if (jam < 15) slot_waktu = 'Siang';
else slot_waktu = 'Sore';

// Hitung durasi_menit
var mulai = new Date('2000-01-01 ' + jam_mulai);
var selesai = new Date('2000-01-01 ' + jam_selesai);
var durasi_menit = Math.round((selesai - mulai) / 60000);

// Hitung periode
var periode = (parseInt(semester) % 2 == 1) ? 'Ganjil' : 'Genap';

// is_active
var is_active = 1;
```

6. Di tab **Fields**, klik **Get Variables** atau tambahkan manual:
   - `waktu_key` (String)
   - `hari_ke` (Integer)
   - `slot_waktu` (String)
   - `durasi_menit` (Integer)
   - `periode` (String)
   - `is_active` (Integer)
7. Klik **OK**

### Step 3: Table Output

(Sama seperti di atas)

---

## 🔍 Di Mana Calculator di Pentaho?

Jika ingin mencari Calculator step:

1. **Kategori**: **Transform** atau **Scripting**
2. **Nama step**: 
   - `Calculator` (versi lama PDI)
   - `Formula` (versi baru)
   - `User Defined Java Expression` (UDJE)
3. **Cara cari**: 
   - Klik kanan di canvas → **New Step**
   - Ketik "calc" atau "formula" di search box
   - Atau buka **Design** → **Transform** → cari "Calculator"

**Tapi TIDAK PERLU** - gunakan SQL langsung lebih mudah!

---

## ✅ Verifikasi

Setelah transformation berhasil:

1. Cek di database:
```sql
SELECT * FROM dim_waktu ORDER BY hari_ke, jam_mulai LIMIT 10;
```

2. Pastikan:
   - ✅ `waktu_key` tidak null dan unique
   - ✅ `hari_ke` antara 1-5
   - ✅ `slot_waktu` berisi 'Pagi', 'Siang', atau 'Sore'
   - ✅ `durasi_menit` > 0
   - ✅ `periode` berisi 'Ganjil' atau 'Genap'
   - ✅ `is_active` = 1

---

## 🔧 Troubleshooting SQL

### Error: "Unknown function CONCAT"
- **MySQL**: Gunakan `CONCAT()`
- **PostgreSQL**: Gunakan `||` atau `CONCAT()`
- **SQLite**: Gunakan `||`

### Error: "Unknown function TIMESTAMPDIFF"
- **MySQL**: `TIMESTAMPDIFF(MINUTE, jam_mulai, jam_selesai)`
- **PostgreSQL**: `EXTRACT(EPOCH FROM (jam_selesai - jam_mulai)) / 60`
- **SQLite**: `(julianday(jam_selesai) - julianday(jam_mulai)) * 1440`

### Error: "Unknown function HOUR"
- **MySQL**: `HOUR(jam_mulai)`
- **PostgreSQL**: `EXTRACT(HOUR FROM jam_mulai)`
- **SQLite**: `CAST(strftime('%H', jam_mulai) AS INTEGER)`

### Error: "Unknown function MOD"
- **MySQL**: `MOD(semester, 2)`
- **PostgreSQL**: `semester % 2`
- **SQLite**: `semester % 2`

---

## 📊 Struktur Tabel dim_waktu

Pastikan tabel sudah dibuat:

```sql
CREATE TABLE dim_waktu (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    waktu_key VARCHAR(255) UNIQUE,
    hari VARCHAR(20),
    jam_mulai TIME,
    jam_selesai TIME,
    semester VARCHAR(20),
    tahun_akademik INT,
    periode VARCHAR(20),
    hari_ke INT,
    slot_waktu VARCHAR(20),
    durasi_menit INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 🎯 Kesimpulan

**Cara TERMUDAH**: 
1. ✅ Gunakan **SQL lengkap di Table Input** (semua kalkulasi di SQL)
2. ✅ Langsung ke **Table Output**
3. ✅ **TIDAK PERLU Calculator step**

**Alternatif**: 
- Gunakan **JavaScript step** jika SQL tidak support semua fungsi

---

**DimWaktu bisa dibuat tanpa Calculator step!** 🎉

