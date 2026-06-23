<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\app\Estudiant;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\ConceptoPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class GenerarRecargosMorosidad extends Command
{
    protected $signature = 'recargos:generar
                            {--debug : Muestra información detallada de cada paso}
                            {--dry-run : Simula la ejecución sin guardar cambios en la base de datos}';
    // php artisan recargos:generar [Ejecuta normalmente, genera recargos y guarda en la base de datos.]
    // php artisan recargos:generar --debug [Ejecuta con trazas detalladas por estudiante y cuota.]
    // php artisan recargos:generar --dry-run [Simula la ejecución completa, no guarda nada (rollback). Ideal para verificar antes de ejecutar en producción.]
    // php artisan recargos:generar --debug --dry-run [Combina ambas opciones: simula y muestra el detalle paso a paso.]

    protected $description = 'Genera recargos por morosidad en cuotas vencidas de los estudiantes (solo crea nuevos, no actualiza)';

    public function handle()
    {
        $hoy = Carbon::now()->format('Y-m-d');
        $isDryRun = $this->option('dry-run');

        $this->info("==== Inicio del proceso de recargos por morosidad - {$hoy} ====");
        if ($isDryRun) {
            $this->warn("⚠️  Modo DRY-RUN activado: no se guardarán cambios en la base de datos.");
        }

        DB::beginTransaction();

        try {
            $estudiants = Estudiant::getEstudiantsFormalyAll();

            $totalProcesados = 0;
            $totalRecargos = 0;
            $nuevos = 0;
            $omitidas = 0;

            foreach ($estudiants as $estudiant) {
                $exchange_ammount_expireBill = round($estudiant->exchange_ammount_expireBill);

                // Solo procesar estudiantes con deuda
                if ($exchange_ammount_expireBill <= 0) {
                    continue;
                }

                if ($this->option('debug')) {
                    $this->line("-> Procesando estudiante ID={$estudiant->id}, Nombre={$estudiant->full_name}");
                }

                // Obtener cuotas vencidas habilitadas para recargo
                $exchange_bills = $estudiant->exchange_late_payment_bills;

                foreach ($exchange_bills as $quotaRM) {
                    $totalProcesados++; // se cuenta toda cuota revisada

                    $ammount = round($quotaRM->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id), 2);

                    // Solo procesar cuotas con deuda significativa
                    if ($ammount <= 0.05) {
                        $omitidas++;
                        if ($this->option('debug')) {
                            $this->warn("   - Cuota {$quotaRM->id} ya pagada o sin deuda. Se omite.");
                        }
                        continue;
                    }

                    // Calcular meses de mora
                    $mesesMora = $this->calcularMesesMora($quotaRM, $hoy);
                    if ($mesesMora <= 0) {
                        $omitidas++;
                        if ($this->option('debug')) {
                            $this->warn("   - Cuota {$quotaRM->id} aún no aplica recargo.");
                        }
                        continue;
                    }

                    // Verificar concepto original
                    $conceptoOriginal = $quotaRM->conceptopagos()->first();
                    if (!$conceptoOriginal) {
                        $omitidas++;
                        $this->error("   - ERROR: La cuota {$quotaRM->id} no tiene ConceptoPago asociado.");
                        continue;
                    }

                    $montoOriginal = $conceptoOriginal->exchange_ammount;
                    if (!$montoOriginal || $montoOriginal <= 0) {
                        $omitidas++;
                        $this->error("   - ERROR: La cuota {$quotaRM->id} tiene monto inválido.");
                        continue;
                    }

                    // Verificar si ya existe recargo asociado a esta cuota y estudiante
                    $recargoExistente = Cuentaxpagar::withoutGlobalScopes()
                        ->where('estudiant_id', $estudiant->id)
                        ->where('quota_original_id', $quotaRM->id)
                        ->first();

                    if ($recargoExistente) {
                        $omitidas++;
                        if ($this->option('debug')) {
                            $this->line("   ⚠️ Cuota {$quotaRM->id} ya tiene recargo existente (ID={$recargoExistente->id}). Se omite.");
                        }
                        continue;
                    }

                    // Calcular monto del recargo (fijo del 1%)
                    $recargoTotal = $montoOriginal * 0.01;

                    // Crear nueva cuota de recargo
                    $quotaRecargo = new Cuentaxpagar();
                    $quotaRecargo->estudiant_id = $estudiant->id;
                    $quotaRecargo->quota_original_id = $quotaRM->id;
                    $quotaRecargo->planpago_id = $quotaRM->planpago_id;
                    $quotaRecargo->name = $quotaRM->name . ' RM1';
                    $quotaRecargo->type = 'INDIVIDUAL';
                    $quotaRecargo->date_expiration = $quotaRM->date_expiration;
                    $quotaRecargo->date_calendar_start = $quotaRM->date_calendar_start;
                    $quotaRecargo->date_calendar_end = $quotaRM->date_calendar_end;
                    $quotaRecargo->description = 'Recargo por Morosidad';
                    $quotaRecargo->observations = 'Recargo por Morosidad';
                    $quotaRecargo->status_inscription = false;
                    $quotaRecargo->status_bad = $quotaRM->status_bad;
                    $quotaRecargo->status_late_payment = true;
                    $quotaRecargo->enable_late_payment = false;

                    if (!$isDryRun) {
                        $quotaRecargo->save();
                    }

                    // Crear concepto de pago asociado al recargo
                    $conceptoRecargo = [
                        'cuentaxpagar_id' => $quotaRecargo->id ?? 0,
                        'quota_id' => $quotaRM->id,
                        'nom_concepto_pago_id' => $conceptoOriginal->nom_concepto_pago_id,
                        'concepto_description' => $conceptoOriginal->concepto_description . ' Recargo por Morosidad',
                        'concepto_observations' => $conceptoOriginal->concepto_observations . ' Recargo por Morosidad',
                        'concepto_ammount' => $recargoTotal,
                        'exchange_ammount' => $recargoTotal,
                        'status_modifiable' => 'false',
                        'status_discount' => 'false',
                        'status_active' => 'true',
                    ];

                    if (!$isDryRun) {
                        ConceptoPago::create($conceptoRecargo);
                    }

                    $nuevos++;
                    $totalRecargos++;

                    $this->info("   ✅ Recargo NUEVO -> Estudiante {$estudiant->id}, Cuota {$quotaRM->id}, MesesMora={$mesesMora}, Monto={$recargoTotal}");
                }
            }

            if ($isDryRun) {
                DB::rollBack();
                $this->warn("⚠️  DRY-RUN: todos los cambios fueron revertidos, no se modificó la base de datos.");
            } else {
                DB::commit();
                $this->info("💾 Cambios guardados correctamente.");
            }

            $this->info("==== Proceso finalizado con éxito ====");
            $this->info("Estudiantes procesados: {$estudiants->count()}");
            $this->info("Cuotas vencidas revisadas: {$totalProcesados}");
            $this->info("Recargos NUEVOS: {$nuevos}");
            $this->info("TOTAL Recargos procesados: {$totalRecargos}");
            $this->info("Cuotas omitidas: {$omitidas}");

        } catch (Exception $e) {
            DB::rollBack();
            $this->error("==== ERROR FATAL ====");
            $this->error($e->getMessage());
            if ($this->option('debug')) {
                $this->error($e->getTraceAsString());
            }
        }

        return 0;
    }

    /**
     * Calcula los meses de mora a partir de la fecha de inicio de mora.
     * Si no tiene fecha válida o aún no aplica, retorna 0.
     */
    private function calcularMesesMora($quotaRM, $hoy): int
    {
        if (empty($quotaRM->date_late_payment)) {
            if ($this->option('debug')) {
                $this->warn("   - Cuota {$quotaRM->id} no tiene date_late_payment, no aplica recargo.");
            }
            return 0;
        }

        try {
            $fechaInicioMora = Carbon::parse($quotaRM->date_calendar_start);
        } catch (Exception $e) {
            if ($this->option('debug')) {
                $this->error("   - ERROR: date_late_payment de la cuota {$quotaRM->id} no es una fecha válida. No aplica recargo.");
            }
            return 0;
        }

        if (Carbon::parse($hoy)->lt($fechaInicioMora)) {
            return 0;
        }

        $mesesMora = $fechaInicioMora->diffInMonths($hoy);
        return ($mesesMora <= 0) ? 1 : $mesesMora;
    }
}
