<?php

namespace Tests\Feature\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard;
use App\Livewire\Planning\Timetable\TimetableWizard as PlanningTimetableWizard;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Peducativo;
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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * SPEC-TIMETABLE-001 §5 / §18 — Flujo del wizard de horario.
 */
class TimetableWizardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_coordinacion_can_access_wizard_page(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        $this->actingAs($user)
            ->get('/app/coordinacion/timetable')
            ->assertOk()
            ->assertSee('Horario Escolar');
    }

    public function test_planner_can_access_wizard_page_in_planning_module(): void
    {
        $user = User::factory()->create(['is_planner' => true]);

        $this->actingAs($user)
            ->get('/app/planning/timetable')
            ->assertOk()
            ->assertSee('Horario Escolar');
    }

    public function test_planner_wizard_component_uses_planning_layout(): void
    {
        $user = User::factory()->create(['is_planner' => true]);

        Livewire::actingAs($user)
            ->test(PlanningTimetableWizard::class)
            ->assertOk()
            ->assertSet('currentStep', 1);
    }

    public function test_step1_creates_calendar_with_lapso(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('lapsoId', $lapso->id)
            ->set('calendarName', 'Horario Test')
            ->call('createCalendar')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 1)
            ->assertSet('calendarId', TimetableCalendar::query()->where('lapso_id', $lapso->id)->first()->id);
    }

    public function test_multiple_drafts_allowed_per_lapso(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('lapsoId', $lapso->id)
            ->set('calendarName', 'Alternativa B')
            ->call('createCalendar')
            ->assertHasNoErrors()
            ->assertSet('calendarId', TimetableCalendar::query()->where('lapso_id', $lapso->id)->orderByDesc('id')->first()->id);

        $this->assertSame(2, TimetableCalendar::query()->where('lapso_id', $lapso->id)->count());
    }

    public function test_calendar_name_must_be_unique_within_lapso(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'name' => 'Horario Test']);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('lapsoId', $lapso->id)
            ->set('calendarName', 'Horario Test')
            ->call('createCalendar');

        $this->assertSame(1, TimetableCalendar::query()->where('lapso_id', $lapso->id)->count());
    }

    public function test_step1_creates_shift_and_periods(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('shiftCode', 'M')
            ->set('shiftName', 'Mañana')
            ->call('createShift')
            ->assertSet('shiftId', TimetableShift::query()->where('code', 'M')->value('id'))
            ->assertHasNoErrors();

        $shift = TimetableShift::query()->where('code', 'M')->first();

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('shiftId', $shift->id)
            ->call('generatePeriods')
            ->call('savePeriods')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 2);

        $this->assertSame(30, TimetablePeriod::query()->where('calendar_id', $calendar->id)->count());
    }

    public function test_step1_creates_periods_for_multiple_shifts(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shiftM = $this->shift('M', 'Mañana');
        $shiftT = $this->shift('T', 'Tarde', '13:00:00', '18:15:00');

        $wizard = Livewire::actingAs($user)->test(TimetableWizard::class);

        // Turno mañana: 5 días × 6 bloques = 30 períodos.
        $wizard->set('calendarId', $calendar->id)
            ->set('shiftId', $shiftM->id)
            ->call('generatePeriods')
            ->call('savePeriods')
            ->assertHasNoErrors();
        $this->assertSame(30, TimetablePeriod::query()->where('calendar_id', $calendar->id)->count());

        // Turno tarde: el guard por-turno no bloquea; suma otros 30 períodos.
        $wizard->set('shiftId', $shiftT->id)
            ->call('generatePeriods')
            ->call('savePeriods')
            ->assertHasNoErrors();
        $this->assertSame(60, TimetablePeriod::query()->where('calendar_id', $calendar->id)->count());
    }

    public function test_step2_registers_room(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('roomCode', 'LAB-01')
            ->set('roomName', 'Laboratorio de Química')
            ->set('roomCapacity', 24)
            ->set('roomType', 'laboratorio')
            ->call('saveRoom')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('timetable_rooms', ['code' => 'LAB-01', 'type' => 'laboratorio']);
    }

    public function test_register_room_with_reactivo_seccion_cascade(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['name' => 'Plan 1', 'status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'name' => '1er Grado', 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('roomPestudioId', $pestudio->id)
            ->set('roomGradoId', $grado->id)
            ->set('roomSeccionId', $seccion->id)
            ->set('roomCode', 'A-201')
            ->set('roomName', 'Aula 201')
            ->set('roomCapacity', 30)
            ->set('roomType', 'aula')
            ->call('saveRoom')
            ->assertHasNoErrors();

        $room = TimetableRoom::query()->where('code', 'A-201')->first();
        $this->assertNotNull($room);
        $this->assertSame($seccion->id, $room->seccion_id);
    }

    public function test_room_cascade_resets_lower_levels(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('roomPestudioId', $pestudio->id)
            ->set('roomGradoId', $grado->id)
            ->set('roomSeccionId', $seccion->id);

        // Al cambiar el pestudio, se resetean grado y sección (hook automático).
        $component->set('roomPestudioId', $pestudio->id + 1)
            ->assertSet('roomGradoId', null)
            ->assertSet('roomSeccionId', null);

        // Al cambiar el grado, se resetea la sección.
        $component->set('roomPestudioId', $pestudio->id)
            ->set('roomGradoId', $grado->id)
            ->set('roomSeccionId', $seccion->id)
            ->set('roomGradoId', $grado->id + 1)
            ->assertSet('roomSeccionId', null);
    }

    public function test_register_room_without_seccion_is_optional(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('roomCode', 'A-301')
            ->set('roomName', 'Aula 301')
            ->set('roomCapacity', 30)
            ->set('roomType', 'aula')
            ->call('saveRoom')
            ->assertHasNoErrors();

        $room = TimetableRoom::query()->where('code', 'A-301')->first();
        $this->assertNotNull($room);
        $this->assertNull($room->seccion_id);
    }

    public function test_register_room_allows_multiple_rooms_per_seccion(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        TimetableRoom::factory()->create(['code' => 'A-401', 'seccion_id' => $seccion->id]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('roomPestudioId', $pestudio->id)
            ->set('roomGradoId', $grado->id)
            ->set('roomSeccionId', $seccion->id)
            ->set('roomCode', 'A-402')
            ->set('roomName', 'Aula 402')
            ->set('roomCapacity', 30)
            ->set('roomType', 'aula')
            ->call('saveRoom')
            ->assertHasNoErrors();

        $rooms = TimetableRoom::query()->where('seccion_id', $seccion->id)->orderBy('code')->get();
        $this->assertCount(2, $rooms);
        $this->assertSame(['A-401', 'A-402'], $rooms->pluck('code')->all());
    }

    public function test_update_room_can_link_seccion_already_used_by_other_room(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $roomA = TimetableRoom::factory()->create(['code' => 'A-401', 'seccion_id' => $seccionA->id]);
        $roomB = TimetableRoom::factory()->create(['code' => 'A-402', 'seccion_id' => null]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->call('editRoom', $roomB->id)
            ->set('editRoomSeccionId', (string) $seccionA->id)
            ->call('updateRoom')
            ->assertHasNoErrors();

        $this->assertSame($seccionA->id, $roomA->refresh()->seccion_id);
        $this->assertSame($seccionA->id, $roomB->refresh()->seccion_id);
    }

    public function test_room_seccion_warning_lists_existing_rooms(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        TimetableRoom::factory()->create(['code' => 'A-401', 'seccion_id' => $seccion->id]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('roomSeccionId', $seccion->id)
            ->assertSet('roomSectionLinkedRooms', fn ($rooms) => $rooms->count() === 1 && $rooms->first()->code === 'A-401');
    }

    public function test_edit_room_warning_excludes_room_being_edited(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $roomA = TimetableRoom::factory()->create(['code' => 'A-401', 'seccion_id' => $seccion->id]);
        $roomB = TimetableRoom::factory()->create(['code' => 'A-402', 'seccion_id' => $seccion->id]);

        // Al editar A-401, el aviso solo lista la otra aula (A-402).
        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->call('editRoom', $roomA->id)
            ->assertSet('editRoomSectionLinkedRooms', fn ($rooms) => $rooms->count() === 1 && $rooms->first()->code === 'A-402');
    }

    public function test_update_room_changes_seccion_link(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $room = TimetableRoom::factory()->create(['code' => 'A-501', 'seccion_id' => $seccionA->id]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->call('editRoom', $room->id)
            ->assertSet('editRoomSeccionId', (string) $seccionA->id)
            ->set('editRoomSeccionId', null)
            ->call('updateRoom')
            ->assertHasNoErrors();

        $room->refresh();
        $this->assertNull($room->seccion_id);
    }

    public function test_bulk_create_rooms_creates_one_per_active_seccion(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'name' => '1er Grado', 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('bulkCreateRooms');

        $this->assertCount(1, $component->get('pendingRooms'));

        // El staging no persiste: la sección aún no tiene aula.
        $this->assertDatabaseMissing('timetable_rooms', ['seccion_id' => $seccion->id]);

        // Guardar todas → persiste.
        $component->call('saveAllRooms');

        $room = TimetableRoom::query()->where('seccion_id', $seccion->id)->first();
        $this->assertNotNull($room);
        $this->assertSame('Aula 1er Grado A', $room->name);
        $this->assertSame('aula', $room->type);
        $this->assertSame($seccion->amount_student, $room->capacity);
    }

    public function test_bulk_create_rooms_skips_seccion_with_existing_room(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'name' => '1er Grado', 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);

        TimetableRoom::create([
            'code' => 'AULA-001', 'name' => 'Aula 1er Grado A',
            'type' => 'aula', 'seccion_id' => $seccion->id, 'status_active' => true,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('bulkCreateRooms');

        $this->assertSame(1, TimetableRoom::query()->where('seccion_id', $seccion->id)->count());
    }

    public function test_bulk_create_rooms_ignores_inactive_seccion(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'false']);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('bulkCreateRooms');

        $this->assertSame(0, TimetableRoom::query()->where('seccion_id', $seccion->id)->count());
    }

    public function test_edit_room_updates_room(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $room = TimetableRoom::create([
            'code' => 'A-101', 'name' => 'Aula 101', 'capacity' => 30,
            'type' => 'aula', 'status_active' => true,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('editRoom', $room->id)
            ->assertSet('roomEditOpen', true)
            ->assertSet('editRoomCode', 'A-101')
            ->assertSet('editRoomName', 'Aula 101')
            ->set('editRoomName', 'Aula 101 (nueva)')
            ->set('editRoomCapacity', 40)
            ->call('updateRoom');

        $this->assertSame('Aula 101 (nueva)', $room->fresh()->name);
        $this->assertSame(40, $room->fresh()->capacity);
        $this->assertFalse($component->get('roomEditOpen'));
    }

    public function test_edit_room_rejects_duplicate_code(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        TimetableRoom::create(['code' => 'A-101', 'name' => 'Aula 101', 'type' => 'aula', 'status_active' => true]);
        $room = TimetableRoom::create(['code' => 'A-102', 'name' => 'Aula 102', 'type' => 'aula', 'status_active' => true]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('editRoom', $room->id)
            ->set('editRoomCode', 'A-101')
            ->call('updateRoom')
            ->assertHasErrors('editRoomCode');

        $this->assertSame('A-102', $room->fresh()->code);
    }

    public function test_rooms_grouped_by_peducativo(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $peducativo = Peducativo::factory()->create(['name' => 'Proyecto A']);
        $pestudio = Pestudio::factory()->create(['peducativo_id' => $peducativo->id, 'status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'name' => '1er Grado', 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('bulkCreateRooms');

        $groups = $component->get('roomsByPeducativo');
        $matching = collect($groups)->first(fn ($g) => $g['name'] === 'Proyecto A');

        $this->assertNotNull($matching);
        $this->assertContains($seccion->id, array_column($matching['rooms'], 'seccion_id'));
    }

    public function test_save_all_rooms_persists_and_clears_pending(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('bulkCreateRooms');

        $this->assertCount(1, $component->get('pendingRooms'));

        $component->call('saveAllRooms');

        $this->assertCount(0, $component->get('pendingRooms'));
        $this->assertDatabaseHas('timetable_rooms', ['seccion_id' => $seccion->id]);
    }

    public function test_remove_pending_room(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('bulkCreateRooms');

        $pendingId = $component->get('pendingRooms')[0]['id'];

        $component->call('removePendingRoom', $pendingId);

        $this->assertCount(0, $component->get('pendingRooms'));
        $this->assertDatabaseMissing('timetable_rooms', ['seccion_id' => $seccion->id]);
    }

    public function test_delete_all_rooms(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        TimetableRoom::create(['code' => 'A-101', 'name' => 'Aula 101', 'type' => 'aula', 'status_active' => true]);
        TimetableRoom::create(['code' => 'A-102', 'name' => 'Aula 102', 'type' => 'aula', 'status_active' => true]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('deleteAllRooms');

        $this->assertSame(0, TimetableRoom::query()->count());
        $this->assertCount(0, $component->get('rooms'));
    }

    public function test_step3_pevaluaciones_grouped_by_pestudio_grado_seccion(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $peducativo = Peducativo::factory()->create(['name' => 'Proyecto A']);
        $pestudio = Pestudio::factory()->create(['peducativo_id' => $peducativo->id, 'name' => 'Plan 1', 'status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'name' => '1er Grado', 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 3, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id]);
        $profesor = Profesor::create([
            'user_id' => User::factory()->create()->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '30001', 'status_active' => 'true',
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('currentStep', 3)
            ->set('calendarId', $calendar->id)
            ->assertSee('Plan 1')
            ->assertSee('1er Grado')
            ->assertSee('Sección A')
            ->assertSee($asignatura->name);
    }

    public function test_step3_excludes_pevaluaciones_of_inactive_grado(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['name' => 'Plan 1', 'status_active' => 'true']);

        // Grado activo con asignatura "Matemáticas".
        $gradoActive = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'name' => '1er Grado', 'status_active' => 'true']);
        $seccionActive = Seccion::factory()->create(['grado_id' => $gradoActive->id, 'name' => 'A', 'status_active' => 'true']);
        $asigActive = Asignatura::factory()->create(['name' => 'Matemáticas', 'hour_t_week' => 3, 'hour_p_week' => 0]);
        $pensumActive = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $gradoActive->id, 'asignatura_id' => $asigActive->id]);
        $profesorA = Profesor::create([
            'user_id' => User::factory()->create()->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '40001', 'status_active' => 'true',
        ]);
        Pevaluacion::factory()->create([
            'profesor_id' => $profesorA->id, 'seccion_id' => $seccionActive->id,
            'pensum_id' => $pensumActive->id, 'lapso_id' => $lapso->id,
        ]);

        // Grado inactivo con asignatura "Química" → NO debe aparecer.
        $gradoInactive = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'name' => '2do Grado', 'status_active' => 'false']);
        $seccionInactive = Seccion::factory()->create(['grado_id' => $gradoInactive->id, 'name' => 'B', 'status_active' => 'true']);
        $asigInactive = Asignatura::factory()->create(['name' => 'Química', 'hour_t_week' => 3, 'hour_p_week' => 0]);
        $pensumInactive = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $gradoInactive->id, 'asignatura_id' => $asigInactive->id]);
        $profesorB = Profesor::create([
            'user_id' => User::factory()->create()->id, 'name' => 'Beto', 'lastname' => 'Pérez',
            'ci_profesor' => '40002', 'status_active' => 'true',
        ]);
        Pevaluacion::factory()->create([
            'profesor_id' => $profesorB->id, 'seccion_id' => $seccionInactive->id,
            'pensum_id' => $pensumInactive->id, 'lapso_id' => $lapso->id,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('currentStep', 3)
            ->set('calendarId', $calendar->id)
            ->assertSee('Matemáticas')
            ->assertDontSee('Química');
    }

    public function test_step3_derives_blocks_from_asignatura_hours(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'period_minutes' => 60]);
        $shift = $this->shift();

        $fixture = $this->pevaluacionFixture($lapso->id);
        $pev = $fixture['pev'];

        $wizard = Livewire::actingAs($user)->test(TimetableWizard::class);
        $wizard->set('calendarId', $calendar->id);
        $wizard->set('selectedPevs', [$pev->id]);
        $wizard->call('loadLessons');

        $lessons = $wizard->get('lessons');
        $this->assertCount(1, $lessons);
        $lesson = $lessons[$pev->id];
        $this->assertSame(3, $lesson['weekly_blocks_t']); // 3 h semanales → 3 bloques de 60'
        $this->assertSame(2, $lesson['weekly_blocks_p']); // 2 h semanales → 2 bloques
    }

    public function test_step3_saves_lessons(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $fixture = $this->pevaluacionFixture($lapso->id);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('selectedPevs', [$fixture['pev']->id])
            ->call('loadLessons')
            ->call('saveLessons')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 4);

        $this->assertSame(1, TimetableLesson::query()->where('calendar_id', $calendar->id)->count());
    }

    public function test_import_lessons_from_csv(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'period_minutes' => 60]);
        $shiftM = $this->shift('M', 'Mañana');
        $fixture = $this->pevaluacionFixture($lapso->id);

        $csv = "pevaluacion_id,turno,bloques_t,bloques_p,aula,prioridad\n";
        $csv .= "{$fixture['pev']->id},M,3,1,,\n";
        $file = UploadedFile::fake()->createWithContent('lecciones.csv', $csv);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('importFile', $file)
            ->call('importLessons')
            ->assertSet('importMessage', '1 lección(es) importada(s).')
            ->assertCount('lessons', 1);

        $lessons = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('importFile', $file)
            ->call('importLessons')
            ->get('lessons');

        $lesson = $lessons[$fixture['pev']->id];
        $this->assertSame($shiftM->id, $lesson['shift_id']);
        $this->assertSame(3, $lesson['weekly_blocks_t']);
        $this->assertSame(1, $lesson['weekly_blocks_p']);
    }

    public function test_import_rejects_duplicate_rows(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $this->shift('M', 'Mañana');
        $fixture = $this->pevaluacionFixture($lapso->id);

        $csv = "pevaluacion_id,turno\n";
        $csv .= "{$fixture['pev']->id},M\n";
        $csv .= "{$fixture['pev']->id},M\n";
        $file = UploadedFile::fake()->createWithContent('lecciones.csv', $csv);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('importFile', $file)
            ->call('importLessons');

        $component->assertCount('lessons', 1);
        $this->assertNotEmpty($component->get('importErrors'));
        $this->assertStringContainsString('duplicada', implode(' ', $component->get('importErrors')));
    }

    public function test_import_rejects_invalid_turno(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $fixture = $this->pevaluacionFixture($lapso->id);

        $csv = "pevaluacion_id,turno\n";
        $csv .= "{$fixture['pev']->id},Z\n";
        $file = UploadedFile::fake()->createWithContent('lecciones.csv', $csv);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('importFile', $file)
            ->call('importLessons');

        $this->assertEmpty($component->get('lessons'));
        $this->assertStringContainsString('turno inválido', implode(' ', $component->get('importErrors')));
    }

    public function test_step5_runs_dry_run_and_shows_preview(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();

        // Períodos para el solver.
        for ($day = 1; $day <= 5; $day++) {
            for ($order = 1; $order <= 3; $order++) {
                TimetablePeriod::factory()->create([
                    'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
                    'day_of_week' => $day, 'order_in_day' => $order, 'is_break' => false,
                ]);
            }
        }

        $fixture = $this->pevaluacionFixture($lapso->id);

        TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('runDryRun')
            ->assertSet('generationState', 'preview_ready');

        $preview = TimetableCalendar::find($calendar->id)->preview_payload;
        $this->assertNotNull($preview);
        $this->assertTrue($preview['dry_run']);
    }

    // ─── Fixtures ──────────────────────────────────────────────

    /**
     * Turno reutilizable (catálogo compartido con code único): firstOrCreate
     * para tolerar turnos 'M'/'T' preexistentes en la BD de tests.
     */
    private function shift($code = 'M', $name = 'Mañana', $start = '07:00:00', $end = '12:15:00'): TimetableShift
    {
        return TimetableShift::query()->firstOrCreate(
            ['code' => $code],
            ['name' => $name, 'start_time' => $start, 'end_time' => $end],
        );
    }

    private function pevaluacionFixture($lapsoId): array
    {
        $user = User::factory()->create();
        $profesor = \App\Models\app\Academy\Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '2001', 'status_active' => 'true',
        ]);
        $pestudio = \App\Models\app\Academy\Pestudio::factory()->create();
        $grado = \App\Models\app\Academy\Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = \App\Models\app\Academy\Seccion::factory()->create(['grado_id' => $grado->id]);
        $asignatura = \App\Models\app\Academy\Asignatura::factory()->create(['hour_t_week' => 3, 'hour_p_week' => 2]);
        $pensum = \App\Models\app\Academy\Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $pev = \App\Models\app\Academy\Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapsoId,
        ]);

        return compact('profesor', 'pev');
    }
}
