# Spec: Cobertura de Tests para el Módulo Leadership

**Versión:** 1.0
**Autor:** Claude Architect
**Fecha:** 2026-07-28
**Estado:** Pendiente de implementación

---

## Alcance

Completar la cobertura de tests unitarios y de feature para el módulo Leadership (`Jefes de Área`) tras la migración del namespace `Planning\Leadership → Leadership` y las mejoras al componente `Activities\IndexComponent`.

**Objetivo:** 7 grupos de tests, ~22 métodos de prueba, 0 regresiones.

---

## Convenciones usadas en este spec

- **Arrange:** `User::factory()->leadership()->create()` — crea usuario con `is_leadership=true`
- **Trait siempre presente:** `DatabaseTransactions` en todos los test classes
- **Livewire:** `Livewire::test(ComponentClass::class)->actingAs($user)`
- **HTTP:** `$this->actingAs($user)->get(route(...))`
- **Service:** `new LeadershipService($user)` o `app(LeadershipService::class, ['user' => $user])`

---

## Grupo 1: User::isLeadership() fix

**Archivo:** `tests/Unit/Leadership/LeadershipScopeTest.php` (agregar al existente)
**Namespace:** `Tests\Unit\Leadership`
**Clase:** `LeadershipScopeTest` (extender)

| # | Método | Arrange | Assert |
|---|--------|---------|--------|
| 1.1 | `test_is_leadership_returns_false_when_admin_only()` | `User::factory()->create(['is_admin' => true, 'is_leadership' => false])` | `$user->isLeadership()` → `false` |
| 1.2 | `test_is_leadership_returns_true_when_leadership_only()` | `User::factory()->create(['is_admin' => false, 'is_leadership' => true])` | `$user->isLeadership()` → `true` |
| 1.3 | `test_is_leadership_returns_false_when_neither()` | `User::factory()->create(['is_admin' => false, 'is_leadership' => false])` | `$user->isLeadership()` → `false` |
| 1.4 | `test_is_leadership_accessor_still_respects_admin_grace()` | `User::factory()->create(['is_admin' => true, 'is_leadership' => false])` | Property `$user->is_leadership` → `true` (accessor incluye admin), pero `$user->isLeadership()` → `false` (método raw) |

**Nota:** El grupo 1.4 documenta la diferencia INTENCIONAL entre el accessor (getIsLeadershipAttribute) y el método isLeadership(). El accessor retorna `is_admin || is_leadership`, el método raw lee SÓLO `is_leadership`. Ver CLAUDE.md — "isLeadership() ahora lee raw attribute en vez de pasar por el accessor".

## Estructura de tablas para helpers de datos

Los grupos 2–6 necesitan construir una cadena de datos. Estas son las tablas involucradas y sus columnas clave:

```sql
-- Área de conocimiento (NO tiene factory — usar DB::table())
-- `area_conocimientos`
--     id, leader_id (FK → users), name, code, pestudio_id, peducativo_id
--     fillable: peducativo_id, pestudio_id, leader_id, name, code, code_sm,
--               description, observations, order, enable_academic_index

-- Pivote Asignatura ↔ Área (NO tiene factory — usar DB::table())
-- `campo_conocimientos`
--     id, area_conocimiento_id (FK → area_conocimientos),
--     asignatura_id (FK → asignaturas)

-- Profesor (NO tiene factory — usar DB::table())
-- `profesors`
--     id, user_id, ti_teacher, ci_profesor, name, lastname, status_active

-- SÍ tienen factory: User, Asignatura, Pestudio, Grado, Lapso, Escala,
--                    Seccion, Pensum, Pevaluacion, Activity, LmsActivityPublication

-- Lapso::current() resuelve el lapso activo vía scope SQL (status_last = 'true')
```

---

## Grupo 2: ActivityOverview (403s + Scope)

**Archivo:** `tests/Feature/Leadership/ActivityOverviewTest.php`
**Namespace:** `Tests\Feature\Leadership`
**Clase:** `ActivityOverviewTest`

