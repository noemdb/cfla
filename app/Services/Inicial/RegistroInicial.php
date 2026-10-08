<?php

namespace App\Services\Inicial;

use App\Models\app\Inicial\Eievaluationk;
use App\Models\app\Inicial\Eifinalk;
use App\Models\app\Inicial\Eiplanningbwk;
use App\Models\app\Inicial\Eiplanningwk;
use App\Models\app\Inicial\Eiprojectk;
use App\Models\app\Inicial\Eispecialk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Catálogo de los 6 documentos del módulo de Educación Inicial y de lo que cada
 * perspectiva necesita saber de ellos.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * POR QUÉ EXISTE
 * ─────────────────────────────────────────────────────────────────────────────
 * Las tres perspectivas (Evaluación, Planificación, Académico) exponen las
 * MISMAS rutas para los mismos 6 documentos: `/{entidad}/{id}` y
 * `/{entidad}/{id}/format`. El legacy resolvió eso con tres controladores que
 * repetían, para cada documento, su modelo, sus relaciones, su vista de detalle
 * y su formato: `Evaluacion/Tab/InicialController.php` (630 líneas), más los de
 * Planning (114) y Académico (69), con el bug ya documentado de los botones
 * quincenales que apuntaban al formato semanal.
 *
 * Aquí el catálogo está en UN sitio y los tres controladores lo consultan. Si
 * mañana se añade un documento, se añade una fila y las tres perspectivas lo
 * obtienen sin tocar código.
 *
 * Cada fila declara:
 *  · `modelo`     clase Eloquent (F0).
 *  · `relaciones` relaciones a eager-load: sin ellas, la tabla de la
 *                 perspectiva dispara N+1 en cada fila.
 *  · `detalle`    parcial de SOLO LECTURA reutilizado de las vistas del docente
 *                 (F3), para que revisión y docente no puedan divergir.
 *  · `formato`    vista imprimible (F4), la misma para las 4 perspectivas: es el
 *                 mismo papel, quien lo autoriza es lo único que cambia.
 *  · `revision`   campo que la Coordinación de Evaluación puede escribir
 *                 (`null` en los documentos que no admiten revisión).
 *  · `titulo`     etiqueta legible para las pestañas.
 */
final class RegistroInicial
{
    /**
     * @var array<string, array{modelo: class-string<Model>, relaciones: array<int,string>, detalle: ?string, formato: string, revision: ?string, titulo: string, variable: string}>
     */
    private const DOCUMENTOS = [
        'eiplanningwks' => [
            'modelo' => Eiplanningwk::class,
            'relaciones' => ['grado', 'seccion', 'profesor', 'eiplanningwsummaries', 'eiplanningwstrategies'],
            'detalle' => 'livewire.inicial.eiplanningwk.partials.plan-details',
            'formato' => 'inicial.eiplanningwk.format',
            'revision' => 'observacion',
            'titulo' => 'Planificación semanal',
            'variable' => 'eiplanningwk',
        ],
        'eiplanningbwks' => [
            'modelo' => Eiplanningbwk::class,
            'relaciones' => ['grado', 'seccion', 'profesor', 'eiplanningbwsummaries', 'eiplanningbwstrategies', 'eiprojectk'],
            'detalle' => 'livewire.inicial.eiplanningbwk.partials.plan-details',
            'formato' => 'inicial.eiplanningbwk.format',
            'revision' => 'observacion',
            'titulo' => 'Planificación quincenal',
            'variable' => 'eiplanningbwk',
        ],
        'eiprojectks' => [
            'modelo' => Eiprojectk::class,
            'relaciones' => ['grado', 'seccion', 'profesor', 'eiprojectsummaries', 'eiprojectkstrategies', 'eiprojectreviews'],
            'detalle' => 'livewire.inicial.eiprojectk.partials.plan-details',
            'formato' => 'inicial.eiprojectk.format',
            'revision' => 'observacion',
            'titulo' => 'Proyecto de aula',
            'variable' => 'eiprojectk',
        ],
        'eispecialks' => [
            'modelo' => Eispecialk::class,
            'relaciones' => ['grado', 'seccion', 'profesor', 'activities', 'eispecialstrategies'],
            'detalle' => 'livewire.inicial.eispecialk.partials.plan-details',
            'formato' => 'inicial.eispecialk.format',
            'revision' => 'observacion',
            'titulo' => 'Plan especial',
            'variable' => 'eispecialk',
        ],
        'eievaluationks' => [
            'modelo' => Eievaluationk::class,
            'relaciones' => ['grado', 'seccion', 'profesor', 'lapso', 'eievaluationps'],
            'detalle' => 'livewire.inicial.eievaluationk.partials.plan-details',
            'formato' => 'inicial.eievaluationk.format',
            // El plan de evaluación se revisa con `recomendacion`, no con
            // `observacion`: es el único documento con columna propia.
            'revision' => 'recomendacion',
            'titulo' => 'Plan de evaluación',
            'variable' => 'eievaluationk',
        ],
        'eifinalks' => [
            'modelo' => Eifinalk::class,
            'relaciones' => ['pevaluacion.profesor', 'pevaluacion.lapso', 'pevaluacion.seccion', 'expectant', 'expectations.area'],
            'detalle' => null,
            'formato' => 'inicial.eifinalk.format',
            // El informe final es el ÚNICO documento sin campo de revisión: se
            // emite firmado por la docente y el boletín lo archiva tal cual.
            'revision' => null,
            'titulo' => 'Informe final',
            'variable' => 'eifinalk',
        ],
    ];

