-- Query untuk DimRuangan di Pentaho Data Integration
-- Gunakan query ini di Table Input step

SELECT 
    id,
    ruangan_key,
    kode_ruangan,
    nama_ruangan,
    kapasitas,
    tipe_ruangan,
    fasilitas,
    prodi,
    status,
    is_active,
    valid_from,
    valid_to,
    created_at,
    updated_at
FROM dim_ruangan
WHERE is_active = 1
ORDER BY kode_ruangan;




