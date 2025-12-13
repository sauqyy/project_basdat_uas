# 🚀 Cara Populate Data Warehouse - CEPAT

## ⚡ CARA TERCEPAT (RECOMMENDED)

Jalankan script PHP langsung:
adaadada
```bash
php populate_dw.php
```
awjdbawduawudoh
Script ini akan:
- ✅ Clear semua data warehouse yang lama
- ✅ Populate semua dimension tables
- ✅ Populate semua fact tables
- ✅ Menampilkan summary di akhir

---

## 📊 Hasil yang Diharapkan

Setelah menjalankan script, Anda akan melihat:

```
✅ Data Warehouse populated successfully!

📊 Summary:
  DimDosen: 25
  DimMataKuliah: 26
  DimRuangan: 27
  DimWaktu: 12
  DimProdi: 5
  DimPreferensi: 0
  FactJadwal: 31
  FactUtilisasiRuangan: 31
  FactKecocokanJadwal: 0
```

---

## ✅ Setelah Populate

1. **Refresh dashboard** di browser
2. Data akan muncul di:
   - Dashboard Super Admin
   - Dashboard Admin Prodi
   - Dashboard Dosen

---

## 🔄 Jika Ada Perubahan Jadwal

Jalankan lagi script:

```bash
php populate_dw.php
```

---

## ⚠️ Troubleshooting

### Dashboard masih 0%

1. Pastikan script berhasil dijalankan (lihat output)
2. Refresh dashboard (Ctrl+F5 untuk hard refresh)
3. Cek apakah ada jadwal aktif: `Jadwal::where('status', true)->count()` harus > 0

### Error saat populate

1. Pastikan database connection sudah benar
2. Pastikan semua migration sudah dijalankan: `php artisan migrate`
3. Pastikan ada data operasional (jadwal, mata kuliah, dosen, ruangan)

---

## 📝 Catatan

- Script ini **TIDAK memanipulasi nilai** - semua data diambil langsung dari fact tables
- Fact tables perlu di-populate ulang setiap kali ada perubahan jadwal
- Dimension tables akan otomatis ter-populate sebelum fact tables

---

**Setelah populate, refresh dashboard dan data akan muncul!** 🎉