    /**
     * Claves del catálogo.
     *
     * @return array<int, string>
     */
    public static function claves(): array
    {
        return array_keys(self::DOCUMENTOS);
    }

    /** @return array<string, array<string, mixed>> */
    public static function todos(): array
    {
        return self::DOCUMENTOS;
    }

    /**
     * Fila del catálogo.
     *
     * @param  string  $entidad  clave `ei*` de la ruta.
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException si la entidad no existe: eso solo puede
     *                                   pasar por una ruta mal escrita, y es un
     *                                   error de programación, no de datos.
     */
    public static function fila(string $entidad): array
    {
        if (! isset(self::DOCUMENTOS[$entidad])) {
            throw new \InvalidArgumentException("Documento de Educación Inicial desconocido: [{$entidad}].");
        }

        return self::DOCUMENTOS[$entidad];
    }

    public static function existe(string $entidad): bool
    {
        return isset(self::DOCUMENTOS[$entidad]);
    }

    /** @return class-string<Model> */
    public static function modelo(string $entidad): string
    {
        return self::fila($entidad)['modelo'];
    }

    /** @return array<int, string> */
    public static function relaciones(string $entidad): array
    {
        return self::fila($entidad)['relaciones'];
    }

    public static function parcialDetalle(string $entidad): ?string
    {
        return self::fila($entidad)['detalle'];
    }

    /** Variable con la que la vista de formato recibe el documento (singular). */
    public static function variable(string $entidad): string
    {
        return self::fila($entidad)['variable'];
    }

    public static function vistaFormato(string $entidad): string
    {
        return self::fila($entidad)['formato'];
    }

    /** Campo que la Coordinación puede escribir, o `null`. */
    public static function campoRevision(string $entidad): ?string
    {
        return self::fila($entidad)['revision'];
    }

    /**
     * Rótulo del campo de revisión, para las etiquetas de la interfaz.
     */
    public static function rotuloRevision(string $entidad): string
    {
        return self::campoRevision($entidad) === 'recomendacion'
            ? 'Recomendación'
            : 'Observación';
    }

