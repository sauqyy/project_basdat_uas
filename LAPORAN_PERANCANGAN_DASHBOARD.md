# LAPORAN PERANCANGAN DASHBOARD DATA WAREHOUSE
## Sistem Generate Jadwal - Data Warehouse Dashboard

---

## 1. OVERVIEW DASHBOARD

Dashboard ini dirancang untuk memberikan analisis komprehensif terhadap sistem generate jadwal kuliah dengan fokus pada:
- **Utilisasi Ruangan**: Analisis penggunaan ruangan secara efisien
- **Beban Kerja Dosen**: Monitoring distribusi beban mengajar dosen
- **Konflik Jadwal**: Identifikasi dan analisis konflik jadwal
- **Kepuasan Preferensi**: Tingkat pemenuhan preferensi dosen
- **Statistik Program Studi**: Analisis per program studi

---

## 2. FAKTA (FACT TABLES)

### 2.1. FactJadwal (Fakta Utama Jadwal)

#### **Schema:**
```sql
CREATE TABLE fact_jadwal (
    id BIGINT PRIMARY KEY,
    dosen_key VARCHAR(50),
    mata_kuliah_key VARCHAR(50),
    ruangan_key VARCHAR(50),
    waktu_key VARCHAR(50),
    prodi_key VARCHAR(50),
    preferensi_key VARCHAR(50) NULLABLE,
    
    -- Measures
    jumlah_sks INTEGER,
    durasi_menit INTEGER,
    kapasitas_kelas INTEGER,
    jumlah_mahasiswa INTEGER,
    utilisasi_ruangan DECIMAL(5,2),
    prioritas_preferensi INTEGER,
    konflik_jadwal BOOLEAN,
    tingkat_konflik INTEGER,
    
    -- Metadata
    status_aktif BOOLEAN,
    created_at_jadwal TIMESTAMP,
    updated_at_jadwal TIMESTAMP
);
```

#### **Tujuan:**
- **Analisis Utama**: Tabel fakta utama untuk semua analisis jadwal
- **Tracking**: Melacak setiap jadwal yang dihasilkan dengan detail lengkap
- **Monitoring**: Memantau utilisasi ruangan, konflik, dan kepuasan preferensi
- **Reporting**: Basis data untuk semua laporan jadwal

#### **Dimensi yang Digunakan:**
- `DimDosen` (dosen_key)
- `DimMataKuliah` (mata_kuliah_key)
- `DimRuangan` (ruangan_key)
- `DimWaktu` (waktu_key)
- `DimProdi` (prodi_key)
- `DimPreferensi` (preferensi_key) - nullable

#### **Measures:**
- `jumlah_sks`: Total SKS per jadwal
- `durasi_menit`: Durasi kelas dalam menit
- `kapasitas_kelas`: Kapasitas maksimal kelas
- `jumlah_mahasiswa`: Jumlah mahasiswa terdaftar
- `utilisasi_ruangan`: Persentase utilisasi ruangan (jumlah_mahasiswa/kapasitas_kelas * 100)
- `prioritas_preferensi`: Prioritas preferensi yang terpenuhi (1-5)
- `konflik_jadwal`: Boolean apakah ada konflik
- `tingkat_konflik`: Level konflik (0-5)

---

### 2.2. FactUtilisasiRuangan (Fakta Utilisasi Ruangan)

#### **Schema:**
```sql
CREATE TABLE fact_utilisasi_ruangan (
    id BIGINT PRIMARY KEY,
    ruangan_key VARCHAR(50),
    waktu_key VARCHAR(50),
    prodi_key VARCHAR(50),
    
    -- Measures
    total_jam_penggunaan INTEGER,
    total_jam_tersedia INTEGER,
    persentase_utilisasi DECIMAL(5,2),
    jumlah_kelas INTEGER,
    jumlah_mahasiswa_total INTEGER,
    rata_rata_kapasitas DECIMAL(5,2),
    peak_hour_utilisasi DECIMAL(5,2),
    
    -- Metadata
    periode_semester VARCHAR(20),
    tahun_akademik VARCHAR(10),
    created_at TIMESTAMP
);
```

#### **Tujuan:**
- **Optimasi Ruangan**: Menganalisis utilisasi ruangan untuk optimasi alokasi
- **Identifikasi Ruangan Underutilized**: Menemukan ruangan yang kurang dimanfaatkan
- **Perencanaan**: Membantu perencanaan penambahan atau pengurangan ruangan
- **Cost Analysis**: Analisis biaya operasional ruangan

#### **Dimensi yang Digunakan:**
- `DimRuangan` (ruangan_key)
- `DimWaktu` (waktu_key)
- `DimProdi` (prodi_key)

#### **Measures:**
- `total_jam_penggunaan`: Total jam ruangan digunakan dalam periode
- `total_jam_tersedia`: Total jam ruangan tersedia
- `persentase_utilisasi`: (total_jam_penggunaan / total_jam_tersedia) * 100
- `jumlah_kelas`: Jumlah kelas yang menggunakan ruangan
- `jumlah_mahasiswa_total`: Total mahasiswa yang menggunakan ruangan
- `rata_rata_kapasitas`: Rata-rata kapasitas terpakai
- `peak_hour_utilisasi`: Utilisasi pada jam sibuk

---

### 2.3. FactBebanKerjaDosen (Fakta Beban Kerja Dosen)

#### **Schema:**
```sql
CREATE TABLE fact_beban_kerja_dosen (
    id BIGINT PRIMARY KEY,
    dosen_key VARCHAR(50),
    prodi_key VARCHAR(50),
    waktu_key VARCHAR(50),
    
    -- Measures
    total_sks INTEGER,
    total_jam_mengajar INTEGER,
    jumlah_mata_kuliah INTEGER,
    jumlah_kelas INTEGER,
    jumlah_mahasiswa_total INTEGER,
    rata_rata_sks_per_mk DECIMAL(3,1),
    beban_kerja_percentage DECIMAL(5,2),
    tingkat_kepuasan_preferensi DECIMAL(3,2),
    
    -- Metadata
    semester VARCHAR(20),
    tahun_akademik VARCHAR(10),
    created_at TIMESTAMP
);
```

