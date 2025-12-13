<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\FactJadwal;
use App\Models\FactUtilisasiRuangan;
use App\Models\FactKecocokanJadwal;
use App\Models\DimDosen;
use App\Models\DimMataKuliah;
use App\Models\DimRuangan;
use App\Models\DimWaktu;
use App\Models\DimProdi;
use App\Models\DimPreferensi;

class ExportToPentaho extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:pentaho 
                            {--table=all : Table to export (all, fact_jadwal, fact_utilisasi_ruangan, fact_kecocokan_jadwal, dim_*, all_dim, all_fact)}
                            {--format=csv : Export format (csv, json)}
                            {--output=storage/exports : Output directory}
                            {--with-dimensions : Include dimension data in fact table exports}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export dimension and fact tables to CSV/JSON for Pentaho';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $table = $this->option('table');
        $format = $this->option('format');
        $outputDir = $this->option('output');

        // Create output directory if not exists
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $this->info("Exporting data to {$format} format...");

        if ($table === 'all') {
            $this->exportAllTables($format, $outputDir);
        } elseif ($table === 'all_dim') {
            $this->exportAllDimensions($format, $outputDir);
        } elseif ($table === 'all_fact') {
            $this->exportAllFacts($format, $outputDir);
        } else {
            $this->exportTable($table, $format, $outputDir);
        }

        $this->info("Export completed! Files saved in: {$outputDir}");
    }

    /**
     * Export all tables
     */
    private function exportAllTables($format, $outputDir)
    {
        // Export dimension tables
        $this->info("Exporting dimension tables...");
        $this->exportTable('dim_dosen', $format, $outputDir);
        $this->exportTable('dim_mata_kuliah', $format, $outputDir);
        $this->exportTable('dim_ruangan', $format, $outputDir);
        $this->exportTable('dim_waktu', $format, $outputDir);
        $this->exportTable('dim_prodi', $format, $outputDir);
        $this->exportTable('dim_preferensi', $format, $outputDir);

        // Export fact tables
        $this->info("Exporting fact tables...");
        $this->exportTable('fact_jadwal', $format, $outputDir);
        $this->exportTable('fact_utilisasi_ruangan', $format, $outputDir);
        $this->exportTable('fact_kecocokan_jadwal', $format, $outputDir);
    }

    /**
     * Export all dimension tables only
     */
    private function exportAllDimensions($format, $outputDir)
    {
        $this->info("Exporting all dimension tables...");
        $this->exportTable('dim_dosen', $format, $outputDir);
        $this->exportTable('dim_mata_kuliah', $format, $outputDir);
        $this->exportTable('dim_ruangan', $format, $outputDir);
        $this->exportTable('dim_waktu', $format, $outputDir);
        $this->exportTable('dim_prodi', $format, $outputDir);
        $this->exportTable('dim_preferensi', $format, $outputDir);
    }

    /**
     * Export all fact tables only
     */
    private function exportAllFacts($format, $outputDir)
    {
        $this->info("Exporting all fact tables...");
        $this->exportTable('fact_jadwal', $format, $outputDir);
        $this->exportTable('fact_utilisasi_ruangan', $format, $outputDir);
        $this->exportTable('fact_kecocokan_jadwal', $format, $outputDir);
    }

    /**
     * Export single table
     */
    private function exportTable($tableName, $format, $outputDir)
    {
        $this->info("Exporting {$tableName}...");

        try {
            // Get data based on table name
            $data = $this->getTableData($tableName);

            if (empty($data)) {
                $this->warn("No data found in {$tableName}");
                return;
            }

            $filename = $tableName . '_' . date('Y-m-d_His') . '.' . $format;
            $filepath = $outputDir . '/' . $filename;

            if ($format === 'csv') {
                $this->exportToCsv($data, $filepath);
            } elseif ($format === 'json') {
                $this->exportToJson($data, $filepath);
            }

            $this->info("✓ {$tableName} exported to {$filename}");
        } catch (\Exception $e) {
            $this->error("Error exporting {$tableName}: " . $e->getMessage());
        }
    }

    /**
     * Get table data
     */
    private function getTableData($tableName)
    {
        switch ($tableName) {
            case 'dim_dosen':
                return DimDosen::where('is_active', true)->get()->toArray();
            
            case 'dim_mata_kuliah':
                return DimMataKuliah::where('is_active', true)->get()->toArray();
            
            case 'dim_ruangan':
                return DimRuangan::where('is_active', true)->get()->toArray();
            
            case 'dim_waktu':
                return DimWaktu::where('is_active', true)->get()->toArray();
            
            case 'dim_prodi':
                return DimProdi::where('is_active', true)->get()->toArray();
            
            case 'dim_preferensi':
                return DimPreferensi::where('is_active', true)->get()->toArray();
            
            case 'fact_jadwal':
                return FactJadwal::where('status_aktif', true)
                    ->with(['dimDosen', 'dimMataKuliah', 'dimRuangan', 'dimWaktu', 'dimProdi', 'dimPreferensi'])
                    ->get()
                    ->map(function($item) {
                        return [
                            'id' => $item->id,
                            'dosen_key' => $item->dosen_key,
                            'dosen_nama' => $item->dimDosen->nama_dosen ?? null,
                            'mata_kuliah_key' => $item->mata_kuliah_key,
                            'mata_kuliah_nama' => $item->dimMataKuliah->nama_mk ?? null,
                            'ruangan_key' => $item->ruangan_key,
                            'ruangan_nama' => $item->dimRuangan->nama_ruangan ?? null,
                            'waktu_key' => $item->waktu_key,
                            'hari' => $item->dimWaktu->hari ?? null,
                            'jam_mulai' => $item->dimWaktu->jam_mulai ?? null,
                            'prodi_key' => $item->prodi_key,
                            'prodi_nama' => $item->dimProdi->nama_prodi ?? null,
                            'preferensi_key' => $item->preferensi_key,
                            'jumlah_sks' => $item->jumlah_sks,
                            'durasi_menit' => $item->durasi_menit,
                            'kapasitas_kelas' => $item->kapasitas_kelas,
                            'jumlah_mahasiswa' => $item->jumlah_mahasiswa,
                            'utilisasi_ruangan' => $item->utilisasi_ruangan,
                            'prioritas_preferensi' => $item->prioritas_preferensi,
                            'konflik_jadwal' => $item->konflik_jadwal ? 1 : 0,
                            'tingkat_konflik' => $item->tingkat_konflik,
                            'status_aktif' => $item->status_aktif ? 1 : 0,
                        ];
                    })->toArray();
            
            case 'fact_utilisasi_ruangan':
                return FactUtilisasiRuangan::with(['dimRuangan', 'dimWaktu', 'dimProdi'])
                    ->get()
                    ->map(function($item) {
                        return [
                            'id' => $item->id,
                            'ruangan_key' => $item->ruangan_key,
                            'ruangan_nama' => $item->dimRuangan->nama_ruangan ?? null,
                            'waktu_key' => $item->waktu_key,
                            'hari' => $item->dimWaktu->hari ?? null,
                            'prodi_key' => $item->prodi_key,
                            'prodi_nama' => $item->dimProdi->nama_prodi ?? null,
                            'total_jam_penggunaan' => $item->total_jam_penggunaan,
                            'total_jam_tersedia' => $item->total_jam_tersedia,
                            'persentase_utilisasi' => $item->persentase_utilisasi,
                            'jumlah_kelas' => $item->jumlah_kelas,
                            'jumlah_mahasiswa_total' => $item->jumlah_mahasiswa_total,
                            'rata_rata_kapasitas' => $item->rata_rata_kapasitas,
                            'peak_hour_utilisasi' => $item->peak_hour_utilisasi,
                            'periode_semester' => $item->periode_semester,
                            'tahun_akademik' => $item->tahun_akademik,
                        ];
                    })->toArray();
            
            case 'fact_kecocokan_jadwal':
                return FactKecocokanJadwal::with(['dimDosen', 'dimPreferensi', 'dimWaktu'])
                    ->get()
                    ->map(function($item) {
                        return [
                            'id' => $item->id,
                            'dosen_key' => $item->dosen_key,
                            'dosen_nama' => $item->dimDosen->nama_dosen ?? null,
                            'preferensi_key' => $item->preferensi_key,
                            'waktu_key' => $item->waktu_key,
                            'hari' => $item->dimWaktu->hari ?? null,
                            'jam_mulai' => $item->dimWaktu->jam_mulai ?? null,
                            'preferensi_hari_terpenuhi' => $item->preferensi_hari_terpenuhi ? 1 : 0,
                            'preferensi_jam_terpenuhi' => $item->preferensi_jam_terpenuhi ? 1 : 0,
                            'skor_kecocokan' => $item->skor_kecocokan,
                            'prioritas_preferensi' => $item->prioritas_preferensi,
                            'jumlah_preferensi_total' => $item->jumlah_preferensi_total,
                            'jumlah_preferensi_terpenuhi' => $item->jumlah_preferensi_terpenuhi,
                            'persentase_kecocokan' => $item->persentase_kecocokan,
                            'catatan_kecocokan' => $item->catatan_kecocokan,
                            'semester' => $item->semester,
                            'tahun_akademik' => $item->tahun_akademik,
                        ];
                    })->toArray();
            
            default:
                // Try to get data directly from database
                return DB::table($tableName)->get()->toArray();
        }
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($data, $filepath)
    {
        if (empty($data)) {
            return;
        }

        $file = fopen($filepath, 'w');

        // Add BOM for UTF-8 (for Excel compatibility)
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write headers
        $headers = array_keys($data[0]);
        fputcsv($file, $headers);

        // Write data
        foreach ($data as $row) {
            // Convert array values to string if needed
            $row = array_map(function($value) {
                if (is_array($value)) {
                    return json_encode($value);
                }
                return $value;
            }, $row);
            
            fputcsv($file, $row);
        }

        fclose($file);
    }

    /**
     * Export to JSON
     */
    private function exportToJson($data, $filepath)
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($filepath, $json);
    }
}
