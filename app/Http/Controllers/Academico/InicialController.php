<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Inicial\PerspectivaInicialController;
use App\Services\Inicial\PerspectivaFiltros;
use App\Services\Inicial\RegistroInicial;
use Illuminate\Http\Request;

/**
 * Perspectiva ACADÉMICA / DIRECCIÓN sobre los documentos de Educación Inicial.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * LA MÁS LIMITADA DEL MÓDULO
 * ─────────────────────────────────────────────────────────────────────────────
 * Dirección solo necesita ver el PLAN DE CLASE: la planificación semanal y el
 * proyecto de aula. Los otros cuatro documentos no se exponen —ni como
 * `show`/`format`— porque no son parte de su encargo. Es una decisión de alcance,
 * no una limitación técnica: las rutas de `routes/app/inicials.php` solo
 * declaran esas dos entidades para esta perspectiva.
 *
 * El legacy mostraba 6 pestañas con 4 deshabilitadas y un placeholder «Content
 * N», resultado de una plantilla mal rellenada. Aquí las 6 siguen visibles (para
 * que quede claro qué existe) pero las 4 sin acceso se explican EN UNA LÍNEA cada
 * una, diciendo dónde se revisan.
 *
 * `show()` y `format()` los hereda de {@see PerspectivaInicialController}.
 */
class InicialController extends PerspectivaInicialController
{
    protected string $prefijoRutas = 'academicos.inicials.';

    /**
     * Las dos entidades que Dirección puede consultar.
     *
     * @var array<int, string>
     */
    private const PESTANAS = [
        'eiplanningwks',
        'eiprojectks',
    ];

    /**
     * Las otras cuatro, con el motivo por el que no están aquí.
     *
     * Se declaran en el controlador y no en la vista para que el motivo viva
     * junto a la decisión de rutas que lo hace cumplir.
     *
     * @var array<string, string>
     */
    private const NO_DISPONIBLES = [
        'eiplanningbwks' => 'La planificación quincenal se revisa en Planificación.',
        'eispecialks' => 'Los planes especiales se revisan en Planificación.',
        'eievaluationks' => 'Los planes de evaluación se revisan en Coordinación de Evaluación.',
        'eifinalks' => 'Los informes finales se revisan en Coordinación de Evaluación.',
    ];

    public function index(Request $request)
    {
        $filtros = new PerspectivaFiltros;

        $profesorId = $request->integer('profesor_id') ?: null;
        $gradoId = $request->integer('grado_id') ?: null;
        $seccionId = $request->integer('seccion_id') ?: null;

        $pestana = $request->string('pestana')->toString();
        if (! in_array($pestana, self::PESTANAS, true)) {
            $pestana = self::PESTANAS[0];
        }

        return view('academico.inicial.index', [
            'listProfesores' => $filtros->profesores(),
            'listGrados' => $filtros->grados(),
            'listSecciones' => $filtros->secciones($gradoId),
            'profesor_id' => $profesorId,
            'grado_id' => $gradoId,
            'seccion_id' => $seccionId,
            'pestana' => $pestana,
            'pestanas' => self::PESTANAS,
            'noDisponibles' => self::NO_DISPONIBLES,
            'documentos' => RegistroInicial::documentos($pestana, [
                'profesor_id' => $profesorId,
                'grado_id' => $gradoId,
                'seccion_id' => $seccionId,
            ]),
        ]);
    }
}
