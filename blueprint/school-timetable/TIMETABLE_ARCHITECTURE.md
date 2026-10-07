# CFLA Timetable Solver Architecture — Structural Summary

## Overview
The CFLA timetable solver is a **constraint satisfaction problem (CSP) solution** using backtracking with forward-checking, implemented in PHP without Eloquent dependencies. The system is designed to schedule lessons (lecciones) across periods, teachers (docentes), sections (secciones), rooms (aulas), and shifts (turnos) while respecting hard constraints and optimizing soft preferences.

**Key Reference:** SPEC-TIMETABLE-001 (specification) and ADR-TT-001 through ADR-TT-011 (architecture decision records).

---

## 1. Core Solver Implementation

### Entry Points & Architecture

**Location:** `app/Services/Timetable/Solver/TimetableSolver.php`

The `TimetableSolver` class implements **backtracking with bounded search**:

- **Algorithm:** Backtracking CSP solver with dynamic ordering and pruning
- **Time Limit:** 30 seconds (configurable, `timeLimitSeconds` parameter)
- **Search Budget:** MAX_COMBO_NODES = 500,000 (prevents exponential explosion)
- **Combination Pool:** MAX_CANDIDATE_POOL = 14 (base), MAX_CANDIDATE_POOL_ABS = 26 (adaptive for large lessons)

#### Constructor Parameters
```php
TimetableSolver(
    array $lessons,                      // LessonToSchedule[] — lessons to schedule
    array $availablePeriodsByTeacher,   // periodIds per teacher (shift-preferenced)
    array $roomsByType,                 // roomIds indexed by roomType
    array $periodMeta = [],             // day/order heuristic metadata
    int $timeLimitSeconds = 30,
    int $maxSubjectsPerPeriod = 2
)
```

#### Main Entry: `solve(): SolverResult`

1. **Lock pre-assigned slots** (ADR-TT-007)
   - Lessons with `preassignedSlots` are occupied first (partial preservation)
   - Conflict detected → lesson marked unassigned

2. **Lock complete locked lessons** (ADR-TT-007)
   - Only if lesson has exactly `blocksNeeded()` locked periods
   - These periods are reserved and unavailable to others

3. **Sort free lessons** by constraint degree (ADR-TT-003)
   - Higher constraint degree = scheduled first
   - Constraint degree = (priority × 10) + (roomTypeRequired ? 5 : 0) + min(blocksNeeded, 9)

4. **Backtrack search** (ADR-TT-009)
   - Explores combinations for each free lesson
   - **Deadline cut-off:** If timeout reached, best partial solution preserved
   - Returns `SolverResult` with assignment + unassigned list

#### Soft Heuristics (§6.2)

**Combination scoring** (`comboScore()`) ranks slot combinations:
- **+100 points** per distinct day (distribute blocks across days)
- **-50 points** per consecutive day between blocks (penalize clustering)
- **-10 points** per theoretical block in late periods (order > 3)

---

### 2. DTOs (Data Transfer Objects)

#### **LessonToSchedule** (§6.1)
**File:** `app/Services/Timetable/Solver/LessonToSchedule.php`

Immutable DTO representing a lesson:

```php
final class LessonToSchedule {
    public readonly int $lessonId;
    public readonly int $seccionId;
    public readonly int $profesorId;
    public readonly int $shiftId;
    public readonly int $blocksT;           // theoretical blocks
    public readonly int $blocksP;           // practical blocks
    public readonly ?string $roomTypeRequired;  // e.g., 'laboratorio', 'auditorio'
    public readonly int $priority;          // ordering weight
    public readonly bool $locked;           // fixed assignment
    public readonly array $lockedPeriodIds; // if locked, which periods
    public readonly ?int $grupoEstableId;   // null = full section, else sub-group
    public readonly bool $isHalfGroup;      // component of formation
    public readonly array $preassignedSlots; // SlotCandidate[] already assigned
    
    public function blocksNeeded(): int;
    public function remainingBlocksT(): int;
    public function remainingBlocksP(): int;
    public function constraintDegree(): int;
}
```

#### **SlotCandidate** (§6.1)
**File:** `app/Services/Timetable/Solver/SlotCandidate.php`

```php
final class SlotCandidate {
    public readonly int $periodId;
    public readonly ?int $roomId;        // null = no dedicated room (theoretical)
    public readonly bool $isPractical;   // separates block type
}
```

