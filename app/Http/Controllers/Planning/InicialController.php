<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Inicial\PerspectivaInicialController;
use App\Services\Inicial\PerspectivaFiltros;
use App\Services\Inicial\RegistroInicial;
use Illuminate\Http\Request;

/**
 * Perspectiva de PLANIFICACIÓN sobre los documentos de Educación Inicial.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * SOLO LECTURA, Y SIN LIVEWIRE A PROPÓSITO
 * ─────────────────────────────────────────────────────────────────────────────
 * Planificación no escribe nada del módulo: consulta y compara. El legacy lo
 * resolvió con `@include` de tablas Blade renderizadas en servidor, y es lo
 * correcto: sin escritura no hay estado que sincronizar, así que un componente
 * Livewire solo añadiría round-trips para pintar filas.
 *
 * `show()` y `format()` los hereda de {@see PerspectivaInicialController}, igual
 * que Evaluación y Académico.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * UNA PESTAÑA POR CONSULTA, NO LAS CINCO
 * ─────────────────────────────────────────────────────────────────────────────
 * El legacy incluía las 6 tablas siempre, así que abrir la página ejecutaba 6
 * consultas para enseñar una. Aquí `?pestana=` elige cuál se consulta y solo se
 * carga esa: las demás pestañas se dibujan vacías hasta que se pulsan.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * PESTAÑAS
 * ─────────────────────────────────────────────────────────────────────────────
 * Cinco documentos reales y una entrada informativa. El legacy tenía ahí una
 * pestaña «Informe Pedagógico» cuyo contenido era la cadena «Sin formato.»: un
 * formato que nunca existió. Este módulo sí tiene informes finales
 * (`eifinalks`), pero viven en la perspectiva de Evaluación —que es quien los
 * revisa—, así que la pestaña lo dice en vez de fingir un botón vacío.
 */
class InicialController extends PerspectivaInicialController
{
    protected string $prefijoRutas = 'plannings.inicials.';

    /**
     * Pestañas reales de esta perspectiva.
     *
     * @var array<int, string>
     */
    private const PESTANAS = [
        'eiplanningwks',
        'eiplanningbwks',
        'eiprojectks',
        'eispecialks',
        'eievaluationks',
    ];

    public function index(Request $request)
    {
        $filtros = new PerspectivaFiltros;

        $profesorId = $request->integer('profesor_id') ?: null;
        $gradoId = $request->integer('grado_id') ?: null;
        $seccionId = $request->integer('seccion_id') ?: null;

        // Una pestaña desconocida cae en la primera: no se refleja un valor
        // arbitrario del request en la URL de los enlaces.
        $pestana = $request->string('pestana')->toString();
        if (! in_array($pestana, self::PESTANAS, true)) {
            $pestana = self::PESTANAS[0];
        }

        return view('planning.inicial.index', [
            'listProfesores' => $filtros->profesores(),
            'listGrados' => $filtros->grados(),
            'listSecciones' => $filtros->secciones($gradoId),
            'profesor_id' => $profesorId,
            'grado_id' => $gradoId,
            'seccion_id' => $seccionId,
            'pestana' => $pestana,
            'pestanas' => self::PESTANAS,
            // Solo se consulta la pestaña visible.
            'documentos' => RegistroInicial::documentos($pestana, [
                'profesor_id' => $profesorId,
                'grado_id' => $gradoId,
                'seccion_id' => $seccionId,
            ]),
        ]);
    }
}
