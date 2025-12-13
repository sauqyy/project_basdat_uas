# Panduan Penggunaan Fact Tables

## Overview

Sistem data warehouse ini memiliki 3 fact tables utama:

1. **FactJadwal** - Fakta utama jadwal dengan semua dimensi
2. **FactUtilisasiRuangan** - Analisis utilisasi ruangan
3. **FactKecocokanJadwal** - Analisis kecocokan jadwal dengan preferensi dosen

---

## 1. FactJadwal

### Dimensi yang Digunakan:
- `DimDosen` (dosen_key)
- `DimMataKuliah` (mata_kuliah_key)
- `DimRuangan` (ruangan_key)
- `DimWaktu` (waktu_key)
- `DimProdi` (prodi_key)
- `DimPreferensi` (preferensi_key) - nullable

### Measures:
- `jumlah_sks` - Total SKS per jadwal
- `durasi_menit` - Durasi kelas dalam menit
- `kapasitas_kelas` - Kapasitas maksimal kelas
- `jumlah_mahasiswa` - Jumlah mahasiswa terdaftar
- `utilisasi_ruangan` - Persentase utilisasi ruangan
- `prioritas_preferensi` - Prioritas preferensi yang terpenuhi (1-5)
- `konflik_jadwal` - Boolean apakah ada konflik
- `tingkat_konflik` - Level konflik (0-5)

### Contoh Query:

```php
// Get jadwal dengan semua dimensi
$jadwal = FactJadwal::with([
    'dimDosen',
    'dimMataKuliah',
    'dimRuangan',
    'dimWaktu',
    'dimProdi',
    'dimPreferensi'
])->where('status_aktif', true)->get();

// Analisis utilisasi ruangan per prodi
$utilisasi = FactJadwal::select(
    'prodi_key',
    DB::raw('AVG(utilisasi_ruangan) as avg_utilisasi'),
    DB::raw('SUM(jumlah_mahasiswa) as total_mahasiswa')
)
->where('status_aktif', true)
->groupBy('prodi_key')
->get();

// Analisis konflik jadwal
$konflik = FactJadwal::where('konflik_jadwal', true)
    ->where('status_aktif', true)
    ->with(['dimDosen', 'dimRuangan', 'dimWaktu'])
    ->get();
```

---

## 2. FactUtilisasiRuangan

### Dimensi yang Digunakan:
- `DimRuangan` (ruangan_key)
- `DimWaktu` (waktu_key)
- `DimProdi` (prodi_key)

### Measures:
- `total_jam_penggunaan` - Total jam ruangan digunakan (dalam menit)
- `total_jam_tersedia` - Total jam ruangan tersedia (dalam menit)
- `persentase_utilisasi` - Persentase utilisasi ruangan
- `jumlah_kelas` - Jumlah kelas yang menggunakan ruangan
- `jumlah_mahasiswa_total` - Total mahasiswa yang menggunakan ruangan
- `rata_rata_kapasitas` - Rata-rata kapasitas terpakai
- `peak_hour_utilisasi` - Utilisasi pada jam sibuk

### Contoh Query:

```php
// Get utilisasi ruangan dengan dimensi
$utilisasi = FactUtilisasiRuangan::with([
    'dimRuangan',
    'dimWaktu',
    'dimProdi'
])->get();

// Analisis utilisasi per tipe ruangan
$utilisasiPerTipe = FactUtilisasiRuangan::select(
    DB::raw('AVG(persentase_utilisasi) as avg_utilisasi'),
    DB::raw('SUM(jumlah_kelas) as total_kelas')
)
->join('dim_ruangan', 'fact_utilisasi_ruangan.ruangan_key', '=', 'dim_ruangan.ruangan_key')
->where('dim_ruangan.tipe_ruangan', 'Laboratorium')
->groupBy('dim_ruangan.tipe_ruangan')
->get();

// Top 10 ruangan paling sering digunakan
$topRuangan = FactUtilisasiRuangan::select(
    'ruangan_key',
    DB::raw('SUM(total_jam_penggunaan) as total_penggunaan'),
    DB::raw('AVG(persentase_utilisasi) as avg_utilisasi')
)
->groupBy('ruangan_key')
->orderBy('total_penggunaan', 'desc')
->limit(10)
->get();
```

---

## 3. FactKecocokanJadwal

### Dimensi yang Digunakan:
- `DimDosen` (dosen_key)
- `DimPreferensi` (preferensi_key)
- `DimWaktu` (waktu_key)

