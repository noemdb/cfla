{{--
    Slot de una asignatura en la grilla de vista previa del asistente (solo lectura).
    ADR-TT-017: admite `half` (0 = superior, 1 = inferior, null = bloque completo).
    Variables: $cell, $multi (bool: la celda tiene más de un slot)
--}}
@php
    $cellHalf = isset($cell['half']) && $cell['half'] !== null ? (int) $cell['half'] : null;
    $cellHalfBlock = ! empty($cell['half_block']);
@endphp
<div
    wire:key="preview-slot-{{ (int) $cell['lesson_id'] }}-{{ (int) $cell['period_id'] }}"
    title="{{ $cellHalfBlock ? 'Asignatura de media franja'.($cellHalf === 0 ? ' (superior)' : ($cellHalf === 1 ? ' (inferior)' : '')) : (!empty($cell['is_half_group']) ? 'Asignatura de medio grupo' : 'Asignatura de grupo completo') }}"
    class="relative flex flex-col gap-px rounded p-0.5 text-center leading-tight {{ ! empty($multi) ? 'bg-white/5' : '' }} {{ $cellHalfBlock ? 'border border-dashed border-sky-500/50 bg-sky-500/[0.06]' : (!empty($cell['is_half_group']) ? 'border-2 border-solid border-violet-500/40 bg-violet-500/[0.06]' : '') }}">
    @if ($cellHalfBlock)
        <div class="flex justify-end">
            <span class="rounded bg-sky-500/20 px-0.5 text-[7px] font-extrabold text-sky-700 dark:text-sky-300" title="Media franja">½</span>
        </div>
    @endif
    <div class="flex min-h-4 items-center justify-center gap-px px-px text-[9px] font-bold text-gray-900 dark:text-white">
        <span>{{ $cell['asignatura'] }}</span>
    </div>
    @if ($cell['profesor'])
        <div class="px-px text-[8px] text-gray-500 dark:text-gray-400">{{ $cell['profesor'] }}</div>
    @endif
    @if ($cell['grupo'])
        <div class="px-px text-[8px] text-emerald-600 dark:text-emerald-400">{{ $cell['grupo'] }}</div>
    @endif
    @if (!empty($cell['collision']))
        <span class="absolute bottom-0 left-0 z-10 inline-flex items-center justify-center rounded-bl-md rounded-tr-md bg-red-500/90 p-0.5 text-white shadow-sm"
            title="Colisión de horario del docente con: {{ implode(', ', $cell['collision_pestudios']) }}"
            aria-label="Colisión de horario del docente con {{ implode(', ', $cell['collision_pestudios']) }}">
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
        </span>
    @endif
</div>
