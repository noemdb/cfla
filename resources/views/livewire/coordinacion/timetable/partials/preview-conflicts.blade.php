<div class="mt-4 p-4 rounded-lg bg-red-500/5 border border-red-500/30">
    <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
        <div>
            <div class="text-[10px] font-bold uppercase tracking-widest text-red-500">
                Conflictos accionables · {{ $activeConflictGroup['grade'] }} · Sección {{ $activeConflictGroup['section'] }} · #{{ $activeConflictGroup['section_id'] }}
            </div>
            <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                Revisa la causa probable y aplica la recomendación antes de volver a previsualizar.
            </p>
        </div>
        <span class="px-2 py-1 rounded-md bg-red-500/10 text-[10px] font-bold text-red-500">
            {{ $activeConflictGroup['count'] }} pendiente(s)
        </span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        @foreach (collect($activeConflictGroup['items'])->groupBy('teacher') as $teacher => $items)
            <details class="rounded-lg border border-red-500/20 bg-white/5 px-3 py-2">
                <summary class="cursor-pointer text-xs font-bold text-gray-700 dark:text-gray-200">
                    {{ $teacher }} · {{ $items->count() }} sin asignar
                </summary>
                <div class="mt-3 space-y-3">
                    @foreach ($items as $item)
                        <div class="rounded-lg border border-white/10 bg-black/5 dark:bg-white/[0.03] p-3">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <div class="text-xs font-bold text-gray-800 dark:text-gray-100">{{ $item['subject'] }}</div>
                                    <div class="mt-0.5 text-[11px] text-gray-500 dark:text-gray-400">
                                        Sección {{ $item['section'] }} · Turno {{ $item['shift'] }}
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    <span class="px-1.5 py-0.5 rounded bg-white/10 text-[10px] text-gray-500 dark:text-gray-300">T {{ $item['blocks_t'] }}</span>
                                    <span class="px-1.5 py-0.5 rounded bg-white/10 text-[10px] text-gray-500 dark:text-gray-300">P {{ $item['blocks_p'] }}</span>
                                    <span class="px-1.5 py-0.5 rounded bg-white/10 text-[10px] text-gray-500 dark:text-gray-300">Aula {{ $item['room_type'] }}</span>
                                </div>
                            </div>
                            <div class="mt-2 rounded-md bg-red-500/10 px-2.5 py-2 text-[11px] text-red-600 dark:text-red-300">
                                <strong>Causa probable:</strong> {{ $item['reason'] }}
                            </div>
                            <div class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">
                                Candidatos: <strong>{{ $item['available_periods'] }}</strong>
                                · Libres: <strong class="{{ $item['free_periods'] < $item['required_periods'] ? 'text-red-500' : '' }}">{{ $item['free_periods'] }}</strong>
                                · Necesarios: <strong>{{ $item['required_periods'] }}</strong>
                            </div>
                            @if (($item['section_blocked_periods'] ?? 0) || ($item['teacher_occupied_periods'] ?? 0) || ($item['room_unavailable_periods'] ?? 0))
                                <div class="mt-1 text-[10px] text-gray-500 dark:text-gray-400">
                                    @if (($item['section_blocked_periods'] ?? 0) > 0) Sección ocupada: {{ $item['section_blocked_periods'] }} @endif
                                    @if (($item['teacher_occupied_periods'] ?? 0) > 0) · Docente ocupado: {{ $item['teacher_occupied_periods'] }} @endif
                                    @if (($item['room_unavailable_periods'] ?? 0) > 0) · Aula no disponible: {{ $item['room_unavailable_periods'] }} @endif
                                </div>
                            @endif
                            <ul class="mt-2 space-y-1 text-[11px] text-gray-600 dark:text-gray-300">
                                @foreach ($item['actions'] as $action)
                                    <li class="flex gap-1.5"><span class="text-emerald-500">→</span><span>{{ $action }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </details>
        @endforeach
    </div>
</div>
