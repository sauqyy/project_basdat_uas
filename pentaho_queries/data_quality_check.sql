-- Query untuk data quality check di PDI
-- Mengecek integrity dan completeness data

-- 1. Cek FactJadwal yang tidak punya dimensi
SELECT 
    'FactJadwal - Missing DimDosen' as check_type,
    COUNT(*) as error_count
FROM fact_jadwal f
LEFT JOIN dim_dosen d ON f.dosen_key = d.dosen_key
WHERE d.dosen_key IS NULL AND f.status_aktif = 1

UNION ALL

SELECT 
    'FactJadwal - Missing DimMataKuliah' as check_type,
    COUNT(*) as error_count
FROM fact_jadwal f
LEFT JOIN dim_mata_kuliah mk ON f.mata_kuliah_key = mk.mata_kuliah_key
WHERE mk.mata_kuliah_key IS NULL AND f.status_aktif = 1

UNION ALL

SELECT 
    'FactJadwal - Missing DimRuangan' as check_type,
    COUNT(*) as error_count
FROM fact_jadwal f
LEFT JOIN dim_ruangan r ON f.ruangan_key = r.ruangan_key
WHERE r.ruangan_key IS NULL AND f.status_aktif = 1

UNION ALL

SELECT 
    'FactJadwal - Missing DimWaktu' as check_type,
    COUNT(*) as error_count
FROM fact_jadwal f
LEFT JOIN dim_waktu w ON f.waktu_key = w.waktu_key
WHERE w.waktu_key IS NULL AND f.status_aktif = 1

UNION ALL

SELECT 
    'FactJadwal - Missing DimProdi' as check_type,
    COUNT(*) as error_count
FROM fact_jadwal f
LEFT JOIN dim_prodi p ON f.prodi_key = p.prodi_key
WHERE p.prodi_key IS NULL AND f.status_aktif = 1

UNION ALL

-- 2. Cek utilisasi ruangan yang tidak valid
SELECT 
    'FactUtilisasiRuangan - Invalid Percentage' as check_type,
    COUNT(*) as error_count
FROM fact_utilisasi_ruangan
WHERE persentase_utilisasi < 0 OR persentase_utilisasi > 100

UNION ALL

-- 3. Cek skor kecocokan yang tidak valid
SELECT 
    'FactKecocokanJadwal - Invalid Score' as check_type,
    COUNT(*) as error_count
FROM fact_kecocokan_jadwal
WHERE skor_kecocokan < 0 OR skor_kecocokan > 100

ORDER BY check_type;




