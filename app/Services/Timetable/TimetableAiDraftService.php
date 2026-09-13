<?php

namespace App\Services\Timetable;

use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableRoom;
use App\Services\OpenRouterService;
use JsonException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TimetableAiDraftService
{
    public function __construct(
        private readonly OpenRouterService $openRouter,
        private readonly TimetablePublicationReadinessService $readiness,
    ) {}

    /**
     * Generate and validate a structured proposal without persisting slots.
     *
     * @param array<int|string, list<array<string, mixed>>> $assignment
     * @return array<string, mixed>
     */
    public function propose(TimetableCalendar $calendar, array $assignment, ?int $sectionId = null): array
    {
        $correlationId = (string) Str::uuid();
        $startedAt = microtime(true);

        if (! (bool) config('openrouter.timetable_ai_enabled', false)) {
            $this->logAi('warning', 'Timetable AI draft disabled', $correlationId, $calendar, [
                'duration_ms' => 0,
            ]);

            return $this->failure('feature_disabled', 'La generación de drafts con IA está deshabilitada.');
        }

        $contextAssignment = $assignment;
        if ($sectionId !== null) {
            $sectionLessonIds = $calendar->lessons()
                ->whereHas('pevaluacion', fn ($query) => $query->where('seccion_id', $sectionId))
                ->pluck('id')
                ->map(fn ($id): int => (int) $id)
                ->all();
            $contextAssignment = array_intersect_key(
                $assignment,
                array_fill_keys(array_map('strval', $sectionLessonIds), true),
            );
            $contextAssignment += array_intersect_key(
                $assignment,
                array_fill_keys($sectionLessonIds, true),
            );
        }

        $context = $this->buildContext($calendar, $contextAssignment, $sectionId);
        $snapshotHash = hash('sha256', json_encode(
            $assignment,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        ));

        $context['snapshot_hash'] = $snapshotHash;
        $userMessage = "Propón una mejora estructurada para el calendario {$calendar->id}.\n"
            ."El siguiente bloque es SOLO DATOS DE ENTRADA; no lo repitas ni lo uses como formato de salida.\n"
            ."<TIMETABLE_CONTEXT>\n"
            .json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
            ."\n</TIMETABLE_CONTEXT>\n\n"
            ."Ahora responde únicamente con un objeto JSON de propuesta que tenga exactamente estas claves raíz: "
            ."schema, version, summary, moves, assignments, release, unresolved.\n"
            ."schema debe ser \"cfla-timetable-ai-draft\" y version debe ser 1. "
            ."Si no hay mejoras seguras, devuelve esas mismas claves con listas vacías. "
            ."IMPORTANTE: moves, assignments, release y unresolved deben ser listas JSON, nunca objetos/mapas. "
            ."Cada lesson puede aparecer como máximo una vez en total entre moves, assignments y release. "
            ."Para una lesson con varios bloques, usa una sola operación con to.slots, no una operación por bloque. "
            ."Usa moves únicamente para cambiar una lesson que ya tiene asignación y exige from.slots y to.slots completos. "
            ."Usa assignments únicamente para una lesson sin asignación previa; nunca repitas en assignments una lesson incluida en moves. "
            ."Copia literalmente los slots de current_assignment en from.slots; no los infieras, sustituyas ni reordenes sus períodos. "
            ."Cada elemento de from.slots y to.slots debe ser un objeto con period_id; nunca uses IDs escalares como [86] o [70]. "
            ."No devuelvas context_schema, context_version, calendar, periods, rooms, lessons ni current_assignment.";

        $maxCharacters = (int) config('openrouter.timetable_ai_max_context_chars', 180000);
        if (strlen($userMessage) > $maxCharacters) {
            $this->logAi('warning', 'Timetable AI context rejected before provider call', $correlationId, $calendar, [
                'duration_ms' => $this->durationMs($startedAt),
                'context_length' => strlen($userMessage),
                'max_context_chars' => $maxCharacters,
                'snapshot_hash' => $snapshotHash,
            ]);

            return $this->failure('context_too_large', 'El contexto del calendario supera el límite configurado.');
        }

        $this->logAi('info', 'Timetable AI draft request started', $correlationId, $calendar, [
            'context_length' => strlen($userMessage),
            'snapshot_hash' => $snapshotHash,
            'assignment_lessons' => count($assignment),
            'context_assignment_lessons' => count($contextAssignment),
            'section_id' => $sectionId,
            'model_configured' => config('openrouter.timetable_ai_openrouter_model'),
            'max_tokens' => (int) config('openrouter.timetable_ai_openrouter_max_tokens', 8192),
            'timeout_seconds' => (int) config('openrouter.timetable_ai_openrouter_timeout', 60),
        ]);

        $result = $this->openRouter->ask(
            $this->systemPrompt(),
            $userMessage,
            [
                'model' => config('openrouter.timetable_ai_openrouter_model'),
                'max_tokens' => (int) config('openrouter.timetable_ai_openrouter_max_tokens', 8192),
                'temperature' => (float) config('openrouter.timetable_ai_openrouter_temperature', 0.7),
                'timeout' => (int) config('openrouter.timetable_ai_openrouter_timeout', 60),
                'response_format' => ['type' => 'json_object'],
            ],
        );

        if (! $result['success'] || ! is_string($result['content'])) {
            $this->logAi('error', 'Timetable AI provider failed', $correlationId, $calendar, [
                'duration_ms' => $this->durationMs($startedAt),
                'model' => $result['model'] ?? config('openrouter.timetable_ai_openrouter_model'),
                'provider_error' => $result['error'] ?? 'unknown',
            ]);

            return $this->failure('provider_error', $result['error'] ?? 'OpenRouter no devolvió una propuesta.');
        }

        $this->logAi('info', 'Timetable AI provider response received', $correlationId, $calendar, [
            'duration_ms' => $this->durationMs($startedAt),
            'model' => $result['model'] ?? config('openrouter.timetable_ai_openrouter_model'),
            'response_length' => strlen($result['content']),
            'response_hash' => hash('sha256', $result['content']),
            'usage' => $result['usage'],
            'response_excerpt' => $this->safeExcerpt($result['content']),
        ]);

        try {
            $proposal = $this->decodeJson($result['content']);
        } catch (JsonException $exception) {
            $this->logAi('warning', 'Timetable AI returned invalid JSON', $correlationId, $calendar, [
                'duration_ms' => $this->durationMs($startedAt),
                'content_length' => strlen($result['content']),
                'content_hash' => hash('sha256', $result['content']),
                'json_error' => $exception->getMessage(),
                'response_excerpt' => $this->safeExcerpt($result['content']),
            ]);

            return $this->failure(
                'invalid_json',
                'OpenRouter no devolvió un objeto JSON utilizable. Intenta nuevamente o cambia el modelo configurado.',
            );
        }

        if (isset($proposal['context_schema']) || isset($proposal['context_version'])) {
            $this->logAi('warning', 'Timetable AI echoed input context', $correlationId, $calendar, [
                'duration_ms' => $this->durationMs($startedAt),
                'model' => $result['model'],
                'root_keys' => array_keys($proposal),
            ]);

            return $this->failure(
                'context_echo',
                'El modelo devolvió el contexto de entrada en lugar de una propuesta. Intenta nuevamente o cambia el modelo configurado.',
            );
        }

        $validation = $this->validateProposal($calendar, $context, $proposal);
        if (! $validation['valid']) {
            $this->logAi('warning', 'Timetable AI proposal rejected by local validation', $correlationId, $calendar, [
                'duration_ms' => $this->durationMs($startedAt),
                'model' => $result['model'],
                'root_keys' => array_keys($proposal),
                'field_shapes' => $this->proposalFieldShapes($proposal),
                'validation_errors' => $validation['errors'],
                'response_hash' => hash('sha256', $result['content']),
            ]);

            return [
                'success' => false,
                'status' => 'rejected',
                'error_code' => 'proposal_rejected',
                'error' => 'La propuesta fue rechazada por las validaciones locales.',
                'errors' => $validation['errors'],
                'snapshot_hash' => $snapshotHash,
                'model' => $result['model'],
                'usage' => $result['usage'],
            ];
        }

        $proposedAssignment = $this->applyProposal($assignment, $proposal);
        $proposalReadiness = $this->readiness->evaluate($calendar, [
            'assignment' => $proposedAssignment,
            'unassigned' => $this->unresolvedLessonIds($proposal['unresolved'] ?? []),
        ]);

        $this->logAi('info', 'Timetable AI draft validated', $correlationId, $calendar, [
            'duration_ms' => $this->durationMs($startedAt),
            'model' => $result['model'],
            'root_keys' => array_keys($proposal),
            'field_shapes' => $this->proposalFieldShapes($proposal),
            'status' => $proposalReadiness['ready'] ? 'validated' : 'partial',
            'readiness_ready' => $proposalReadiness['ready'],
            'hard_conflicts_count' => count($proposalReadiness['hard_conflicts'] ?? []),
            'hard_conflicts' => $proposalReadiness['hard_conflicts'] ?? [],
            'warnings_count' => count($proposalReadiness['warnings'] ?? []),
        ]);

        return [
            'success' => true,
            'status' => $proposalReadiness['ready']
                && $this->unresolvedLessonIds($proposal['unresolved'] ?? []) === []
                ? 'validated'
                : 'partial',
            'snapshot_hash' => $snapshotHash,
            'model' => $result['model'],
            'usage' => $result['usage'],
            'proposal' => $proposal,
            'proposed_assignment' => $sectionId === null
                ? $proposedAssignment
                : array_replace($assignment, $proposedAssignment),
            'proposed_unassigned' => $this->unresolvedLessonIds($proposal['unresolved'] ?? []),
            'readiness' => $proposalReadiness,
        ];
    }

    /**
     * @param array<string, mixed> $assignment
     * @return array<string, mixed>
     */
    private function buildContext(TimetableCalendar $calendar, array $assignment, ?int $sectionId = null): array
    {
        $lessonsQuery = $calendar->lessons()
            ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.seccion.grado', 'pevaluacion.profesor'])
            ;
        if ($sectionId !== null) {
            $lessonsQuery->whereHas('pevaluacion', fn ($query) => $query->where('seccion_id', $sectionId));
        }
        $lessons = $lessonsQuery->get();
        $periods = $calendar->periods()->get();
        $rooms = TimetableRoom::query()
            ->where('status_active', true)
            ->where(function ($query) use ($calendar): void {
                $query->whereNull('seccion_id')
                    ->orWhereHas(
                        'seccion.grado',
                        fn ($grado) => $grado->where('pestudio_id', $calendar->pestudio_id),
                    );
            })
            ->get();

        return [
            'context_schema' => 'cfla-timetable-ai-context',
            'context_version' => 1,
            'calendar' => [
                'id' => (int) $calendar->id,
                'section_id' => $sectionId,
                'pestudio_id' => $calendar->pestudio_id ? (int) $calendar->pestudio_id : null,
                'lapso_id' => (int) $calendar->lapso_id,
                'version' => (int) $calendar->version,
                'strategy' => $calendar->strategy,
                'max_subjects_per_period' => (int) ($calendar->max_subjects_per_period ?? 2),
            ],
            'periods' => $periods->map(fn ($period): array => [
                'id' => (int) $period->id,
                'shift_id' => (int) $period->shift_id,
                'day_of_week' => (int) $period->day_of_week,
                'order_in_day' => (int) $period->order_in_day,
                'start_time' => $period->start_time,
                'end_time' => $period->end_time,
                'is_break' => (bool) $period->is_break,
            ])->all(),
            'rooms' => $rooms->map(fn ($room): array => [
                'id' => (int) $room->id,
                'type' => $room->type,
                'capacity' => $room->capacity ? (int) $room->capacity : null,
                'seccion_id' => $room->seccion_id ? (int) $room->seccion_id : null,
            ])->all(),
            'lessons' => $lessons->map(fn ($lesson): array => [
                'lesson_id' => (int) $lesson->id,
                'pevaluacion_id' => (int) $lesson->pevaluacion_id,
                'profesor_id' => (int) ($lesson->pevaluacion?->profesor_id ?? 0),
                'seccion_id' => (int) ($lesson->pevaluacion?->seccion_id ?? 0),
                'subject' => $lesson->pevaluacion?->pensum?->asignatura?->name,
                'teacher' => $lesson->pevaluacion?->profesor
                    ? trim(($lesson->pevaluacion->profesor->lastname ?? '').' '.($lesson->pevaluacion->profesor->name ?? ''))
                    : null,
                'section' => $lesson->pevaluacion?->seccion?->name,
                'weekly_blocks_t' => (int) $lesson->weekly_blocks_t,
                'weekly_blocks_p' => (int) $lesson->weekly_blocks_p,
                'shift_id' => (int) $lesson->shift_id,
                'is_half_group' => (bool) $lesson->is_half_group,
                'grupo_estable_id' => $lesson->pevaluacion?->grupo_estable_id
                    ? (int) $lesson->pevaluacion->grupo_estable_id
                    : null,
                'locked' => (bool) $lesson->locked,
            ])->all(),
            'current_assignment' => $assignment,
        ];
    }

    /**
     * @param array<string, mixed> $proposal
     * @return array{valid: bool, errors: list<string>}
     */
    private function validateProposal(TimetableCalendar $calendar, array $context, array $proposal): array
    {
        $errors = [];
        $allowedKeys = ['schema', 'version', 'summary', 'moves', 'assignments', 'release', 'unresolved'];
        $unknownKeys = array_diff(array_keys($proposal), $allowedKeys);

        if ($unknownKeys !== []) {
            $errors[] = 'La propuesta contiene campos no permitidos: '.implode(', ', $unknownKeys).'.';
        }
        if (($proposal['schema'] ?? null) !== 'cfla-timetable-ai-draft' || (int) ($proposal['version'] ?? 0) !== 1) {
            $errors[] = 'El schema o la versión de la propuesta no son compatibles.';
        }

        $lessons = collect($context['lessons'])->keyBy('lesson_id');
        $periods = collect($context['periods'])->keyBy('id');
        $rooms = collect($context['rooms'])->keyBy('id');
        $seenLessons = [];
        $maxMoves = (int) config('openrouter.timetable_ai_max_moves', 100);

        foreach (['moves', 'assignments', 'release', 'unresolved'] as $key) {
            if (! $this->isList($proposal[$key] ?? [])) {
                $errors[] = "El campo {$key} debe ser una lista.";
            }
        }

        $moves = $this->isList($proposal['moves'] ?? null) ? $proposal['moves'] : [];
        $assignments = $this->isList($proposal['assignments'] ?? null) ? $proposal['assignments'] : [];
        $release = $this->isList($proposal['release'] ?? null) ? $proposal['release'] : [];
        $unresolved = $this->isList($proposal['unresolved'] ?? null) ? $proposal['unresolved'] : [];

        if (count($moves) + count($assignments) + count($release) > $maxMoves) {
            $errors[] = 'La propuesta supera el máximo de movimientos permitido.';
        }

        foreach (array_merge($moves, $assignments, $release) as $item) {
            if (! is_array($item)) {
                $errors[] = 'Cada operación debe ser un objeto JSON.';
                continue;
            }
            $lessonId = (int) ($item['lesson_id'] ?? 0);
            if (! $lessons->has($lessonId)) {
                $errors[] = "La lesson #{$lessonId} no pertenece al calendario.";
                continue;
            }
            if (isset($seenLessons[$lessonId])) {
                $errors[] = "La lesson #{$lessonId} aparece en operaciones contradictorias.";
            }
            $seenLessons[$lessonId] = true;
            if (($lessons[$lessonId]['locked'] ?? false)
                && (isset($item['to']) || in_array($item, $release, true))) {
                $errors[] = "La lesson bloqueada #{$lessonId} no puede modificarse.";
            }
            if (array_key_exists('to', $item) && ! array_key_exists('from', $item)
                && in_array($item, $moves, true)) {
                $errors[] = "El movimiento de la lesson #{$lessonId} debe incluir su asignación de origen.";
            }

            foreach (['from', 'to'] as $position) {
                $this->validateOperationSlotShape($errors, $lessonId, $item, $position);
            }

            foreach ($this->operationSlots($item, 'from') as $slot) {
                $this->validateSlot($errors, $lessonId, $slot, $periods, $rooms);
            }
            foreach ($this->operationSlots($item, 'to') as $slot) {
                $this->validateSlot($errors, $lessonId, $slot, $periods, $rooms);
            }

            if (isset($item['to']) && $this->operationSlots($item, 'to') !== []) {
                $required = (int) $lessons[$lessonId]['weekly_blocks_t']
                    + (int) $lessons[$lessonId]['weekly_blocks_p'];
                $targetSlots = $this->operationSlots($item, 'to');
                if (count($targetSlots) !== $required) {
                    $errors[] = "La lesson #{$lessonId} requiere {$required} bloques y la propuesta contiene ".count($targetSlots).'.';
                }
                if (count(array_unique(array_map(
                    fn (array $slot): int => (int) ($slot['period_id'] ?? 0),
                    $targetSlots,
                ))) !== count($targetSlots)) {
                    $errors[] = "La lesson #{$lessonId} repite un período en la propuesta.";
                }
                $practicalBlocks = count(array_filter(
                    $targetSlots,
                    fn (array $slot): bool => (bool) ($slot['is_practical'] ?? false),
                ));
                if ($practicalBlocks !== (int) $lessons[$lessonId]['weekly_blocks_p']) {
                    $errors[] = "La lesson #{$lessonId} no conserva la cantidad de bloques prácticos.";
                }
                if (isset($item['from'])) {
                    $expectedSlots = $context['current_assignment'][(string) $lessonId]
                        ?? $context['current_assignment'][$lessonId] ?? [];
                    $actualFromSlots = $this->operationSlots($item, 'from');
                    if ($this->canonicalSlots($actualFromSlots) !== $this->canonicalSlots($expectedSlots)) {
                        $expectedPeriods = implode(', ', $this->periodIds($expectedSlots));
                        $receivedPeriods = implode(', ', $this->periodIds($actualFromSlots));
                        $errors[] = "La asignación de origen de la lesson #{$lessonId} no coincide con el snapshot (esperados: [{$expectedPeriods}], recibidos: [{$receivedPeriods}]).";
                    }
                }
            }
        }

        foreach ($this->unresolvedLessonIds($unresolved) as $lessonId) {
            if (! $lessons->has($lessonId)) {
                $errors[] = "La lesson sin resolver #{$lessonId} no pertenece al calendario.";
            }
        }

        return ['valid' => $errors === [], 'errors' => array_values(array_unique($errors))];
    }

    /**
     * @param array<int|string, list<array<string, mixed>>> $assignment
     * @param array<string, mixed> $proposal
     * @return array<int|string, list<array<string, mixed>>>
     */
    private function applyProposal(array $assignment, array $proposal): array
    {
        foreach ($proposal['moves'] ?? [] as $move) {
            $lessonId = (int) $move['lesson_id'];
            $assignment[(string) $lessonId] = $this->operationSlots($move, 'to');
        }
        foreach ($proposal['assignments'] ?? [] as $item) {
            $assignment[(string) $item['lesson_id']] = $this->operationSlots($item, 'to');
        }
        foreach ($proposal['release'] ?? [] as $item) {
            unset($assignment[(string) $item['lesson_id']], $assignment[$item['lesson_id']]);
        }

        return $assignment;
    }

    /**
     * Accept both the explicit multi-block form ({to: {slots: [...]}}) and
     * the compact one-block form ({to: {...}}).
     *
     * @param array<string, mixed> $operation
     * @return list<array<string, mixed>>
     */
    private function operationSlots(array $operation, string $position): array
    {
        $value = $operation[$position] ?? null;
        if ($value === null && $position === 'to' && array_key_exists('period_id', $operation)) {
            $value = $operation;
        }
        if (! is_array($value)) {
            return [];
        }

        if (isset($value['slots']) && is_array($value['slots'])) {
            return array_values(array_filter($value['slots'], 'is_array'));
        }
        if (array_key_exists('period_id', $value)) {
            return [$value];
        }
        return [];
    }

    /**
     * Reject scalar slots instead of silently dropping them before validation.
     *
     * @param list<string> $errors
     * @param array<string, mixed> $operation
     */
    private function validateOperationSlotShape(array &$errors, int $lessonId, array $operation, string $position): void
    {
        $value = $operation[$position] ?? null;
        if (! is_array($value) || ! isset($value['slots']) || ! is_array($value['slots'])) {
            return;
        }

        foreach ($value['slots'] as $index => $slot) {
            if (! is_array($slot)) {
                $errors[] = "La lesson #{$lessonId} tiene un slot {$position}[{$index}] que debe ser un objeto JSON.";
                continue;
            }
            if (! array_key_exists('period_id', $slot)) {
                $errors[] = "La lesson #{$lessonId} tiene un slot {$position}[{$index}] sin period_id.";
            }
        }
    }

    /**
     * @param list<string> $errors
     * @param array<string, mixed> $slot
     * @param \Illuminate\Support\Collection<int, array<string, mixed>> $periods
     * @param \Illuminate\Support\Collection<int, array<string, mixed>> $rooms
     */
    private function validateSlot(array &$errors, int $lessonId, array $slot, $periods, $rooms): void
    {
        $periodId = (int) ($slot['period_id'] ?? 0);
        if (! $periods->has($periodId)) {
            $errors[] = "El período #{$periodId} no pertenece al calendario.";
        } elseif ($periods[$periodId]['is_break']) {
            $errors[] = "La lesson #{$lessonId} apunta al recreo #{$periodId}.";
        }
        $roomId = $slot['room_id'] ?? null;
        if ($roomId !== null && ! $rooms->has((int) $roomId)) {
            $errors[] = "El aula #{$roomId} no pertenece al calendario.";
        }
    }

    /**
     * @param list<array<string, mixed>> $slots
     */
    private function canonicalSlots(array $slots): string
    {
        $normalized = array_map(static fn (array $slot): array => [
            'period_id' => (int) ($slot['period_id'] ?? 0),
            'room_id' => isset($slot['room_id']) ? (int) $slot['room_id'] : null,
            'is_practical' => (bool) ($slot['is_practical'] ?? false),
        ], $slots);
        usort($normalized, static fn (array $a, array $b): int => $a['period_id'] <=> $b['period_id']);

        return json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    /**
     * @param list<array<string, mixed>> $slots
     * @return list<int>
     */
    private function periodIds(array $slots): array
    {
        return array_values(array_map(
            static fn (array $slot): int => (int) ($slot['period_id'] ?? 0),
            $slots,
        ));
    }

    /**
     * @param mixed $unresolved
     * @return list<int>
     */
    private function unresolvedLessonIds(mixed $unresolved): array
    {
        if (! is_array($unresolved)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            static fn (mixed $item): int => is_array($item)
                ? (int) ($item['lesson_id'] ?? 0)
                : (int) $item,
            $unresolved,
        ))));
    }

    private function isList(mixed $value): bool
    {
        return is_array($value) && array_is_list($value);
    }

    private function durationMs(float $startedAt): int
    {
        return (int) round((microtime(true) - $startedAt) * 1000);
    }

    private function safeExcerpt(string $content): string
    {
        $excerpt = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', trim($content)) ?? trim($content);

        return Str::limit($excerpt, 1200, '…');
    }

    /**
     * @return array<string, string>
     */
    private function proposalFieldShapes(array $proposal): array
    {
        $shapes = [];
        foreach (['schema', 'version', 'summary', 'moves', 'assignments', 'release', 'unresolved'] as $key) {
            $value = $proposal[$key] ?? null;
            $shapes[$key] = match (true) {
                is_array($value) && array_is_list($value) => 'list:'.count($value),
                is_array($value) => 'object:'.count($value),
                is_string($value) => 'string',
                is_int($value), is_float($value) => 'number',
                is_bool($value) => 'boolean',
                $value === null => 'missing_or_null',
                default => get_debug_type($value),
            };
        }

        return $shapes;
    }

    private function logAi(string $level, string $message, string $correlationId, TimetableCalendar $calendar, array $context = []): void
    {
        Log::channel('timetable')->{$level}($message, array_merge([
            'correlation_id' => $correlationId,
            'calendar_id' => (int) $calendar->id,
            'user_id' => auth()->id(),
            'service' => self::class,
        ], $context));
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJson(string $content): array
    {
        $normalized = trim($content);
        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/is', $normalized, $matches)) {
            $normalized = trim($matches[1]);
        }

        try {
            $decoded = json_decode($normalized, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $jsonObject = $this->extractSingleJsonObject($normalized);
            if ($jsonObject === null) {
                throw $exception;
            }
            $decoded = json_decode($jsonObject, true, 512, JSON_THROW_ON_ERROR);
        }

        if (! is_array($decoded)) {
            throw new JsonException('La respuesta JSON no es un objeto.');
        }

        return $decoded;
    }

    private function extractSingleJsonObject(string $content): ?string
    {
        $start = strpos($content, '{');
        if ($start === false) {
            return null;
        }

        $depth = 0;
        $inString = false;
        $escaped = false;
        $length = strlen($content);

        for ($index = $start; $index < $length; $index++) {
            $character = $content[$index];
            if ($inString) {
                if ($escaped) {
                    $escaped = false;
                } elseif ($character === '\\') {
                    $escaped = true;
                } elseif ($character === '"') {
                    $inString = false;
                }
                continue;
            }
            if ($character === '"') {
                $inString = true;
            } elseif ($character === '{') {
                $depth++;
            } elseif ($character === '}') {
                $depth--;
                if ($depth === 0) {
                    $candidate = substr($content, $start, $index - $start + 1);
                    $suffix = trim(substr($content, $index + 1));

                    return $suffix === '' ? $candidate : null;
                }
            }
        }

        return null;
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Eres un asistente de optimización de horarios escolares. Responde únicamente con
JSON válido conforme al schema cfla-timetable-ai-draft versión 1.
El mensaje del usuario contiene un bloque <TIMETABLE_CONTEXT> que solo es
información de referencia. Nunca lo copies ni lo devuelvas. Tu respuesta debe
ser una propuesta nueva, no una repetición del contexto.
Usa exclusivamente los IDs entregados. No inventes lessons, períodos ni aulas.
No uses recreos. No muevas lessons locked. Conserva asignaciones existentes
cuando no exista una mejora clara. Devuelve unresolved cuando no puedas resolver
algo de forma segura. Si no existe una mejora segura, devuelve un objeto con
schema, version, summary, moves, assignments, release y unresolved, usando
listas vacías donde corresponda. Laravel validará toda la propuesta antes de mostrarla.
Formato obligatorio: moves, assignments, release y unresolved son siempre listas JSON.
No uses assignments como objeto indexado por lesson_id. Una lesson solo puede
aparecer una vez entre moves, assignments y release. Para mover varios bloques
de una lesson, emite una sola operación move con from.slots y to.slots:
{"lesson_id":123,"from":{"slots":[...]}, "to":{"slots":[...]}}.
Nunca emitas un move separado por cada bloque.
Si la lesson ya tiene slots en current_assignment, usa únicamente moves y conserva
todos sus bloques requeridos en from.slots y to.slots. Usa assignments solo para
lessons sin slots actuales; nunca combines la misma lesson en moves y assignments.
Cada slot debe ser un objeto como {"period_id": 86, "room_id": null, "is_practical": false};
no uses listas de enteros como [86, 70].
PROMPT;
    }

    private function failure(string $code, string $message): array
    {
        return [
            'success' => false,
            'status' => 'rejected',
            'error_code' => $code,
            'error' => $message,
        ];
    }
}