**Dependencias de datos (helper):**
```php
private function buildLeadershipScenario(): array
{
    // 1. Crear leader user
    $leader = User::factory()->leadership()->create();

    // 2. Crear área + campo + asignatura
    $area = \App\Models\app\Academy\AreaConocimiento::factory()->create([
        'leader_id' => $leader->id,
    ]);
    $campo = \App\Models\app\Academy\CampoConocimiento::factory()->create([
        'area_conocimiento_id' => $area->id,
    ]);
    $asignatura = \App\Models\app\Academy\Asignatura::factory()->create();
    // Vincular asignatura al campo (pivot)
    // ... (según estructura real de la tabla pivote)

    // 3. Crear cadena: Pestudio → Grado → Sección → Pensum → Pevaluacion → Activities
    $pestudio = \App\Models\app\Academy\Pestudio::factory()->create(['planning_module' => true]);
    $grado = \App\Models\app\Academy\Grado::factory()->create(['pestudio_id' => $pestudio->id]);
    $seccion = \App\Models\app\Academy\Seccion::factory()->create();
    $lapso = \App\Models\app\Academy\Lapso::factory()->create();
    $pensum = \App\Models\app\Academy\Pensum::factory()->create([
        'asignatura_id' => $asignatura->id,
        'grado_id' => $grado->id,
        'pestudio_id' => $pestudio->id,
    ]);
    $peva = \App\Models\app\Academy\Pevaluacion::factory()->create([
        'pensum_id' => $pensum->id,
        'seccion_id' => $seccion->id,
        'lapso_id' => $lapso->id,
        'profesor_id' => $profesor->id,
    ]);

    return compact('leader', 'area', 'asignatura', 'peva', ...);
}
```

**⚠️ Nota importante:** Las factorías para los modelos de la cadena educativa pueden no existir. El helper que construye el escenario deberá insertar registros con `DB::table()` cuando no exista factory. Ver `LessonWizardCharacterizationTest` como referencia del patrón `buildFkChain()`.

| # | Método | Arrange | Assert |
|---|--------|---------|--------|
| 2.1 | `test_create_observation_returns_403()` | `$leader = User::factory()->leadership()->create()`, `Livewire::test(ActivityOverview::class)->actingAs($leader)` | `->call('createObservation', 1)->assertForbidden()` |
| 2.2 | `test_save_observation_returns_403()` | ídem | `->call('saveObservation')->assertForbidden()` |
| 2.3 | `test_delete_observation_returns_403()` | ídem | `->call('deleteObservation', 1)->assertForbidden()` |
| 2.4 | `test_render_scopes_by_leadership_area()` | Leader con 1 área. Crear 2 pevas: una DENTRO del área del líder, otra FUERA. | Render OK. `$component->assertSee($pevaInside->pensum->asignatura->name)`, `$component->assertDontSee($pevaOutside->...)` |

---

## Grupo 3: LessonMonitor

**Archivo:** `tests/Feature/Leadership/LessonMonitorTest.php`
**Namespace:** `Tests\Feature\Leadership`
**Clase:** `LessonMonitorTest`

**Dependencias:** Misma cadena FK que Grupo 2, pero además necesita actividades con `LmsActivityPublication`.

| # | Método | Arrange | Assert |
|---|--------|---------|--------|
| 3.1 | `test_renders_successfully()` | `$leader = User::factory()->leadership()->create()`, datos con actividades en su área | `Livewire::test(LessonMonitor::class)->actingAs($leader) ->assertStatus(200)` |
| 3.2 | `test_filters_by_lapso()` | Crear 2 actividades en lapsos distintos. | Setear `lapso_id` al primero → ver la del primero, no ver la del segundo. |
| 3.3 | `test_search_filters_by_topic()` | Crear 2 actividades con topics distintos ("Matemáticas", "Historia") | Setear `search="Matemáticas"` → ver solo "Matemáticas" |
| 3.4 | `test_preview_lesson_loads_data_structure()` | Actividad con secciones, recursos, links y publicación | Llamar `previewLesson($activity->id)` → `$component->get('previewData')` tiene keys: `subject, title, sections, resources, links` |
| 3.5 | `test_confirm_publish_shows_modal()` | Actividad con `LmsActivityPublication` status SCHEDULED | `->call('confirmPublishLesson', $id)` → `$component->get('showPublishModal')` es `true`, `publishActivityId` es `$id` |
| 3.6 | `test_do_publish_publishes_scheduled_lesson()` | Actividad SCHEDULED | `->call('confirmPublishLesson', $id)->call('doPublishLesson')` → Publication status cambia a `PUBLISHED`, `published_at` no null, `published_by` es `$leader->id`. Se dispara evento `notify` con `type=success` |
| 3.7 | `test_do_publish_does_not_publish_non_scheduled()` | Actividad con status PUBLISHED | `->call('confirmPublishLesson', $id)->call('doPublishLesson')` → Status sigue PUBLISHED. Se dispara evento `notify` con `type=warning` |

