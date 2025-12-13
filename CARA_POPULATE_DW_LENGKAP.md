# Cara Populate Data Warehouse - LENGKAP

**PENTING**: Dashboard Admin Prodi HARUS menampilkan data dari fact tables. Ikuti langkah-langkah berikut:

---

## ⚠️ PRASYARAT

Sebelum populate fact tables, pastikan:

1. ✅ Ada jadwal di tabel `jadwals` (status = true)
2. ✅ Ada mata kuliah, dosen, dan ruangan
3. ✅ Dimension tables sudah ter-populate (atau akan di-populate otomatis)

---AWJDHAUIWDGAUWHD(*AW)

## 🚀 CARA CEPAT (RECOMMENDED)

### Opsi 1: Menggunakan Command Baru (All-in-One)

```bash
php artisan dw:populate-all --fresh
```

Command ini akan:
- ✅ Populate semua dimension tables (DimDosen, DimMataKuliah, DimRuangan, DimWaktu, DimProdi, DimPreferensi)
- ✅ Populate semua fact tables (FactJadwal, FactUtilisasiRuangan, FactKecocokanJadwal)
- ✅ Menghitung semua measures langsung dari data operasional
- ✅ Menampilkan summary di akhir

### Opsi 2: Menggunakan Script Batch (Windows)

Jalankan file:
```bash
populate_data_warehouse.bat
```

---

## 📋 CARA MANUAL (Step-by-Step)

### Step 1: Pastikan Ada Data Operasional

```bash
php artisan tinker
```

Di dalam tinker, cek:
```php
\App\Models\Jadwal::where('status', true)->count();
\App\Models\MataKuliah::count();
\App\Models\User::where('role', 'dosen')->count();
\App\Models\Ruangan::count();
```

**Jika semua 0**, Anda perlu:
1. Generate jadwal dulu (login sebagai Admin Prodi → Generate Jadwal)
2. Atau buat jadwal manual

### Step 2: Populate Dimension Tables

#### 2.1. DimDosen
```php
php artisan tinker
```

```php
use App\Models\User;
use App\Models\DimDosen;

User::whereIn('role', ['dosen', 'admin_prodi'])->get()->each(function($u) {
    DimDosen::updateOrCreate(
        ['nip' => $u->nip ?? $u->id],
        [
            'dosen_key' => 'DOSEN_' . ($u->nip ?? $u->id),
            'nama_dosen' => $u->name,
            'email' => $u->email,
            'prodi' => $u->prodi,
            'role' => $u->role,
            'profile_picture' => $u->profile_picture,
            'judul_skripsi' => $u->judul_skripsi,
            'is_active' => true,
            'valid_from' => $u->created_at ?? now(),
            'valid_to' => null,
        ]
    );
});

echo 'DimDosen: ' . DimDosen::count();
```

#### 2.2. DimMataKuliah
```php
use App\Models\MataKuliah;
use App\Models\DimMataKuliah;

MataKuliah::all()->each(function($mk) {
    DimMataKuliah::updateOrCreate(
        ['kode_mk' => $mk->kode_mk],
        [
            'mata_kuliah_key' => 'MK_' . $mk->kode_mk,
            'nama_mk' => $mk->nama_mk,
            'sks' => $mk->sks,
            'semester' => $mk->semester,
            'prodi' => $mk->prodi,
            'kapasitas' => $mk->kapasitas,
            'deskripsi' => $mk->deskripsi,
            'tipe_kelas' => $mk->tipe_kelas,
            'menit_per_sks' => $mk->menit_per_sks,
            'ada_praktikum' => $mk->ada_praktikum ?? false,
            'sks_praktikum' => $mk->sks_praktikum ?? 0,
            'sks_materi' => $mk->sks_materi ?? 0,
            'is_active' => true,
            'valid_from' => $mk->created_at ?? now(),
            'valid_to' => null,
        ]
    );
});

echo 'DimMataKuliah: ' . DimMataKuliah::count();
```

#### 2.3. DimRuangan
```php
use App\Models\Ruangan;
use App\Models\DimRuangan;

Ruangan::all()->each(function($r) {
    DimRuangan::updateOrCreate(
        ['kode_ruangan' => $r->kode_ruangan],
        [
            'ruangan_key' => 'R_' . $r->kode_ruangan,
            'nama_ruangan' => $r->nama_ruangan,
            'kapasitas' => $r->kapasitas,
            'tipe_ruangan' => $r->tipe_ruangan,
            'fasilitas' => $r->fasilitas,
            'prodi' => $r->prodi,
            'status' => $r->status ? 'tersedia' : 'tidak_tersedia',
            'is_active' => true,
            'valid_from' => $r->created_at ?? now(),
            'valid_to' => null,
        ]
    );
});

echo 'DimRuangan: ' . DimRuangan::count();
```

#### 2.4. DimWaktu
```php
use App\Models\Jadwal;
use App\Models\DimWaktu;

Jadwal::where('status', true)
    ->select('hari', 'jam_mulai', 'jam_selesai', 'semester', 'tahun_akademik')
    ->distinct()
    ->get()
    ->each(function($j) {
        $jamMulai = is_object($j->jam_mulai) ? $j->jam_mulai->format('H:i') : $j->jam_mulai;
        $jamSelesai = is_object($j->jam_selesai) ? $j->jam_selesai->format('H:i') : $j->jam_selesai;
        
        $waktuKey = $j->hari . '_' . str_replace(':', '', $jamMulai) . '_' . str_replace(':', '', $jamSelesai) . '_' . $j->semester . '_' . $j->tahun_akademik;
        
        $hariKe = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5][$j->hari] ?? 0;
        $hour = (int) explode(':', $jamMulai)[0];
        $slotWaktu = $hour < 12 ? 'Pagi' : ($hour < 15 ? 'Siang' : 'Sore');
        
        $start = strtotime($jamMulai);
        $end = strtotime($jamSelesai);
        $durasiMenit = (int) (($end - $start) / 60);
        $periode = (intval($j->semester) % 2 == 1) ? 'Ganjil' : 'Genap';
        
        DimWaktu::updateOrCreate(
            [
                'hari' => $j->hari,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'semester' => $j->semester,
                'tahun_akademik' => $j->tahun_akademik,
            ],
            [
                'waktu_key' => $waktuKey,
                'hari_ke' => $hariKe,
                'slot_waktu' => $slotWaktu,
                'durasi_menit' => $durasiMenit,
                'periode' => $periode,
                'is_active' => true,
            ]
        );
    });

echo 'DimWaktu: ' . DimWaktu::count();
```

