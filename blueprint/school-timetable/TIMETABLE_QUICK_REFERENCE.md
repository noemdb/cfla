# CFLA Timetable Solver — Quick Index

## 🎯 What is This?
A **constraint satisfaction solver** (backtracking CSP) that schedules school lessons across periods, teachers, sections, rooms, and shifts. Used in CFLA for the timetable planning module.

---

## 📍 Key Files at a Glance

### **Solver Core** (pure PHP, no Eloquent)
- `app/Services/Timetable/Solver/TimetableSolver.php` — 482 lines | Backtracking algorithm
- `app/Services/Timetable/Solver/LessonToSchedule.php` — 67 lines | Input DTO
- `app/Services/Timetable/Solver/SlotCandidate.php` — 17 lines | Slot representation  
- `app/Services/Timetable/Solver/SolverResult.php` — 27 lines | Output DTO
- `app/Services/Timetable/Solver/SchedulingContext.php` — 123 lines | Mutable state tracker

### **Queue & Job**
- `app/Jobs/Timetable/GenerateTimetableJob.php` — 742 lines | Entry point, orchestrates solver → persistence

### **Validation & Rules**
- `app/Services/Timetable/ConflictValidator.php` — 140 lines | Hard constraint checks
- `app/Services/Timetable/TimetableAvailabilityService.php` — Teacher availability filter
- `app/Services/Timetable/TimetableRoomEligibilityService.php` — Room type eligibility

### **UI Integration**
- `app/Livewire/Coordinacion/Timetable/TimetableWizard.php` — 5-step wizard (Step 1-5)
- `app/Livewire/Planning/Timetable/TimetableWizard.php` — Planning view variant
- `resources/views/livewire/coordinacion/timetable/timetable-wizard.blade.php` — Wizard template

### **Database Models** (13 models)
- `TimetableCalendar` — Calendar entity (main aggregate root)
- `TimetableLesson` — Lesson instance
- `TimetableSlot` — Result (lesson ↔ period ↔ room)
- `TimetableConflict` — Unassigned/failed lessons
- `TimetablePeriod`, `TimetableShift`, `TimetableRoom`, etc.

### **Configuration & Logging**
- `config/timetable.php` — Legacy CSV path config
- `config/logging.php` — Timetable log channel (→ `storage/logs/timetable.log`)

### **Tests**
- `tests/Unit/Timetable/TimetableSolverTest.php` — Solver unit tests (no DB)
- `tests/Feature/Timetable/GenerateTimetableJobTest.php` — Job integration tests
- `tests/Feature/Timetable/TimetableWizardTest.php` — UI/wizard tests
- Plus 9 more feature test files

---

## 🔄 Data Flow

```
Wizard (Step 5)
    ↓
GenerateTimetableJob (queue)
    ↓
TimetableSolver.solve() [30s timeout]
    ↓
    → Backtracking CSP
    → Returns {assignment, unassigned, timedOut}
    ↓
Persist to DB OR Preview
    ↓
Status: active/draft
Broadcast event
```

---

## ⚙️ Solver Algorithm (30 seconds timeout)

1. **Reserve locked lessons** (ADR-TT-007)
2. **Sort free lessons** by constraint degree (ADR-TT-003)
3. **Backtrack search** (bounded by 500K nodes, MAX_COMBOS_PER_LESSON=1000)
4. **Cut-off deadline** → preserve best partial solution (ADR-TT-009)

### Hard Constraints (enforced in-solver)
- ✓ Teacher double-booking  
- ✓ Section conflict  
- ✓ Room double-booking  
- ✓ Shift alignment  
- ✓ Teacher availability  
- ✓ Room type match  
- ✓ Block distribution (no period repetition per lesson)

### Soft Heuristics (scoring)
- +100 pts per distinct day (distribute blocks)
- -50 pts per consecutive day (avoid clustering)
- -10 pts per late theoretical block (order > 3)

---

## 📊 Database Schema (Simplified)

```
TimetableCalendar
  ├─→ TimetablePeriod (day, order, shift, is_break)
  ├─→ TimetableShift (code, start_time, end_time)
  ├─→ TimetableLesson (pevaluacion_id, weekly_blocks_t/p, locked)
  │    ├─→ TimetableSlot (period, room, locked) [solver output]
  │    └─→ TimetableConflict (unassigned reason) [solver unassigned]
  ├─→ TimetableRoom (type, capacity)
  └─→ TimetableTeacherAvailability (teacher, period, available)
```

