<?php

namespace App\Services\Planning;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lapso;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Deriva el rango candidato y las semanas de un plan de clases.
 *
 * La barra de filtros de actividades no expone fechas libres: el rango sale
 * de las fechas válidas de las actividades del universo y, como respaldo, del
 * lapso. Sobre ese rango se generan semanas lunes–domingo.
 */
class DidacticPlanPeriodResolver
{
    /**
     * @param  iterable<int, Activity>  $activities
     * @return array{start: ?string, end: ?string, origin: ?string}
     */
    public function resolveRange(iterable $activities, ?Lapso $lapso = null): array
    {
        $lapsoStart = $this->normalizeDate($lapso?->finicial);
        $lapsoEnd = $this->normalizeDate($lapso?->ffinal);

        $dates = [];

        foreach ($activities as $activity) {
            foreach (['finicial', 'ffinal'] as $field) {
                $date = $this->normalizeDate($activity->{$field} ?? null);

                if ($date === null) {
                    continue;
                }
                if ($lapsoStart !== null && $date < $lapsoStart) {
                    continue;
                }
                if ($lapsoEnd !== null && $date > $lapsoEnd) {
                    continue;
                }

                $dates[] = $date;
            }
        }

        if ($dates !== []) {
            sort($dates);

            return [
                'start' => $dates[0],
                'end' => $dates[count($dates) - 1],
                'origin' => 'activities',
            ];
        }

        if ($lapsoStart !== null && $lapsoEnd !== null) {
            return [
                'start' => $lapsoStart,
                'end' => $lapsoEnd,
                'origin' => 'lapso',
            ];
        }

        return ['start' => null, 'end' => null, 'origin' => null];
    }

    /**
     * Genera semanas lunes–domingo dentro del rango. La primera y la última
     * pueden estar cortadas.
     *
     * @return array<int, array{period_start: string, period_end: string, days: int}>
     */
    public function weeks(?string $rangeStart, ?string $rangeEnd): array
    {
        $start = $this->normalizeDate($rangeStart);
        $end = $this->normalizeDate($rangeEnd);

        if ($start === null || $end === null || $start > $end) {
            return [];
        }

        $cursor = Carbon::parse($start)->startOfDay();
        $limit = Carbon::parse($end)->startOfDay();
        $weeks = [];

        while ($cursor->lessThanOrEqualTo($limit)) {
            $monday = $cursor->copy()->startOfWeek(Carbon::MONDAY);
            $sunday = $monday->copy()->endOfWeek(Carbon::SUNDAY);

            $weekStart = $monday->lessThan($cursor) ? $cursor->copy() : $monday;
            $weekEnd = $sunday->greaterThan($limit) ? $limit->copy() : $sunday;

            $weeks[] = [
                'period_start' => $weekStart->toDateString(),
                'period_end' => $weekEnd->toDateString(),
                'days' => $weekStart->diffInDays($weekEnd) + 1,
            ];

            $cursor = $weekEnd->copy()->addDay()->startOfDay();
        }

        return $weeks;
    }

    public function isWithinWeek(mixed $date, string $weekStart, string $weekEnd): bool
    {
        $normalized = $this->normalizeDate($date);

        return $normalized !== null
            && $normalized >= $weekStart
            && $normalized <= $weekEnd;
    }

    /**
     * @return array<int, string> Errores de validación; vacío si la semana es válida.
     */
    public function validateWeek(mixed $start, mixed $end): array
    {
        $start = $this->normalizeDate($start);
        $end = $this->normalizeDate($end);

        if ($start === null || $end === null) {
            return ['La semana debe tener fecha de inicio y fin.'];
        }
        if ($end < $start) {
            return ['La fecha de fin no puede ser anterior a la de inicio.'];
        }

        $days = Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1;

        if ($days > 7) {
            return ['La semana no puede superar 7 días.'];
        }

        return [];
    }

    public function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            $date = Carbon::instance($value)->toDateString();

            return $this->isPlausibleDate($date) ? $date : null;
        }

        $text = trim((string) $value);

        if ($text === '' || str_starts_with($text, '0000-00-00')) {
            return null;
        }

        try {
            $date = Carbon::parse($text)->toDateString();
        } catch (\Throwable) {
            return null;
        }

        return $this->isPlausibleDate($date) ? $date : null;
    }

    private function isPlausibleDate(string $date): bool
    {
        if (! preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches)) {
            return false;
        }

        return (int) $matches[1] >= 1900;
    }

    /**
     * @param  iterable<int, Activity>  $activities
     * @return Collection<int, Activity>
     */
    public function activitiesInWeek(iterable $activities, string $weekStart, string $weekEnd): Collection
    {
        return collect($activities)->filter(function (Activity $activity) use ($weekStart, $weekEnd): bool {
            $start = $this->normalizeDate($activity->finicial ?? null);
            $end = $this->normalizeDate($activity->ffinal ?? null) ?? $start;

            if ($start === null) {
                return false;
            }

            return $start <= $weekEnd && $end >= $weekStart;
        })->values();
    }
}
