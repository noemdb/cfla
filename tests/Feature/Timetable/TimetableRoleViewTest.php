<?php

namespace Tests\Feature\Timetable;

use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Learner\Estudiant;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableShift;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * SPEC-TIMETABLE-001 §8/§9 (mejoras 3 y 4) — Vistas por rol:
 * estudiante ve su sección, docente sus slots, leadership/director lectura.
 */
class TimetableRoleViewTest extends TestCase
{
    use DatabaseTransactions;

    public function test_student_sees_only_own_section_timetable(): void
    {
        $fixture = $this->roleFixture();
        $student = User::factory()->create(['is_student' => true]);

        $representant = \App\Models\app\Learner\Representant::create([
            'user_id' => $student->id, 'ci_representant' => '5101', 'name' => 'María García',
            'status_active' => 'true',
        ]);

        $estudiant = Estudiant::create([
            'user_id' => $student->id,
            'name' => 'Luis',
            'lastname' => 'García',
            'ci_estudiant' => '5001',
            'representant_id' => $representant->id,
            'planpago_id' => $this->planpagoId(),
        ]);
        Inscripcion::create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $fixture['seccionA']->id,
            'tipo_id' => $this->tipoInscripcionId(),
            'programacion_id' => $this->programacionId(),
        ]);

        $this->actingAs($student)
            ->get('/app/estudiante/timetable')
            ->assertOk()
            ->assertSee('Mi horario')
            ->assertSee($fixture['asignaturaA']->name);
    }

    public function test_student_without_inscription_gets_404(): void
    {
        $this->roleFixture();
        $student = User::factory()->create(['is_student' => true]);

        $this->actingAs($student)
            ->get('/app/estudiante/timetable')
            ->assertNotFound();
    }

    public function test_teacher_sees_only_own_slots(): void
    {
        $fixture = $this->roleFixture();

        $this->actingAs($fixture['teacherUser'])
            ->get('/app/profesors/timetable')
            ->assertOk()
            ->assertSee('Mi horario')
            ->assertSee($fixture['asignaturaA']->name);
    }

    public function test_leadership_can_view_any_section_readonly(): void
    {
        $fixture = $this->roleFixture();
        $leader = User::factory()->create(['is_leadership' => true]);

        $this->actingAs($leader)
            ->get(route('app.leadership.timetable.view', ['seccion' => $fixture['seccionA']->id]))
            ->assertOk()
            ->assertSee('Sección')
            ->assertSee($fixture['asignaturaA']->name);
    }

    public function test_director_can_view_any_section_readonly(): void
    {
        $fixture = $this->roleFixture();
        $director = User::factory()->create(['is_director' => true]);

        $this->actingAs($director)
            ->get(route('app.director.timetable.view', ['seccion' => $fixture['seccionA']->id]))
            ->assertOk()
            ->assertSee('Sección')
            ->assertSee($fixture['asignaturaA']->name);
    }

    public function test_leadership_without_seccion_gets_404(): void
    {
        $this->roleFixture();
        $leader = User::factory()->create(['is_leadership' => true]);

        $this->actingAs($leader)
            ->get(route('app.leadership.timetable.view'))
            ->assertNotFound();
    }

    // ─── Fixtures ──────────────────────────────────────────────

    private function planpagoId(): int
    {
        $id = \DB::table('planpagos')->value('id');

        if ($id) {
            return (int) $id;
        }

        return (int) \DB::table('planpagos')->insertGetId(['name' => 'Plan test']);
    }

    private function tipoInscripcionId(): int
    {
        $id = \DB::table('tinscripcions')->value('id');

        if ($id) {
            return (int) $id;
        }

        return (int) \DB::table('tinscripcions')->insertGetId(['name' => 'Tipo test']);
    }

    private function programacionId(): int
    {
        $id = \DB::table('programacions')->value('id');

        if ($id) {
            return (int) $id;
        }

        return (int) \DB::table('programacions')->insertGetId(['name' => 'Programación test']);
    }

    private function roleFixture(): array
    {
        $teacherUser = User::factory()->create(['is_profesor' => true]);
        $profesor = Profesor::create([
            'user_id' => $teacherUser->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '6001', 'status_active' => 'true',
        ]);
        $pestudio = Pestudio::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id]);
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id]);

        $asignaturaA = Asignatura::factory()->create(['hour_t_week' => 3, 'hour_p_week' => 0]);
        $pensumA = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignaturaA->id,
        ]);

        // Lapso vigente: los lectores resuelven el activo del lapso actual.
        $lapso = Lapso::factory()->create([
            'finicial' => now()->subMonth(),
            'ffinal' => now()->addMonth(),
        ]);
        $pevA = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccionA->id,
            'pensum_id' => $pensumA->id, 'lapso_id' => $lapso->id,
        ]);

        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'status' => 'active']);
        $shift = TimetableShift::factory()->create();

        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);

        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevA->id, 'shift_id' => $shift->id,
            'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);

        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $period->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccionA->id,
            'room_id' => null,
        ]);

        return compact('calendar', 'shift', 'period', 'lesson', 'profesor', 'seccionA', 'seccionB', 'asignaturaA', 'teacherUser');
    }
}
