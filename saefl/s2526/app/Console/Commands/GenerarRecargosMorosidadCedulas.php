<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\app\Estudiant;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\ConceptoPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class GenerarRecargosMorosidadCedulas extends Command
{
    protected $signature = 'recargos:cedulas 
                            {--cedulas=* : Lista de cédulas separadas por espacio o coma} 
                            {--file= : Ruta a un archivo CSV con las cédulas}';

    protected $description = 'Genera recargos por morosidad solo para los estudiantes especificados por cédulas o archivo CSV';
    //php artisan recargos:cedulas --cedulas=312117992009,11920466440,11613618445
    //php artisan recargos:cedulas --file=/ruta/alumnos.csv

    public function handle()
    {
        $cedulas = $this->option('cedulas');
        $file = $this->option('file');

        // Normalizar cédulas si vienen separadas por comas
        if (!empty($cedulas) && count($cedulas) === 1 && str_contains($cedulas[0], ',')) {
            $cedulas = explode(',', $cedulas[0]);
        }

        // Leer cédulas desde CSV si se pasa el archivo
        if ($file) {
            if (! file_exists($file)) {
                $this->error("El archivo no existe: {$file}");
                return 1;
            }

            $this->info("Leyendo cédulas desde archivo CSV: {$file}");
            $csvCedulas = [];

            if (($handle = fopen($file, 'r')) !== false) {
                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    if (! empty($row[0]) && strtolower($row[0]) !== 'cedula') {
                        $csvCedulas[] = trim($row[0]);
                    }
                }
                fclose($handle);
            }

            $cedulas = array_merge($cedulas, $csvCedulas);
        }

        if (empty($cedulas)) {
            $this->error("Debe proporcionar cédulas con --cedulas o --file.");
            return 1;
        }

        $hoy = Carbon::now()->format('Y-m-d');
        $this->info("==== Inicio proceso recargos por morosidad - {$hoy} ====");

        $estudiantes = Estudiant::whereIn('ci_estudiant', $cedulas)->get();

        if ($estudiantes->isEmpty()) {
            $this->warn("No se encontraron estudiantes con las cédulas proporcionadas.");
            return 0;
        }

        //dd($cedulas,$estudiantes);

        DB::beginTransaction();
        try {
            $totalProcesados = 0;
            $totalRecargos = 0;
            $omitidas = 0;

            foreach ($estudiantes as $estudiant) {
                $this->line("-> Procesando estudiante ID={$estudiant->id}, Nombre={$estudiant->full_name}");

                $quotas = $estudiant->exchange_expire_bills
                ->where('enable_late_payment',true)
                ->where('date_expiration','>=','2025-09-01')
                ;

                foreach ($quotas as $quotaRM) {

                    $totalProcesados++;

                    $enable_late_payment = $quotaRM->enable_late_payment;
                    if ($enable_late_payment == false) {
                        $this->info("   - Cuota {$quotaRM->id} no habilitada para recargo. Se omite.");
                        $omitidas++;
                        continue;
                    }

                    // Verificar si ya existe una cuota de recargo para esta cuota original
                    $recargoExistente = Cuentaxpagar::where('estudiant_id', $estudiant->id)
                        ->where('quota_original_id', $quotaRM->id)
                        ->exists();

                    if ($recargoExistente) {
                        $this->info("   - Cuota {$quotaRM->id} ya tiene un recargo generado. Se omite.");
                        $omitidas++;
                        continue;
                    }

                    $ammount = round($quotaRM->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id), 2);
                    if ($ammount > 0.05) {
                        $this->info("   - Cuota {$quotaRM->id} ya pagada. Se omite.");
                        $omitidas++;
                        continue;
                    }

                    // Calcular meses de mora
                    $mesesMora = 0;
                    if (! empty($quotaRM->date_late_payment)) {
                        try {
                            $fechaInicioMora = Carbon::parse($quotaRM->date_late_payment);
                            if (! Carbon::parse($hoy)->lt($fechaInicioMora)) {
                                $mesesMora = $fechaInicioMora->diffInMonths($hoy);
                                $mesesMora = ($mesesMora <= 0) ? 1 : $mesesMora;
                            }
                        } catch (Exception $e) {
                            $this->error("   - ERROR: date_late_payment inválida en cuota {$quotaRM->id}");
                            $omitidas++;
                            continue;
                        }
                    }

                    if ($mesesMora <= 0) {
                        $this->warn("   - Cuota {$quotaRM->id} aún no aplica recargo.");
                        $omitidas++;
                        continue;
                    }

                    // Concepto original
                    $conceptoOriginal = $quotaRM->conceptopagos()->first();
                    if (! $conceptoOriginal) {
                        $this->error("   - ERROR: Cuota {$quotaRM->id} sin ConceptoPago asociado.");
                        $omitidas++;
                        continue;
                    }

                    $montoOriginal = $conceptoOriginal->exchange_ammount;
                    if (! $montoOriginal || $montoOriginal <= 0) {
                        $this->error("   - ERROR: Cuota {$quotaRM->id} con monto inválido.");
                        $omitidas++;
                        continue;
                    }

                    $montoOriginal = $conceptoOriginal->exchange_ammount;
                    if ($montoOriginal <= 0) {
                        $this->info("   - Info: Cuota {$quotaRM->id} con monto igual a cero.");
                        $omitidas++;
                        continue;
                    }

                    // Calcular recargo
                    $recargoTotal = min($mesesMora, 12) * ($montoOriginal * 0.01);

                    // Crear o actualizar cuota de recargo (única por cuota original)
                    $quotaObj = Cuentaxpagar::updateOrCreate(
                        [
                            'estudiant_id'       => $estudiant->id,
                            'planpago_id'        => $quotaRM->planpago_id,
                            'quota_original_id'  => $quotaRM->id, // 👈 clave para unicidad
                        ],
                        [
                            'name' => $quotaRM->name . ' RM1',
                            'type' => 'INDIVIDUAL',
                            'date_expiration' => $quotaRM->date_expiration,
                            'date_calendar_start' => $quotaRM->date_expiration,
                            'date_calendar_end' => $quotaRM->date_expiration,
                            'description' => 'Recargo por Morosidad',
                            'observations' => 'Recargo por Morosidad',
                            'status_inscription' => false,
                            'status_bad' => $quotaRM->status_bad,
                            'status_late_payment' => true,
                            'enable_late_payment' => false,
                        ]
                    );

                    // Crear o actualizar concepto de recargo
                    ConceptoPago::updateOrCreate(
                        [
                            'cuentaxpagar_id' => $quotaObj->id,
                            'quota_id' => $quotaRM->id,
                        ],
                        [
                            'nom_concepto_pago_id' => $conceptoOriginal->nom_concepto_pago_id,
                            'concepto_description' => $conceptoOriginal->concepto_description . ' Recargo por Morosidad',
                            'concepto_observations' => $conceptoOriginal->concepto_observations . ' Recargo por Morosidad',
                            'concepto_ammount' => $recargoTotal,
                            'exchange_ammount' => $recargoTotal,
                            'status_modifiable' => 'false',
                            'status_discount' => 'false',
                            'status_active' => 'true',
                        ]
                    );

                    $totalRecargos++;
                    $this->info("   ✓ Recargo aplicado -> Estudiante {$estudiant->id}, Cuota {$quotaRM->id}, MesesMora={$mesesMora}, Monto={$recargoTotal}");
                }
            }

            DB::commit();

            $this->info("==== Proceso finalizado ====");
            $this->info("Estudiantes procesados: {$estudiantes->count()}");
            $this->info("Cuotas vencidas revisadas: {$totalProcesados}");
            $this->info("Recargos generados/actualizados: {$totalRecargos}");
            $this->info("Cuotas omitidas: {$omitidas}");

        } catch (Exception $e) {
            DB::rollBack();
            $this->error("==== ERROR FATAL ====");
            $this->error($e->getMessage());
        }

        return 0;
    }
}