#### **Tujuan:**
- **Distribusi Beban**: Memastikan distribusi beban kerja dosen yang merata
- **Workload Monitoring**: Memantau beban kerja dosen per semester
- **Fairness Analysis**: Menganalisis keadilan distribusi beban mengajar
- **Resource Planning**: Perencanaan alokasi dosen untuk semester berikutnya

#### **Dimensi yang Digunakan:**
- `DimDosen` (dosen_key)
- `DimProdi` (prodi_key)
- `DimWaktu` (waktu_key)

#### **Measures:**
- `total_sks`: Total SKS yang diampu dosen
- `total_jam_mengajar`: Total jam mengajar dalam menit
- `jumlah_mata_kuliah`: Jumlah mata kuliah berbeda yang diampu
- `jumlah_kelas`: Jumlah kelas yang diampu
- `jumlah_mahasiswa_total`: Total mahasiswa yang diajar
- `rata_rata_sks_per_mk`: Rata-rata SKS per mata kuliah
- `beban_kerja_percentage`: Persentase beban kerja (dibandingkan standar)
- `tingkat_kepuasan_preferensi`: Rata-rata prioritas preferensi yang terpenuhi

---

### 2.4. FactKonflikJadwal (Fakta Konflik Jadwal)

#### **Schema:**
```sql
CREATE TABLE fact_konflik_jadwal (
    id BIGINT PRIMARY KEY,
    dosen_key VARCHAR(50),
    ruangan_key VARCHAR(50),
    waktu_key VARCHAR(50),
    prodi_key VARCHAR(50),
    
    -- Measures
    jenis_konflik VARCHAR(50), -- 'dosen', 'ruangan', 'mahasiswa'
    tingkat_keparahan INTEGER, -- 1-5
    jumlah_terlibat INTEGER, -- jumlah jadwal yang konflik
    durasi_konflik_menit INTEGER,
    status_resolusi VARCHAR(20), -- 'unresolved', 'resolved', 'pending'
    
    -- Metadata
    semester VARCHAR(20),
    tahun_akademik VARCHAR(10),
    created_at TIMESTAMP,
    resolved_at TIMESTAMP NULLABLE
);
```

#### **Tujuan:**
- **Deteksi Konflik**: Mengidentifikasi semua konflik jadwal secara sistematis
- **Prioritasi Resolusi**: Memprioritaskan konflik berdasarkan tingkat keparahan
- **Quality Assurance**: Memastikan kualitas jadwal yang dihasilkan
- **Trend Analysis**: Menganalisis pola konflik untuk perbaikan algoritma

#### **Dimensi yang Digunakan:**
- `DimDosen` (dosen_key)
- `DimRuangan` (ruangan_key)
- `DimWaktu` (waktu_key)
- `DimProdi` (prodi_key)

#### **Measures:**
- `jenis_konflik`: Jenis konflik (dosen ganda, ruangan ganda, dll)
- `tingkat_keparahan`: Level keparahan konflik (1-5)
- `jumlah_terlibat`: Jumlah jadwal yang terlibat dalam konflik
- `durasi_konflik_menit`: Durasi konflik dalam menit
- `status_resolusi`: Status penyelesaian konflik

---

### 2.5. FactKepuasanPreferensi (Fakta Kepuasan Preferensi)

#### **Schema:**
```sql
CREATE TABLE fact_kepuasan_preferensi (
    id BIGINT PRIMARY KEY,
    dosen_key VARCHAR(50),
    preferensi_key VARCHAR(50),
    waktu_key VARCHAR(50),
    
    -- Measures
    preferensi_hari_terpenuhi BOOLEAN,
    preferensi_jam_terpenuhi BOOLEAN,
    skor_kepuasan INTEGER, -- 0-100
    prioritas_preferensi INTEGER, -- 1-5
    jumlah_preferensi_total INTEGER,
    jumlah_preferensi_terpenuhi INTEGER,
    persentase_kepuasan DECIMAL(5,2),
    
    -- Metadata
    semester VARCHAR(20),
    tahun_akademik VARCHAR(10),
    created_at TIMESTAMP
);
```

#### **Tujuan:**
- **Kepuasan Dosen**: Mengukur tingkat kepuasan dosen terhadap jadwal
- **Quality Metric**: Metrik kualitas algoritma generate jadwal
- **Improvement Tracking**: Melacak peningkatan kepuasan dari waktu ke waktu
- **Feedback Analysis**: Analisis feedback untuk perbaikan sistem

#### **Dimensi yang Digunakan:**
- `DimDosen` (dosen_key)
- `DimPreferensi` (preferensi_key)
- `DimWaktu` (waktu_key)

#### **Measures:**
- `preferensi_hari_terpenuhi`: Apakah preferensi hari terpenuhi
- `preferensi_jam_terpenuhi`: Apakah preferensi jam terpenuhi
- `skor_kepuasan`: Skor kepuasan 0-100
- `prioritas_preferensi`: Prioritas preferensi (1-5)
- `jumlah_preferensi_total`: Total preferensi dosen
- `jumlah_preferensi_terpenuhi`: Jumlah preferensi yang terpenuhi
- `persentase_kepuasan`: (jumlah_preferensi_terpenuhi / jumlah_preferensi_total) * 100

---

## 3. DIMENSI (DIMENSION TABLES)

### 3.1. DimDosen
**Atribut:**
- `dosen_key` (Surrogate Key)
- `nip` (Natural Key)
- `nama_dosen`
- `email`
- `prodi`
- `role`
- `profile_picture`
- `judul_skripsi`
- `is_active`
- `valid_from`, `valid_to` (SCD Type 2)

