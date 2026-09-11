<?php

namespace App\Services\Timetable;

use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Academy\Pevaluacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class TimetableLessonPersistenceService
{
    /**
     * Persists the current wizard selection without deleting existing rows.
     * This lets the step 3 checkbox act as an edit scope, not a delete action.
     *
     * @param  array<int|string, array<string, mixed>>  $lessons
     */
    public function persist(int $calendarId, array $lessons): void
    {
        // Livewire puede rehidratar el payload indexado por pevaluacion_id.
        // Normalizarlo aquí evita confundir la clave de la fila con una
        // referencia académica ausente.
        $lessons = collect($lessons)
            ->map(function ($lesson, $key): array {
                $lesson = is_array($lesson) ? $lesson : [];
                $lesson['pev_id'] = (int) ($lesson['pev_id'] ?? $key);

                return $lesson;
            })
            ->all();

        $invalidPevaluacionRows = collect($lessons)
            ->map(fn ($lesson, $key) => [
                'key' => $key,
                'id' => (int) $lesson['pev_id'],
            ])
            ->filter(fn ($row) => $row['id'] <= 0);
        $pevaluacionIds = collect($lessons)
            ->map(fn ($lesson) => (int) $lesson['pev_id'])
            ->filter()
            ->unique()
            ->values();
        $pevaluaciones = Pevaluacion::query()
            ->whereIn('id', $pevaluacionIds)
            ->get(['id', 'seccion_id'])
            ->keyBy('id');
        $existingPevaluacionIds = $pevaluaciones->keys()->map(fn ($id) => (int) $id);
        $missingPevaluacionIds = $pevaluacionIds->diff($existingPevaluacionIds)->values();
        $sectionMismatches = collect($lessons)
            ->map(function (array $lesson, $key) use ($pevaluaciones): ?string {
                $pevaluacion = $pevaluaciones->get((int) $lesson['pev_id']);
                $lessonSectionId = (int) ($lesson['seccion_id'] ?? 0);

                if (! $pevaluacion || $lessonSectionId <= 0 || $lessonSectionId === (int) $pevaluacion->seccion_id) {
                    return null;
                }

                return "fila {$key}: sección {$lessonSectionId}, Pevaluacion #{$lesson['pev_id']} pertenece a la sección {$pevaluacion->seccion_id}";
            })
            ->filter()
            ->values();

        if ($invalidPevaluacionRows->isNotEmpty() || $missingPevaluacionIds->isNotEmpty() || $sectionMismatches->isNotEmpty()) {
            $invalidIds = $invalidPevaluacionRows
                ->map(fn ($row) => (string) $row['key'])
                ->values();
            $references = $missingPevaluacionIds
                ->map(fn ($id) => '#'.$id)
                ->merge($invalidIds->map(fn ($key) => "fila {$key} sin ID"))
                ->merge($sectionMismatches)
                ->implode(', ');

            throw ValidationException::withMessages([
                'lessons' => ($sectionMismatches->isNotEmpty() && $missingPevaluacionIds->isEmpty() && $invalidPevaluacionRows->isEmpty()
                    ? 'No se pueden registrar lessons con una sección distinta a su Pevaluacion: '
                    : 'No se pueden registrar lessons con Pevaluacion inexistente: ')
                    .$references.'.',
            ]);
        }

        DB::transaction(function () use ($calendarId, $lessons): void {
            foreach ($lessons as $lesson) {
                $pevaluacionId = (int) $lesson['pev_id'];
                if ($pevaluacionId <= 0) {
                    continue;
                }

                TimetableLesson::query()->updateOrCreate(
                    [
                        'calendar_id' => $calendarId,
                        'pevaluacion_id' => $pevaluacionId,
                    ],
                    [
                        'shift_id' => (int) ($lesson['shift_id'] ?? 0),
                        'weekly_blocks_t' => max(0, (int) ($lesson['weekly_blocks_t'] ?? 0)),
                        'weekly_blocks_p' => max(0, (int) ($lesson['weekly_blocks_p'] ?? 0)),
                        'room_type_required' => $lesson['room_type_required'] ?: null,
                        'is_half_group' => (bool) ($lesson['is_half_group'] ?? false),
                        'priority' => max(0, (int) ($lesson['priority'] ?? 0)),
                        'locked' => (bool) ($lesson['locked'] ?? false),
                    ],
                );
            }
        });
    }
}
