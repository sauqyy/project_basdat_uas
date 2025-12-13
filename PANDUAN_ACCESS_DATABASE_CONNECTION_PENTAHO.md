# Panduan Konfigurasi Access Database Connection di Pentaho

## 📋 Opsi Access di Database Connection

Di Pentaho, saat membuat database connection, Anda akan melihat dropdown **"Access:"** dengan 3 opsi:

1. **Native (JDBC)** ← **RECOMMENDED untuk semua koneksi**
2. **ODBC**
3. **JNDI**

---

## ✅ Rekomendasi: Gunakan Native (JDBC) untuk Semua Koneksi

### Untuk Operational_DB:
- ✅ **Access**: `Native (JDBC)` ← **PILIH INI**
- ✅ **Connection Type**: `MySQL` (atau sesuai database Anda)
- ✅ **Host Name**: `localhost` atau IP server
- ✅ **Database Name**: Nama database operasional
- ✅ **Port**: `3306` (default MySQL)
- ✅ **User Name**: Username database
- ✅ **Password**: Password database

### Untuk DW_Connection:
- ✅ **Access**: `Native (JDBC)` ← **PILIH INI JUGA**
- ✅ **Connection Type**: `MySQL` (atau sesuai database Anda)
- ✅ **Host Name**: `localhost` atau IP server
- ✅ **Database Name**: Nama database data warehouse (bisa sama atau berbeda dengan Operational_DB)
- ✅ **Port**: `3306` (default MySQL)
- ✅ **User Name**: Username database
- ✅ **Password**: Password database

---

## 📝 Penjelasan Setiap Opsi

### 1. Native (JDBC) ✅ RECOMMENDED

**Kapan digunakan:**
- ✅ **Untuk semua koneksi** (Operational_DB dan DW_Connection)
- ✅ Koneksi langsung ke database menggunakan JDBC driver
- ✅ Lebih cepat dan stabil
- ✅ Tidak perlu setup tambahan

**Keuntungan:**
- ✅ Performa lebih baik
- ✅ Setup lebih mudah
- ✅ Support untuk semua database (MySQL, PostgreSQL, SQLite, dll)
- ✅ Tidak perlu konfigurasi ODBC atau JNDI

**Cara setup:**
1. Pilih **Native (JDBC)**
2. Pilih **Connection Type** (MySQL, PostgreSQL, dll)
3. Isi host, database, port, username, password
4. Klik **Test** → Jika berhasil, klik **OK**

---

### 2. ODBC (Tidak Direkomendasikan)

**Kapan digunakan:**
- ❌ Hanya jika Native (JDBC) tidak berfungsi
- ❌ Jika database hanya support ODBC
- ❌ Jika sudah ada ODBC DSN yang dikonfigurasi

**Kekurangan:**
- ❌ Perlu setup ODBC DSN terlebih dahulu di Windows
- ❌ Lebih lambat dibanding JDBC
- ❌ Setup lebih kompleks

**Cara setup (jika diperlukan):**
1. Setup ODBC DSN di Windows (Control Panel → Administrative Tools → ODBC Data Sources)
2. Pilih **ODBC** di Pentaho
3. Pilih DSN yang sudah dibuat
4. Isi username dan password

---

### 3. JNDI (Untuk Server Environment)

**Kapan digunakan:**
- ❌ Hanya untuk environment server (Pentaho Server/BA Server)
- ❌ Jika menggunakan connection pooling di server
- ❌ Tidak untuk development lokal

**Kekurangan:**
- ❌ Perlu konfigurasi JNDI di server
- ❌ Tidak bisa digunakan di Spoon (PDI Desktop)
- ❌ Lebih kompleks untuk setup

---

## 🔧 Setup Database Connection Lengkap

### Step 1: Setup Operational_DB

1. Buka Pentaho Spoon
2. **View** → **Database Connections** (atau `Ctrl+Shift+D`)
3. Klik kanan → **New**
4. Isi konfigurasi:
   ```
   Connection Name: Operational_DB
   Connection Type: MySQL
   Access: Native (JDBC) ← PILIH INI
   Host Name: localhost
   Database Name: sistem_akademik (atau nama database Anda)
   Port Number: 3306
   User Name: root (atau username Anda)
   Password: password Anda
   ```
5. Klik **Test** → Pastikan "Connection test successful"
6. Klik **OK**

### Step 2: Setup DW_Connection

1. Di window **Database Connections**, klik kanan → **New** lagi
2. Isi konfigurasi:
   ```
   Connection Name: DW_Connection
   Connection Type: MySQL
   Access: Native (JDBC) ← PILIH INI JUGA
   Host Name: localhost
   Database Name: sistem_akademik (bisa sama dengan Operational_DB)
   Port Number: 3306
   User Name: root (atau username Anda)
   Password: password Anda
   ```
3. Klik **Test** → Pastikan "Connection test successful"
4. Klik **OK**

---

## ✅ Checklist Setup

- [ ] **Operational_DB** dibuat dengan **Access: Native (JDBC)**
- [ ] **DW_Connection** dibuat dengan **Access: Native (JDBC)**
- [ ] Kedua connection sudah di-test dan berhasil
- [ ] Nama connection **HARUS SAMA PERSIS** dengan yang ada di file KTR:
  - `Operational_DB` (untuk baca data operasional)
  - `DW_Connection` (untuk tulis ke data warehouse)

---

## 🔍 Verifikasi Connection

Setelah setup, verifikasi dengan:

1. Buka salah satu file KTR (misal: `01_Populate_Dim_Dosen.ktr`)
2. Double-click step **Table Input**
3. Di dropdown **Connection**, pastikan muncul:
   - `Operational_DB`
   - `DW_Connection`
4. Pilih connection dan test query

---

## ⚠️ Troubleshooting

### Error: "Connection not found"
- **Solusi**: Pastikan nama connection **SAMA PERSIS** dengan yang ada di file KTR
- Cek case-sensitive: `Operational_DB` bukan `operational_db`

### Error: "Driver not found"
- **Solusi**: Download dan install JDBC driver untuk database Anda
- MySQL: Download MySQL Connector/J
- Copy ke folder: `pentaho/data-integration/lib`
- Restart Pentaho

### Error: "Access denied"
- **Solusi**: 
  - Periksa username dan password
  - Pastikan user memiliki akses ke database
  - Test connection di Database Connections window

---

## 📊 Ringkasan

| Connection | Access | Connection Type | Database |
|------------|--------|-----------------|----------|
| **Operational_DB** | **Native (JDBC)** ✅ | MySQL | Database operasional |
| **DW_Connection** | **Native (JDBC)** ✅ | MySQL | Database data warehouse |

---

## 🎯 Kesimpulan

**JAWABAN SINGKAT:**
- ✅ **Gunakan Native (JDBC) untuk SEMUA koneksi** (Operational_DB dan DW_Connection)
- ✅ **TIDAK PERLU diubah** ke ODBC atau JNDI
- ✅ Native (JDBC) adalah pilihan terbaik untuk development dan production

**TIDAK PERLU:**
- ❌ Tidak perlu ubah ke ODBC
- ❌ Tidak perlu ubah ke JNDI
- ❌ Tetap gunakan Native (JDBC) untuk semua

---

**Gunakan Native (JDBC) untuk semua database connection di Pentaho!** 🎉

