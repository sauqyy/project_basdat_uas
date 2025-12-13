# 🎯 SKENARIO DEMO SISTEM PENJADWALAN AI
## Kampus Merdeka - Sistem Penjadwalan Otomatis

---

## 📋 **OVERVIEW DEMO**
Sistem penjadwalan AI yang menggunakan algoritma cerdas untuk mengatur jadwal kuliah berdasarkan preferensi dosen, ketersediaan ruangan, dan konflik waktu.

---

## 🎭 **PEMAIN DALAM DEMO**

### 1. **Super Admin** 
- **Nama:** Dr. Ahmad (Super Admin)
- **Role:** Mengelola seluruh sistem, generate jadwal untuk semua prodi
- **Login:** `/admin/login`

### 2. **Admin Prodi**
- **Nama:** Dr. Sarah (Admin Prodi Teknologi Sains Data)
- **Role:** Mengelola jadwal untuk prodi Teknologi Sains Data
- **Login:** `/admin/login`

### 3. **Dosen**
- **Nama:** Raihan, Indah Fahmiyah, Maryamah
- **Role:** Mengatur preferensi jadwal, melihat jadwal yang sudah dibuat
- **Login:** `/login`

---

## 🎬 **SKENARIO DEMO (30 MENIT)**

### **BAGIAN 1: SETUP AWAL (5 menit)**

#### **1.1 Login sebagai Super Admin**
```
URL: http://localhost:8000/admin/login
Username: superadmin@kampusmerdeka.ac.id
Password: password123
```

**Aksi:**
- Login berhasil
- Redirect ke dashboard Super Admin
- Tampilkan menu: Dashboard, Kelas, Jadwal

#### **1.2 Cek Data Master**
**Aksi:**
- Buka menu "Kelas" → Lihat daftar ruangan
- Buka menu "Jadwal" → Lihat jadwal yang sudah ada
- **Poin penting:** Tunjukkan ada konflik jadwal (Multivariat SD-A2 di ruangan 8.01)

---

### **BAGIAN 2: PREFERENSI DOSEN (8 menit)**

#### **2.1 Login sebagai Dosen Raihan**
```
URL: http://localhost:8000/login
Username: raihan@kampusmerdeka.ac.id
Password: password123
```

**Aksi:**
- Login berhasil
- Buka menu "Preferensi"
- **Set Preferensi Raihan:**
  - Hari: Senin, Kamis
  - Jam: 08:00-09:00, 09:00-10:00, 10:00-11:00, 11:00-12:00, 13:00-14:00, 14:00-15:00
  - Prioritas: 1 (Tinggi)

#### **2.2 Login sebagai Dosen Indah**
```
Username: indah@kampusmerdeka.ac.id
Password: password123
```

**Aksi:**
- Buka menu "Preferensi"
- **Set Preferensi Indah:**
  - Hari: Selasa, Rabu, Kamis
  - Jam: 08:00-09:00, 09:00-10:00, 10:00-11:00, 11:00-12:00, 13:00-14:00, 14:00-15:00
  - Prioritas: 2 (Sedang)

#### **2.3 Login sebagai Dosen Maryamah**
```
Username: maryamah@kampusmerdeka.ac.id
Password: password123
```

**Aksi:**
- Buka menu "Preferensi"
- **Set Preferensi Maryamah:**
  - Hari: Senin, Rabu, Jumat
  - Jam: 08:00-09:00, 09:00-10:00, 10:00-11:00, 11:00-12:00, 13:00-14:00, 14:00-15:00
  - Prioritas: 3 (Rendah)

---

### **BAGIAN 3: GENERATE JADWAL AI (12 menit)**

#### **3.1 Login sebagai Super Admin**
```
URL: http://localhost:8000/admin/login
Username: superadmin@kampusmerdeka.ac.id
Password: password123
```

#### **3.2 Generate Jadwal AI**
**Aksi:**
- Buka menu "Jadwal"
- Klik tombol "Generate Jadwal AI"
- **Tampilkan proses:**
  - Loading indicator
  - Log proses di console (opsional)
  - Progress bar

#### **3.3 Hasil Generate Jadwal**
**Yang akan ditampilkan:**

**✅ JADWAL BERHASIL DIBUAT:**
- **Raihan:** TEST (Materi) - Kamis 10:00-12:00 - Ruangan 8.01
- **Raihan:** TEST (Praktikum) - Kamis 13:00-15:00 - Ruangan 8.01
- **Indah:** Multivariat SD-A2 (Materi) - Selasa 08:00-10:00 - Ruangan 6.04
- **Maryamah:** Algoritma SD-A1 (Materi) - Senin 09:00-11:00 - Ruangan 7.02

**⚠️ WARNING (jika ada):**
- Mata kuliah yang tidak bisa dibuat jadwal
- Alasan: Konflik ruangan, tidak sesuai preferensi, dll

#### **3.4 Verifikasi Hasil**
**Aksi:**
- Tampilkan tabel jadwal yang sudah dibuat
- **Highlight fitur cerdas:**
  - Jadwal sesuai preferensi dosen
  - Tidak ada konflik ruangan
  - Kombinasi slot jam yang optimal
  - Prioritas dosen dipertimbangkan

---

### **BAGIAN 4: VERIFIKASI DOSEN (5 menit)**

