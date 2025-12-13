-- Query untuk mendapatkan semua dimensi dalam satu query
-- Berguna untuk validasi atau cross-reference di PDI

-- DimDosen
SELECT 'DimDosen' as table_name, COUNT(*) as record_count
FROM dim_dosen WHERE is_active = 1

UNION ALL

-- DimMataKuliah
SELECT 'DimMataKuliah' as table_name, COUNT(*) as record_count
FROM dim_mata_kuliah WHERE is_active = 1

UNION ALL

-- DimRuangan
SELECT 'DimRuangan' as table_name, COUNT(*) as record_count
FROM dim_ruangan WHERE is_active = 1

UNION ALL

-- DimWaktu
SELECT 'DimWaktu' as table_name, COUNT(*) as record_count
FROM dim_waktu WHERE is_active = 1

UNION ALL

-- DimProdi
SELECT 'DimProdi' as table_name, COUNT(*) as record_count
FROM dim_prodi WHERE is_active = 1

UNION ALL

-- DimPreferensi
SELECT 'DimPreferensi' as table_name, COUNT(*) as record_count
FROM dim_preferensi WHERE is_active = 1

ORDER BY table_name;



