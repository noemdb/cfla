<?php

namespace App\Http\Controllers\Evaluacion;

use App\Http\Controllers\Inicial\PerspectivaInicialController;
use App\Services\Inicial\EducationStatsService;
use App\Services\Inicial\PerspectivaFiltros;
use Illuminate\Http\Request;

/**
 * Perspectiva de Coordinación de Evaluación sobre los documentos de Inicial.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * NO ES SOLO LECTURA
 * ─────────────────────────────────────────────────────────────────────────────
 * Además de filtrar y ver, el coordinador escribe `observacion` en los cuatro
 * documentos de planificación y `recomendacion` en el plan de evaluación (regla
 * `min:5`). Esa frontera de escritura vive en
 * {@see \App\Livewire\Evaluacion\Inicial\EvaluacionDocumentComponent}, no aquí.
 *
 * `pevaluacion.status_official` separa los informes oficiales de los generados
 * por el componente, y esa marca se ve en el listado de informes finales.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * DIFERENCIA CON LAS OTRAS DOS PERSPECTIVAS
 * ─────────────────────────────────────────────────────────────────────────────
 * Esta es la ÚNICA con escritura y la ÚNICA con estadísticas, así que su
 * `index()` es propio; `show()` y `format()` los hereda de
 * {@see PerspectivaInicialController} igual que Planificación y Académico.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * FILTROS
 * ─────────────────────────────────────────────────────────────────────────────
 * GET clásico (`profesor_id`, `grado_id`, `seccion_id`), como en el legacy: son
 * de consulta, no de edición, y así el filtro sobrevive a un F5 y se puede
 * compartir por URL.
 *
 * El legacy leía `seccion_id` del request sin ofrecerlo en el formulario, de
 * modo que la sección era un filtro invisible; aquí los tres selects están y las
 * secciones dependen del grado elegido
 * ({@see PerspectivaFiltros::secciones()}).
 */
class InicialController extends PerspectivaInicialController
{
    protected string $prefijoRutas = 'evaluacions.inicials.';

    /**
     * Índice con los 6 documentos en pestañas y los indicadores de
     * planificación.
     */
    public function index(Request $request)
    {
        $filtros = new PerspectivaFiltros;

        // Se normalizan a `null` lo que llega vacío: un `?grado_id=` en blanco
        // debe significar "sin filtro", no "grado 0".
        $profesorId = $request->integer('profesor_id') ?: null;
        $gradoId = $request->integer('grado_id') ?: null;
        $seccionId = $request->integer('seccion_id') ?: null;

        $stats = new EducationStatsService;

        return view('evaluacion.inicial.index', [
            'listProfesores' => $filtros->profesores(),
            'listGrados' => $filtros->grados(),
            'listSecciones' => $filtros->secciones($gradoId),
            'listLapsos' => $filtros->lapsos(),
            'profesor_id' => $profesorId,
            'grado_id' => $gradoId,
            'seccion_id' => $seccionId,
            'stats' => $stats->getEducationStats($profesorId, $gradoId, $seccionId),
            'indicadores' => $stats->indicadores($profesorId, $gradoId, $seccionId),
        ]);
    }
}