### 3.2. DimMataKuliah
**Atribut:**
- `mata_kuliah_key` (Surrogate Key)
- `kode_mk` (Natural Key)
- `nama_mk`
- `sks`
- `semester`
- `prodi`
- `kapasitas`
- `deskripsi`
- `tipe_kelas`
- `menit_per_sks`
- `ada_praktikum`
- `sks_praktikum`, `sks_materi`
- `is_active`
- `valid_from`, `valid_to` (SCD Type 2)

### 3.3. DimRuangan
**Atribut:**
- `ruangan_key` (Surrogate Key)
- `kode_ruangan` (Natural Key)
- `nama_ruangan`
- `kapasitas`
- `tipe_ruangan`
- `fasilitas`
- `prodi`
- `status`
- `is_active`
- `valid_from`, `valid_to` (SCD Type 2)

### 3.4. DimWaktu
**Atribut:**
- `waktu_key` (Surrogate Key)
- `hari` (Senin-Jumat)
- `jam_mulai`, `jam_selesai`
- `semester`
- `tahun_akademik`
- `periode` (Ganjil/Genap)
- `hari_ke` (1-5)
- `slot_waktu` (Pagi/Siang/Sore)
- `durasi_menit`
- `is_active`

### 3.5. DimProdi
**Atribut:**
- `prodi_key` (Surrogate Key)
- `kode_prodi` (Natural Key)
- `nama_prodi`
- `fakultas`
- `deskripsi`
- `akreditasi`
- `is_active`
- `valid_from`, `valid_to` (SCD Type 2)

### 3.6. DimPreferensi
**Atribut:**
- `preferensi_key` (Surrogate Key)
- `dosen_id`
- `mata_kuliah_id`
- `preferensi_hari` (JSON Array)
- `preferensi_jam` (JSON Array)
- `prioritas` (1-5)
- `catatan`
- `is_active`
- `valid_from`, `valid_to` (SCD Type 2)

---

## 4. PROSES ETL (EXTRACT, TRANSFORM, LOAD)

### 4.1. Overview Proses ETL

```
Source System (Operational Database)
    ↓
    [EXTRACT]
    ↓
Staging Area
    ↓
    [TRANSFORM]
    ↓
Data Warehouse (Fact & Dimension Tables)
    ↓
    [LOAD]
    ↓
Dashboard & Reports
```

### 4.2. Tahap EXTRACT

**Sumber Data:**
1. **Tabel Jadwal** (`jadwals`)
   - Data jadwal yang sudah di-generate
   - Relasi dengan dosen, mata kuliah, ruangan, waktu

2. **Tabel Dosen** (`users` dengan role='dosen')
   - Data dosen dan informasi profil

3. **Tabel Mata Kuliah** (`mata_kuliahs`)
   - Data mata kuliah, SKS, semester, dll

4. **Tabel Ruangan** (`ruangans`)
   - Data ruangan, kapasitas, fasilitas

5. **Tabel Preferensi** (`preferensi_dosens`)
   - Preferensi hari dan jam dosen

6. **Tabel Program Studi** (`prodis`)
   - Data program studi dan fakultas

**Metode Extract:**
```php
// Contoh Extract untuk FactJadwal
$sourceJadwals = DB::table('jadwals')
    ->join('users', 'jadwals.dosen_id', '=', 'users.id')
    ->join('mata_kuliahs', 'jadwals.mata_kuliah_id', '=', 'mata_kuliahs.id')
    ->join('ruangans', 'jadwals.ruangan_id', '=', 'ruangans.id')
    ->where('jadwals.status', 'active')
    ->select('jadwals.*', 'users.*', 'mata_kuliahs.*', 'ruangans.*')
    ->get();
```

### 4.3. Tahap TRANSFORM

#### **4.3.1. Transform untuk Dimensi (SCD Type 2)**

**DimDosen:**
```php
foreach ($sourceDosen as $dosen) {
    // Cek apakah sudah ada
    $existing = DimDosen::where('nip', $dosen->nip)
        ->where('is_active', true)
        ->first();
    
    if ($existing) {
        // Cek perubahan
        if ($existing->nama_dosen != $dosen->nama || 
            $existing->email != $dosen->email) {
            // Close old record
            $existing->update([
                'is_active' => false,
                'valid_to' => now()
            ]);
            
            // Create new record
            DimDosen::create([
                'dosen_key' => Str::uuid(),
                'nip' => $dosen->nip,
                'nama_dosen' => $dosen->nama,
                'email' => $dosen->email,
                'prodi' => $dosen->prodi,
                'valid_from' => now(),
                'is_active' => true
            ]);
        }
    } else {
        // New record
        DimDosen::create([...]);
    }
}
```

**DimWaktu:**
```php
// Generate waktu dimension untuk semua kombinasi
$hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
$jamSlots = [
    ['07:00', '08:40'],
    ['08:50', '10:30'],
    ['10:40', '12:20'],
    ['13:00', '14:40'],
    ['14:50', '16:30'],
    ['16:40', '18:20']
];

foreach ($hari as $h) {
    foreach ($jamSlots as $slot) {
        DimWaktu::create([
            'waktu_key' => Str::uuid(),
            'hari' => $h,
            'jam_mulai' => $slot[0],
            'jam_selesai' => $slot[1],
            'semester' => 'Ganjil',
            'tahun_akademik' => '2024/2025',
            'slot_waktu' => determineSlot($slot[0]),
            'durasi_menit' => calculateDuration($slot[0], $slot[1])
        ]);
    }
}
```

#### **4.3.2. Transform untuk Fact Tables**

