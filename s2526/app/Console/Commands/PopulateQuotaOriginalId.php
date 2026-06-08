<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\app\Estudiant;
use App\Models\app\Planpago\Cuentaxpagar;
use Illuminate\Support\Facades\DB;

class PopulateQuotaOriginalId extends Command
{
    protected $signature = 'recargos:populate-quota-original-id';
    protected $description = 'Rellena quota_original_id usando el contexto por estudiante y getAllQuotas()';

    public function handle()
    {
        $this->info('🔍 Buscando recargos sin quota_original_id...');

        // Obtener todos los estudiantes que tienen recargos sin vincular
        $estudiantesConRecargos = DB::table('cuentaxpagars')
            ->select('estudiant_id')
            ->whereNull('quota_original_id')
            ->where(function ($q) {
                $q->where('name', 'like', '% RM1')
                  ->orWhere('description', 'like', '%Recargo por Morosidad%');
            })
            ->groupBy('estudiant_id')
            ->pluck('estudiant_id');

        if ($estudiantesConRecargos->isEmpty()) {
            $this->info('✅ No hay recargos pendientes.');
            return 0;
        }

        $this->info("Procesando {$estudiantesConRecargos->count()} estudiantes...");

        DB::beginTransaction();
        try {
            $totalActualizados = 0;
            $totalNoEncontrados = 0;

            foreach ($estudiantesConRecargos as $estudiantId) {
                $estudiante = Estudiant::find($estudiantId);
                if (!$estudiante) {
                    $this->warn("⚠️ Estudiante ID {$estudiantId} no encontrado.");
                    continue;
                }

                // Obtener recargos del estudiante sin quota_original_id
                $recargos = Cuentaxpagar::where('estudiant_id', $estudiantId)
                    ->whereNull('quota_original_id')
                    ->where(function ($q) {
                        $q->where('name', 'like', '% RM1')
                          ->orWhere('description', 'like', '%Recargo por Morosidad%');
                    })
                    ->get();

                if ($recargos->isEmpty()) continue;

                // Obtener todas las cuotas válidas del estudiante (incluyendo las generales)
                $cuotasValidas = $estudiante->getAllQuotas();

                // Crear mapa: nombre_cuota => id
                $mapaCuotas = [];
                foreach ($cuotasValidas as $cuota) {
                    $mapaCuotas[$cuota->name] = $cuota->id;
                }

                foreach ($recargos as $recargo) {
                    // Extraer nombre original quitando ' RM1'
                    $nombreOriginal = str_replace(' RM1', '', $recargo->name);

                    if (isset($mapaCuotas[$nombreOriginal])) {
                        $recargo->quota_original_id = $mapaCuotas[$nombreOriginal];
                        $recargo->save();
                        $totalActualizados++;
                        $this->info("✓ Estudiante {$estudiantId}: {$recargo->name} → {$nombreOriginal} (ID {$recargo->quota_original_id})");
                    } else {
                        $totalNoEncontrados++;
                        $this->warn("⚠️ Estudiante {$estudiantId}: No se encontró '{$nombreOriginal}' para recargo '{$recargo->name}'");
                    }
                }
            }

            DB::commit();

            $this->info("==== Resultado Final ====");
            $this->info("✅ Recargos vinculados: {$totalActualizados}");
            $this->info("⚠️ Recargos no vinculados: {$totalNoEncontrados}");

            if ($totalNoEncontrados > 0) {
                $this->warn("Revisa los recargos no vinculados manualmente.");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}