---

## Grupo 4: ProfesorIndicators

**Archivo:** `tests/Feature/Leadership/ProfesorIndicatorsTest.php`
**Namespace:** `Tests\Feature\Leadership`
**Clase:** `ProfesorIndicatorsTest`

| # | Método | Arrange | Assert |
|---|--------|---------|--------|
| 4.1 | `test_renders_with_profesor_list()` | Leader con área que tiene 2 profesores asignados. | Render OK. `$component->assertSee($prof1->lastname)`, `assertSee($prof2->lastname)` |
| 4.2 | `test_select_profesor_shows_kpis()` | Leader con 1 profesor que tiene pevas en un lapso. | `->set('selectedProfesorId', $prof->id)->set('selectedLapsoId', $lapso->id)` → KPI estructura: `iee, ire, goal_notas, real_notas, total_pevas` |
| 4.3 | `test_empty_state_when_no_profesores_in_area()` | Leader con área que NO tiene profesores. | Render OK. `assertSee('No hay profesores')` o similar. |
| 4.4 | `test_select_profesor_before_selecting_shows_placeholder()` | Leader con profesores, sin seleccionar ninguno. | Render OK. El panel de detalle muestra placeholder, no KPIs. |

---

## Grupo 5: LeadershipService — métodos scope no cubiertos

**Archivo:** `tests/Unit/Leadership/LeadershipServiceScopeTest.php`
**Namespace:** `Tests\Unit\Leadership`
**Clase:** `LeadershipServiceScopeTest`

**Helper compartido:** Construir el grafo de datos: AreaConocimiento → CampoConocimiento → Asignatura ×2 (una dentro del scope, otra fuera).

| # | Método | Arrange | Assert |
|---|--------|---------|--------|
| 5.1 | `test_scope_pensums_filters_by_asignatura()` | 2 pensums: uno vinculado a asignatura del líder, otro no. | `$service->scopePensums(Pensum::query())->pluck('id')` contiene solo el ID del pensum dentro del scope. |
| 5.2 | `test_scope_activities_filters_by_asignatura()` | 2 actividades: una dentro, otra fuera. | `$service->scopeActivities(Activity::query())->pluck('id')` contiene solo la actividad dentro del scope. |
| 5.3 | `test_scope_pevaluacions_filters_by_asignatura()` | 2 pevas: una dentro, otra fuera. | `$service->scopePevaluacions(Pevaluacion::query())->pluck('id')` contiene solo la peva dentro del scope. |
| 5.4 | `test_get_assigned_profesores_returns_only_assigned()` | 2 profesores: uno con peva en asignatura del líder, otro sin. | `$service->getAssignedProfesores()->pluck('id')` contiene solo el profe dentro del scope. |
| 5.5 | `test_assert_can_access_asignatura_allows_valid()` | Asignatura dentro del scope del líder. | `$service->assertCanAccessAsignatura($asignaturaId)` → no lanza excepción. |
| 5.6 | `test_assert_can_access_asignatura_throws_403()` | Asignatura FUERA del scope del líder. | `$this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class)` o `expectExceptionCode(403)`. |
| 5.7 | `test_admin_bypasses_all_scopes()` | Admin user (sin leadership). Crear datos en cualquier asignatura. | `getAssignedAreaIds()` → colección vacía. `scopePevaluacions($q)->count()` = todos los registros, sin filtrar. |
| 5.8 | `test_get_assigned_profesores_empty_when_no_areas()` | Leader sin áreas asignadas. | `$service->getAssignedProfesores()` → colección vacía. |

---

## Grupo 6: Activities IndexComponent — filter_status

