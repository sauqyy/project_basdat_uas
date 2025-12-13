-- Query untuk DimPreferensi di Pentaho Data Integration
-- Gunakan query ini di Table Input step

SELECT 
    id,
    preferensi_key,
    dosen_id,
    mata_kuliah_id,
    preferensi_hari,
    preferensi_jam,
    prioritas,
    catatan,
    is_active,
    valid_from,
    valid_to,
    created_at,
    updated_at
FROM dim_preferensi
WHERE is_active = 1
ORDER BY dosen_id, prioritas;




