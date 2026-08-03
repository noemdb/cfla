<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class FlowDiagramController extends Controller
{
    /**
     * Directorio donde viven las infografías de flujo (documentos estáticos).
     */
    private const DIAGRAMS_PATH = 'docs/infografia';

    /**
     * Hub: lista los diagramas de flujo disponibles.
     *
     * Cada archivo `flujo{Studly}.html` del directorio se publica como
     * `/app/planning/diagram/flow/{slug}`, donde {slug} = kebab(Studly).
     */
    public function index()
    {
        $diagrams = collect(glob(base_path(self::DIAGRAMS_PATH.'/flujo*.html')))
            ->map(fn (string $path): array => [
                'slug' => Str::kebab(Str::after(pathinfo($path, PATHINFO_FILENAME), 'flujo')),
                'path' => $path,
            ])
            ->filter(fn (array $diagram): bool => $diagram['slug'] !== '')
            ->sortBy('slug')
            ->values()
            ->map(fn (array $diagram): array => $this->describe($diagram['slug']))
            ->values();

        return view('planning.flow', compact('diagrams'));
    }

    /**
     * Sirve una infografía de flujo por slug.
     *
     * Ej.: /diagram/flow/activity-lesson → docs/infografia/flujoActivityLesson.html
     */
    public function show(string $diagram)
    {
        if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $diagram)) {
            abort(404);
        }

        $file = base_path(self::DIAGRAMS_PATH.'/flujo'.Str::studly($diagram).'.html');

        abort_unless(is_file($file), 404);

        return response()->file($file);
    }

    /**
     * Metadatos de presentación para cada diagrama.
     * Los diagramas desconocidos usan un título derivado del slug.
     */
    private function describe(string $slug): array
    {
        $known = [
            'activity-lesson' => [
                'title'       => 'Flujo de Actividad y Lección (LMS)',
                'description' => 'Recorrido completo de una actividad académica hasta convertirse en lección visible para los estudiantes: aprobación, programación y publicación.',
                'badge'       => 'Actividad → Lección',
            ],
            'consejo-directivo' => [
                'title'       => 'Informe al Consejo Directivo · CFLA 2026',
                'description' => 'Puntos presentados ante el Consejo Directivo: propuestas tecnológicas (IA y correo institucional), continuidad de SAEF 25-26, renovación del dominio web y nuevos proyectos de innovación con el fundamento metodológico de Marco Lógico.',
                'badge'       => 'Consejo Directivo · 2026',
            ],
        ];

        $fallback = [
            'title'       => 'Flujo: '.ucwords(str_replace('-', ' ', $slug)),
            'description' => 'Diagrama de flujo de referencia para la comunidad educativa.',
            'badge'       => 'Diagrama',
        ];

        return array_merge(['slug' => $slug], $known[$slug] ?? $fallback);
    }
}
