-- Query untuk FactJadwal dengan semua dimensi di Pentaho Data Integration
-- Gunakan query ini di Table Input step untuk mendapatkan data lengkap

SELECT 
    f.id,
    f.dosen_key,
    d.nama_dosen,
    d.nip,
    f.mata_kuliah_key,
    mk.kode_mk,
    mk.nama_mk,
    mk.sks,
    f.ruangan_key,
    r.kode_ruangan,
    r.nama_ruangan,
    r.tipe_ruangan,
    r.kapasitas as kapasitas_ruangan,
    f.waktu_key,
    w.hari,
    w.jam_mulai,
    w.jam_selesai,
    w.semester,
    w.tahun_akademik,
    w.slot_waktu,
    f.prodi_key,
    p.kode_prodi,
    p.nama_prodi,
    p.fakultas,
    f.preferensi_key,
    f.jumlah_sks,
    f.durasi_menit,
    f.kapasitas_kelas,
    f.jumlah_mahasiswa,
    f.utilisasi_ruangan,
    f.prioritas_preferensi,
    f.konflik_jadwal,
    f.tingkat_konflik,
    f.status_aktif,
    f.created_at_jadwal,
    f.updated_at_jadwal
FROM fact_jadwal f
LEFT JOIN dim_dosen d ON f.dosen_key = d.dosen_key
LEFT JOIN dim_mata_kuliah mk ON f.mata_kuliah_key = mk.mata_kuliah_key
LEFT JOIN dim_ruangan r ON f.ruangan_key = r.ruangan_key
LEFT JOIN dim_waktu w ON f.waktu_key = w.waktu_key
LEFT JOIN dim_prodi p ON f.prodi_key = p.prodi_key
WHERE f.status_aktif = 1
ORDER BY w.hari_ke, w.jam_mulai, d.nama_dosen;




