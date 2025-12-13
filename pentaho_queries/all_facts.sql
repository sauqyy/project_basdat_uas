-- Query untuk mendapatkan semua fact tables dalam satu query
-- Berguna untuk validasi atau summary di PDI

-- FactJadwal
SELECT 'FactJadwal' as table_name, COUNT(*) as record_count
FROM fact_jadwal WHERE status_aktif = 1

UNION ALL

-- FactUtilisasiRuangan
SELECT 'FactUtilisasiRuangan' as table_name, COUNT(*) as record_count
FROM fact_utilisasi_ruangan

UNION ALL

-- FactKecocokanJadwal
SELECT 'FactKecocokanJadwal' as table_name, COUNT(*) as record_count
FROM fact_kecocokan_jadwal

ORDER BY table_name;




