<?php

namespace App\Services\Planning;

use Illuminate\Support\Str;

class FlowDiagramService
{
    /**
     * Directorio donde viven las infografías de flujo (documentos estáticos).
     */
    public const DIAGRAMS_PATH = 'docs/infografia';

    /**
     * Directorio adicional de diagramas de coexistencia/análisis
     * (slug directo: `{slug}.html`, sin prefijo `flujo`).
     */
    public const COEXISTENCE_PATH = 'docs/coexistencia';

    /**
     * Descubre todos los diagramas disponibles con sus metadatos de presentación.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function list(string $sortBy = 'order'): \Illuminate\Support\Collection
    {
        $infografia = collect(glob(base_path(self::DIAGRAMS_PATH.'/flujo*.html')))
            ->map(fn (string $path): array => [
                'slug' => Str::kebab(Str::after(pathinfo($path, PATHINFO_FILENAME), 'flujo')),
                'path' => $path,
            ]);

        $coexistencia = collect(glob(base_path(self::COEXISTENCE_PATH.'/*.html')))
            ->map(fn (string $path): array => [
                'slug' => Str::kebab(pathinfo($path, PATHINFO_FILENAME)),
                'path' => $path,
            ]);

        $diagrams = $infografia
            ->merge($coexistencia)
            ->filter(fn (array $diagram): bool => $diagram['slug'] !== '')
            ->unique('slug')
            ->values()
            ->map(function (array $diagram): array {
                $described = $this->describe($diagram['slug']);
                $mtime = is_file($diagram['path']) ? filemtime($diagram['path']) : null;
                $described['updated_at'] = $mtime ? date('Y-m-d', $mtime) : null;
                unset($described['path']);

                return $described;
            })
            ->pipe(fn ($collection) => $this->sort($collection, $sortBy))
            ->values();

        return $diagrams;
    }

    /**
     * Ordena una colección de diagramas según la clave solicitada.
     *
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $diagrams
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function sort($diagrams, string $sortBy = 'order')
    {
        return match ($sortBy) {
            'recent' => $diagrams->sortByDesc('updated_at'),
            'category' => $diagrams->sortBy([
                ['category', 'asc'],
                ['title', 'asc'],
            ]),
            default => $diagrams->sortBy('order'),
        };
    }

    /**
     * Resuelve la ruta absoluta de un diagrama por slug o null si no existe.
     *
     * Busca primero en `docs/infografia` (patrón `flujo{Studly}.html`) y luego
     * en `docs/coexistencia` (`{slug}.html`).
     */
    public function resolveFile(string $diagram): ?string
    {
        if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $diagram)) {
            return null;
        }

        $file = base_path(self::DIAGRAMS_PATH.'/flujo'.Str::studly($diagram).'.html');

        if (is_file($file)) {
            return $file;
        }

        $file = base_path(self::COEXISTENCE_PATH.'/'.$diagram.'.html');

        return is_file($file) ? $file : null;
    }

    /**
     * Metadatos de presentación para cada diagrama.
     * Los diagramas desconocidos usan un título derivado del slug.
     *
     * @return array<string, mixed>
     */
    public function describe(string $slug): array
    {
        $known = [
            'activity-lesson' => [
                'title' => 'Flujo de Actividad y Lección (LMS)',
                'description' => 'Recorrido completo de una actividad académica hasta convertirse en lección visible para los estudiantes: aprobación, programación y publicación.',
                'badge' => 'Actividad → Lección',
                'order' => 1,
                'accent' => 'cyan',
                'tags' => ['LMS', 'Actividad', 'Publicación'],
                'label' => 'Abrir el diagrama del flujo de actividad y lección LMS',
                'category' => 'LMS',
                'duration' => '5 min',
                'audience' => 'Docentes · Coordinación',
                'status' => 'actualizado',
            ],
            'activity-lesson-planning' => [
                'title' => 'Planificación en el Flujo Actividad / Lección',
                'description' => 'Casos de uso de Planning en el recorrido de una actividad académica: carga académica, aprobación de la actividad, monitorización LMS y publicación de la lección.',
                'badge' => 'Planning · Actividad → Lección',
                'order' => 3,
                'accent' => 'cyan',
                'tags' => ['Planning', 'LMS', 'Actores', 'Actividad', 'Lección'],
                'label' => 'Abrir el diagrama de casos de uso de Planning en el flujo Actividad-Lección',
                'category' => 'Planning',
                'duration' => '6 min',
                'audience' => 'Planificación · Coordinación',
                'status' => 'nuevo',
            ],
            'consejo-directivo' => [
                'title' => 'Informe al Consejo Directivo · CFLA 2026',
                'description' => 'Puntos presentados ante el Consejo Directivo: propuestas tecnológicas (IA y correo institucional), continuidad de SAEF 25-26, renovación del dominio web y nuevos proyectos de innovación con el fundamento metodológico de Marco Lógico.',
                'badge' => 'Consejo Directivo · 2026',
                'order' => 2,
                'accent' => 'emerald',
                'tags' => ['Consejo Directivo', '2026', 'Informe'],
                'label' => 'Abrir el informe al Consejo Directivo CFLA 2026',
                'category' => 'Informe',
                'duration' => '10 min',
                'audience' => 'Consejo Directivo',
                'status' => 'nuevo',
            ],
            'dualidad-aula-virtual' => [
                'title' => 'Dualidad de Funciones en el Aula Virtual',
                'description' => 'Mapa de solapamiento entre SAE (asistescolar.com) y el Módulo de Planificación: funciones exclusivas de cada sistema, la zona de dualidad del aula virtual y su resolución por audiencia canónica (estudiante, representante, docente).',
                'badge' => 'Coexistencia · SAE ↔ Planificación',
                'order' => 4,
                'accent' => 'amber',
                'tags' => ['Coexistencia', 'SAE', 'Aula Virtual', 'Análisis'],
                'label' => 'Abrir el diagrama de dualidad de funciones en el aula virtual',
                'category' => 'Coexistencia',
                'duration' => '5 min',
                'audience' => 'Planificación · Dirección',
                'status' => 'nuevo',
            ],
            'comparativo-aulas-virtuales' => [
                'title' => 'Comparativo de Aulas Virtuales — SAE vs. Planificación',
                'description' => 'Comparativa objetiva basada en hechos verificados: capacidades confirmadas, parciales o no evidenciadas de la capa e-learning de SAE WEB frente a las documentadas en el módulo de planificación (contenidos, tareas, gobernanza, publicación y comunicación).',
                'badge' => 'Coexistencia · Comparativo',
                'order' => 5,
                'accent' => 'amber',
                'tags' => ['Coexistencia', 'SAE', 'LMS', 'Comparativa'],
                'label' => 'Abrir el comparativo de aulas virtuales SAE versus módulo de planificación',
                'category' => 'Coexistencia',
                'duration' => '7 min',
                'audience' => 'Planificación · Dirección',
                'status' => 'nuevo',
            ],
        ];

        $fallback = [
            'title' => 'Flujo: '.ucwords(str_replace('-', ' ', $slug)),
            'description' => 'Diagrama de flujo de referencia para la comunidad educativa.',
            'badge' => 'Diagrama',
            'order' => 999,
            'accent' => 'cyan',
            'tags' => ['Diagrama'],
            'label' => 'Abrir el diagrama de flujo: '.str_replace('-', ' ', $slug),
            'category' => 'General',
            'duration' => null,
            'audience' => null,
            'status' => 'vigente',
        ];

        return array_merge(['slug' => $slug], $known[$slug] ?? $fallback);
    }
}
