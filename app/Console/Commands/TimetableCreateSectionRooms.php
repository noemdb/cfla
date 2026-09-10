<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Seccion;
use App\Models\app\Timetable\TimetableRoom;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TimetableCreateSectionRooms extends Command
{
    protected $signature = 'timetable:create-section-rooms
        {--pestudio= : Limita la creación a un plan de estudio}
        {--capacity= : Capacidad por defecto cuando la sección no tiene cantidad de estudiantes}
        {--dry-run : Muestra las aulas que se crearían sin persistir}';

    protected $description = 'Crea un aula activa por cada sección activa, por ejemplo 1ER-GRADO-A';

    public function handle(): int
    {
        $capacity = max(1, (int) ($this->option('capacity') ?: 30));

        $sections = Seccion::query()
            ->where('seccions.status_active', 'true')
            ->when($this->option('pestudio'), fn ($query, $pestudioId) => $query->whereHas(
                'grado',
                fn ($grade) => $grade->where('grados.pestudio_id', (int) $pestudioId),
            ))
            ->whereHas('grado', fn ($grade) => $grade
                ->where('grados.status_active', 'true')
                ->whereHas('pestudio', fn ($pestudio) => $pestudio->where('pestudios.status_active', 'true')))
            ->with('grado')
            ->get()
            ->sortBy(fn (Seccion $section) => $this->sectionLabel($section))
            ->values();

        $created = 0;
        $skipped = 0;

        foreach ($sections as $section) {
            if (TimetableRoom::query()->where('seccion_id', $section->id)->exists()) {
                $skipped++;

                continue;
            }

            $label = $this->sectionLabel($section);
            $code = $this->uniqueCode($label, $section->id);
            $room = [
                'code' => $code,
                'name' => $label,
                'capacity' => (int) ($section->amount_student ?: $capacity),
                'type' => 'aula',
                'seccion_id' => $section->id,
                'status_active' => true,
            ];

            if (! $this->option('dry-run')) {
                TimetableRoom::create($room);
            }

            $created++;
            $this->line(($this->option('dry-run') ? '[dry-run] ' : '').$label.' · sección '.$section->id);
        }

        $this->info("Aulas ".($this->option('dry-run') ? 'a generar' : 'creadas').": {$created}. Omitidas por existir: {$skipped}.");

        return self::SUCCESS;
    }

    private function sectionLabel(Seccion $section): string
    {
        return Str::upper(Str::slug(trim(($section->grado?->name ?? 'GRADO').' '.$section->name), '-'));
    }

    private function uniqueCode(string $label, int $sectionId): string
    {
        $base = substr($label, 0, 20);

        if (! TimetableRoom::query()->where('code', $base)->exists()) {
            return $base;
        }

        return 'AULA-'.str_pad((string) $sectionId, 3, '0', STR_PAD_LEFT);
    }
}
