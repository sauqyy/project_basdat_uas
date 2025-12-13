-- Query untuk DimProdi di Pentaho Data Integration
-- Gunakan query ini di Table Input step

SELECT 
    id,
    prodi_key,
    kode_prodi,
    nama_prodi,
    fakultas,
    deskripsi,
    akreditasi,
    is_active,
    valid_from,
    valid_to,
    created_at,
    updated_at
FROM dim_prodi
WHERE is_active = 1
ORDER BY kode_prodi;




