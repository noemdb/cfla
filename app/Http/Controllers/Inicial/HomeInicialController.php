<?php

namespace App\Http\Controllers\Inicial;

use App\Services\Inicial\RegistroInicial;
use Illuminate\Support\Facades\Auth;

/**
 * Portada del módulo de Educación Inicial para el docente.
 *
 * Rutas: `inicials.home` (dashboard del módulo) e `inicials.use-cases`
 * (los 7 casos de uso que el legacy exponía como documentación viva en
 * `/app/inicials/use-cases`).
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * POR QUÉ EXISTE UNA PORTADA Y NO SOLO UN MENÚ
 * ─────────────────────────────────────────────────────────────────────────────
 * El módulo son 6 documentos × 3 acciones (crear, revisar, imprimir) y el
 * legacy los ofrecía mediante una navbar lateral. Aquí el acceso se hace desde
 * las vistas que ya existen (una tarjeta por documento con su enlace y su
 * formato), que es el mismo criterio de "sin menús propios" del resto del
 * proyecto.
 *
 * La portada además responde la pregunta que el legacy no contestaba: **¿cuánto
 * he hecho?** Los seis contadores salen del mismo servicio que usan los
 * indicadores de la Coordinación ({@see \App\Services\Inicial\EducationStatsService}),
 * de modo que la cifra que ve el docente y la que ve la coordinación no pueden
 * discrepar.
 */
class HomeInicialController extends AbstractInicialController
{
    /**
     * Dashboard del módulo.
     */
    public function index()
    {
        $user = Auth::user();

        abort_if(! $user || (! $user->isInicial() && ! $user->is_admin), 403, 'Acceso denegado al módulo de Educación Inicial.');

        $stats = new \App\Services\Inicial\EducationStatsService;

        // Contexto del docente para la navbar-info (nombre, lapso activo), igual
        // que hace el home del profesor. `is_docente` separa al docente del
        // admin/coordinador que llega a esta portada.
        $profesor = $user->profesor;
        $lapsoActivo = \App\Models\app\Academy\Lapso::current();

        return view('inicial.home', [
            'documentos' => array_map(
                fn (string $clave) => [
                    'clave' => $clave,
                    'titulo' => RegistroInicial::titulo($clave),
                    'ruta' => route('inicials.'.$clave.'.index'),
                    // Color del borde superior de la tarjeta: cada documento se
                    // distingue como los grupos del dashboard del profesor.
                    'color' => match ($clave) {
                        'eiplanningwks' => 'cyan',
                        'eiplanningbwks' => 'sky',
                        'eiprojectks' => 'indigo',
                        'eispecialks' => 'rose',
                        'eievaluationks' => 'amber',
                        'eifinalks' => 'emerald',
                        default => 'gray',
                    },
                ],
                RegistroInicial::claves()
            ),
            'stats' => $stats->getEducationStats(),
            'profesor' => $profesor,
            'lapsoActivo' => $lapsoActivo,
            'esDocente' => ! $user->is_admin,
        ]);
    }

    /**
     * Los 7 casos de uso del módulo, como documentación viva.
     *
     * El legacy los duplicaba literalmente en los cuatro controladores de
     * perspectiva ({@see doc 01, §"duplicado literal"}). Aquí el contenido está
     * en UN sitio y cada perspectiva que lo muestre comparte estos datos.
     */
    public function useCases()
    {
        return view('inicial.use-cases', [
            'casos' => $this->casosDeUso(),
        ]);
    }

    /**
     * Perfil del docente dentro del módulo.
     *
     * Equivalente a `app.profesors.users.index`: la misma ficha (`profesors`
     * por `user_id`), pero con navegación del módulo (vuelve a
     * `inicials.home`, no al dashboard del profesor). Un usuario `is_inicial`
     * no es necesariamente `is_profesor`, así que sin ficha se muestra el
     * estado vacío en vez de reventar.
     */
    public function users()
    {
        $user = Auth::user();

        abort_if(! $user || (! $user->isInicial() && ! $user->is_admin), 403, 'Acceso denegado al módulo de Educación Inicial.');

        $profesor = \App\Models\app\Academy\Profesor::where('user_id', $user->id)->first();

        return view('inicial.users.index', compact('profesor'));
    }

    /**
     * Los 7 casos de uso del módulo.
     *
     * @return array<int, array{titulo: string, descripcion: string, icono: string, color: string}>
     */
    public function casosDeUso(): array
    {
        return [
            [
                'titulo' => 'Autenticación y acceso',
                'descripcion' => 'El docente de Educación Inicial entra al módulo con su credencial y ve solo sus documentos.',
                'icono' => 'lock',
                'color' => 'cyan',
            ],
            [
                'titulo' => 'Planificación semanal',
                'descripcion' => 'Crea el plan de la semana, completa la rejilla de estrategias por momento de la rutina diaria y lo reimprime.',
                'icono' => 'calendar',
                'color' => 'cyan',
            ],
            [
                'titulo' => 'Planificación quincenal',
                'descripcion' => 'Documento quincenal con sus propios resúmenes por área y su rejilla de estrategias.',
                'icono' => 'calendar-days',
                'color' => 'sky',
            ],
            [
                'titulo' => 'Proyectos de aula',
                'descripcion' => 'Proyecto con diagnóstico, revisión por área y estrategias; se puede encadenar con la planificación semanal.',
                'icono' => 'folder',
                'color' => 'indigo',
            ],
            [
                'titulo' => 'Planes de evaluación',
                'descripcion' => 'Evaluación por periodo y área, con las posiciones de cada niño y la recomendación de la Coordinación.',
                'icono' => 'clipboard-check',
                'color' => 'amber',
            ],
            [
                'titulo' => 'Planes especiales',
                'descripcion' => 'Atención a la diversidad: justificación, actividades con indicadores y estrategias propias.',
                'icono' => 'heart',
                'color' => 'rose',
            ],
            [
                'titulo' => 'Informes finales por estudiante',
                'descripcion' => 'Informe individual con las expectativas de aprendizaje del área marcadas, listo para el boletín.',
                'icono' => 'document-text',
                'color' => 'emerald',
            ],
        ];
    }
}
