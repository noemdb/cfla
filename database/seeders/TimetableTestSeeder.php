<?php

namespace Database\Seeders;

use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableRoom;
use App\Models\app\Timetable\TimetableShift;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * SPEC-TIMETABLE-001 §18 / ticket 001i — Seeder de dataset sintético y
 * reproducible (seed fijo) para QA manual y para los tests del módulo.
 *
 * Genera: Pestudio/Grado/Secciones, Profesores (con su User), Asignaturas
 * con horas semanales realistas, Pensum, un Lapso, Pevaluaciones, y el
 * calendario completo (turno, períodos Lun–Vie, aulas, lecciones derivadas
 * de hour_t_week/hour_p_week). El catálogo está calibrado para que la carga
 * por sección (≤ 30 bloques) sea factible para el solver.
 *
 * Uso:
 *   php8.2 artisan db:seed --class=TimetableTestSeeder
 *   php8.2 artisan db:seed --class=TimetableTestSeeder -- --sections=2 --teachers=6
 */
class TimetableTestSeeder extends Seeder
{
    public int $sections = 3;

    public int $teachers = 8;

    public int $seed = 20260818;

    /**
     * Catálogo base de asignaturas: [nombre, hour_t_week, hour_p_week].
     * Con period_minutes=45 la carga por sección queda en 27 bloques ≤ 30.
     *
     * @var array<int, array{0: string, 1: int, 2: int}>
     */
    protected array $catalogo = [
        ['Matemática', 3, 0],
        ['Lengua y Literatura', 3, 0],
        ['Ciencias Naturales', 2, 1],
        ['Ciencias Sociales', 2, 0],
        ['Inglés', 2, 0],
        ['Educación Física', 1, 1],
        ['Arte y Patrimonio', 1, 0],
        ['Informática', 0, 1],
    ];

    /**
     * @return array<int, array{0: string, 1: int, 2: int}>
     */
    public function catalog(): array
    {
        return $this->catalogo;
    }

    public function run(): void
    {
        $faker = FakerFactory::create('es_VE');
        $faker->seed($this->seed);
        $faker->unique(true);

        DB::transaction(function () use ($faker) {
            [$pestudio, $grado] = $this->makeAcademicStructure();
            $secciones = $this->makeSections($grado);
            $profesores = $this->makeTeachers($faker);
            $asignaturas = $this->makeAsignaturas($pestudio);
            $pensums = $this->makePensums($pestudio, $grado, $asignaturas);
            $lapso = $this->makeLapso($faker);
            $pevs = $this->makePevaluaciones($lapso, $secciones, $pensums, $profesores);

            $calendar = $this->makeCalendar($lapso);
            $shift = $this->makeShiftAndPeriods($calendar);
            $this->makeRooms();
            $this->makeLessons($calendar, $shift, $pevs);

            $this->command?->info(sprintf(
                '[TimetableTestSeeder] calendario #%s · %s secciones · %s docentes · %s pevaluaciones · %s lecciones · %s períodos',
                $calendar->id,
                $secciones->count(),
                $profesores->count(),
                $pevs->count(),
                TimetableLesson::query()->where('calendar_id', $calendar->id)->count(),
                TimetablePeriod::query()->where('calendar_id', $calendar->id)->count(),
            ));
        });
    }

