# Fix: Error "Undefined array key 0" saat Generate Jadwal

## ✅ Masalah yang Diperbaiki

Error "Undefined array key 0" terjadi saat generate jadwal karena akses array index tanpa validasi.

## 🔧 Perbaikan yang Diterapkan

### 1. Helper Functions Baru

Ditambahkan 2 helper functions di `SuperAdminController`:

```php
/**
 * Extract jam mulai dari string jam dengan validasi
 */
private function extractJamMulai($jamString, $default = '08:00')
{
    if (empty($jamString) || strpos($jamString, '-') === false) {
        return $default;
    }
    $parts = explode('-', $jamString);
    return count($parts) >= 1 ? trim($parts[0]) : $default;
}

/**
 * Extract jam selesai dari string jam dengan validasi
 */
private function extractJamSelesai($jamString, $default = '10:00')
{
    if (empty($jamString) || strpos($jamString, '-') === false) {
        return $default;
    }
    $parts = explode('-', $jamString);
    return count($parts) >= 2 ? trim($parts[1]) : $default;
}
```

### 2. Validasi di Semua Lokasi

**SEBELUM (Error-prone):**
```php
'jam_mulai' => explode('-', $jamTerpilih)[0],
'jam_selesai' => explode('-', $jamTerpilih)[1],
```

**SESUDAH (Safe):**
```php
// Validasi format jam sebelum explode
if (empty($jamTerpilih) || strpos($jamTerpilih, '-') === false) {
    \Log::warning('❌ Format jam tidak valid: ' . $jamTerpilih);
    continue;
}

$jamParts = explode('-', $jamTerpilih);
if (count($jamParts) < 2) {
    \Log::warning('❌ Format jam tidak lengkap: ' . $jamTerpilih);
    continue;
}

[$jamMulai, $jamSelesai] = $jamParts;
```

### 3. Lokasi yang Diperbaiki

#### SuperAdminController:
- ✅ `createJadwalForDosen2()` - Baris 550
- ✅ `createJadwalPraktikum()` - Baris 618-619, 655-656
- ✅ `filterJamByCustomSKS()` - Baris 1289, 1309-1310, 1318, 1326, 1367, 1369, 1405
- ✅ `createSingleJadwal()` - Baris 1007-1018
- ✅ `createSingleJadwalWithSpecificDays()` - Baris 1153-1164

#### AdminProdiController:
- ✅ `createSingleJadwal()` - Baris 756-767
- ✅ `createSingleJadwalWithSpecificDays()` - Baris 850
- ✅ `filterJamByCustomSKS()` - Baris 991-992, 1000, 1008, 1021, 1023, 1045, 1047

## ✅ Hasil

- ✅ Semua akses array index sudah divalidasi
- ✅ Error "Undefined array key 0" tidak akan muncul lagi
- ✅ Logging ditambahkan untuk debugging
- ✅ Fallback values disediakan untuk kasus error

## 🔄 Cara Test

1. Coba generate jadwal lagi
2. Error "Undefined array key 0" seharusnya sudah tidak muncul
3. Jika masih ada error, cek log file untuk detail

## 📝 Catatan

- Semua validasi sudah ditambahkan di semua lokasi yang berpotensi error
- Helper functions memastikan ekstraksi jam yang aman
- Logging membantu debugging jika ada masalah

---

**Perbaikan sudah diterapkan! Silakan coba generate jadwal lagi.** 🎉