    /**
     * Columnas de la tabla de revisión: `[etiqueta, callable]`.
     *
     * ─────────────────────────────────────────────────────────────────────────────
     * POR QUÉ VIVEN AQUÍ Y NO EN LOS COMPONENTES
     * ─────────────────────────────────────────────────────────────────────────────
     * Son una propiedad del DOCUMENTO, no de la perspectiva que lo muestra: la
     * tabla de Evaluación (Livewire) y la de Planificación (Blade, servidor) de
     * esta misma serie deben enseñar las mismas cosas en el mismo orden. Definir
     * las columnas en un único sitio hace imposible que se desincronicen, y
     * `Eifaxis…` deja de ser una lista de columnas repetida cinco veces.
     *
     * Se devuelve desde un método y no desde una `const` porque PHP no admite
     * closures en constantes de clase.
     *
     * @return array<int, array{0: string, 1: callable}>
     */
    public static function columnas(string $entidad): array
    {
        return match ($entidad) {
            'eiplanningwks' => [
                ['N°', fn ($plan) => $plan->id],
                ['F. inicial', fn ($plan) => $plan->finicial?->format('d/m/Y')],
                ['F. final', fn ($plan) => $plan->ffinal?->format('d/m/Y')],
                ['Grupo', fn ($plan) => trim(($plan->grado?->name ?? '—').' · '.($plan->seccion?->name ?? '—'))],
                ['Docente', fn ($plan) => $plan->profesor?->full_name],
                ['T. resumen', fn ($plan) => $plan->eiplanningwsummaries->count()],
                ['Estrategias', fn ($plan) => $plan->eiplanningwstrategies->count()],
                ['Diagnóstico', fn ($plan) => str($plan->diagnostico)->limit(80)],
            ],
            'eiplanningbwks' => [
                ['N°', fn ($plan) => $plan->id],
                ['F. inicial', fn ($plan) => $plan->finicial?->format('d/m/Y')],
                ['F. final', fn ($plan) => $plan->ffinal?->format('d/m/Y')],
                ['Grupo', fn ($plan) => trim(($plan->grado?->name ?? '—').' · '.($plan->seccion?->name ?? '—'))],
                ['Docente', fn ($plan) => $plan->profesor?->full_name],
                ['Proyecto', fn ($plan) => $plan->eiprojectk?->id],
                ['T. resumen', fn ($plan) => $plan->eiplanningbwsummaries->count()],
                ['Diagnóstico', fn ($plan) => str($plan->diagnostico)->limit(80)],
            ],
            'eiprojectks' => [
                ['N°', fn ($proyecto) => $proyecto->id],
                ['F. inicial', fn ($proyecto) => $proyecto->finicial?->format('d/m/Y')],
                ['F. final', fn ($proyecto) => $proyecto->ffinal?->format('d/m/Y')],
                ['Tiempo', fn ($proyecto) => $proyecto->tiempo_ejecucion],
                ['Grupo', fn ($proyecto) => trim(($proyecto->grado?->name ?? '—').' · '.($proyecto->seccion?->name ?? '—'))],
                ['Docente', fn ($proyecto) => $proyecto->profesor?->full_name],
                ['Revisiones', fn ($proyecto) => $proyecto->eiprojectreviews->count()],
                ['Diagnóstico', fn ($proyecto) => str($proyecto->diagnostico)->limit(80)],
            ],
            'eispecialks' => [
                ['N°', fn ($plan) => $plan->id],
                ['F. inicial', fn ($plan) => $plan->finicial?->format('d/m/Y')],
                ['F. final', fn ($plan) => $plan->ffinal?->format('d/m/Y')],
                ['Grupo', fn ($plan) => trim(($plan->grado?->name ?? '—').' · '.($plan->seccion?->name ?? '—'))],
                ['Docente', fn ($plan) => $plan->profesor?->full_name],
                ['Actividades', fn ($plan) => $plan->activities->count()],
                ['Justificación', fn ($plan) => str($plan->justificacion)->limit(80)],
            ],
            'eievaluationks' => [
                ['N°', fn ($plan) => $plan->id],
                ['F. inicial', fn ($plan) => $plan->finicial?->format('d/m/Y')],
                ['F. final', fn ($plan) => $plan->ffinal?->format('d/m/Y')],
                ['Lapso', fn ($plan) => $plan->lapso?->name],
                ['Grupo', fn ($plan) => trim(($plan->grado?->name ?? '—').' · '.($plan->seccion?->name ?? '—'))],
                ['Docente', fn ($plan) => $plan->profesor?->full_name],
                ['Posiciones', fn ($plan) => $plan->eievaluationps->count()],
                ['Asistencia', fn ($plan) => $plan->asistencia],
            ],
            'eifinalks' => [
                ['Estudiante', fn ($informe) => $informe->expectant?->full_name],
                ['Cédula', fn ($informe) => $informe->expectant?->ci_estudiant],
                ['Docente', fn ($informe) => $informe->pevaluacion?->profesor?->full_name],
                ['Grupo', fn ($informe) => trim(
                    ($informe->pevaluacion?->seccion?->name ?? '—').' · '.($informe->pevaluacion?->lapso?->name ?? '—')
                )],
                // El total lo calcula el componente de Evaluación al agrupar;
                // en otras perspectivas el atributo no existe y sale «—».
                ['Informes', fn ($informe) => $informe->informes_total],
                ['Título', fn ($informe) => str($informe->title)->limit(60)],
            ],
            default => throw new \InvalidArgumentException("Documento de Educación Inicial desconocido: [{$entidad}]."),
        };
    }

