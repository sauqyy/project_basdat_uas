-- Query untuk DimMataKuliah di Pentaho Data Integration
-- Gunakan query ini di Table Input step

SELECT 
    id,
    mata_kuliah_key,
    kode_mk,
    nama_mk,
    sks,
    semester,
    prodi,
    kapasitas,
    deskripsi,
    tipe_kelas,
    menit_per_sks,
    ada_praktikum,
    sks_praktikum,
    sks_materi,
    is_active,
    valid_from,
    valid_to,
    created_at,
    updated_at
FROM dim_mata_kuliah
WHERE is_active = 1
ORDER BY kode_mk;