#### **SolverResult** (§6.1)
**File:** `app/Services/Timetable/Solver/SolverResult.php`

```php
final class SolverResult {
    public readonly array $assignment;      // lessonId => SlotCandidate[]
    public readonly array $unassigned;      // lessonIds without solution
    public readonly bool $timedOut;         // true if deadline cut (ADR-TT-009)
    public readonly float $elapsedSeconds;
    
    public function isComplete(): bool;
}
```

#### **SchedulingContext** (§6.1)
**File:** `app/Services/Timetable/Solver/SchedulingContext.php`

Mutable state tracking occupied slots during backtracking:

```php
final class SchedulingContext {
    private array $teacherBusy;            // "periodId:profesorId" → true
    private array $roomBusy;               // "periodId:roomId" → true (roomId != null)
    private array $sectionWholeBusy;       // "periodId:seccionId" → true
    private array $sectionGroupCount;      // "periodId:seccionId" → count
    private array $sectionHalfGroupCount;  // "periodId:seccionId" → count
    private array $sectionGroupBusy;       // "periodId:seccionId:grupoId" → true
    
    public function isFree(int $periodId, int $profesorId, int $seccionId, 
                          ?int $roomId, ?int $grupoEstableId, bool $isHalfGroup): bool;
    public function occupy(...);
    public function release(...);
    public function halfGroupLoad(int $periodId, int $seccionId): int;
}
```

---

## 3. Constraints & Rule Validation

### Hard Constraints (SPEC-TIMETABLE-001 §6)

All enforced during backtracking via `SchedulingContext.isFree()`:

| Constraint | Check | Implementation |
|-----------|-------|-----------------|
| **Teacher Double-Booking** | Teacher cannot occupy 2+ periods simultaneously | `teacherBusy["periodId:profesorId"]` |
| **Section Conflict** | Section cannot have 2+ subjects in same period (except half-groups) | `sectionWholeBusy`, `sectionGroupBusy`, `sectionGroupCount` |
| **Room Double-Booking** | Room cannot be occupied twice in same period | `roomBusy["periodId:roomId"]` (only if roomId != null) |
| **Shift Alignment** | Lesson's shift must match period's shift | Pre-filtered in `buildAvailablePeriods()` |
| **Teacher Availability** | Teacher must be available (not marked unavailable) in that period | `TimetableAvailabilityService.isAvailable()` |
| **Room Type Match** | If roomTypeRequired specified, practical blocks must use compatible room | Domain filtering in `buildDomain()` |
| **Block Distribution** | No period repetition within a single lesson | Checked in `combinationsOfSize()` |

### Validation Service

**File:** `app/Services/Timetable/ConflictValidator.php`

Synchronous validation for manual editor and wizard **before** queueing:

```php
public function validate(
    int $calendarId,
    int $lessonId,
    int $periodId,
    int $profesorId,
    int $seccionId,
    ?int $roomId = null,
    ?int $ignoreSlotId = null,
    ?int $grupoEstableId = null
): array {
    // Returns ['valid' => bool, 'reasons' => list<string>]
    // Checks: shift, teacher double-booking, section conflict, room conflict, availability
}

public function recordConflict(int $calendarId, int $lessonId, int $periodId, 
                             string $type, array $details = []): void;
```

### Half-Group (Medio Grupo) Rules

- **Full section lessons** (`grupoEstableId = null`, `isHalfGroup = false`): Monopolize period (no other activity)
- **Sub-group lessons** (`grupoEstableId = X`, `isHalfGroup = false`): Only conflict with own sub-group
- **Half-group lessons** (`isHalfGroup = true`): Up to `maxSubjectsPerPeriod` pairs can share period
- Domain sorted by load: lighter periods preferred for half-groups

---

## 4. GenerateTimetableJob (Queue Entry Point)

**File:** `app/Jobs/Timetable/GenerateTimetableJob.php`

Queue job triggered by wizard step 5 or publication workflow:

```php
class GenerateTimetableJob implements ShouldQueue {
    public int $calendarId;
    public bool $dryRun = false;
    public ?array $previewPayload = null;  // from wizard preview
    public ?array $pevaluacionIds = null;  // scoped generation
    public ?array $lessonIds = null;       // scoped persistence
    
    public function handle(): void;
}
```

### Execution Flow

