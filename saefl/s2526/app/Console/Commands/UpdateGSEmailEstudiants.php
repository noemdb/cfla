<?php

namespace App\Console\Commands;

use App\Models\app\Estudiant;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class UpdateGSEmailEstudiants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan estudiants:update-gsemail
     */
    protected $signature = 'estudiants:update-gsemail
                            {--year=2526 : Carpeta del año escolar (ej. 2526)}
                            {--file=updateGSEmail : Nombre del archivo CSV sin extensión}';

    /**
     * The console command description.
     */
    protected $description = 'Actualiza los gsemail de estudiants a partir de un CSV';

    public function handle()
    {
        $datas = collect();

        try {
            $year   = $this->option('year');
            $file   = $this->option('file');
            $folder = "estudiants";

            $csvPath = public_path("csv/{$year}/{$folder}/{$file}.csv");

            if (!file_exists($csvPath)) {
                $this->error("El archivo CSV no existe: {$csvPath}");
                return Command::FAILURE;
            }

            $arrData = csv_to_array($csvPath, ";");

            if (empty($arrData)) {
                $this->warn("El archivo CSV está vacío o no tiene datos.");
                return Command::SUCCESS;
            }

            foreach ($arrData as $estudiantCsv) {
                $ci = $estudiantCsv['ci_estudiant'] ?? null;
                $gsemail = $estudiantCsv['gsemail'] ?? null;

                if (!$ci || !$gsemail) {
                    $this->warn("Fila ignorada: falta CI o gsemail");
                    continue;
                }

                if (!validate_email($gsemail)) {
                    $this->warn("Email inválido: {$gsemail}");
                    continue;
                }

                $estudiant = Estudiant::where('ci_estudiant', $ci)->first();

                if (!$estudiant) {
                    $this->warn("No existe estudiant con CI: {$ci}");
                    continue;
                }

                $exists = Estudiant::where('gsemail', $gsemail)
                    ->where('id', '!=', $estudiant->id)
                    ->exists();

                if ($exists) {
                    $this->warn("Email duplicado: {$gsemail}");
                    continue;
                }

                $estudiant->update(['gsemail' => $gsemail]);
                $datas->push($estudiant);

                $this->info("✅ Actualizado: {$ci} → {$gsemail}");
            }

            $this->info("Proceso completado. Total actualizados: " . $datas->count());
            Log::info("UpdateGSEmailEstudiants completado", [
                'total' => $datas->count(),
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error("Error en UpdateGSEmailEstudiants", ['error' => $e->getMessage()]);
            $this->error("Ocurrió un error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
