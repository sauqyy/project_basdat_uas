-- Query untuk DimWaktu di Pentaho Data Integration
-- Gunakan query ini di Table Input step

SELECT 
    id,
    waktu_key,
    hari,
    jam_mulai,
    jam_selesai,
    semester,
    tahun_akademik,
    periode,
    hari_ke,
    slot_waktu,
    durasi_menit,
    is_active,
    created_at,
    updated_at
FROM dim_waktu
WHERE is_active = 1
ORDER BY hari_ke, jam_mulai;