**FactJadwal:**
```php
foreach ($sourceJadwals as $jadwal) {
    // Get dimension keys
    $dosenKey = DimDosen::where('nip', $jadwal->nip)->first()->dosen_key;
    $mkKey = DimMataKuliah::where('kode_mk', $jadwal->kode_mk)->first()->mata_kuliah_key;
    $ruanganKey = DimRuangan::where('kode_ruangan', $jadwal->kode_ruangan)->first()->ruangan_key;
    $waktuKey = DimWaktu::where('hari', $jadwal->hari)
        ->where('jam_mulai', $jadwal->jam_mulai)
        ->first()->waktu_key;
    $prodiKey = DimProdi::where('kode_prodi', $jadwal->kode_prodi)->first()->prodi_key;
    
    // Calculate measures
    $utilisasi = ($jadwal->jumlah_mahasiswa / $jadwal->kapasitas) * 100;
    $konflik = checkConflict($jadwal);
    $tingkatKonflik = calculateConflictLevel($jadwal);
    
    FactJadwal::create([
        'dosen_key' => $dosenKey,
        'mata_kuliah_key' => $mkKey,
        'ruangan_key' => $ruanganKey,
        'waktu_key' => $waktuKey,
        'prodi_key' => $prodiKey,
        'jumlah_sks' => $jadwal->sks,
        'durasi_menit' => $jadwal->durasi,
        'kapasitas_kelas' => $jadwal->kapasitas,
        'jumlah_mahasiswa' => $jadwal->jumlah_mahasiswa,
        'utilisasi_ruangan' => $utilisasi,
        'konflik_jadwal' => $konflik,
        'tingkat_konflik' => $tingkatKonflik,
        'status_aktif' => true,
        'created_at_jadwal' => now()
    ]);
}
```

**FactUtilisasiRuangan:**
```php
// Aggregate dari FactJadwal
$utilisasiData = FactJadwal::select(
    'ruangan_key',
    'waktu_key',
    DB::raw('SUM(durasi_menit) as total_menit'),
    DB::raw('COUNT(*) as jumlah_kelas'),
    DB::raw('SUM(jumlah_mahasiswa) as total_mahasiswa'),
    DB::raw('AVG(utilisasi_ruangan) as avg_utilisasi')
)
->where('status_aktif', true)
->groupBy('ruangan_key', 'waktu_key')
->get();

foreach ($utilisasiData as $data) {
    $totalJamTersedia = 8 * 60; // 8 jam * 60 menit
    $persentase = ($data->total_menit / $totalJamTersedia) * 100;
    
    FactUtilisasiRuangan::create([
        'ruangan_key' => $data->ruangan_key,
        'waktu_key' => $data->waktu_key,
        'total_jam_penggunaan' => $data->total_menit,
        'total_jam_tersedia' => $totalJamTersedia,
        'persentase_utilisasi' => $persentase,
        'jumlah_kelas' => $data->jumlah_kelas,
        'jumlah_mahasiswa_total' => $data->total_mahasiswa,
        'rata_rata_kapasitas' => $data->avg_utilisasi
    ]);
}
```

**FactBebanKerjaDosen:**
```php
$bebanKerja = FactJadwal::select(
    'dosen_key',
    'prodi_key',
    DB::raw('SUM(jumlah_sks) as total_sks'),
    DB::raw('SUM(durasi_menit) as total_menit'),
    DB::raw('COUNT(DISTINCT mata_kuliah_key) as jumlah_mk'),
    DB::raw('COUNT(*) as jumlah_kelas'),
    DB::raw('SUM(jumlah_mahasiswa) as total_mahasiswa'),
    DB::raw('AVG(prioritas_preferensi) as avg_kepuasan')
)
->where('status_aktif', true)
->groupBy('dosen_key', 'prodi_key')
->get();

foreach ($bebanKerja as $data) {
    $standarBeban = 12; // Standar SKS per semester
    $bebanPercentage = ($data->total_sks / $standarBeban) * 100;
    
    FactBebanKerjaDosen::create([
        'dosen_key' => $data->dosen_key,
        'prodi_key' => $data->prodi_key,
        'total_sks' => $data->total_sks,
        'total_jam_mengajar' => $data->total_menit,
        'jumlah_mata_kuliah' => $data->jumlah_mk,
        'jumlah_kelas' => $data->jumlah_kelas,
        'beban_kerja_percentage' => $bebanPercentage,
        'tingkat_kepuasan_preferensi' => $data->avg_kepuasan
    ]);
}
```

**FactKonflikJadwal:**
```php
// Deteksi konflik dari FactJadwal
$konflikJadwals = FactJadwal::where('konflik_jadwal', true)
    ->where('status_aktif', true)
    ->get();

foreach ($konflikJadwals as $jadwal) {
    $jenisKonflik = determineConflictType($jadwal);
    $keparahan = $jadwal->tingkat_konflik;
    
    FactKonflikJadwal::create([
        'dosen_key' => $jadwal->dosen_key,
        'ruangan_key' => $jadwal->ruangan_key,
        'waktu_key' => $jadwal->waktu_key,
        'prodi_key' => $jadwal->prodi_key,
        'jenis_konflik' => $jenisKonflik,
        'tingkat_keparahan' => $keparahan,
        'jumlah_terlibat' => countConflictingSchedules($jadwal),
        'status_resolusi' => 'unresolved'
    ]);
}
```

