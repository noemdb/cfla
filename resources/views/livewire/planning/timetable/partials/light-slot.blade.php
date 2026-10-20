{{--
    Slot de una asignatura en la grilla del horario rápido (light).
    ADR-TT-017: admite `half` (0 = superior, 1 = inferior, null = bloque completo)
    y `half_block` (la unidad ocupa media franja).
    Variables: $cell, $targetPeriod, $highlightProfesorId
--}}
@php
    $cellProfesorId = (int) ($cell['profesor_id'] ?? 0);
    $cellHighlighted = ! empty($highlightProfesorId) && $cellProfesorId === (int) $highlightProfesorId;
    $cellDimmed = ! empty($highlightProfesorId) && ! $cellHighlighted;
    $cellHalf = isset($cell['half']) && $cell['half'] !== null ? (int) $cell['half'] : null;
    $cellHalfBlock = ! empty($cell['half_block']);
    $cellLocked = ! empty($cell['locked']);
@endphp
<div
    id="light-slot-{{ (int) $cell['lesson_id'] }}-{{ (int) $cell['period_id'] }}"
    wire:key="light-slot-{{ $cell['lesson_id'] }}-{{ $cell['period_id'] }}"
    draggable="true"
    x-on:dragstart="$event.dataTransfer.effectAllowed = 'move'; $event.dataTransfer.setData('lesson-id', '{{ (int) $cell['lesson_id'] }}'); $event.dataTransfer.setData('period-id', '{{ (int) $cell['period_id'] }}'); $event.dataTransfer.setData('half', '{{ $cellHalf !== null ? $cellHalf : '' }}')"
    title="{{ $cellHalfBlock ? 'Asignatura de media franja'.($cellHalf === 0 ? ' (superior)' : ($cellHalf === 1 ? ' (inferior)' : '')) : (! empty($cell['is_half_group']) ? 'Asignatura de medio grupo' : 'Asignatura de grupo completo') }}"
    @if (! empty($cell['collision']))
        data-collision="1"
        data-collision-sources="{{ implode(' · ', $cell['collision_pestudios'] ?? []) }}"
    @endif
    class="relative flex cursor-grab flex-col gap-px rounded p-0.5 text-center leading-tight transition-all active:cursor-grabbing {{ $cellHighlighted ? 'ring-2 ring-fuchsia-500' : '' }} {{ $cellDimmed ? 'opacity-30' : '' }} {{ $cellHalfBlock ? 'border border-dashed border-sky-500/50 bg-sky-500/[0.06]' : (! empty($cell['is_half_group']) ? 'border-2 border-solid border-violet-500/40 bg-violet-500/[0.06]' : '') }}">
    <div class="flex min-h-3.5 items-center justify-between gap-px">
        <label class="flex shrink-0 items-center" title="{{ ! empty($cell['locked']) ? 'Desbloquear este bloque' : 'Bloquear este bloque' }}">
            <input type="checkbox"
                wire:key="light-slot-lock-{{ (int) $cell['lesson_id'] }}-{{ (int) $cell['period_id'] }}-{{ ! empty($cell['locked']) ? 'l' : 'u' }}"
                wire:change="togglePreviewSlotLock({{ (int) $cell['lesson_id'] }}, {{ (int) $cell['period_id'] }})"
                @checked(! empty($cell['locked']))
                autocomplete="off"
                aria-label="Bloquear/desbloquear {{ $cell['asignatura'] }} en este período"
                class="h-3 w-3 shrink-0 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
        </label>
        <span class="min-w-0 flex-1"></span>
        @if ($cellHalfBlock)
            <span class="shrink-0 rounded bg-sky-500/20 px-0.5 text-[8px] font-extrabold text-sky-700 dark:text-sky-300" title="Media franja">½</span>
            @if ($cellHalf !== null)
                <button type="button"
                    wire:click.stop="movePreviewLesson({{ (int) $cell['lesson_id'] }}, {{ (int) $cell['period_id'] }}, {{ (int) $cell['period_id'] }}, {{ $cellHalf === 0 ? 1 : 0 }})"
                    @disabled($cellLocked)
                    title="{{ $cellLocked ? 'Desbloquea el bloque para cambiar de mitad' : 'Cambiar a la mitad '.($cellHalf === 0 ? 'inferior' : 'superior').' (intercambia si está ocupada)' }}"
                    aria-label="Cambiar {{ $cell['asignatura'] }} a la mitad {{ $cellHalf === 0 ? 'inferior' : 'superior' }}"
                    class="inline-flex h-3.5 w-3.5 shrink-0 items-center justify-center rounded border border-sky-500/40 bg-sky-500/10 text-sky-700 transition-colors hover:bg-sky-500/25 focus:outline-none focus:ring-2 focus:ring-sky-400 disabled:cursor-not-allowed disabled:opacity-40 dark:text-sky-300">
                    <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4 4 4m10 8v8m0 0 4-4m-4 4-4-4"/>
                    </svg>
                </button>
            @endif
        @endif
        <button type="button"
            wire:click.stop="confirmRemovePreviewLesson({{ (int) $cell['lesson_id'] }}, {{ (int) $cell['period_id'] }})"
            title="Retirar esta lección del preview"
            aria-label="Retirar {{ $cell['asignatura'] }} del preview"
            class="inline-flex h-3.5 w-3.5 shrink-0 items-center justify-center rounded-full border border-gray-300/70 bg-white/80 text-[9px] font-bold leading-none text-gray-500 transition-colors hover:border-red-400/60 hover:bg-red-500/10 hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-red-400 dark:border-white/15 dark:bg-gray-900/50 dark:text-gray-300">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    <div class="flex min-h-4 items-center justify-center gap-px px-px text-[9px] font-bold text-gray-900 dark:text-white">
        <span>{{ $cell['asignatura'] }}</span>
    </div>
    @if ($cell['profesor'])
        <button type="button"
            wire:click.stop="toggleHighlightProfesor({{ (int) $cell['profesor_id'] }})"
            title="Resaltar las clases de {{ $cell['profesor'] }}"
            aria-label="Resaltar las clases de {{ $cell['profesor'] }}"
            class="px-px text-[8px] transition-colors {{ $cellHighlighted ? 'font-bold text-fuchsia-700 dark:text-fuchsia-300' : 'text-gray-500 hover:text-fuchsia-600 dark:text-gray-400 dark:hover:text-fuchsia-300' }}">
            {{ $cell['profesor'] }}
        </button>
    @endif
    @if (! empty($cell['is_half_group']) || $cellHalfBlock)
        <div class="flex justify-end">
            <button type="button"
                wire:click.stop="openAddPreviewLessonModal({{ (int) $targetPeriod?->id }})"
                @disabled(! $targetPeriod)
                title="Agregar una lección a este período"
                aria-label="Agregar una lección al período {{ $targetPeriod?->period_label }}"
                class="inline-flex h-3.5 w-3.5 items-center justify-center rounded-full bg-emerald-600 text-[10px] font-bold leading-none text-white shadow-sm transition-colors hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-40">
                <span aria-hidden="true">+</span>
            </button>
        </div>
    @endif
    {{-- Colisión del docente: se ancla a ESTA lección, no a la celda, para
         que no se confunda cuando el período tiene dos mitades o dos docentes. --}}
    @if (! empty($cell['collision']))
        <span class="absolute bottom-0 left-0 z-10 inline-flex items-center justify-center rounded-bl-md rounded-tr-md bg-red-500/90 p-0.5 text-white shadow-sm"
            title="Colisión de horario del docente con: {{ implode(', ', $cell['collision_pestudios'] ?? []) }}"
            aria-label="Colisión de horario del docente con {{ implode(', ', $cell['collision_pestudios'] ?? []) }}">
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
        </span>
    @endif
</div>