    /**
     * Consulta de la tabla de revisión con los filtros de la URL aplicados.
     *
     * @param  array<string, int|null>  $filtros  `profesor_id`, `grado_id`,
     *                                            `seccion_id` (los `null` se
     *                                            ignoran).
     * @return Collection<int, Model>
     */
    public static function documentos(string $entidad, array $filtros = [])
    {
        $query = self::consulta($entidad);

        // El informe final hereda profesor y sección de la carga académica: no
        // tiene esas columnas, y filtrarlas directamente sería un error de SQL.
        if ($entidad === 'eifinalks') {
            $query->whereHas('pevaluacion', function ($pevaluacion) use ($filtros) {
                if ($filtros['profesor_id'] ?? null) {
                    $pevaluacion->where('profesor_id', $filtros['profesor_id']);
                }

                if ($filtros['seccion_id'] ?? null) {
                    $pevaluacion->where('seccion_id', $filtros['seccion_id']);
                }
            });

            if ($filtros['grado_id'] ?? null) {
                $gradoId = $filtros['grado_id'];
                $query->whereHas('pevaluacion.pensum', fn ($pensum) => $pensum->where('grado_id', $gradoId));
            }

            return $query->latest('id')->get();
        }

        foreach (['profesor_id', 'grado_id', 'seccion_id'] as $columna) {
            if ($filtros[$columna] ?? null) {
                $query->where($columna, $filtros[$columna]);
            }
        }

        return $query->latest('id')->get();
    }

    public static function titulo(string $entidad): string
    {
        return self::fila($entidad)['titulo'];
    }

    /**
     * Base de la consulta de un documento, ya con sus relaciones cargadas.
     *
     * @return Builder<Model>
     */
    public static function consulta(string $entidad): Builder
    {
        /** @var class-string<Model> $modelo */
        $modelo = self::modelo($entidad);

        return $modelo::query()->with(self::relaciones($entidad));
    }

    /**
     * Busca un documento por id con sus relaciones.
     *
     * 404 (no 403) a propósito: un coordinador que pide el id 9999 y uno que pide
     * el id de un documento ajeno reciben la misma respuesta, así que la ruta no
     * sirve para confirmar qué ids existen. Las perspectivas son de solo lectura
     * sobre TODO el módulo, así que no hay propiedad que comprobar aquí.
     *
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public static function buscar(string $entidad, int $id): Model
    {
        return self::consulta($entidad)->whereKey($id)->firstOrFail();
    }
}