**FactKepuasanPreferensi:**
```php
$preferensiData = FactJadwal::join('dim_preferensi', 'fact_jadwal.preferensi_key', '=', 'dim_preferensi.preferensi_key')
    ->select(
        'fact_jadwal.dosen_key',
        'fact_jadwal.preferensi_key',
        'fact_jadwal.waktu_key',
        'dim_preferensi.preferensi_hari',
        'dim_preferensi.preferensi_jam',
        'fact_jadwal.prioritas_preferensi'
    )
    ->where('fact_jadwal.status_aktif', true)
    ->get();

foreach ($preferensiData as $data) {
    $hariTerpenuhi = checkHariPreferensi($data);
    $jamTerpenuhi = checkJamPreferensi($data);
    $skor = calculateSatisfactionScore($hariTerpenuhi, $jamTerpenuhi, $data->prioritas_preferensi);
    
    FactKepuasanPreferensi::create([
        'dosen_key' => $data->dosen_key,
        'preferensi_key' => $data->preferensi_key,
        'waktu_key' => $data->waktu_key,
        'preferensi_hari_terpenuhi' => $hariTerpenuhi,
        'preferensi_jam_terpenuhi' => $jamTerpenuhi,
        'skor_kepuasan' => $skor,
        'prioritas_preferensi' => $data->prioritas_preferensi
    ]);
}
```

### 4.4. Tahap LOAD

**Strategi Load:**
1. **Full Load**: Untuk data awal (initial load)
2. **Incremental Load**: Untuk update harian/mingguan
3. **Delta Load**: Hanya load data yang berubah

**Schedule ETL:**
- **Daily**: Update FactJadwal, FactKonflikJadwal
- **Weekly**: Update FactUtilisasiRuangan, FactBebanKerjaDosen
- **Monthly**: Update FactKepuasanPreferensi, refresh dimensi

**Load Process:**
```php
// Full Load Process
public function runETL() {
    // 1. Extract
    $sourceData = $this->extract();
    
    // 2. Transform Dimensions
    $this->transformDimensions($sourceData);
    
    // 3. Transform Facts
    $this->transformFacts($sourceData);
    
    // 4. Load to Data Warehouse
    $this->loadToWarehouse();
    
    // 5. Update Dashboard
    $this->refreshDashboard();
}
```

---

## 5. VISUALISASI DASHBOARD

### 5.1. Dashboard Overview

#### **Panel 1: Key Performance Indicators (KPI)**
- **Total Jadwal Aktif**: Count dari FactJadwal
- **Utilisasi Ruangan Rata-rata**: AVG dari FactUtilisasiRuangan
- **Total Konflik**: Count dari FactKonflikJadwal (status='unresolved')
- **Tingkat Kepuasan Preferensi**: AVG dari FactKepuasanPreferensi

**Visualisasi:**
- Card dengan angka besar dan trend indicator (↑/↓)

#### **Panel 2: Utilisasi Ruangan**
**Chart 1: Bar Chart - Utilisasi Ruangan per Tipe**
```sql
SELECT 
    r.tipe_ruangan,
    AVG(f.persentase_utilisasi) as avg_utilisasi
FROM fact_utilisasi_ruangan f
JOIN dim_ruangan r ON f.ruangan_key = r.ruangan_key
GROUP BY r.tipe_ruangan
```

**Chart 2: Heatmap - Utilisasi Ruangan per Hari dan Jam**
- X-axis: Hari (Senin-Jumat)
- Y-axis: Slot Waktu (Pagi, Siang, Sore)
- Color: Persentase utilisasi (0-100%)

**Chart 3: Top 10 Ruangan Paling Sering Digunakan**
- Horizontal Bar Chart
- Sorted by total_jam_penggunaan

**Chart 4: Line Chart - Trend Utilisasi per Bulan**
- Time series analysis

#### **Panel 3: Beban Kerja Dosen**
**Chart 1: Distribution Beban Kerja Dosen**
- Histogram showing distribution of total_sks
- Bins: 0-6, 6-12, 12-18, 18-24, 24+

**Chart 2: Top 10 Dosen dengan Beban Tertinggi**
- Bar Chart dengan total_sks dan jumlah_mata_kuliah

**Chart 3: Beban Kerja per Program Studi**
- Stacked Bar Chart
- Grouped by prodi, showing distribution

**Chart 4: Scatter Plot - Beban Kerja vs Kepuasan Preferensi**
- X-axis: total_sks
- Y-axis: tingkat_kepuasan_preferensi
- Size: jumlah_mahasiswa_total

#### **Panel 4: Analisis Konflik Jadwal**
**Chart 1: Pie Chart - Distribusi Jenis Konflik**
```sql
SELECT 
    jenis_konflik,
    COUNT(*) as jumlah
FROM fact_konflik_jadwal
WHERE status_resolusi = 'unresolved'
GROUP BY jenis_konflik
```

**Chart 2: Bar Chart - Konflik per Hari**
- Showing which day has most conflicts

**Chart 3: Timeline - Konflik per Waktu**
- Line chart showing conflict trends

**Chart 4: Table - Detail Konflik**
- Sortable table dengan filter
- Columns: Dosen, Ruangan, Waktu, Jenis, Tingkat Keparahan, Status

#### **Panel 5: Kepuasan Preferensi**
**Chart 1: Gauge Chart - Overall Satisfaction Score**
- 0-100 scale dengan color coding

**Chart 2: Bar Chart - Kepuasan per Dosen**
- Top 10 dan Bottom 10 dosen

**Chart 3: Stacked Bar - Preferensi Terpenuhi vs Tidak**
- Showing preferensi_hari_terpenuhi dan preferensi_jam_terpenuhi

**Chart 4: Trend Line - Kepuasan dari Waktu ke Waktu**
- Time series showing improvement

#### **Panel 6: Statistik Program Studi**
**Chart 1: Donut Chart - Distribusi Jadwal per Prodi**
```sql
SELECT 
    p.nama_prodi,
    COUNT(f.id) as jumlah_jadwal
FROM fact_jadwal f
JOIN dim_prodi p ON f.prodi_key = p.prodi_key
GROUP BY p.nama_prodi
```

**Chart 2: Comparison Chart - Prodi Metrics**
- Side-by-side comparison
- Metrics: Total Jadwal, Total Dosen, Total Mahasiswa, Avg Utilisasi

**Chart 3: Tree Map - Distribusi Mata Kuliah per Prodi**
- Visual representation of course distribution

