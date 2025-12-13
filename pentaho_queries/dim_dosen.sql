-- Query untuk DimDosen di Pentaho Data Integration
-- Gunakan query ini di Table Input step

SELECT 
    id,
    dosen_key,
    nip,
    nama_dosen,
    email,
    prodi,
    role,
    profile_picture,
    judul_skripsi,
    is_active,
    valid_from,
    valid_to,
    created_at,
    updated_at
FROM dim_dosen
WHERE is_active = 1
ORDER BY nama_dosen;