1. **Idempotent by design** (optimistic locking §15)
   - Identified by `calendar_id + timestamp`
   - Version check prevents stale jobs overwriting

2. **Strategy selection** (§17)
   - `DEFAULT_STRATEGY`: Solver-based
   - `STRATEGY_LEGACY`: Reproduces imported CSV assignments (if available)

3. **DTO construction** from DB models
   - Loads lessons with relationships (pevaluacion, sections, teacher)
   - Constructs `LessonToSchedule[]` with constraints from DB

4. **Availability building**
   - Preferred periods = teacher's shift
   - Fallback = other shifts (unless lesson locked)
   - Filters by `TimetableAvailabilityService.isAvailable()`

5. **Room eligibility**
   - `TimetableRoomEligibilityService.idsByType()` → roomIds per type

6. **Solver invocation**
   - 30-second timeout, max 2 subjects per period (configurable)

7. **Output handling**
   - **Dry-run**: Saves to `preview_payload` (status = 'draft')
   - **Production**: Persists to `timetable_slots` + `timetable_conflicts` (status = 'active')

8. **Notifications** (non-blocking)
   - Dispatches `NotifyTimetableChangesJob` with diff (§10)
   - Broadcasts `TimetableGenerated` event

### Key Methods

```php
private function runSolver(TimetableCalendar): SolverResult;
private function buildAvailablePeriods(TimetableCalendar, array): array;
private function buildRoomsByType(TimetableCalendar): array;
private function buildPeriodMeta(TimetableCalendar): array;
private function persist(TimetableCalendar, SolverResult, int): void;  // §15 optimistic lock
private function computeDiff(TimetableCalendar, SolverResult): array;  // §10 diff for notifications
private function serializeAssignment(SolverResult): array;
private function qualityScore(SolverResult): float;  // % of lessons scheduled
```

---

## 5. TimetableWizard UI Integration

**Files:**
- `app/Livewire/Coordinacion/Timetable/TimetableWizard.php` (coordinator view)
- `app/Livewire/Planning/Timetable/TimetableWizard.php` (planning view)
- `resources/views/livewire/coordinacion/timetable/timetable-wizard.blade.php`

### Step Structure (SPEC-TIMETABLE-001 §5)

1. **Step 1 — Calendar Selection**
   - Select/create `TimetableCalendar`
   - Sets academic plan (`pestudio_id`)

2. **Step 2 — Shift Configuration**
   - Create/select shift (`TimetableShift`)
   - Configure periods (`TimetablePeriod`)

3. **Step 3 — Lesson Loading**
   - Map `Pevaluacion` (academic loads) → `TimetableLesson`
   - Assign block counts (theoretical/practical)
   - Mark room type requirements

4. **Step 4 — Availability & Locking**
   - Teacher availability (`TimetableTeacherAvailability`)
   - Lock lessons to specific periods

5. **Step 5 — Generation & Preview**
   - Trigger `GenerateTimetableJob` (dry-run mode)
   - Display preview with conflicts
   - Publish (activate) if acceptable

### Livewire Properties

```php
public int $currentStep = 1;
public int $calendarId;
public int $shiftId;
public array $periods;
public array $lessons;
public bool $showCreateCalendarForm;
public bool $showEditCalendarForm;
// ...and many more for step state
```

---

## 6. Database Models

**Directory:** `app/Models/app/Timetable/`

| Model | Purpose |
|-------|---------|
| `TimetableCalendar` | Main entity: calendar ↔ pestudio, status, version |
| `TimetablePeriod` | A single class period (day, order, shift) |
| `TimetableShift` | Shift definition (morning, afternoon, etc.) |
| `TimetableLesson` | Lesson instance (pevaluacion, blocks, locked state) |
| `TimetableSlot` | Result: lesson ↔ period ↔ room assignment |
| `TimetableConflict` | Unassigned lessons or validation failures |
| `TimetableRoom` | Classroom entity with type & capacity |
| `TimetableTeacherAvailability` | Availability matrix (teacher × period × availability) |
| `TimetableAbsence` | Teacher absence records |
| `TimetableSubstituteAssignment` | Substitute teacher assignments |
| `TimetableChangeLog` | Audit trail of changes |
| `TimetableCalendarVersion` | Version snapshots |

---

## 7. Config Files & Logging

### Config: `config/timetable.php`