### 5.2. Interactive Features

1. **Filters:**
   - Semester/Tahun Akademik
   - Program Studi
   - Rentang Tanggal
   - Dosen (dropdown)
   - Ruangan (dropdown)

2. **Drill-Down:**
   - Click chart → Detail view
   - Click prodi → Jadwal prodi
   - Click dosen → Jadwal dosen

3. **Export:**
   - PDF Report
   - Excel Export
   - CSV Data

4. **Real-time Updates:**
   - Auto-refresh setiap 5 menit
   - Manual refresh button

---

## 6. CONTOH DATA DUMMY

### 6.1. Dummy Data FactJadwal

```json
[
  {
    "id": 1,
    "dosen_key": "DOSEN-001",
    "mata_kuliah_key": "MK-101",
    "ruangan_key": "R-201",
    "waktu_key": "W-SEN-07-08",
    "prodi_key": "PRODI-IF",
    "preferensi_key": "PREF-001",
    "jumlah_sks": 3,
    "durasi_menit": 100,
    "kapasitas_kelas": 40,
    "jumlah_mahasiswa": 35,
    "utilisasi_ruangan": 87.50,
    "prioritas_preferensi": 4,
    "konflik_jadwal": false,
    "tingkat_konflik": 0,
    "status_aktif": true,
    "created_at_jadwal": "2024-10-01 08:00:00"
  },
  {
    "id": 2,
    "dosen_key": "DOSEN-002",
    "mata_kuliah_key": "MK-102",
    "ruangan_key": "R-202",
    "waktu_key": "W-SEN-08-10",
    "prodi_key": "PRODI-IF",
    "preferensi_key": "PREF-002",
    "jumlah_sks": 3,
    "durasi_menit": 100,
    "kapasitas_kelas": 40,
    "jumlah_mahasiswa": 40,
    "utilisasi_ruangan": 100.00,
    "prioritas_preferensi": 5,
    "konflik_jadwal": false,
    "tingkat_konflik": 0,
    "status_aktif": true,
    "created_at_jadwal": "2024-10-01 08:00:00"
  },
  {
    "id": 3,
    "dosen_key": "DOSEN-001",
    "mata_kuliah_key": "MK-103",
    "ruangan_key": "R-201",
    "waktu_key": "W-SEN-07-08",
    "prodi_key": "PRODI-IF",
    "preferensi_key": null,
    "jumlah_sks": 2,
    "durasi_menit": 100,
    "kapasitas_kelas": 30,
    "jumlah_mahasiswa": 25,
    "utilisasi_ruangan": 83.33,
    "prioritas_preferensi": null,
    "konflik_jadwal": true,
    "tingkat_konflik": 3,
    "status_aktif": true,
    "created_at_jadwal": "2024-10-01 08:00:00"
  }
]
```

### 6.2. Dummy Data FactUtilisasiRuangan

```json
[
  {
    "id": 1,
    "ruangan_key": "R-201",
    "waktu_key": "W-SEN-07-08",
    "prodi_key": "PRODI-IF",
    "total_jam_penggunaan": 480,
    "total_jam_tersedia": 480,
    "persentase_utilisasi": 100.00,
    "jumlah_kelas": 6,
    "jumlah_mahasiswa_total": 240,
    "rata_rata_kapasitas": 85.50,
    "peak_hour_utilisasi": 100.00,
    "periode_semester": "Ganjil",
    "tahun_akademik": "2024/2025"
  },
  {
    "id": 2,
    "ruangan_key": "R-202",
    "waktu_key": "W-SEN-08-10",
    "prodi_key": "PRODI-IF",
    "total_jam_penggunaan": 400,
    "total_jam_tersedia": 480,
    "persentase_utilisasi": 83.33,
    "jumlah_kelas": 5,
    "jumlah_mahasiswa_total": 200,
    "rata_rata_kapasitas": 90.00,
    "peak_hour_utilisasi": 95.00,
    "periode_semester": "Ganjil",
    "tahun_akademik": "2024/2025"
  },
  {
    "id": 3,
    "ruangan_key": "R-203",
    "waktu_key": "W-SEL-07-08",
    "prodi_key": "PRODI-SI",
    "total_jam_penggunaan": 240,
    "total_jam_tersedia": 480,
    "persentase_utilisasi": 50.00,
    "jumlah_kelas": 3,
    "jumlah_mahasiswa_total": 90,
    "rata_rata_kapasitas": 75.00,
    "peak_hour_utilisasi": 60.00,
    "periode_semester": "Ganjil",
    "tahun_akademik": "2024/2025"
  }
]
```

### 6.3. Dummy Data FactBebanKerjaDosen

```json
[
  {
    "id": 1,
    "dosen_key": "DOSEN-001",
    "prodi_key": "PRODI-IF",
    "waktu_key": "W-SEMESTER-GANJIL-2024",
    "total_sks": 12,
    "total_jam_mengajar": 1200,
    "jumlah_mata_kuliah": 4,
    "jumlah_kelas": 6,
    "jumlah_mahasiswa_total": 180,
    "rata_rata_sks_per_mk": 3.0,
    "beban_kerja_percentage": 100.00,
    "tingkat_kepuasan_preferensi": 4.2,
    "semester": "Ganjil",
    "tahun_akademik": "2024/2025"
  },
  {
    "id": 2,
    "dosen_key": "DOSEN-002",
    "prodi_key": "PRODI-IF",
    "waktu_key": "W-SEMESTER-GANJIL-2024",
    "total_sks": 15,
    "total_jam_mengajar": 1500,
    "jumlah_mata_kuliah": 5,
    "jumlah_kelas": 8,
    "jumlah_mahasiswa_total": 240,
    "rata_rata_sks_per_mk": 3.0,
    "beban_kerja_percentage": 125.00,
    "tingkat_kepuasan_preferensi": 3.8,
    "semester": "Ganjil",
    "tahun_akademik": "2024/2025"
  },
  {
    "id": 3,
    "dosen_key": "DOSEN-003",
    "prodi_key": "PRODI-SI",
    "waktu_key": "W-SEMESTER-GANJIL-2024",
    "total_sks": 9,
    "total_jam_mengajar": 900,
    "jumlah_mata_kuliah": 3,
    "jumlah_kelas": 4,
    "jumlah_mahasiswa_total": 120,
    "rata_rata_sks_per_mk": 3.0,
    "beban_kerja_percentage": 75.00,
    "tingkat_kepuasan_preferensi": 4.5,
    "semester": "Ganjil",
    "tahun_akademik": "2024/2025"
  }
]
```

