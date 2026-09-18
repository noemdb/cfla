<div class="fade-in">
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Mi horario</h1>
            <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">
                {{ $profesor->lastname }}, {{ $profesor->name }} · Lunes a Viernes
            </p>
        </div>
        @if ($activeLapso)
            <span class="inline-flex items-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-3 py-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-300">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                {{ $activeLapso->name }}
            </span>
        @endif
    </div>

    @if ($lapsos->isEmpty())
        <div class="rounded-lg border border-dashed border-gray-300 px-6 py-12 text-center text-sm text-gray-500 dark:border-white/10 dark:text-gray-400">
            Todavía no tenés un horario publicado.
        </div>
    @else
        {{-- Pestañas: un tab por lapso --}}
        <div class="mb-5 flex flex-wrap gap-2" role="tablist" aria-label="Lapsos">
            @foreach ($lapsos as $lapso)
                <button type="button"
                    wire:key="lapso-tab-{{ $lapso['id'] }}"
                    wire:click="selectLapso({{ $lapso['id'] }})"
                    role="tab"
                    aria-selected="{{ (int) $activeLapsoId === (int) $lapso['id'] ? 'true' : 'false' }}"
                    class="rounded-lg px-4 py-2 text-sm font-bold transition-colors {{ (int) $activeLapsoId === (int) $lapso['id'] ? 'bg-emerald-600 text-white shadow-sm' : 'border border-gray-200 text-gray-500 hover:bg-gray-100 dark:border-white/10 dark:text-gray-400 dark:hover:bg-white/5' }}">
                    {{ $lapso['name'] }}
                </button>
            @endforeach
        </div>

        {{-- Horario del lapso activo, por P.Educativo --}}
        @forelse ($peducativos as $peducativoData)
            <section class="mb-5 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900/40">
                <header class="flex flex-wrap items-center justify-between gap-2 bg-emerald-700 px-4 py-2.5 dark:bg-emerald-900">
                    <h2 class="text-sm font-extrabold uppercase tracking-wide text-white">
                        {{ $peducativoData['peducativo']?->name ?? 'P.Educativo' }}
                    </h2>
                    @if ($activeLapso)
                        <span class="rounded-md bg-white/20 px-2 py-0.5 text-[11px] font-bold text-white">{{ $activeLapso->name }}</span>
                    @endif
                </header>

                @foreach ($peducativoData['schedules'] as $shiftSchedule)
                    <div class="flex flex-wrap items-baseline gap-2 border-b border-emerald-500/10 bg-emerald-50/60 px-4 py-2 dark:border-emerald-400/10 dark:bg-emerald-500/5">
                        <span class="text-xs font-extrabold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">
                            Turno: {{ $shiftSchedule['shift']->name ?? ('Turno '.$shiftSchedule['shift']->id) }}
                        </span>
                        @if ($shiftSchedule['shift']->start_time)
                            <span class="font-mono text-[11px] font-bold text-emerald-600/80 dark:text-emerald-300/80">
                                ({{ substr((string) $shiftSchedule['shift']->start_time, 0, 5) }}–{{ substr((string) $shiftSchedule['shift']->end_time, 0, 5) }})
                            </span>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full table-fixed border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-white/5">
                                    <th class="w-20 border-b border-gray-300 px-2 py-2 text-center text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:border-white/10 dark:text-gray-400">
                                        Bloque
                                    </th>
                                    @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dayLabel)
                                        <th class="border-b border-gray-300 px-2 py-2 text-left text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:border-white/10 dark:text-gray-400">
                                            {{ $dayLabel }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shiftSchedule['periods'] as $order => $dayPeriods)
                                    @php($firstPeriod = $dayPeriods->first())
                                    <tr class="border-b border-gray-200 last:border-0 dark:border-white/5">
                                        <td class="px-2 py-2 text-center align-top text-xs font-bold text-gray-500 dark:text-gray-400">
                                            {{ $order }}º<br>
                                            <span class="text-[10px] font-medium">{{ substr((string) $firstPeriod->start_time, 0, 5) }}–{{ substr((string) $firstPeriod->end_time, 0, 5) }}</span>
                                        </td>
                                        @foreach (range(1, 5) as $day)
                                            @php($period = $dayPeriods->get($day))
                                            @if ($period?->is_break)
                                                <td class="bg-amber-50 px-2 py-2 text-left align-top text-[10px] font-bold text-amber-700 dark:bg-amber-500/5 dark:text-amber-300">
                                                    Receso
                                                </td>
                                            @else
                                                <td class="px-2 py-2 align-top">
                                                    @forelse ($shiftSchedule['grid']->get($order, collect())->get($day, collect()) as $slot)
                                                        <div class="text-xs font-bold text-emerald-700 dark:text-emerald-300">
                                                            {{ $slot->lesson?->pevaluacion?->pensum?->asignatura?->name ?? '?' }}
                                                        </div>
                                                        <div class="mt-0.5 text-[10px] text-gray-500 dark:text-gray-400">
                                                            {{ $slot->lesson?->pevaluacion?->seccion?->grado?->name ?? '' }}
                                                            · {{ $slot->lesson?->pevaluacion?->seccion?->name ?? '' }}
                                                            @if ($slot->grupo_estable_id)
                                                                · {{ $slot->lesson?->pevaluacion?->grupoEstable?->name ?? 'G'.$slot->grupo_estable_id }}
                                                            @endif
                                                            @if ($slot->room?->code)
                                                                · Aula {{ $slot->room->code }}
                                                            @endif
                                                        </div>
                                                    @empty
                                                        <span class="text-gray-300 dark:text-white/10">—</span>
                                                    @endforelse
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </section>
        @empty
            <div class="rounded-lg border border-dashed border-gray-300 px-6 py-12 text-center text-sm text-gray-500 dark:border-white/10 dark:text-gray-400">
                No tenés horarios publicados en este lapso.
            </div>
        @endforelse

        {{-- Referencia del calendario: una entrada por P.Estudio asociado --}}
        @if (! empty($calendarRefs))
            <footer class="mt-6 rounded-xl border border-gray-200 bg-gray-50/70 p-4 dark:border-white/10 dark:bg-white/[0.02]">
                <h3 class="mb-3 text-[11px] font-extrabold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                    Referencia del calendario
                </h3>
                <ul class="grid gap-2 sm:grid-cols-2">
                    @foreach ($calendarRefs as $ref)
                        <li class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs dark:border-white/10 dark:bg-gray-900/40">
                            <div class="font-bold text-emerald-700 dark:text-emerald-300">{{ $ref['pestudio'] }}</div>
                            <div class="mt-0.5 text-gray-500 dark:text-gray-400">
                                {{ $ref['name'] }}
                                · Versión: {{ $ref['version'] ?? '—' }}
                                · Creado: {{ $ref['created_at'] ?? '—' }}
                                · Actualizado: {{ $ref['updated_at'] ?? '—' }}
                            </div>
                        </li>
                    @endforeach
                </ul>
            </footer>
        @endif
    @endif
</div>