```php
return [
    'legacy_csv_dir' => env(
        'TIMETABLE_LEGACY_CSV_DIR',
        base_path('blueprint/school-timetable/legacy/csv')
    ),
];
```

- Path to legacy CSV imports for fallback strategy
- Used by `TimetableImportLegacy` command

### Logging Channel: `config/logging.php`

```php
'timetable' => [
    'driver' => 'single',
    'path' => storage_path('logs/timetable.log'),
    'level' => 'debug',
]
```

**Logged Events:**
- Job start/end (timing, unassigned count, timeout flag)
- Version conflicts (stale job detection)
- Optimization failures (carriage race during activation)
- Notification dispatch failures

**Log Format:**
```
[calendar_id-timestamp] correlation_id: uniquely identifies a run
strategy: 'solver' or 'legacy'
elapsed_seconds: solver runtime
unassigned: count of unscheduled lessons
timed_out: true if deadline cut
```

**Example:**
```
[2026-09-12 14:30:45] timetable.INFO: GenerateTimetableJob: inicio
{correlation_id: "42-20260912143045", calendar_id: 42, dry_run: false, strategy: "solver"}

[2026-09-12 14:31:02] timetable.INFO: GenerateTimetableJob: fin
{correlation_id: "42-20260912143045", elapsed_seconds: 17.23, unassigned: 2, timed_out: false}
```

---

## 8. Test Patterns

**Directory:** `tests/`

### Unit Tests

**File:** `tests/Unit/Timetable/TimetableSolverTest.php`

Pure PHP tests (no Eloquent, no DB):

```php
class TimetableSolverTest extends TestCase {
    // Setup: 5 days × 6 periods = 30 periods
    private array $periods;
    private array $periodMeta;
    
    // Helper: build solver with lessons + availability
    private function solver(array $lessons, int $timeLimit = 30): TimetableSolver
    
    // Helper: create lesson DTO
    private function lesson($id, $profesor, $blocksT, $blocksP, ...): LessonToSchedule
    
    // Test cases:
    public function test_feasible_small_dataset_solves_without_conflicts(): void;
    public function test_solver_revisits_earlier_assignments_to_preserve_all_blocks(): void;
    public function test_infeasible_dataset_reports_unassigned_without_double_booking(): void;
    // ...more
}
```

### Feature Tests

**Files:** `tests/Feature/Timetable/`

Integration tests with DB transactions + Livewire:

- `GenerateTimetableJobTest.php` — Job dispatch, persistence, conflicts
- `TimetableWizardTest.php` — Step navigation, form validation, dry-run
- `TimetableEditorTest.php` — Manual slot movement, validation
- `TimetablePublicationTest.php` — Activation, version locking
- `TimetableEndToEndTest.php` — Full workflow (calendar → generation → publication)

**Pattern:**
```php
public function test_generates_without_conflicts_and_updates_status(): void {
    $calendar = TimetableCalendar::factory()->create();
    $lesson = TimetableLesson::factory()->for($calendar)->create();
    
    GenerateTimetableJob::dispatch($calendar->id);
    Queue::assertPushed(GenerateTimetableJob::class);
    // or: $this->artisan('queue:work')->expectsOutput('...');
    
    $this->assertDatabaseHas('timetable_slots', [...]);
    $this->assertDatabaseMissing('timetable_conflicts', [...]);
}
```

### Test Concerns

**File:** `tests/Concerns/TimetableShiftHelper.php`

Reusable builders for common setup:
- Create shift with periods
- Create lessons with Pevaluacion
- Assert no conflicts

---

## 9. Architecture Decisions (ADRs)

| ADR | Title | Summary |
|-----|-------|---------|
| ADR-TT-001 | Lesson Envelope | Pevaluacion wraps docente, sección, turno |
| ADR-TT-002 | Transaction Pattern | Atomic persist with optimistic lock |
| ADR-TT-003 | Constraint Ordering | Schedule high-constraint lessons first |
| ADR-TT-004 | Block Type Separation | Theoretical ('t') vs. practical ('p') tracks |
| ADR-TT-005 | Room Type Match | Practical blocks require eligible rooms |
| ADR-TT-006 | (Reserved) | — |
| ADR-TT-007 | Lock First | Pre-assigned + locked lessons occupy before free |
| ADR-TT-008 | Room Tracking | Only track when roomId != null (avoid key collision) |
| ADR-TT-009 | Partial Solution | Preserve best partial if timeout or infeasible |
| ADR-TT-010 | Room Exemption | Theoretical blocks don't require dedicated room |
| ADR-TT-011 | Adaptive Pool | Combination pool grows with lesson block count |

