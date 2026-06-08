<div class="overflow-x-auto">
    <table class="w-full text-left">
        <thead>
            <tr class="border-b border-white/5">
                <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">N°</th>
                <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Asignatura / Grado / Sección</th>
                <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Act.</th>
                <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Ind.</th>
                <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Lapso</th>
                <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pevaluacions as $pevaluacion)
                @php
                    $activities   = $pevaluacion->activities;
                    $achievementsCount = $activities->sum(fn($a) => $a->achievements->count());
                    $pensum       = $pevaluacion->pensum;
                    $grado        = $pensum?->grado;
                    $pestudio     = $grado?->pestudio;
                    $grupoEstable = $pevaluacion->grupoEstable;
                    $hasActivities = $activities->count() > 0;
                @endphp
                <tr class="border-b border-white/5 hover:bg-white/[0.02] transition-colors duration-150">
                    <td class="py-4 px-4 text-xs text-gray-400 font-mono">{{ $loop->iteration }}</td>
                    <td class="py-4 px-4">
                        <p class="text-sm font-semibold text-white">{{ $pevaluacion->pensum?->asignatura?->name ?? '—' }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[11px] text-gray-400 font-medium">
                                {{ $grado->name ?? '—' }} {{ $pevaluacion->seccion?->name ?? '—' }}
                            </span>
                            @if($pestudio)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-500/10 text-gray-500 border border-white/5">
                                    {{ $pestudio->code }}
                                </span>
                            @endif
                        </div>
                        @if($grupoEstable)
                            <p class="text-[10px] text-gray-600 mt-1">Comp. Formación: {{ $grupoEstable->name }}</p>
                        @endif
                    </td>
                    <td class="py-4 px-4 text-center">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold {{ $hasActivities ? 'bg-emerald-500/10 text-emerald-400' : 'bg-gray-500/10 text-gray-500' }}">
                            {{ $activities->count() }}
                        </span>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold {{ $achievementsCount > 0 ? 'bg-blue-500/10 text-blue-400' : 'bg-gray-500/10 text-gray-500' }}">
                            {{ $achievementsCount }}
                        </span>
                    </td>
                    <td class="py-4 px-4">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            {{ $pevaluacion->lapso?->name ?? '—' }}
                        </span>
                    </td>
                    <td class="py-4 px-4 text-right">
                        <div class="inline-flex items-center gap-1.5">
                            {{-- Registrar Actividades --}}
                            <a href="{{ route('app.profesors.activities.create', $pevaluacion->id) }}"
                                title="Registrar Actividades"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold transition-all duration-200 {{ $hasActivities ? 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20' : 'bg-gray-500/10 text-gray-500 hover:bg-gray-500/20 border border-white/5' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>

                            {{-- Resumen PDF --}}
                            <a href="{{ route('app.profesors.activities.resume', $pevaluacion->id) }}"
                                title="Resumen del Plan de Actividades"
                                target="_blank"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </a>

                            {{-- Plan PDF --}}
                            <a href="{{ route('app.profesors.activities.format', $pevaluacion->id) }}"
                                title="Plan de Actividades Completo"
                                target="_blank"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 border border-purple-500/20 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-12 text-center">
                        <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-sm font-medium text-gray-400">Sin registros</p>
                        <p class="text-xs text-gray-600 mt-1">No se encontraron áreas de formación con los filtros seleccionados.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