### 6.4. Dummy Data FactKonflikJadwal

```json
[
  {
    "id": 1,
    "dosen_key": "DOSEN-001",
    "ruangan_key": "R-201",
    "waktu_key": "W-SEN-07-08",
    "prodi_key": "PRODI-IF",
    "jenis_konflik": "dosen",
    "tingkat_keparahan": 3,
    "jumlah_terlibat": 2,
    "durasi_konflik_menit": 100,
    "status_resolusi": "unresolved",
    "semester": "Ganjil",
    "tahun_akademik": "2024/2025",
    "created_at": "2024-10-01 08:00:00",
    "resolved_at": null
  },
  {
    "id": 2,
    "dosen_key": "DOSEN-004",
    "ruangan_key": "R-205",
    "waktu_key": "W-RAB-10-12",
    "prodi_key": "PRODI-IF",
    "jenis_konflik": "ruangan",
    "tingkat_keparahan": 2,
    "jumlah_terlibat": 2,
    "durasi_konflik_menit": 100,
    "status_resolusi": "resolved",
    "semester": "Ganjil",
    "tahun_akademik": "2024/2025",
    "created_at": "2024-10-01 08:00:00",
    "resolved_at": "2024-10-02 10:00:00"
  }
]
```

### 6.5. Dummy Data FactKepuasanPreferensi

```json
[
  {
    "id": 1,
    "dosen_key": "DOSEN-001",
    "preferensi_key": "PREF-001",
    "waktu_key": "W-SEN-07-08",
    "preferensi_hari_terpenuhi": true,
    "preferensi_jam_terpenuhi": true,
    "skor_kepuasan": 95,
    "prioritas_preferensi": 5,
    "jumlah_preferensi_total": 5,
    "jumlah_preferensi_terpenuhi": 5,
    "persentase_kepuasan": 100.00,
    "semester": "Ganjil",
    "tahun_akademik": "2024/2025"
  },
  {
    "id": 2,
    "dosen_key": "DOSEN-002",
    "preferensi_key": "PREF-002",
    "waktu_key": "W-SEL-08-10",
    "preferensi_hari_terpenuhi": true,
    "preferensi_jam_terpenuhi": false,
    "skor_kepuasan": 70,
    "prioritas_preferensi": 3,
    "jumlah_preferensi_total": 4,
    "jumlah_preferensi_terpenuhi": 3,
    "persentase_kepuasan": 75.00,
    "semester": "Ganjil",
    "tahun_akademik": "2024/2025"
  },
  {
    "id": 3,
    "dosen_key": "DOSEN-003",
    "preferensi_key": "PREF-003",
    "waktu_key": "W-RAB-13-15",
    "preferensi_hari_terpenuhi": false,
    "preferensi_jam_terpenuhi": false,
    "skor_kepuasan": 40,
    "prioritas_preferensi": 2,
    "jumlah_preferensi_total": 3,
    "jumlah_preferensi_terpenuhi": 1,
    "persentase_kepuasan": 33.33,
    "semester": "Ganjil",
    "tahun_akademik": "2024/2025"
  }
]
```

### 6.6. Dummy Data Dimensi

**DimDosen:**
```json
[
  {
    "id": 1,
    "dosen_key": "DOSEN-001",
    "nip": "198001012001011001",
    "nama_dosen": "Dr. Ahmad Fauzi, S.Kom., M.T.",
    "email": "ahmad.fauzi@university.ac.id",
    "prodi": "Informatika",
    "role": "dosen",
    "is_active": true,
    "valid_from": "2024-01-01 00:00:00",
    "valid_to": null
  },
  {
    "id": 2,
    "dosen_key": "DOSEN-002",
    "nip": "198002022001011002",
    "nama_dosen": "Prof. Dr. Siti Nurhaliza, S.Kom., M.Sc.",
    "email": "siti.nurhaliza@university.ac.id",
    "prodi": "Informatika",
    "role": "dosen",
    "is_active": true,
    "valid_from": "2024-01-01 00:00:00",
    "valid_to": null
  }
]
```

**DimMataKuliah:**
```json
[
  {
    "id": 1,
    "mata_kuliah_key": "MK-101",
    "kode_mk": "IF101",
    "nama_mk": "Pemrograman Dasar",
    "sks": 3,
    "semester": 1,
    "prodi": "Informatika",
    "kapasitas": 40,
    "tipe_kelas": "Teori",
    "ada_praktikum": true,
    "sks_praktikum": 1,
    "sks_materi": 2,
    "is_active": true
  },
  {
    "id": 2,
    "mata_kuliah_key": "MK-102",
    "kode_mk": "IF102",
    "nama_mk": "Struktur Data",
    "sks": 3,
    "semester": 2,
    "prodi": "Informatika",
    "kapasitas": 40,
    "tipe_kelas": "Teori",
    "ada_praktikum": true,
    "sks_praktikum": 1,
    "sks_materi": 2,
    "is_active": true
  }
]
```

