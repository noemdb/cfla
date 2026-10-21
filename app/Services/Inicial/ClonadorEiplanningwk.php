<?php

namespace App\Services\Inicial;

use App\Exceptions\SemanaLibreNoDisponibleException;
use App\Models\app\Inicial\Eiplanningwk;
use App\Models\app\Inicial\Eiplanningwstrategy;
use App\Models\app\Inicial\Eiplanningwsummary;
use Illuminate\Support\Facades\DB;

/**
 * Clona una planificación semanal completa.
 *
 * La cabecera, las estrategias y los resúmenes se copian dentro de una sola
 * transacción: si falla cualquier inserción no queda una copia a medias. La
 * copia se asigna SIEMPRE al docente autenticado, nunca al `profesor_id` del
 * plan de origen.
 */
class ClonadorEiplanningwk
{
    /**
     * Primera semana posterior libre para el mismo docente, nivel y sección.
     *
     * El clonado no puede conservar `finicial`: la restricción única de la
     * tabla impide dos planes para el mismo grupo en la misma semana. Se avanza
     * de siete en siete días hasta encontrar un hueco, conservando la duración
     * original (normalmente lunes a viernes).
     *
     * @return array{finicial: \Carbon\Carbon, ffinal: \Carbon\Carbon}
     */
    public function proximaSemanaLibre(Eiplanningwk $origen, int $profesorId): array
    {
        $inicio = $origen->finicial->copy()->startOfDay();
        $duracion = max(0, (int) $origen->finicial->diffInDays($origen->ffinal));

        // 260 semanas = cinco años. Es un tope de seguridad para no dejar el
        // request girando si los datos del grupo están corruptos.
        for ($intento = 0; $intento < 260; $intento++) {
            $inicio->addWeek();
            $fin = $inicio->copy()->addDays($duracion);

            $ocupada = Eiplanningwk::query()
                ->where('profesor_id', $profesorId)
                ->where('grado_id', $origen->grado_id)
                ->where('seccion_id', $origen->seccion_id)
                ->whereDate('finicial', $inicio->toDateString())
                ->exists();

            if (! $ocupada) {
                return ['finicial' => $inicio, 'ffinal' => $fin];
            }
        }

        throw new SemanaLibreNoDisponibleException(
            'No se encontró una semana libre para clonar este plan. Revisa las fechas del grupo.'
        );
    }

    /**
     * @return array{copia: Eiplanningwk, estrategias: int, resumenes: int}
     */
    public function clonar(Eiplanningwk $origen, int $profesorId): array
    {
        return DB::transaction(function () use ($origen, $profesorId) {
            $fechas = $this->proximaSemanaLibre($origen, $profesorId);

            $copia = Eiplanningwk::create([
                'profesor_id' => $profesorId,
                'grado_id' => $origen->grado_id,
                'seccion_id' => $origen->seccion_id,
                'pensum_id' => $origen->pensum_id,
                'eiprojectk_id' => $origen->eiprojectk_id,
                'finicial' => $fechas['finicial']->toDateString(),
                'ffinal' => $fechas['ffinal']->toDateString(),
                'tiempo_ejecucion' => $origen->tiempo_ejecucion,
                'diagnostico' => $origen->diagnostico,
                'observacion' => $origen->observacion,
            ]);

            $estrategias = 0;

            foreach ($origen->eiplanningwstrategies()->get() as $estrategia) {
                $data = $estrategia->only([
                    'day_of_week', 'momento_rutina_diaria',
                    'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'order',
                ]);
                $data['eiplanningwk_id'] = $copia->id;

                Eiplanningwstrategy::create($data);
                $estrategias++;
            }

            $resumenes = 0;

            foreach ($origen->eiplanningwsummaries()->get() as $resumen) {
                $data = $resumen->only([
                    'pevaluacion_id', 'componente', 'objetivo', 'aprendizaje_esperado',
                    'indicadores', 'linea_investigacion', 'enfasis_curriculares', 'order',
                ]);
                $data['eiplanningwk_id'] = $copia->id;

                Eiplanningwsummary::create($data);
                $resumenes++;
            }

            return compact('copia', 'estrategias', 'resumenes');
        });
    }
}
