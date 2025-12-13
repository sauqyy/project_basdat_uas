-- Query untuk FactUtilisasiRuangan dengan dimensi di Pentaho Data Integration
-- Gunakan query ini di Table Input step

SELECT 
    f.id,
    f.ruangan_key,
    r.kode_ruangan,
    r.nama_ruangan,
    r.tipe_ruangan,
    r.kapasitas,
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
    f.total_jam_penggunaan,
    f.total_jam_tersedia,
    f.persentase_utilisasi,
    f.jumlah_kelas,
    f.jumlah_mahasiswa_total,
    f.rata_rata_kapasitas,
    f.peak_hour_utilisasi,
    f.periode_semester,
    f.tahun_akademik as tahun_akademik_fact,
    f.created_at,
    f.updated_at
FROM fact_utilisasi_ruangan f
LEFT JOIN dim_ruangan r ON f.ruangan_key = r.ruangan_key
LEFT JOIN dim_waktu w ON f.waktu_key = w.waktu_key
LEFT JOIN dim_prodi p ON f.prodi_key = p.prodi_key
ORDER BY f.persentase_utilisasi DESC, r.nama_ruangan;




