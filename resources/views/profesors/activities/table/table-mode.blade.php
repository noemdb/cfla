<div class="overflow-x-auto">
    <table class="w-full text-[11px]">
        {{-- Table Header --}}
        <thead>
            <tr class="border-b border-white/5">
                <th class="px-2 py-2.5 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider w-8">#</th>

                {{-- Asignatura (sortable) --}}
                <th class="px-2 py-2.5 text-left">
                    <a href="{{ route('app.profesors.activities.index', array_merge(request()->query(), ['sort' => 'asignaturas.name', 'direction' => $sort === 'asignaturas.name' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                        class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-500 uppercase tracking-wider hover:text-white transition-colors">
                        Asignatura
                        @if($sort === 'asignaturas.name')
                            <svg class="w-3 h-3 {{ $direction === 'asc' ? 'text-emerald-400' : 'text-emerald-400 rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        @endif
                    </a>
                </th>

                {{-- Grado / Sección (sortable by grado) --}}
                <th class="px-2 py-2.5 text-left">
                    <a href="{{ route('app.profesors.activities.index', array_merge(request()->query(), ['sort' => 'grados.name', 'direction' => $sort === 'grados.name' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                        class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-500 uppercase tracking-wider hover:text-white transition-colors">
                        Grado / Sección
                        @if($sort === 'grados.name')
                            <svg class="w-3 h-3 {{ $direction === 'asc' ? 'text-emerald-400' : 'text-emerald-400 rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        @endif
                    </a>
                </th>

                {{-- Fechas (sortable by finicial) --}}
                <th class="px-2 py-2.5 text-left">
                    <a href="{{ route('app.profesors.activities.index', array_merge(request()->query(), ['sort' => 'pevaluacions.finicial', 'direction' => $sort === 'pevaluacions.finicial' && $direction === 'asc' ? 'desc' : 'asc'])) }}"
                        class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-500 uppercase tracking-wider hover:text-white transition-colors">
                        Fechas
                        @if($sort === 'pevaluacions.finicial')
                            <svg class="w-3 h-3 {{ $direction === 'asc' ? 'text-emerald-400' : 'text-emerald-400 rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        @endif
                    </a>
                </th>

                {{-- Actividades --}}
                <th class="px-2 py-2.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider w-20">Actividades</th>

                {{-- Indicadores --}}
                <th class="px-2 py-2.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider w-20">Indicadores</th>

                {{-- Estado --}}
                <th class="px-2 py-2.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider w-24">Estado</th>

                {{-- Acciones --}}
                <th class="px-2 py-2.5 text-right text-[10px] font-bold text-gray-500 uppercase tracking-wider w-28">Acciones</th>
            </tr>
        </thead>

        {{-- Table Body --}}
        <tbody>
            @forelse($pevaluacions as $index => $pevaluacion)
                @php
                    $activities        = $pevaluacion->activities;
                    $achievementsCount = $activities->sum(fn($a) => $a->achievements->count());
                    $pensum            = $pevaluacion->pensum;
                    $grado             = $pensum?->grado;
                    $subjectName       = $pevaluacion->pensum?->asignatura?->name ?? '—';
                    $seccionName       = $pevaluacion->seccion?->name ?? '—';
                    $gradoName         = $grado->name ?? '—';
                    $hasActivities     = $activities->count() > 0;
                    $rowNumber         = ($pevaluacions->currentPage() - 1) * $pevaluacions->perPage() + $index + 1;

                    $earliestDate = $activities->min('finicial');
                    $latestDate   = $activities->max('ffinal');
                    $dateRange    = $earliestDate && $latestDate
                        ? \Carbon\Carbon::parse($earliestDate)->format('d/m') . ' - ' . \Carbon\Carbon::parse($latestDate)->format('d/m/y')
                        : '—';

                    $hasTeaching    = $activities->contains(fn($a) => !empty($a->teaching));
                    $hasEvaluative  = $activities->contains(fn($a) => !empty($a->description));
                    $allApproved    = $hasActivities && $activities->every(fn($a) => $a->status);
                @endphp
                <tr class="border-b border-white/5 hover:bg-gray-700/20 transition-colors group">
                    {{-- # --}}
                    <td class="px-2 py-2.5 text-gray-500 text-[10px] font-medium">{{ $rowNumber }}</td>

                    {{-- Asignatura --}}
                    <td class="px-2 py-2.5">
                        <div class="flex items-center gap-2">
                            <span class="text-white text-xs font-semibold truncate max-w-[180px]">{{ $subjectName }}</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shrink-0">
                                {{ $pevaluacion->lapso?->name ?? '—' }}
                            </span>
                        </div>
                    </td>

                    {{-- Grado / Sección --}}
                    <td class="px-2 py-2.5">
                        <span class="text-gray-300 font-medium">{{ $gradoName }}</span>
                        <span class="text-gray-500"> {{ $seccionName }}</span>
                    </td>

                    {{-- Fechas --}}
                    <td class="px-2 py-2.5">
                        @if($dateRange !== '—')
                            <span class="text-gray-400 text-[10px] flex items-center gap-1">
                                <svg class="w-3 h-3 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $dateRange }}
                            </span>
                        @else
                            <span class="text-gray-600">—</span>
                        @endif
                    </td>

                    {{-- Actividades --}}
                    <td class="px-2 py-2.5 text-center">
                        <span class="inline-flex items-center justify-center min-w-[28px] px-1.5 py-0.5 rounded text-[10px] font-bold {{ $hasActivities ? 'bg-emerald-500/10 text-emerald-400' : 'bg-gray-500/10 text-gray-500' }}">
                            {{ $activities->count() }}
                        </span>
                    </td>

                    {{-- Indicadores --}}
                    <td class="px-2 py-2.5 text-center">
                        <span class="inline-flex items-center justify-center min-w-[28px] px-1.5 py-0.5 rounded text-[10px] font-bold {{ $achievementsCount > 0 ? 'bg-blue-500/10 text-blue-400' : 'bg-gray-500/10 text-gray-500' }}">
                            {{ $achievementsCount }}
                        </span>
                    </td>

                    {{-- Estado --}}
                    <td class="px-2 py-2.5">
                        <div class="flex items-center justify-center gap-1">
                            @if($hasTeaching)
                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[8px] font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20" title="Contiene planificación de enseñanza">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <span>E</span>
                                </span>
                            @endif
                            @if($hasEvaluative)
                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[8px] font-medium bg-sky-500/10 text-sky-400 border border-sky-500/20" title="Contiene actividad evaluativa">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Ev</span>
                                </span>
                            @endif
                            @if($allApproved)
                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[8px] font-medium bg-green-500/10 text-green-400 border border-green-500/20" title="Todas las actividades aprobadas">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Ok</span>
                                </span>
                            @endif
                            @if(!$hasTeaching && !$hasEvaluative && !$allApproved)
                                <span class="text-gray-600 text-[9px]">—</span>
                            @endif
                        </div>
                    </td>

                    {{-- Acciones --}}
                    <td class="px-2 py-2.5">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('app.profesors.activities.create', $pevaluacion->id) }}"
                                title="Registrar Actividades"
                                class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-[9px] font-bold transition-all duration-200 {{ $hasActivities ? 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20' : 'bg-gray-500/10 text-gray-500 hover:bg-gray-500/20 border border-white/5' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span class="hidden sm:inline">Actividades</span>
                            </a>
                            <a href="{{ route('app.profesors.activities.resume', $pevaluacion->id) }}"
                                title="Resumen PDF"
                                target="_blank"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 transition-all duration-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </a>
                            <a href="{{ route('app.profesors.activities.format', $pevaluacion->id) }}"
                                title="Plan Completo PDF"
                                target="_blank"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 border border-purple-500/20 transition-all duration-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </a>
                            {{-- Competencias button --}}
                            <button type="button"
                                @click="window.Livewire.dispatch('openCompetenciasDialog', { pensumId: {{ $pevaluacion->pensum_id }} })"
                                title="Competencias / Indicadores"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all duration-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-2 py-12 text-center">
                        <svg class="w-10 h-10 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-400">Sin registros</p>
                        <p class="text-xs text-gray-600 mt-1">No se encontraron áreas de formación con los filtros seleccionados.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