### Measures:
- `preferensi_hari_terpenuhi` - Boolean apakah preferensi hari terpenuhi
- `preferensi_jam_terpenuhi` - Boolean apakah preferensi jam terpenuhi
- `skor_kecocokan` - Skor kecocokan 0-100
- `prioritas_preferensi` - Prioritas preferensi (1-5)
- `jumlah_preferensi_total` - Total preferensi dosen
- `jumlah_preferensi_terpenuhi` - Jumlah preferensi yang terpenuhi
- `persentase_kecocokan` - Persentase kecocokan
- `catatan_kecocokan` - Catatan tentang kecocokan

### Contoh Query:

```php
// Get kecocokan jadwal dengan dimensi
$kecocokan = FactKecocokanJadwal::with([
    'dimDosen',
    'dimPreferensi',
    'dimWaktu'
])->get();

// Analisis kepuasan dosen
$kepuasanDosen = FactKecocokanJadwal::select(
    'dosen_key',
    DB::raw('AVG(skor_kecocokan) as avg_skor'),
    DB::raw('AVG(persentase_kecocokan) as avg_persentase'),
    DB::raw('SUM(CASE WHEN preferensi_hari_terpenuhi = 1 THEN 1 ELSE 0 END) as hari_terpenuhi'),
    DB::raw('SUM(CASE WHEN preferensi_jam_terpenuhi = 1 THEN 1 ELSE 0 END) as jam_terpenuhi')
)
->groupBy('dosen_key')
->get();

// Dosen dengan kepuasan tertinggi
$topKepuasan = FactKecocokanJadwal::select(
    'dosen_key',
    DB::raw('AVG(skor_kecocokan) as avg_skor')
)
->groupBy('dosen_key')
->orderBy('avg_skor', 'desc')
->limit(10)
->get();
```

---

## Populate Data ke Fact Tables

### Menggunakan Command:

```bash
# Populate semua fact tables
php artisan fact:populate

# Populate dengan menghapus data lama terlebih dahulu
php artisan fact:populate --fresh
```

### Proses Populate:

1. **FactJadwal**: Di-populate dari tabel `jadwals` operasional
2. **FactUtilisasiRuangan**: Di-populate dari agregasi `FactJadwal`
3. **FactKecocokanJadwal**: Di-populate dari `jadwals` dan `preferensi_dosens`

### Catatan Penting:

- Pastikan dimensi sudah ter-populate sebelum menjalankan command
- Command akan otomatis menghitung measures seperti utilisasi, konflik, dan kecocokan
- Data akan di-update jika sudah ada (menggunakan `updateOrCreate`)

---

## Relasi Model

### FactJadwal
```php
$factJadwal->dimDosen
$factJadwal->dimMataKuliah
$factJadwal->dimRuangan
$factJadwal->dimWaktu
$factJadwal->dimProdi
$factJadwal->dimPreferensi
```

### FactUtilisasiRuangan
```php
$factUtilisasi->dimRuangan
$factUtilisasi->dimWaktu
$factUtilisasi->dimProdi
```

### FactKecocokanJadwal
```php
$factKecocokan->dimDosen
$factKecocokan->dimPreferensi
$factKecocokan->dimWaktu
```

### Dimensi ke Fact Tables
```php
// DimRuangan
$dimRuangan->factJadwals
$dimRuangan->factUtilisasiRuangan

// DimWaktu
$dimWaktu->factJadwals
$dimWaktu->factUtilisasiRuangan
$dimWaktu->factKecocokanJadwal

// DimDosen
$dimDosen->factJadwals
$dimDosen->factKecocokanJadwal

// DimPreferensi
$dimPreferensi->factJadwals
$dimPreferensi->factKecocokanJadwal

// DimProdi
$dimProdi->factJadwals
$dimProdi->factUtilisasiRuangan
```

---

## Best Practices

1. **Jalankan populate secara berkala** setelah ada perubahan jadwal
2. **Gunakan index** yang sudah dibuat untuk performa query yang lebih baik
3. **Filter dengan status_aktif** untuk mendapatkan data yang relevan
4. **Gunakan eager loading** (`with()`) untuk menghindari N+1 query problem
5. **Cache hasil query** yang sering digunakan untuk performa lebih baik

---

## Troubleshooting

### Error: Dimension key not found
- Pastikan dimensi sudah ter-populate sebelum populate fact tables
- Periksa mapping antara data operasional dan dimensi

### Data tidak ter-populate
- Periksa apakah jadwal memiliki status aktif
- Periksa apakah relasi antara jadwal dan dimensi sudah benar
- Lihat log error untuk detail lebih lanjut

### Performa lambat
- Pastikan index sudah dibuat dengan benar
- Gunakan pagination untuk query besar
- Pertimbangkan untuk menjalankan populate di background job




