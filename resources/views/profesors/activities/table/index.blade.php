<div x-data="{ mode: localStorage.getItem('pevaluacions-view-mode') || 'grid' }" x-init="() => { if (!localStorage.getItem('pevaluacions-view-mode')) localStorage.setItem('pevaluacions-view-mode', 'grid') }" x-on:pevaluacions-view-mode-changed.window="mode = $event.detail.mode">

    {{-- Grid Mode --}}
    <div x-show="mode === 'grid'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2.5">
            @forelse($pevaluacions as $pevaluacion)
                @php
                    $activities        = $pevaluacion->activities;
                    $achievementsCount = $activities->sum(fn($a) => $a->achievements->count());
                    $pensum            = $pevaluacion->pensum;
                    $grado             = $pensum?->grado;
                    $pestudio          = $grado?->pestudio;
                    $grupoEstable      = $pevaluacion->grupoEstable;
                    $hasActivities     = $activities->count() > 0;
                    $subjectName       = $pevaluacion->pensum?->asignatura?->name ?? '—';
                    $seccionName       = $pevaluacion->seccion?->name ?? '—';
                    $gradoName         = $grado->name ?? '—';

                    // -- Enriched data ------------------------------------
                    $earliestDate = $activities->min('finicial');
                    $latestDate   = $activities->max('ffinal');
                    $dateRange    = $earliestDate && $latestDate
                        ? \Carbon\Carbon::parse($earliestDate)->format('d/m') . ' - ' . \Carbon\Carbon::parse($latestDate)->format('d/m/y')
                        : null;

                    $topics = $activities->pluck('topic')->filter()->take(3);
                    $topicsTotal = $activities->pluck('topic')->filter()->count();
                    $hasExtraTopics = $topicsTotal > $topics->count();

                    $achievementNames = $activities
                        ->flatMap(fn($a) => $a->achievements->pluck('name'))
                        ->filter()
                        ->take(3);
                    $hasExtraAchievements = $achievementsCount > $achievementNames->count();

                    $hasTeaching    = $activities->contains(fn($a) => !empty($a->teaching));
                    $hasLearning    = $activities->contains(fn($a) => !empty($a->learning));
                    $hasEvaluative  = $activities->contains(fn($a) => !empty($a->description));
                    $allApproved    = $hasActivities && $activities->every(fn($a) => $a->status);
                @endphp
                <div class="bg-gray-800/30 border border-white/5 rounded-lg hover:border-emerald-500/30 hover:bg-gray-800/50 transition-all duration-200 group">

                    {{-- Card Header --}}
                    <div class="px-3 pt-2.5 pb-1.5 border-b border-white/5">
                        <div class="flex items-start justify-between gap-1.5">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-white truncate leading-tight">{{ $subjectName }}</p>
                                <p class="text-[10px] text-gray-500 mt-0.5">
                                    {{ $gradoName }} {{ $seccionName }}
                                    @if($pestudio)
                                        <span class="inline-flex items-center px-1 py-0.5 rounded text-[8px] font-bold bg-gray-500/10 text-gray-500 ml-1">{{ $pestudio->code }}</span>
                                    @endif
                                </p>
                            </div>
                            {{-- Lapso badge --}}
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shrink-0">
                                {{ $pevaluacion->lapso?->name ?? '—' }}
                            </span>
                        </div>

                        {{-- Grupo Estable --}}
                        @if($grupoEstable)
                            <p class="text-[9px] text-gray-600 mt-1 truncate">Comp. Formación: {{ $grupoEstable->name }}</p>
                        @endif

                        {{-- Date Range --}}
                        @if($dateRange)
                            <p class="text-[9px] text-gray-600 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ $dateRange }}</span>
                            </p>
                        @endif
                    </div>

                    {{-- Activity Topics --}}
                    @if($topics->isNotEmpty())
                        <div class="px-3 py-1.5 border-b border-white/5">
                            <div class="flex flex-wrap gap-1">
                                @foreach($topics as $topic)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-medium bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 truncate max-w-[120px]" title="{{ $topic }}">
                                        <svg class="w-2.5 h-2.5 mr-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                        {{ \Illuminate\Support\Str::limit($topic, 18) }}
                                    </span>
                                @endforeach
                                @if($hasExtraTopics)
                                    <span class="text-[8px] text-gray-500 leading-5">+{{ $topicsTotal - $topics->count() }} más</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Stats Row --}}
                    <div class="flex items-center justify-between px-3 py-2 gap-2">
                        <div class="flex items-center gap-2.5">
                            {{-- Activity count --}}
                            <div class="flex items-center gap-1" title="Actividades registradas">
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded text-[9px] font-bold {{ $hasActivities ? 'bg-emerald-500/10 text-emerald-400' : 'bg-gray-500/10 text-gray-500' }}">
                                    {{ $activities->count() }}
                                </span>
                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            {{-- Achievement count --}}
                            <div class="flex items-center gap-1" title="Indicadores / Logros">
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded text-[9px] font-bold {{ $achievementsCount > 0 ? 'bg-blue-500/10 text-blue-400' : 'bg-gray-500/10 text-gray-500' }}">
                                    {{ $achievementsCount }}
                                </span>
                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Status badges --}}
                        <div class="flex items-center gap-1">
                            @if($hasTeaching)
                                <span class="inline-flex items-center gap-0.5 px-1 py-0.5 rounded text-[8px] font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20" title="Contiene planificación de enseñanza (Inicio-Desarrollo-Cierre)">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <span>E</span>
                                </span>
                            @endif
                            @if($hasEvaluative)
                                <span class="inline-flex items-center gap-0.5 px-1 py-0.5 rounded text-[8px] font-medium bg-sky-500/10 text-sky-400 border border-sky-500/20" title="Contiene actividad evaluativa">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Ev</span>
                                </span>
                            @endif
                            @if($allApproved)
                                <span class="inline-flex items-center gap-0.5 px-1 py-0.5 rounded text-[8px] font-medium bg-green-500/10 text-green-400 border border-green-500/20" title="Todas las actividades aprobadas">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Ok</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Achievement preview --}}
                    @if($achievementNames->isNotEmpty())
                        <div class="px-3 pb-1">
                            <div class="flex flex-wrap gap-1">
                                @foreach($achievementNames as $name)
                                    <span class="inline-flex items-center px-1 py-0.5 rounded text-[8px] font-medium bg-blue-500/10 text-blue-400 truncate max-w-[100px]" title="{{ $name }}">
                                        {{ \Illuminate\Support\Str::limit($name, 14) }}
                                    </span>
                                @endforeach
                                @if($hasExtraAchievements)
                                    <span class="text-[8px] text-gray-500 leading-5">+{{ $achievementsCount - $achievementNames->count() }}</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Card Actions --}}
                    <div class="px-3 pb-2.5 flex items-center gap-1.5">
                        <a href="{{ route('app.profesors.activities.create', $pevaluacion->id) }}"
                            title="Registrar Actividades"
                            class="inline-flex items-center justify-center flex-1 gap-1 px-2 py-1.5 rounded-lg text-[10px] font-bold transition-all duration-200 {{ $hasActivities ? 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20' : 'bg-gray-500/10 text-gray-500 hover:bg-gray-500/20 border border-white/5' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Actividades
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
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm font-medium text-gray-400">Sin registros</p>
                    <p class="text-xs text-gray-600 mt-1">No se encontraron áreas de formación con los filtros seleccionados.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Table Mode --}}
    <div x-show="mode === 'table'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        @include('profesors.activities.table.table-mode')
    </div>
</div>