    /**
     * @return array{0: Pestudio, 1: Grado}
     */
    private function makeAcademicStructure(): array
    {
        $pestudio = Pestudio::factory()->create(['name' => 'Pestudio Timetable Test']);

        return [
            $pestudio,
            Grado::factory()->create([
                'pestudio_id' => $pestudio->id,
                'name' => 'Grado Timetable Test',
                'status_active' => 'true',
            ]),
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int, Seccion>
     */
    private function makeSections(Grado $grado): \Illuminate\Support\Collection
    {
        return collect(range(1, $this->sections))->map(
            fn (int $i) => Seccion::factory()->create([
                'grado_id' => $grado->id,
                'name' => "Sección {$i}",
                'status_active' => 'true',
            ])
        );
    }

    /**
     * @return \Illuminate\Support\Collection<int, Profesor>
     */
    private function makeTeachers($faker): \Illuminate\Support\Collection
    {
        return collect(range(1, $this->teachers))->map(function (int $i) use ($faker) {
            $user = User::factory()->create([
                'is_profesor' => true,
                'is_active' => 'enable',
            ]);

            return Profesor::create([
                'user_id' => $user->id,
                'name' => $faker->firstName(),
                'lastname' => $faker->lastName(),
                'ci_profesor' => (string) (90000000 + $i),
                'status_active' => 'true',
            ]);
        });
    }

    /**
     * @return \Illuminate\Support\Collection<int, Asignatura>
     */
    private function makeAsignaturas(Pestudio $pestudio): \Illuminate\Support\Collection
    {
        return collect($this->catalogo)->map(fn (array $a) => Asignatura::factory()->create([
            'pestudio_id' => $pestudio->id,
            'name' => $a[0],
            'hour_t_week' => $a[1],
            'hour_p_week' => $a[2],
        ]));
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Asignatura>  $asignaturas
     * @return \Illuminate\Support\Collection<int, Pensum>
     */
    private function makePensums(Pestudio $pestudio, Grado $grado, $asignaturas): \Illuminate\Support\Collection
    {
        return $asignaturas->map(fn (Asignatura $asig) => Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asig->id,
            'status_active' => true,
        ]));
    }

    private function makeLapso($faker): Lapso
    {
        return Lapso::factory()->create(['name' => 'Lapso Timetable '.$faker->year()]);
    }

    /**
     * Una pevaluación por (sección × pensum), docente en round-robin.
     *
     * @param  \Illuminate\Support\Collection<int, Seccion>  $secciones
     * @param  \Illuminate\Support\Collection<int, Pensum>  $pensums
     * @param  \Illuminate\Support\Collection<int, Profesor>  $profesores
     * @return \Illuminate\Support\Collection<int, Pevaluacion>
     */
    private function makePevaluaciones(Lapso $lapso, $secciones, $pensums, $profesores): \Illuminate\Support\Collection
    {
        $pevs = collect();
        $index = 0;

        foreach ($secciones as $seccion) {
            foreach ($pensums as $pensum) {
                $pevs->push(Pevaluacion::factory()->create([
                    'profesor_id' => $profesores[$index % $profesores->count()]->id,
                    'seccion_id' => $seccion->id,
                    'pensum_id' => $pensum->id,
                    'lapso_id' => $lapso->id,
                ]));
                $index++;
            }
        }

        return $pevs;
    }

    private function makeCalendar(Lapso $lapso): TimetableCalendar
    {
        return TimetableCalendar::create([
            'lapso_id' => $lapso->id,
            'name' => 'Horario '.$lapso->name,
            'period_minutes' => 45,
            'status' => TimetableCalendar::STATUS_DRAFT,
            'version' => 0,
        ]);
    }

    private function makeShiftAndPeriods(TimetableCalendar $calendar): TimetableShift
    {
        // El turno es un catálogo compartido (code único) → firstOrCreate.
        $shift = TimetableShift::query()->firstOrCreate(
            ['code' => TimetableShift::CODE_MORNING],
            [
                'name' => 'Mañana',
                'start_time' => '07:00:00',
                'end_time' => '12:15:00',
            ],
        );

        // Mismo algoritmo que el wizard (savePeriods): 5 días × 6 bloques.
        foreach ([1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie'] as $day => $label) {
            foreach (range(1, 6) as $order) {
                $offset = ($order - 1) * (int) $calendar->period_minutes;
                $start = strtotime('07:00');
                TimetablePeriod::create([
                    'calendar_id' => $calendar->id,
                    'shift_id' => $shift->id,
                    'day_of_week' => $day,
                    'order_in_day' => $order,
                    'start_time' => date('H:i', $start + $offset * 60),
                    'end_time' => date('H:i', $start + ($offset + (int) $calendar->period_minutes) * 60),
                    'is_break' => false,
                ]);
            }
        }

        return $shift;
    }

    private function makeRooms(): void
    {
        collect(range(1, 6))->each(fn () => TimetableRoom::factory()->create());          // aulas
        collect(range(1, 2))->each(fn () => TimetableRoom::factory()->laboratory()->create()); // laboratorios
    }

    /**
     * Lecciones derivadas de las pevaluaciones: bloques desde las horas de la
     * asignatura (misma regla de redondeo que el wizard) y tipo de aula para
     * bloques prácticos.
     *
     * @param  \Illuminate\Support\Collection<int, Pevaluacion>  $pevs
     */
    private function makeLessons(TimetableCalendar $calendar, TimetableShift $shift, $pevs): void
    {
        $minutes = (int) $calendar->period_minutes;

        $pevs->each(function (Pevaluacion $pev) use ($calendar, $shift, $minutes) {
            $asignatura = $pev->pensum?->asignatura;
            if (! $asignatura) {
                return;
            }

            $blocksT = (int) ceil(((int) $asignatura->hour_t_week) * 60 / $minutes);
            $blocksP = (int) ceil(((int) $asignatura->hour_p_week) * 60 / $minutes);

            if ($blocksT <= 0 && $blocksP <= 0) {
                return;
            }

            TimetableLesson::create([
                'calendar_id' => $calendar->id,
                'pevaluacion_id' => $pev->id,
                'shift_id' => $shift->id,
                'weekly_blocks_t' => max(1, $blocksT),
                'weekly_blocks_p' => $blocksP,
                'room_type_required' => $blocksP > 0 ? 'laboratorio' : null,
                'priority' => 0,
                'locked' => false,
            ]);
        });
    }
}
