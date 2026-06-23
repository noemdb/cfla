<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\app\Instrument\DiagReferent;
use App\Models\app\Instrument\DiagCompetency;
use App\Models\app\Instrument\DiagIndicator;

class PopulateReferentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=PopulateReferentsSeeder
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks to allow inserting referents with potentially missing pestudios
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // 1. Create the Normative Referents
            $this->createNormativeReferent('2017/referenteNormativo.json');
            $this->createNormativeReferent('2023/referenteNormativo.json');

            // 2. Process Subject Files
            // 2017 Files -> Ref ID 1
            $this->processFolder('2017', 1);

            // 2023 Files -> Ref ID 2
            $this->processFolder('2023', 2);
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    protected function createNormativeReferent($relativePath)
    {
        $path = database_path("referents/$relativePath");
        if (!File::exists($path)) {
            $this->command->error("Normative file not found: $path");
            return;
        }

        $content = File::get($path);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['referente_normativo'])) {
            $this->command->error("Invalid Normative JSON in: $relativePath");
            return;
        }

        $refData = $data['referente_normativo'];

        // Ensure we force the ID if possible or update existing
        // Since ID is auto-increment usually, but here likely we want specific IDs 1 and 2 if user insisted.
        // We can try to force it by passing 'id'.

        $referent = DiagReferent::updateOrCreate(
            ['id' => $refData['id']],
            [
                'pestudio_id' => $refData['pestudio_id'],
                'name' => $refData['name'],
                'code' => $refData['code'],
                'version' => $refData['version'],
                'description' => $refData['description'],
                'active' => $refData['active'] ?? true,
            ]
        );

        $this->command->info("Processed Normative Referent: {$referent->name} (ID: {$referent->id})");
    }

    protected function processFolder($folderName, $referentId)
    {
        $path = database_path("referents/$folderName");
        if (!File::exists($path)) {
            $this->command->warn("Folder not found: $folderName");
            return;
        }

        $files = File::files($path);

        foreach ($files as $file) {
            // Skip normative file as it's already processed
            if ($file->getFilename() === 'referenteNormativo.json') {
                continue;
            }
            if ($file->getExtension() !== 'json') {
                continue;
            }

            $content = File::get($file->getPathname());
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->command->error("Invalid JSON in file: " . $file->getFilename());
                continue;
            }

            $this->processSubjectFile($data, $file->getFilename(), $referentId);
        }
    }

    protected function processSubjectFile($data, $filename, $referentId)
    {
        // Extract Subject Info (Pensum ID)
        $area = $data['area_formacion'] ?? null;
        if (!$area) {
            $this->command->warn("Missing area_formacion in $filename");
            return;
        }

        $pensumId = $area['pensumId']; // The subject ID

        $compMap = []; // Map JSON ID -> DB ID

        // Process Competencies
        if (isset($data['competencias']) && is_array($data['competencias'])) {
            foreach ($data['competencias'] as $compData) {
                // Link to the Global Normative Referent ($referentId)
                // And the Specific Subject ($pensumId)

                $competency = DiagCompetency::updateOrCreate(
                    [
                        'referent_id' => $referentId,
                        'pensum_id' => $pensumId,
                        'name' => $compData['nombre'],
                    ],
                    [
                        'description' => $compData['descripcion'] ?? null,
                    ]
                );

                if (isset($compData['id'])) {
                    $compMap[$compData['id']] = $competency->id;
                }
            }
        }

        // Process Indicators
        if (isset($data['indicadores']) && is_array($data['indicadores'])) {
            foreach ($data['indicadores'] as $indData) {
                $compId = $compMap[$indData['competency_id']] ?? null;

                if (!$compId) {
                    // Try to find if mapped already in database? 
                    // Unlikely if we just created it.
                    $this->command->warn("Indicator {$indData['codigo']} in $filename has unknown competency_id: {$indData['competency_id']}");
                    continue;
                }

                DiagIndicator::updateOrCreate(
                    [
                        'competency_id' => $compId,
                        'code' => $indData['codigo'],
                    ],
                    [
                        'description' => $indData['descripcion'],
                        'expected_level' => $indData['nivel_esperado'] ?? null,
                    ]
                );
            }
        }

        $this->command->info("Processed Subject File: $filename for Referent ID: $referentId");
    }
}
