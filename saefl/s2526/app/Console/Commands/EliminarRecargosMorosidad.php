<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\app\Planpago\Cuentaxpagar;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EliminarRecargosMorosidad extends Command
{
    protected $signature = 'recargos:eliminar
                            {--debug : Muestra información detallada de cada cuota}
                            {--force : Ejecuta la eliminación real de las cuotas}';

    protected $description = 'Elimina las cuotas de recargo por morosidad que aún no han sido canceladas.';
    //Modo simulación (sin borrar)
    //php artisan recargos:eliminar --debug

    //Modo real (ejecuta eliminación):
    //php artisan recargos:eliminar --force

    public function handle()
    {
        $hoy = Carbon::now()->format('Y-m-d');
        $total = 0;
        $eliminadas = 0;
        $this->info("==== Inicio del proceso de eliminación de recargos - {$hoy} ====");

        // Confirmación de seguridad
        if (! $this->option('force')) {
            $this->warn('Modo simulación (dry-run): No se eliminarán datos reales.');
            $this->warn('Usa --force para ejecutar la eliminación real.');
        }

        // Encapsulamos en transacción para seguridad
        DB::beginTransaction();
        try {
            // Aquí implementaremos la lógica de búsqueda y eliminación
            $this->info("Preparando búsqueda de cuotas elegibles para eliminación...");

            // Paso 3: Buscar cuotas elegibles
            $cuotas = Cuentaxpagar::where('status_late_payment', true)
                ->where('type', 'INDIVIDUAL')
                ->where('name','like','OCTUBRE RM%')
                ->whereNotNull('quota_original_id')
                ->get();

            $total = $cuotas->count();

            if ($total === 0) {
                $this->info("No se encontraron cuotas de recargo elegibles para eliminación.");
                DB::commit();
                return 0;
            }

            $this->info("Se encontraron {$total} cuotas elegibles para revisión.");

            foreach ($cuotas as $cuota) {

                // Obtener estudiante relacionado
                $estudiant = $cuota->estudiant;

                if (! $estudiant) {
                    $this->warn("⚠️ Cuota ID {$cuota->id} sin estudiante asociado, se omite.");
                    continue;
                }

                // Calcular montos
                $ammountOriginal = round($cuota->TotalExchangeMontoCuentasXPagar($estudiant->id), 2);
                $ammountAdeudado = round($cuota->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id), 2);

                // Mostrar información si está en modo debug
                if ($this->option('debug')) {
                    $this->line("→ Cuota ID {$cuota->id} {$cuota->name} | Estudiante[{$estudiant->id}] {$estudiant->ci_estudiant} | Original: {$ammountOriginal} | Adeudado: {$ammountAdeudado}");
                }

                // Condición clave: solo eliminar si no se ha pagado nada
                if ($ammountAdeudado !== $ammountOriginal) {
                    if ($this->option('debug')) {
                        $this->warn("   - Cuota {$cuota->id} tiene pagos parciales o completos. No se elimina.");
                    }
                    continue;
                }

                // Simulación sin eliminar
                if (! $this->option('force')) {
                    $this->warn("Simulación: se eliminaría cuota #{$cuota->id} (no pagada, {$ammountAdeudado} / {$ammountOriginal}).");
                    continue;
                }

                // === Eliminación real ===
                try {
                    $conceptos = $cuota->conceptopagos ?? collect();
                    foreach ($conceptos as $concepto) {
                        $concepto->delete();
                    }

                    $cuota->delete();
                    $eliminadas++;

                    $this->info("✅ Eliminada cuota #{$cuota->id} sin pagos registrados.");
                } catch (\Exception $e) {
                    $this->error("⚠️ Error al eliminar cuota #{$cuota->id}: " . $e->getMessage());
                }
            }


            DB::commit();
            $this->info("==== Proceso finalizado correctamente ====");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("==== ERROR FATAL ====");
            $this->error($e->getMessage());
        }

        if ($this->option('force')) {
            $this->info("==== Proceso completado ====");
            $this->info("Total de cuotas eliminadas: {$eliminadas}");
        } else {
            $this->warn("==== Simulación completada ====");
            $this->warn("Total de cuotas que se eliminarían: {$total}");
        }

        return 0;
    }
}