---

## 10. Service Layer

**Directory:** `app/Services/Timetable/`

| Service | Purpose |
|---------|---------|
| `TimetableSolver` | Core CSP solver (§1 above) |
| `ConflictValidator` | Synchronous hard constraint check (§3) |
| `TimetableAvailabilityService` | Check teacher availability per period |
| `TimetableRoomEligibilityService` | Room filtering by type & capacity |
| `TimetablePublicationReadinessService` | Validate calendar ready for activation |
| `TimetableLessonPersistenceService` | Bulk lesson creation from Pevaluacion |
| `TimetableViewService` | Grid/calendar rendering |
| `TimetableAiDraftService` | AI-assisted suggestions (if enabled) |

---

## 11. Quick Reference: Critical Files

```
app/
├── Services/Timetable/Solver/
│   ├── TimetableSolver.php          ← Core backtracking solver
│   ├── LessonToSchedule.php         ← Input DTO
│   ├── SlotCandidate.php            ← Slot representation
│   ├── SolverResult.php             ← Output DTO
│   └── SchedulingContext.php        ← Mutable state
├── Services/Timetable/
│   ├── ConflictValidator.php        ← Hard constraint checks
│   ├── TimetableAvailabilityService.php
│   ├── TimetableRoomEligibilityService.php
│   └── [other services]
├── Jobs/Timetable/
│   ├── GenerateTimetableJob.php     ← Queue entry point
│   └── NotifyTimetableChangesJob.php
├── Livewire/
│   ├── Coordinacion/Timetable/TimetableWizard.php  ← UI (5 steps)
│   ├── Planning/Timetable/TimetableWizard.php
│   ├── TimetableEditor.php          ← Manual editor
│   └── [other components]
├── Models/app/Timetable/
│   ├── TimetableCalendar.php
│   ├── TimetableLesson.php
│   ├── TimetableSlot.php
│   ├── TimetablePeriod.php
│   └── [13 models total]
config/
├── timetable.php                    ← CSV dir config
└── logging.php                      ← Timetable log channel
tests/
├── Unit/Timetable/
│   └── TimetableSolverTest.php
└── Feature/Timetable/
    ├── GenerateTimetableJobTest.php
    ├── TimetableWizardTest.php
    ├── TimetableEditorTest.php
    └── [~12 test files]
```

---

## 12. Flow Diagram: Wizard → Job → Persistence

```
[TimetableWizard Step 5]
    ↓
    → Validate dry-run (ConflictValidator)
    → Queue GenerateTimetableJob(calendarId, dryRun=true)
    ↓
[GenerateTimetableJob.handle()]
    ↓
    → Load lessons (TimetableLesson with pevaluacion)
    → Build LessonToSchedule[] DTOs
    → Get availablePeriodsByTeacher (TimetableAvailabilityService)
    → Get roomsByType (TimetableRoomEligibilityService)
    ↓
[TimetableSolver.solve() — 30s timeout]
    ↓
    → Reserve locked + pre-assigned slots
    → Backtrack on free lessons (ordered by constraint degree)
    → Return SolverResult {assignment, unassigned, timedOut}
    ↓
[Dry-run path]
    → Store preview_payload in TimetableCalendar
    → status = 'draft'
    ↓
[OR Production path (publish)]
    → Check version (optimistic lock)
    → Persist slots to TimetableSlot
    → Record unassigned in TimetableConflict
    → Demote old active calendars
    → status = 'active'
    → Dispatch NotifyTimetableChangesJob
```

---

## End of Summary

This document captures the structural essence of CFLA's timetable solver without exhaustive file dumps. The architecture prioritizes:

1. **Pure DTOs** — No Eloquent in solver core (fast, testable)
2. **Bounded search** — Timeout + node budget prevent explosion
3. **Partial solutions** — Preserve best progress even if incomplete
4. **Hard constraints** — Enforced in-solver via SchedulingContext
5. **Idempotent jobs** — Optimistic locking prevents race conditions
6. **Audit trail** — Logging + ChangeLog + events for traceability
7. **Test coverage** — Unit (solver) + Feature (job/UI) patterns