#### **4.1 Login sebagai Dosen Raihan**
```
URL: http://localhost:8000/login
Username: raihan@kampusmerdeka.ac.id
Password: password123
```

**Aksi:**
- Buka menu "Jadwal"
- **Tampilkan jadwal Raihan:**
  - TEST (Materi) - Kamis 10:00-12:00
  - TEST (Praktikum) - Kamis 13:00-15:00
- **Highlight:** Jadwal sesuai preferensi (Kamis, jam yang diinginkan)

#### **4.2 Login sebagai Dosen Indah**
```
Username: indah@kampusmerdeka.ac.id
Password: password123
```

**Aksi:**
- Buka menu "Jadwal"
- **Tampilkan jadwal Indah:**
  - Multivariat SD-A2 (Materi) - Selasa 08:00-10:00
- **Highlight:** Jadwal sesuai preferensi (Selasa, jam pagi)

---

## 🎯 **POIN DEMO YANG DITONJOLKAN**

### **1. Kecerdasan AI**
- ✅ **Preferensi Dosen:** Sistem menghormati preferensi hari dan jam dosen
- ✅ **Konflik Detection:** Otomatis menghindari konflik ruangan dan dosen
- ✅ **Slot Optimization:** Menggabungkan slot jam untuk mata kuliah 2+ SKS
- ✅ **Priority System:** Dosen dengan prioritas tinggi diprioritaskan

### **2. User Experience**
- ✅ **Role-based Access:** Login berbeda untuk admin dan dosen
- ✅ **Real-time Feedback:** Loading dan progress indicator
- ✅ **Warning System:** Pemberitahuan jika ada jadwal yang gagal dibuat
- ✅ **Responsive Design:** Interface yang user-friendly

### **3. Business Logic**
- ✅ **Prodi Management:** Admin Prodi hanya mengelola prodi sendiri
- ✅ **Resource Optimization:** Pemanfaatan ruangan yang efisien
- ✅ **Conflict Resolution:** Otomatis mencari solusi alternatif
- ✅ **Audit Trail:** Log lengkap untuk debugging

---

## 🚀 **FITUR UNGGULAN YANG DITONJOLKAN**

### **1. Algoritma Cerdas**
```
- Filter jam berdasarkan SKS mata kuliah
- Kombinasi slot jam yang optimal
- Pengecekan konflik yang akurat
- Fallback mechanism jika ada masalah
```

### **2. Preferensi System**
```
- Preferensi hari (Senin, Selasa, dll)
- Preferensi jam (08:00-09:00, dll)
- Prioritas dosen (1=Tinggi, 2=Sedang, 3=Rendah)
- Strict preference enforcement
```

### **3. Conflict Resolution**
```
- Room conflict detection
- Dosen conflict detection
- Time overlap prevention
- Alternative slot finding
```

---

## 📊 **METRICS DEMO**

### **Before Generate:**
- Total jadwal: 0
- Konflik: 0
- Preferensi: Belum diatur

### **After Generate:**
- Total jadwal: 4-6 jadwal
- Konflik: 0 (semua resolved)
- Preferensi: 100% sesuai
- Efficiency: Optimal

---

## 🎪 **TIPS DEMO**

### **1. Persiapan**
- Pastikan data sudah ada (dosen, mata kuliah, ruangan)
- Clear browser cache
- Siapkan backup data

### **2. Presentasi**
- Jelaskan setiap step dengan detail
- Highlight fitur unggulan
- Tunjukkan error handling
- Demonstrasikan user experience

### **3. Q&A Preparation**
- Siapkan jawaban untuk pertanyaan teknis
- Demonstrasikan edge cases
- Tunjukkan scalability
- Highlight security features

---

## 🔧 **TROUBLESHOOTING DEMO**

### **Jika Generate Gagal:**
1. Cek preferensi dosen sudah diatur
2. Cek ruangan tersedia
3. Cek konflik jadwal
4. Gunakan fallback mechanism

### **Jika Login Gagal:**
1. Cek credentials
2. Cek role assignment
3. Clear session
4. Restart server

### **Jika Data Kosong:**
1. Run seeder
2. Import data
3. Cek database connection
4. Verify migrations

---

## 📝 **SCRIPT DEMO**

### **Opening:**
"Selamat datang di demo Sistem Penjadwalan AI Kampus Merdeka. Sistem ini menggunakan algoritma cerdas untuk mengatur jadwal kuliah berdasarkan preferensi dosen, ketersediaan ruangan, dan menghindari konflik waktu."

### **Closing:**
"Sistem Penjadwalan AI ini telah berhasil mengoptimalkan jadwal kuliah dengan mempertimbangkan preferensi dosen, menghindari konflik, dan memanfaatkan ruangan secara efisien. Terima kasih atas perhatiannya."

---

## 🎯 **HASIL AKHIR DEMO**

Setelah demo, audience akan memahami:
1. **Cara kerja algoritma AI** dalam penjadwalan
2. **Manfaat sistem** untuk kampus
3. **User experience** yang smooth
4. **Scalability** untuk kampus besar
5. **ROI** dari implementasi sistem

---

*Demo ini dirancang untuk memberikan pemahaman yang komprehensif tentang sistem penjadwalan AI yang cerdas dan user-friendly.*