**Archivo:** `tests/Feature/Activities/IndexComponentFilterStatusTest.php`
**Namespace:** `Tests\Feature\Activities`
**Clase:** `IndexComponentFilterStatusTest`

**Helper:** Construir 1 peva con 2 actividades: una `status=0` (revisión) y otra `status=1` (aprobada). Y otra peva con todas las actividades aprobadas.

| # | Método | Arrange | Assert |
|---|--------|---------|--------|
| 6.1 | `test_filter_status_all_shows_all()` | Sin filtro (por defecto) | Render OK. Muestra todas las pevas. `activities_count > 0` para ambas. |
| 6.2 | `test_filter_status_pending_shows_only_pevas_with_pending_activities()` | Set `filter_status='pending'` | Muestra solo la peva que tiene al menos una actividad en revisión. |
| 6.3 | `test_filter_status_approved_shows_only_pevas_with_all_approved()` | Set `filter_status='approved'` | Muestra solo la peva donde TODAS las actividades están aprobadas. |
| 6.4 | `test_counters_are_present()` | Peva con 2 actividades (1 aprobada, 1 en revisión) | `$item->activities_count == 2`, `$item->activities_approved_count == 1`, `$item->activities_revision_count == 1` |

---

## Grupo 7: AreaConocimientoObserver — cache invalidation

**Archivo:** `tests/Unit/Leadership/AreaConocimientoObserverTest.php`
**Namespace:** `Tests\Unit\Leadership`
**Clase:** `AreaConocimientoObserverTest`

**Precondición:** El observer debe estar registrado en `AppServiceProvider.php`.

| # | Método | Arrange | Assert |
|---|--------|---------|--------|
| 7.1 | `test_cache_invalidated_on_area_save()` | Leader tiene área asignada. `Cache::put("leadership:{$leader->id}:areas", [1,2,3], 300)`. | Llamar `AreaConocimiento::find($area->id)->touch()` (o update), luego `Cache::get("leadership:{$leader->id}:areas")` es `null`. |
| 7.2 | `test_cache_both_keys_invalidated_on_area_save()` | Mismo escenario + ambas claves cacheadas. | `touch()` el área → ambas claves (`areas` y `asignaturas`) son `null`. |
| 7.3 | `test_cache_invalidated_on_area_delete()` | Mismo escenario. | Eliminar el área → `Cache::get("leadership:{$leader->id}:areas")` es `null`. |

---

## Orden de implementación sugerido

| Orden | Grupo | Dependencias | Esfuerzo estimado |
|-------|-------|-------------|-------------------|
| 1 | **Grupo 1** — User::isLeadership() | Ninguna. 4 tests, sin factories extra. | 10 min |
| 2 | **Grupo 5** — LeadershipService scope | Cadena FK: necesita datos, pero se pueden insertar vía DB::table() | 30 min |
| 3 | **Grupo 7** — Observer cache | Cache::fake() + modelo existente | 15 min |
| 4 | **Grupo 2** — ActivityOverview 403s | Livewire + cadena FK | 20 min |
| 5 | **Grupo 3** — LessonMonitor | Livewire + cadena FK + LmsActivityPublication | 40 min |
| 6 | **Grupo 4** — ProfesorIndicators | Livewire + Profesor::getProfesorIEE() sin datos reales | 30 min |
| 7 | **Grupo 6** — filter_status | Livewire + cadena FK con actividades | 25 min |

**Total estimado:** ~3 horas

---

## Criterios de aceptación

- [ ] Todos los tests pasan con `php8.2 artisan test --filter=Leadership`
- [ ] `isLeadership()` se comporta distinto al accessor `is_leadership` (documentado en Grupo 1)
- [ ] Los 3 métodos de observación en ActivityOverview arrojan 403
- [ ] LessonMonitor puede publicar una lección programada
- [ ] ProfesorIndicators muestra KPIs cuando se selecciona un profesor
- [ ] Los scope methods filtran correctamente por asignatura
- [ ] El observer invalida cache al crear/actualizar/eliminar áreas
- [ ] 0 regresiones en tests existentes (`php8.2 artisan test --filter=Leadership/DashboardMetricsTest --filter=Leadership/LeadershipMiddlewareTest`)