#### 2.5. DimProdi
```php
use App\Models\DimProdi;

$prodiList = collect();
$prodiList = $prodiList->merge(\App\Models\User::whereNotNull('prodi')->distinct()->pluck('prodi'));
$prodiList = $prodiList->merge(\App\Models\MataKuliah::whereNotNull('prodi')->distinct()->pluck('prodi'));
$prodiList = $prodiList->merge(\App\Models\Jadwal::whereNotNull('prodi')->distinct()->pluck('prodi'));
$prodiList = $prodiList->unique()->filter();

$prodiList->each(function($prodi) {
    $kodeProdi = strtoupper(substr(str_replace(' ', '', $prodi), 0, 10));
    $fakultas = stripos($prodi, 'Teknik') !== false ? 'Fakultas Teknik' : 'Fakultas Lainnya';
    
    DimProdi::updateOrCreate(
        ['nama_prodi' => $prodi],
        [
            'prodi_key' => 'PRODI_' . $kodeProdi,
            'kode_prodi' => $kodeProdi,
            'fakultas' => $fakultas,
            'is_active' => true,
            'valid_from' => now(),
            'valid_to' => null,
        ]
    );
});

echo 'DimProdi: ' . DimProdi::count();
```

#### 2.6. DimPreferensi
```php
use App\Models\PreferensiDosen;
use App\Models\DimPreferensi;

PreferensiDosen::all()->each(function($pref) {
    DimPreferensi::updateOrCreate(
        [
            'dosen_id' => $pref->dosen_id,
            'mata_kuliah_id' => $pref->mata_kuliah_id,
        ],
        [
            'preferensi_key' => 'PREF_' . $pref->id,
            'preferensi_hari' => is_string($pref->preferensi_hari) ? $pref->preferensi_hari : json_encode($pref->preferensi_hari),
            'preferensi_jam' => is_string($pref->preferensi_jam) ? $pref->preferensi_jam : json_encode($pref->preferensi_jam),
            'prioritas' => $pref->prioritas,
            'catatan' => $pref->catatan,
            'is_active' => true,
            'valid_from' => $pref->created_at ?? now(),
            'valid_to' => null,
        ]
    );
});

echo 'DimPreferensi: ' . DimPreferensi::count();
```

### Step 3: Populate Fact Tables

Setelah semua dimension tables ter-populate, jalankan:

```bash
php artisan fact:populate --fresh
```

---

## ✅ VERIFIKASI

Setelah populate, verifikasi data:

```bash
php artisan tinker
```

```php
// Cek dimension tables
\App\Models\DimDosen::count();
\App\Models\DimMataKuliah::count();
\App\Models\DimRuangan::count();
\App\Models\DimWaktu::count();
\App\Models\DimProdi::count();
\App\Models\DimPreferensi::count();

// Cek fact tables
\App\Models\FactJadwal::count();
\App\Models\FactUtilisasiRuangan::count();
\App\Models\FactKecocokanJadwal::count();

// Cek data per prodi
$prodi = \App\Models\DimProdi::where('nama_prodi', 'Teknologi Sains Data')->first();
if ($prodi) {
    echo 'FactJadwal untuk TSD: ' . \App\Models\FactJadwal::where('prodi_key', $prodi->prodi_key)->count();
    echo 'Avg Utilisasi: ' . \App\Models\FactUtilisasiRuangan::where('prodi_key', $prodi->prodi_key)->avg('persentase_utilisasi');
}
```

---

## 🔄 REFRESH DATA

Jika ada perubahan jadwal, jalankan lagi:

```bash
php artisan dw:populate-all --fresh
```

Atau:

```bash
php artisan fact:populate --fresh
```

---

## ⚠️ TROUBLESHOOTING

### Dashboard masih 0%

**Penyebab**: Fact tables belum ter-populate atau tidak ada jadwal.

**Solusi**:
1. Pastikan ada jadwal: `Jadwal::where('status', true)->count()` harus > 0
2. Jalankan: `php artisan dw:populate-all --fresh`
3. Refresh dashboard

### Error: Foreign key constraint

**Penyebab**: Dimension tables belum ter-populate.

**Solusi**: Jalankan populate dimension tables dulu (Step 2).

### Error: Column not found

**Penyebab**: Migration belum dijalankan.

**Solusi**: `php artisan migrate`

---

## 📝 CATATAN PENTING

- ✅ **TIDAK ADA MANIPULASI NILAI**: Semua data diambil langsung dari fact tables di database
- ✅ **Persentase**: Diambil langsung dari kolom `persentase_utilisasi` dan `persentase_kecocokan` di fact tables
- ✅ **Update**: Fact tables perlu di-populate ulang setiap kali ada perubahan jadwal
- ✅ **Urutan**: Dimension tables HARUS ter-populate sebelum fact tables

---

**Setelah populate, refresh dashboard dan data akan muncul!** 🎉