---

## 🧪 Testing Pattern

### Unit Test (no DB)
```php
$lessons = [
    LessonToSchedule(id: 1, profesor: 101, blocksT: 3),
    LessonToSchedule(id: 2, profesor: 102, blocksT: 3),
];
$solver = new TimetableSolver($lessons, $available, $roomsByType);
$result = $solver->solve();
$this->assertTrue($result->isComplete());
```

### Feature Test (with DB)
```php
$calendar = TimetableCalendar::factory()->create();
GenerateTimetableJob::dispatch($calendar->id);
Queue::assertPushed(GenerateTimetableJob::class);
$this->assertDatabaseHas('timetable_slots', [...]);
```

---

## 🎓 Architecture Decisions (ADRs)

| ADR | | 
|-----|---|
| **ADR-TT-001** | Lesson wraps Pevaluacion (docente, sección, turno) |
| **ADR-TT-002** | Atomic persist with optimistic lock |
| **ADR-TT-003** | Schedule high-constraint lessons first |
| **ADR-TT-004** | Separate theoretical ('t') & practical ('p') blocks |
| **ADR-TT-007** | Pre-assigned + locked lessons occupy before free |
| **ADR-TT-008** | Only track room when roomId != null (avoid key collision) |
| **ADR-TT-009** | Preserve best partial if timeout or infeasible |
| **ADR-TT-010** | Theoretical blocks don't need dedicated room |
| **ADR-TT-011** | Adaptive combination pool (scales with block count) |

---

## 📋 Constraints at a Glance

### By Component

**Docente (Teacher)**
- Cannot have 2+ classes in same period (periodId:profesorId unique)
- Must be available (marked in TimetableTeacherAvailability)

**Sección (Section)**
- Full section lessons monopolize period (no other activity)
- Sub-group lessons only conflict with same sub-group
- Half-group pairs share period (up to maxSubjectsPerPeriod)

**Aula (Room)**
- Only tracked if roomId != null (no collision for theory-only)
- Cannot double-book same room + period
- Must match roomTypeRequired if specified

**Turno (Shift)**
- Period must belong to lesson's shift
- Locked lessons stay in their shift

**Disponibilidad (Availability)**
- Teacher must be marked available for that period

---

## 🔍 How to Debug

1. **Check logs**: `tail -f storage/logs/timetable.log`
   - Look for: `correlation_id`, `elapsed_seconds`, `unassigned`, `timed_out`

2. **Inspect conflicts**: Query `timetable_conflicts` table
   - `type` = 'unassigned', details show reason

3. **Manual test**: See `tests/Unit/Timetable/TimetableSolverTest.php`
   - Run: `php artisan test tests/Unit/Timetable/TimetableSolverTest.php`

4. **Preview before publish**: Wizard stores `preview_payload` (dry-run mode)
   - Review conflicts before activating

---

## 📚 Key References

- **Main Spec**: SPEC-TIMETABLE-001 (architecture decisions 1-11)
- **Architecture**: TIMETABLE_ARCHITECTURE.md (this project)
- **Tests**: `tests/Unit/Timetable/` & `tests/Feature/Timetable/`
- **Logging**: `storage/logs/timetable.log` (JSON format)

---

## 💡 Common Tasks

### Adjust solver timeout
`config/timetable.php` or pass to `GenerateTimetableJob`:
```php
GenerateTimetableJob::dispatch($calendarId, timeLimitSeconds: 60);
```

### Add new constraint
1. Add logic to `SchedulingContext.isFree()`
2. Update `ConflictValidator.validate()`
3. Add test to `TimetableSolverTest.php`

### Generate timetable manually
```php
$calendar = TimetableCalendar::find($id);
GenerateTimetableJob::dispatch($calendar->id, dryRun: false);
// or: GenerateTimetableJob::dispatchSync($calendar->id, dryRun: false);
```

### Check solver quality
```php
$calendar->quality_score; // 0–100, % of lessons scheduled
$calendar->preview_payload['timed_out']; // true if deadline cut
$calendar->preview_payload['elapsed_seconds']; // solver runtime
```

---

## 🚀 Next Steps

- Read **TIMETABLE_ARCHITECTURE.md** for full structural breakdown
- Review **TimetableSolver.php** for algorithm internals
- Run **TimetableSolverTest.php** to understand constraint enforcement
- Trace **GenerateTimetableJob** for wizard integration
