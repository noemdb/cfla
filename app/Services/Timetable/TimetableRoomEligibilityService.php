<?php

namespace App\Services\Timetable;

use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableRoom;
use Illuminate\Database\Eloquent\Collection;

final class TimetableRoomEligibilityService
{
    public function forCalendar(TimetableCalendar $calendar): Collection
    {
        return TimetableRoom::query()
            ->active()
            ->with('seccion.grado')
            ->get()
            ->filter(function (TimetableRoom $room) use ($calendar): bool {
                $roomPestudioId = $room->seccion?->grado?->pestudio_id;

                return ! $calendar->pestudio_id
                    || ! $roomPestudioId
                    || (int) $roomPestudioId === (int) $calendar->pestudio_id;
            })
            ->values();
    }

    /**
     * @return array<string, list<int>>
     */
    public function idsByType(TimetableCalendar $calendar): array
    {
        $byType = [];

        foreach ($this->forCalendar($calendar) as $room) {
            $byType[$room->type][] = (int) $room->id;
        }

        return $byType;
    }
}
