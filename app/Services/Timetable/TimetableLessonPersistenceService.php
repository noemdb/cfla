<?php

namespace App\Services\Timetable;

use App\Models\app\Timetable\TimetableLesson;
use Illuminate\Support\Facades\DB;

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
        DB::transaction(function () use ($calendarId, $lessons): void {
            foreach ($lessons as $lesson) {
                $pevaluacionId = (int) ($lesson['pev_id'] ?? 0);
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
