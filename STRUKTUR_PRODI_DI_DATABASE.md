# Struktur Prodi di Database

## ✅ Ya, Ada Prodi di Database!

Database memiliki struktur prodi di beberapa tempat:

---

## 📊 1. Tabel Khusus Prodi: `dim_prodi`

Tabel dimension khusus untuk program studi di data warehouse.

**Struktur:**
```sql
dim_prodi
├── id (Primary Key)
├── prodi_key (Surrogate Key, Unique) - Contoh: PRODI_TSD
├── kode_prodi (Unique) - Contoh: TSD
├── nama_prodi - Contoh: Teknologi Sains Data
├── fakultas - Contoh: Fakultas Teknik
├── deskripsi (nullable)
├── akreditasi (nullable)
├── is_active (boolean)
├── valid_from (timestamp, nullable)
├── valid_to (timestamp, nullable)
└── timestamps
```

**Cek data:**
```sql
SELECT * FROM dim_prodi WHERE is_active = 1;
```

---

## 📋 2. Kolom Prodi di Tabel Operasional

### a. Tabel `users`
- **Kolom**: `prodi` (string, nullable)
- **Digunakan untuk**: 
  - Admin Prodi: menentukan prodi yang dikelola
  - Dosen: menentukan prodi dosen
- **Contoh nilai**: `'Teknologi Sains Data'`, `'Rekayasa Nanoteknologi'`, dll

### b. Tabel `mata_kuliahs`
- **Kolom**: `prodi` (string)
- **Digunakan untuk**: menentukan prodi yang memiliki mata kuliah tersebut
- **Contoh nilai**: `'Teknologi Sains Data'`, `'Teknik Industri'`, dll

### c. Tabel `ruangans`
- **Kolom**: `prodi` (string, nullable)
- **Digunakan untuk**: menentukan prodi yang memiliki ruangan (bisa null untuk ruangan umum)
- **Contoh nilai**: `'Teknologi Sains Data'` atau `NULL` (untuk auditorium umum)

### d. Tabel `jadwals`
- **Kolom**: `prodi` (string)
- **Digunakan untuk**: menentukan prodi jadwal tersebut
- **Default**: `'Teknik Informatika'`
- **Contoh nilai**: `'Teknologi Sains Data'`, `'Rekayasa Nanoteknologi'`, dll

---

## 🔗 3. Prodi di Data Warehouse (Dimension & Fact Tables)

### Dimension Tables dengan Prodi:

#### `dim_dosen`
- **Kolom**: `prodi` (string)
- Menyimpan prodi dosen

#### `dim_mata_kuliah`
- **Kolom**: `prodi` (string)
- Menyimpan prodi mata kuliah

#### `dim_ruangan`
- **Kolom**: `prodi` (string, nullable)
- Menyimpan prodi ruangan (bisa null)

### Fact Tables dengan Prodi:

#### `fact_jadwal`
- **Kolom**: `prodi_key` (string, foreign key ke `dim_prodi.prodi_key`)
- Menyimpan referensi ke prodi melalui surrogate key

#### `fact_utilisasi_ruangan`
- **Kolom**: `prodi_key` (string, foreign key ke `dim_prodi.prodi_key`)
- Menyimpan referensi ke prodi

---

## 📊 Program Studi yang Tersedia

Berdasarkan seeder, ada **5 Program Studi**:

1. **Teknologi Sains Data** (TSD)
2. **Rekayasa Nanoteknologi** (RN)
3. **Teknik Industri** (TI)
4. **Teknik Elektro** (TE)
5. **Teknik Robotika dan Kecerdasan Buatan** (TRKB)

---

## 🔍 Query untuk Melihat Prodi

### Cek Prodi di DimProdi:
```sql
SELECT * FROM dim_prodi WHERE is_active = 1;
```

### Cek Prodi di Tabel Operasional:
```sql
-- Prodi dari Users
SELECT DISTINCT prodi FROM users WHERE prodi IS NOT NULL;

-- Prodi dari MataKuliah
SELECT DISTINCT prodi FROM mata_kuliahs WHERE prodi IS NOT NULL;

-- Prodi dari Jadwal
SELECT DISTINCT prodi FROM jadwals WHERE prodi IS NOT NULL;

-- Prodi dari Ruangan
SELECT DISTINCT prodi FROM ruangans WHERE prodi IS NOT NULL;
```

### Cek Prodi di Data Warehouse:
```sql
-- Prodi dari DimProdi
SELECT prodi_key, kode_prodi, nama_prodi, fakultas 
FROM dim_prodi 
WHERE is_active = 1;

-- Jadwal per Prodi
SELECT 
    p.nama_prodi,
    COUNT(f.id) as total_jadwal
FROM fact_jadwal f
JOIN dim_prodi p ON f.prodi_key = p.prodi_key
WHERE f.status_aktif = 1
GROUP BY p.nama_prodi;
```

---

## 📝 Cara Menggunakan Prodi di Pentaho

### 1. Untuk Populate DimProdi:

Query di Table Input:
```sql
-- Ambil prodi dari Users
SELECT DISTINCT prodi as nama_prodi 
FROM users 
WHERE prodi IS NOT NULL AND prodi != ''

UNION

-- Ambil prodi dari MataKuliah
SELECT DISTINCT prodi as nama_prodi 
FROM mata_kuliahs 
WHERE prodi IS NOT NULL AND prodi != ''

UNION

-- Ambil prodi dari Jadwal
SELECT DISTINCT prodi as nama_prodi 
FROM jadwals 
WHERE prodi IS NOT NULL AND prodi != '';
```

### 2. Untuk Lookup Prodi di Fact Tables:

Gunakan Database Lookup dengan:
- **Lookup table**: `dim_prodi`
- **Lookup key**: `nama_prodi` (dari tabel operasional)
- **Return value**: `prodi_key`

---

## ✅ Kesimpulan

**Ya, ada prodi di database!**

1. ✅ **Tabel khusus**: `dim_prodi` (dimension table)
2. ✅ **Kolom prodi** di tabel operasional:
   - `users.prodi`
   - `mata_kuliahs.prodi`
   - `ruangans.prodi`
   - `jadwals.prodi`
3. ✅ **Prodi key** di fact tables:
   - `fact_jadwal.prodi_key`
   - `fact_utilisasi_ruangan.prodi_key`
4. ✅ **Kolom prodi** di dimension tables:
   - `dim_dosen.prodi`
   - `dim_mata_kuliah.prodi`
   - `dim_ruangan.prodi`

**Total: 5 Program Studi** yang tersedia di sistem.

---

**Prodi tersedia di database dan siap digunakan!** 🎓

