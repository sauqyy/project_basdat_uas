-- Query untuk FactKecocokanJadwal dengan dimensi di Pentaho Data Integration
-- Gunakan query ini di Table Input step

SELECT 
    f.id,
    f.dosen_key,
    d.nama_dosen,
    d.nip,
    d.prodi as prodi_dosen,
    f.preferensi_key,
    p.dosen_id,
    p.mata_kuliah_id,
    p.preferensi_hari,
    p.preferensi_jam,
    p.prioritas as prioritas_preferensi,
    p.catatan as catatan_preferensi,
    f.waktu_key,
    w.hari,
    w.jam_mulai,
    w.jam_selesai,
    w.semester,
    w.tahun_akademik,
    w.slot_waktu,
    f.preferensi_hari_terpenuhi,
    f.preferensi_jam_terpenuhi,
    f.skor_kecocokan,
    f.prioritas_preferensi as prioritas_fact,
    f.jumlah_preferensi_total,
    f.jumlah_preferensi_terpenuhi,
    f.persentase_kecocokan,
    f.catatan_kecocokan,
    f.semester as semester_fact,
    f.tahun_akademik as tahun_akademik_fact,
    f.created_at,
    f.updated_at
FROM fact_kecocokan_jadwal f
LEFT JOIN dim_dosen d ON f.dosen_key = d.dosen_key
LEFT JOIN dim_preferensi p ON f.preferensi_key = p.preferensi_key
LEFT JOIN dim_waktu w ON f.waktu_key = w.waktu_key
ORDER BY f.skor_kecocokan DESC, d.nama_dosen;