**DimRuangan:**
```json
[
  {
    "id": 1,
    "ruangan_key": "R-201",
    "kode_ruangan": "LAB-201",
    "nama_ruangan": "Laboratorium Komputer 201",
    "kapasitas": 40,
    "tipe_ruangan": "Laboratorium",
    "fasilitas": "Komputer, Proyektor, AC",
    "prodi": "Informatika",
    "status": "aktif",
    "is_active": true
  },
  {
    "id": 2,
    "ruangan_key": "R-202",
    "kode_ruangan": "KLS-202",
    "nama_ruangan": "Kelas 202",
    "kapasitas": 40,
    "tipe_ruangan": "Kelas",
    "fasilitas": "Proyektor, AC, Whiteboard",
    "prodi": "Informatika",
    "status": "aktif",
    "is_active": true
  }
]
```

**DimWaktu:**
```json
[
  {
    "id": 1,
    "waktu_key": "W-SEN-07-08",
    "hari": "Senin",
    "jam_mulai": "07:00",
    "jam_selesai": "08:40",
    "semester": "Ganjil",
    "tahun_akademik": "2024/2025",
    "periode": "Ganjil",
    "hari_ke": 1,
    "slot_waktu": "Pagi",
    "durasi_menit": 100,
    "is_active": true
  },
  {
    "id": 2,
    "waktu_key": "W-SEN-08-10",
    "hari": "Senin",
    "jam_mulai": "08:50",
    "jam_selesai": "10:30",
    "semester": "Ganjil",
    "tahun_akademik": "2024/2025",
    "periode": "Ganjil",
    "hari_ke": 1,
    "slot_waktu": "Pagi",
    "durasi_menit": 100,
    "is_active": true
  }
]
```

**DimProdi:**
```json
[
  {
    "id": 1,
    "prodi_key": "PRODI-IF",
    "kode_prodi": "IF",
    "nama_prodi": "Teknik Informatika",
    "fakultas": "Fakultas Teknologi Informasi",
    "akreditasi": "A",
    "is_active": true
  },
  {
    "id": 2,
    "prodi_key": "PRODI-SI",
    "kode_prodi": "SI",
    "nama_prodi": "Sistem Informasi",
    "fakultas": "Fakultas Teknologi Informasi",
    "akreditasi": "A",
    "is_active": true
  }
]
```

**DimPreferensi:**
```json
[
  {
    "id": 1,
    "preferensi_key": "PREF-001",
    "dosen_id": 1,
    "mata_kuliah_id": 1,
    "preferensi_hari": ["Senin", "Selasa", "Rabu"],
    "preferensi_jam": ["07:00-08:40", "08:50-10:30"],
    "prioritas": 5,
    "catatan": "Lebih suka pagi hari",
    "is_active": true
  },
  {
    "id": 2,
    "preferensi_key": "PREF-002",
    "dosen_id": 2,
    "mata_kuliah_id": 2,
    "preferensi_hari": ["Kamis", "Jumat"],
    "preferensi_jam": ["13:00-14:40", "14:50-16:30"],
    "prioritas": 4,
    "catatan": "Lebih suka siang hari",
    "is_active": true
  }
]
```

---

## 7. IMPLEMENTASI DASHBOARD

### 7.1. Teknologi yang Digunakan

- **Backend**: Laravel (PHP)
- **Frontend**: 
  - Chart.js / Chartist.js untuk visualisasi
  - Bootstrap untuk UI
  - jQuery untuk interaktivitas
- **Database**: MySQL/SQLite
- **ETL**: Laravel Commands (Scheduled Jobs)

### 7.2. Struktur File Dashboard

```
resources/views/dashboard/
├── index.blade.php (Main Dashboard)
├── utilisasi-ruangan.blade.php
├── beban-kerja-dosen.blade.php
├── konflik-jadwal.blade.php
├── kepuasan-preferensi.blade.php
└── statistik-prodi.blade.php

app/Http/Controllers/
└── DashboardController.php

app/Console/Commands/
├── ETLFactJadwal.php
├── ETLFactUtilisasiRuangan.php
├── ETLFactBebanKerjaDosen.php
├── ETLFactKonflikJadwal.php
└── ETLFactKepuasanPreferensi.php
```

### 7.3. API Endpoints untuk Dashboard

```php
// routes/api.php atau routes/web.php
Route::prefix('api/dashboard')->group(function () {
    Route::get('/kpi', [DashboardController::class, 'getKPI']);
    Route::get('/utilisasi-ruangan', [DashboardController::class, 'getUtilisasiRuangan']);
    Route::get('/beban-kerja-dosen', [DashboardController::class, 'getBebanKerjaDosen']);
    Route::get('/konflik-jadwal', [DashboardController::class, 'getKonflikJadwal']);
    Route::get('/kepuasan-preferensi', [DashboardController::class, 'getKepuasanPreferensi']);
    Route::get('/statistik-prodi', [DashboardController::class, 'getStatistikProdi']);
});
```

---

## 8. KESIMPULAN

Dashboard ini dirancang dengan arsitektur data warehouse berbasis **Snowflake Schema** yang terdiri dari:

1. **5 Tabel Fakta** untuk analisis multidimensi:
   - FactJadwal (fakta utama)
   - FactUtilisasiRuangan
   - FactBebanKerjaDosen
   - FactKonflikJadwal
   - FactKepuasanPreferensi

2. **6 Tabel Dimensi** dengan SCD Type 2 untuk historical tracking

3. **Proses ETL** yang terstruktur untuk ekstraksi, transformasi, dan loading data

4. **Visualisasi komprehensif** untuk berbagai kebutuhan analisis

5. **Data dummy** yang representatif untuk pengujian dan demonstrasi

Dashboard ini memberikan insight yang mendalam untuk pengambilan keputusan dalam sistem generate jadwal kuliah.

---

**Dokumen ini dibuat untuk keperluan dokumentasi perancangan dashboard data warehouse sistem generate jadwal.**





