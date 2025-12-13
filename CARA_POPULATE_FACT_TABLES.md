# Cara Populate Fact Tables

**PENTING**: Dashboard Admin Prodi menggunakan data dari fact tables. Jika fact tables belum ter-populate, semua nilai akan 0.

## Langkah-langkah

### 1. Pastikan Dimension Tables Sudah Ter-populate

Sebelum populate fact tables, pastikan dimension tables sudah terisi:

```bash
# Cek apakah dim tables sudah ada data
php artisan tinker
>>> \App\Models\DimDosen::count()
>>> \App\Models\DimMataKuliah::count()
>>> \App\Models\DimRuangan::count()
>>> \App\Models\DimWaktu::count()
>>> \App\Models\DimProdi::count()
>>> \App\Models\DimPreferensi::count()
```

Jika semua return 0, Anda perlu populate dimension tables terlebih dahulu (menggunakan Pentaho atau command Laravel).

### 2. Populate Fact Tables

Jalankan command untuk populate fact tables:

```bash
php artisan fact:populate
```

Atau jika ingin clear data lama dulu:

```bash
php artisan fact:populate --fresh
```

### 3. Verifikasi Data

Cek apakah fact tables sudah terisi:

```bash
php artisan tinker
>>> \App\Models\FactJadwal::count()
>>> \App\Models\FactUtilisasiRuangan::count()
>>> \App\Models\FactKecocokanJadwal::count()
```

### 4. Cek Data per Prodi

```bash
php artisan tinker
>>> $prodi = \App\Models\DimProdi::where('nama_prodi', 'Teknologi Sains Data')->first();
>>> \App\Models\FactJadwal::where('prodi_key', $prodi->prodi_key)->count()
>>> \App\Models\FactUtilisasiRuangan::where('prodi_key', $prodi->prodi_key)->avg('persentase_utilisasi')
>>> \App\Models\FactKecocokanJadwal::whereIn('dosen_key', [...])->avg('persentase_kecocokan')
```

## Troubleshooting

### Dashboard Menampilkan 0%

**Penyebab**: Fact tables belum ter-populate atau tidak ada data jadwal.

**Solusi**:
1. Pastikan ada jadwal di tabel `jadwals` (status = true)
2. Jalankan `php artisan fact:populate`
3. Refresh dashboard

### Error: Foreign key constraint

**Penyebab**: Dimension tables belum ter-populate.

**Solusi**: Populate dimension tables terlebih dahulu.

### Data tidak sesuai

**Penyebab**: Fact tables perlu di-update setelah ada perubahan jadwal.

**Solusi**: Jalankan `php artisan fact:populate --fresh` untuk refresh data.

## Catatan Penting

- **TIDAK ADA MANIPULASI NILAI**: Semua data diambil langsung dari fact tables di database
- **Persentase**: Diambil langsung dari kolom `persentase_utilisasi` dan `persentase_kecocokan`
- **Update**: Fact tables perlu di-populate ulang setiap kali ada perubahan jadwal

