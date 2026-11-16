<?php

namespace App\Services\Planning;

use App\Models\app\Academy\DidacticPlan;
use App\Models\app\Academy\DidacticPlanSession;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Persistencia y ciclo de vida del plan de clases / secuencia didáctica.
 *
 * El servicio concentra las reglas que no deben vivir en el componente
 * Livewire: reemplazo atómico de sesiones, indicadores y fuentes, checksum,
 * aprobación por planificación y versionado.
 */
class DidacticPlanService
{
    /**
     * Crea o actualiza un borrador con sus hijos en una sola transacción.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ValidationException
     */
    public function saveDraft(array $data, User $user): DidacticPlan
    {
        return DB::transaction(function () use ($data, $user): DidacticPlan {
            $attributes = $this->planAttributes($data, $user);

            $plan = ! empty($data['id'])
                ? DidacticPlan::query()->lockForUpdate()->findOrFail($data['id'])
                : $this->findDraft($attributes, (int) $user->id);

            if ($plan !== null && ! $plan->isDraft()) {
                throw ValidationException::withMessages([
                    'plan' => 'El plan ya fue aprobado. Crea una nueva versión para editarlo.',
                ]);
            }

            if ($plan !== null && isset($data['lock_version'])
                && (int) $data['lock_version'] !== (int) $plan->lock_version) {
                throw ValidationException::withMessages([
                    'plan' => 'Otra persona modificó este borrador. Recarga antes de guardar.',
                ]);
            }

            if ($plan === null) {
                $plan = DidacticPlan::query()->create($attributes + [
                    'status' => DidacticPlan::STATUS_DRAFT,
                    'version' => max(1, (int) ($data['version'] ?? 1)),
                    'lock_version' => 0,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            } else {
                $plan->fill($attributes);
                $plan->updated_by = $user->id;
                $plan->lock_version = (int) $plan->lock_version + 1;
                $plan->save();
            }

            $this->syncChildren($plan, $data);
            $plan->checksum = $this->checksum($plan);
            $plan->save();

            return $plan->fresh(['sessions.indicators', 'indicators', 'sources']);
        });
    }

    /**
     * Aprueba el borrador con el usuario de planificación autenticado.
     *
     * @throws ValidationException
     */
    public function approve(DidacticPlan $plan, User $user): DidacticPlan
    {
        return DB::transaction(function () use ($plan, $user): DidacticPlan {
            $locked = DidacticPlan::query()->lockForUpdate()->findOrFail($plan->id);

            if (! $locked->isDraft()) {
                throw ValidationException::withMessages([
                    'plan' => 'Solo un borrador puede aprobarse.',
                ]);
            }

            $errors = $this->validationErrorsForApproval($locked);

            if ($errors !== []) {
                throw ValidationException::withMessages(['plan' => $errors]);
            }

            $key = DidacticPlan::approvedKeyFor(
                (int) $locked->seccion_id,
                $locked->lapso_id !== null ? (int) $locked->lapso_id : null,
                $locked->period_start->toDateString(),
                $locked->period_end->toDateString(),
            );

            DidacticPlan::query()
                ->where('approved_key', $key)
                ->lockForUpdate()
                ->get()
                ->each(function (DidacticPlan $previous) use ($locked): void {
                    $previous->forceFill([
                        'status' => DidacticPlan::STATUS_SUPERSEDED,
                        'approved_key' => null,
                        'superseded_by_id' => $locked->id,
                    ])->save();
                });

            $locked->forceFill([
                'status' => DidacticPlan::STATUS_APPROVED,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'approved_key' => $key,
                'updated_by' => $user->id,
            ])->save();

            return $locked->fresh(['sessions.indicators', 'indicators', 'sources']);
        });
    }

    /**
     * Copia un plan aprobado a un nuevo borrador con versión incrementada.
     */
    public function newVersion(DidacticPlan $plan, User $user): DidacticPlan
    {
        $plan->loadMissing(['sessions.indicators', 'sources']);

        $payload = $this->toPayload($plan);
        $payload['id'] = null;
        $payload['version'] = (int) $plan->version + 1;

        return $this->saveDraft($payload, $user);
    }

    /**
     * Checklist de aprobación calculado sobre el plan persistido.
     *
     * @return array<int, string> Errores; vacío cuando puede aprobarse.
     */
    public function validationErrorsForApproval(DidacticPlan $plan): array
    {
        $plan->loadMissing(['sessions.indicators', 'indicators']);

        $errors = [];
        $hasJustification = trim((string) $plan->notes) !== '';

        if ($plan->sessions->isEmpty()) {
            $errors[] = 'El plan necesita al menos una sesión de clase.';
        }

        foreach ($plan->sessions as $index => $session) {
            $label = 'Sesión '.($index + 1);

            if (! $session->date) {
                $errors[] = "{$label}: falta la fecha.";
            } elseif ($session->date->lt($plan->period_start) || $session->date->gt($plan->period_end)) {
                $errors[] = "{$label}: la fecha está fuera del periodo del plan.";
            }

            if (trim((string) $session->title) === '') {
                $errors[] = "{$label}: falta el título o tema.";
            }

            $missingMoments = array_filter([
                'inicio' => trim((string) $session->start) === '',
                'desarrollo' => trim((string) $session->development) === '',
                'cierre' => trim((string) $session->close) === '',
            ]);

            if ($missingMoments !== [] && ! $hasJustification) {
                $errors[] = "{$label}: falta ".implode(', ', array_keys($missingMoments)).
                    '. Completa los momentos o justifica la ausencia en las notas del plan.';
            }

            if (trim((string) $session->evaluation_description) === '' && ! $hasJustification) {
                $errors[] = "{$label}: falta la actividad evaluativa o la justificación en notas.";
            }

            $hasIndicators = $session->indicators->isNotEmpty()
                || $plan->indicators->whereNull('didactic_plan_session_id')->isNotEmpty();

            if (! $hasIndicators && ! $hasJustification) {
                $errors[] = "{$label}: agrega al menos un indicador o justifica la ausencia en notas.";
            }
        }

        $ods = $plan->ods ?? [];
        foreach ($plan->sessions as $session) {
            $ods = array_merge($ods, $session->ods ?? []);
        }

        if ($ods === [] && ! $hasJustification) {
            $errors[] = 'El plan no menciona ningún ODS. Agrega uno o justifica la ausencia en notas.';
        }

        return $errors;
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(DidacticPlan $plan): array
    {
        $plan->loadMissing(['sessions.indicators', 'sources']);

        return [
            'id' => $plan->id,
            'version' => $plan->version,
            'lock_version' => $plan->lock_version,
            'pestudio_id' => $plan->pestudio_id,
            'grado_id' => $plan->grado_id,
            'seccion_id' => $plan->seccion_id,
            'lapso_id' => $plan->lapso_id,
            'period_start' => $plan->period_start->toDateString(),
            'period_end' => $plan->period_end->toDateString(),
            'axis' => $plan->axis,
            'project_title' => $plan->project_title,
            'objective' => $plan->objective,
            'ods' => $plan->ods ?? [],
            'notes' => $plan->notes,
            'pdf_notes' => $plan->pdf_notes,
            'filter_snapshot' => $plan->filter_snapshot ?? [],
            'filter_hash' => $plan->filter_hash,
            'universe_hash' => $plan->universe_hash,
            'filtered_count' => $plan->filtered_count,
            'selected_count' => $plan->selected_count,
            'sessions' => $plan->sessions->map(fn (DidacticPlanSession $session): array => [
                'date' => $session->date?->toDateString(),
                'day_index' => $session->day_index,
                'sort_order' => $session->sort_order,
                'title' => $session->title,
                'thematic' => $session->thematic,
                'objective' => $session->objective,
                'start' => $session->start,
                'development' => $session->development,
                'close' => $session->close,
                'resources' => $session->resources,
                'evaluation_instrument' => $session->evaluation_instrument,
                'evaluation_description' => $session->evaluation_description,
                'observations' => $session->observations,
                'ods' => $session->ods ?? [],
                'is_non_teaching' => $session->is_non_teaching,
                'source_activity_id' => $session->source_activity_id,
                'source_field' => $session->source_field,
                'source_excerpt' => $session->source_excerpt,
                'source_checksum' => $session->source_checksum,
                'confidence' => $session->confidence,
                'indicators' => $session->indicators->map(fn ($indicator): array => [
                    'name' => $indicator->name,
                    'weighting' => $indicator->weighting,
                    'is_quantitative' => $indicator->is_quantitative,
                    'origin' => $indicator->origin,
                    'source_achievement_id' => $indicator->source_achievement_id,
                ])->all(),
            ])->all(),
            'sources' => $plan->sources->map(fn ($source): array => [
                'activity_id' => $source->activity_id,
                'pevaluacion_id' => $source->pevaluacion_id,
                'activity_checksum' => $source->activity_checksum,
                'fields_used' => $source->fields_used,
                'excerpt' => $source->excerpt,
                'included' => $source->included,
            ])->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function planAttributes(array $data, User $user): array
    {
        $snapshot = (array) ($data['filter_snapshot'] ?? []);
        $filterHash = (string) ($data['filter_hash'] ?? '');
        $universeHash = (string) ($data['universe_hash'] ?? '');

        return [
            'pestudio_id' => (int) $data['pestudio_id'],
            'grado_id' => (int) $data['grado_id'],
            'seccion_id' => (int) $data['seccion_id'],
            'lapso_id' => ! empty($data['lapso_id']) ? (int) $data['lapso_id'] : null,
            'period_start' => (string) $data['period_start'],
            'period_end' => (string) $data['period_end'],
            'axis' => (string) ($data['axis'] ?? 'Alimentación, cultura e identidad'),
            'project_title' => $this->nullableString($data['project_title'] ?? null, 180),
            'objective' => $this->nullableString($data['objective'] ?? null),
            'ods' => array_values(array_unique((array) ($data['ods'] ?? []))),
            'source_mode' => (string) ($data['source_mode'] ?? 'filtered_activities'),
            'filter_snapshot' => $snapshot,
            'filter_hash' => $filterHash !== '' ? $filterHash : hash('sha256', json_encode($snapshot)),
            'universe_hash' => $universeHash !== '' ? $universeHash : hash('sha256', ''),
            'filtered_count' => (int) ($data['filtered_count'] ?? 0),
            'selected_count' => (int) ($data['selected_count'] ?? 0),
            'notes' => $this->nullableString($data['notes'] ?? null),
            'pdf_notes' => $this->nullableString($data['pdf_notes'] ?? null),
            'updated_by' => $user->id,
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function findDraft(array $attributes, int $userId): ?DidacticPlan
    {
        return DidacticPlan::query()
            ->where('seccion_id', $attributes['seccion_id'])
            ->where('lapso_id', $attributes['lapso_id'])
            ->whereDate('period_start', $attributes['period_start'])
            ->whereDate('period_end', $attributes['period_end'])
            ->where('status', DidacticPlan::STATUS_DRAFT)
            ->where('created_by', $userId)
            ->lockForUpdate()
            ->first();
    }

    /**
     * Reemplaza sesiones, indicadores y fuentes del plan.
     *
     * @param  array<string, mixed>  $data
     */
    private function syncChildren(DidacticPlan $plan, array $data): void
    {
        $plan->sessions()->delete();
        $plan->indicators()->delete();
        $plan->sources()->delete();

        foreach ((array) ($data['sessions'] ?? []) as $index => $row) {
            $session = $plan->sessions()->create([
                'date' => $row['date'] ?? null,
                'day_index' => (int) ($row['day_index'] ?? 1),
                'sort_order' => (int) ($row['sort_order'] ?? $index),
                'title' => (string) ($row['title'] ?? 'Sesión sin título'),
                'thematic' => $this->nullableString($row['thematic'] ?? null, 180),
                'objective' => $this->nullableString($row['objective'] ?? null),
                'start' => $this->nullableString($row['start'] ?? null),
                'development' => $this->nullableString($row['development'] ?? null),
                'close' => $this->nullableString($row['close'] ?? null),
                'resources' => $this->nullableString($row['resources'] ?? null),
                'evaluation_instrument' => $this->nullableString($row['evaluation_instrument'] ?? null, 60),
                'evaluation_description' => $this->nullableString($row['evaluation_description'] ?? null),
                'observations' => $this->nullableString($row['observations'] ?? null),
                'ods' => array_values(array_unique((array) ($row['ods'] ?? []))),
                'is_non_teaching' => (bool) ($row['is_non_teaching'] ?? false),
                'source_activity_id' => ! empty($row['source_activity_id']) ? (int) $row['source_activity_id'] : null,
                'source_field' => $this->nullableString($row['source_field'] ?? null, 40),
                'source_excerpt' => $this->nullableString($row['source_excerpt'] ?? null),
                'source_checksum' => $this->nullableString($row['source_checksum'] ?? null, 64),
                'confidence' => isset($row['confidence']) ? (float) $row['confidence'] : null,
            ]);

            foreach ((array) ($row['indicators'] ?? []) as $order => $indicator) {
                $name = trim((string) ($indicator['name'] ?? ''));

                if ($name === '') {
                    continue;
                }

                $session->indicators()->create([
                    'didactic_plan_id' => $plan->id,
                    'name' => mb_substr($name, 0, 191),
                    'weighting' => isset($indicator['weighting']) && $indicator['weighting'] !== ''
                        ? (float) $indicator['weighting']
                        : null,
                    'is_quantitative' => (bool) ($indicator['is_quantitative'] ?? false),
                    'origin' => (string) ($indicator['origin'] ?? 'manual'),
                    'source_achievement_id' => ! empty($indicator['source_achievement_id'])
                        ? (int) $indicator['source_achievement_id']
                        : null,
                    'sort_order' => (int) ($indicator['sort_order'] ?? $order),
                ]);
            }
        }

        foreach ((array) ($data['sources'] ?? []) as $source) {
            if (empty($source['activity_id'])) {
                continue;
            }

            $plan->sources()->create([
                'activity_id' => (int) $source['activity_id'],
                'pevaluacion_id' => ! empty($source['pevaluacion_id']) ? (int) $source['pevaluacion_id'] : null,
                'activity_checksum' => $this->nullableString($source['activity_checksum'] ?? null, 64),
                'fields_used' => array_values((array) ($source['fields_used'] ?? [])),
                'excerpt' => $this->nullableString($source['excerpt'] ?? null),
                'included' => (bool) ($source['included'] ?? true),
            ]);
        }
    }

    private function checksum(DidacticPlan $plan): string
    {
        $plan->loadMissing(['sessions.indicators', 'sources']);

        return hash('sha256', json_encode([
            'plan' => Arr::only($plan->getAttributes(), [
                'seccion_id', 'period_start', 'period_end', 'axis', 'project_title',
                'objective', 'ods', 'notes', 'pdf_notes',
            ]),
            'sessions' => $plan->sessions->toArray(),
            'sources' => $plan->sources->toArray(),
        ], JSON_UNESCAPED_UNICODE));
    }

    private function nullableString(mixed $value, ?int $limit = null): ?string
    {
        $text = trim((string) $value);

        if ($text === '') {
            return null;
        }

        return $limit !== null ? mb_substr($text, 0, $limit) : $text;
    }
}
