<div class="fade-in space-y-6" x-data @keydown.slash.window.prevent="$refs.searchInput.focus()" @keydown.e.window.prevent="if({{ $questions->first()?->id ?? 0 }}) $wire.openQuestionModal({{ $questions->first()?->id ?? 0 }})" @keydown.v.window.prevent="if({{ $questions->first()?->id ?? 0 }}) $wire.openDetail({{ $questions->first()?->id ?? 0 }})" @keydown.escape.window="$wire.closeDetail(); $wire.closeQuestionModal()">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-1">Diagnóstico · Revisión de Preguntas</h1>
            <p class="text-amber-600 dark:text-amber-400 font-medium text-sm">
                Revisa y da seguimiento a las preguntas de diagnóstico de las áreas de conocimiento que tienes asignadas.
            </p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
            Seguimiento y revisión
        </span>
    </div>

    {{-- Réplica planning: header Lapso/Pestudio/Referente + grid 8 (respeta is_leadership) --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="px-5 py-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Lapso</p>
                    <p class="text-sm text-white font-medium">{{ $displayLapso?->name ?? '—' }}</p>
                </div>
                <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Plan de Estudio</p>
                    <p class="text-sm text-white font-medium">
                        @if($displayPestudio)
                            {{ $displayPestudio->code }} — {{ $displayPestudio->name }}
                        @else
                            <span class="text-gray-400">Múltiples planes</span>
                        @endif
                    </p>
                </div>
                <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Referente</p>
                    <p class="text-sm text-white font-medium">{{ $displayReferent ? $displayReferent->code.' — '.$displayReferent->name : '—' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                    <p class="text-lg font-extrabold text-cyan-400">{{ $questionsCount ?? 0 }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Preguntas</p>
                </div>
                <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                    <p class="text-lg font-extrabold text-sky-400">{{ $pensumsWithAnswersCount ?? 0 }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">A.Formación c/ resp.</p>
                </div>
                <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                    <p class="text-lg font-extrabold text-indigo-400">{{ $questionsWithAnswersCount ?? 0 }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Preg. c/ resp.</p>
                </div>
                <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                    <p class="text-lg font-extrabold text-amber-400">{{ $totalAnswersCount ?? 0 }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Respuestas</p>
                </div>
                <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                    <p class="text-lg font-extrabold text-purple-400">{{ $studentsEvaluated ?? 0 }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Estudiantes</p>
                </div>
                <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                    @if($completionRate !== null)
                        <p class="text-lg font-extrabold {{ $completionRate >= 80 ? 'text-emerald-400' : ($completionRate >= 50 ? 'text-amber-400' : 'text-red-400') }}">{{ $completionRate }}%</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">% Completitud</p>
                        <p class="text-[10px] text-gray-500">{{ $completedSessions ?? 0 }} / {{ $sessionsCount ?? 0 }}</p>
                    @else
                        <p class="text-lg font-extrabold text-gray-500">—</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">% Completitud</p>
                        <p class="text-[10px] text-gray-600">Sin datos</p>
                    @endif
                </div>
                <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                    @if($abandonRate !== null)
                        <p class="text-lg font-extrabold {{ $abandonRate <= 20 ? 'text-emerald-400' : ($abandonRate <= 40 ? 'text-amber-400' : 'text-red-400') }}">{{ $abandonRate }}%</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Tasa Abandono</p>
                        <p class="text-[10px] text-gray-500">{{ ($sessionsCount ?? 0) - ($completedSessions ?? 0) }} / {{ $sessionsCount ?? 0 }}</p>
                    @else
                        <p class="text-lg font-extrabold text-gray-500">—</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Tasa Abandono</p>
                        <p class="text-[10px] text-gray-600">Sin datos</p>
                    @endif
                </div>
                <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                    @if($precision !== null)
                        <p class="text-lg font-extrabold {{ $precision >= 80 ? 'text-emerald-400' : ($precision >= 60 ? 'text-amber-400' : 'text-red-400') }}">{{ $precision }}%</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Precisión</p>
                        <p class="text-[10px] text-gray-500">{{ $precisionCorrect }} / {{ $precisionTotal }}</p>
                    @else
                        <p class="text-lg font-extrabold text-gray-500">—</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Precisión</p>
                        <p class="text-[10px] text-gray-600">Sin datos</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-2 sm:p-5 rounded-lg space-y-3">
        {{-- Diagnóstico (instrumento) — encima de la grilla de filtros --}}
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Diagnóstico</label>
            <select wire:model.live="filterDiagMain" class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 outline-none"><option value="">Todos</option>@foreach($diagMains as $dm)<option value="{{ $dm->id }}">{{ $dm->name }}</option>@endforeach</select>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div><label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Área</label>
                <select wire:model.live="filterAreaId" class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 outline-none"><option value="">Todas mis áreas ({{ $areas->count() }})</option>@foreach($areas as $a)<option value="{{ $a->id }}">{{ $a->name }} [{{ $a->pestudio->code ?? '?' }}]</option>@endforeach</select></div>
            <div><label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Pensum</label>
                <select wire:model.live="filterPensumId" @if($filterAreaId === '') disabled @endif class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 outline-none disabled:opacity-40 disabled:cursor-not-allowed"><option value="">Todos los pensums</option>@foreach($pensumsOptions as $p)<option value="{{ $p->id }}">{{ $p->asignatura?->name ?? '?' }} — {{ $p->grado?->name ?? '?' }}</option>@endforeach</select></div>
            <div><label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Buscar <span class="normal-case font-normal text-gray-600">( / )</span> @if($search !== '')<span class="ml-1 text-amber-400">{{ $questions->total() }} resultados</span>@endif</label>
                <input type="text" x-ref="searchInput" wire:model.live.debounce.300ms="search" placeholder="Pregunta o tipo..." class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 outline-none placeholder:text-gray-600"></div>
            <div><label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Tipo</label>
                <select wire:model.live="filterTipo" class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 outline-none"><option value="">Todos</option>@foreach($tipos as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach</select></div>
            <div><label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Activa</label>
                <select wire:model.live="filterActive" class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 outline-none"><option value="">Todas</option><option value="1">Activas</option><option value="0">Inactivas</option></select></div>
        </div>
    </div>

    {{-- Enriquecimiento — respeta is_leadership (AreaConocimiento → Pensum) --}}
    <div wire:init="loadEnriched" class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        @if(!$enrichedLoaded)
            <div class="lg:col-span-3 flex items-center justify-center py-8 gap-2 text-sm text-gray-500">
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Cargando estadísticas...
            </div>
        @else
            <div class="lg:col-span-2 bg-gray-800/30 border border-white/5 rounded-lg overflow-hidden">
                <div class="px-3 py-2 border-b border-white/5 flex items-center justify-between">
                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Sesiones recientes</h4>
                    <span class="text-[10px] text-gray-500">{{ $recentSessions->count() }} última(s)</span>
                </div>
            @if($recentSessions->isEmpty())
                <p class="text-xs text-gray-500 text-center py-6">Sin sesiones en tu ámbito.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-white/[0.02] border-b border-white/5">
                            <tr class="text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                <th class="text-left px-3 py-1.5">Estudiante</th>
                                <th class="text-left px-3 py-1.5">Área</th>
                                <th class="text-left px-3 py-1.5">Progreso</th>
                                <th class="text-left px-3 py-1.5">Iniciado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($recentSessions as $sess)
                                <tr class="hover:bg-white/[0.02]">
                                    <td class="px-3 py-1.5 text-white">{{ $sess->estudiant?->full_name ?? $sess->estudiant?->name ?? '—' }}</td>
                                    <td class="px-3 py-1.5 text-gray-400">{{ $sess->pensum?->full_name ?? $sess->pensum?->asignatura?->name ?? '—' }}</td>
                                    <td class="px-3 py-1.5">
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-12 h-1.5 bg-white/10 rounded-full overflow-hidden"><div class="h-full bg-cyan-500 rounded-full" style="width: {{ $sess->progreso ?? 0 }}%"></div></div>
                                            <span class="text-[10px] text-gray-400">{{ $sess->progreso ?? 0 }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-1.5 text-gray-500 text-[11px]">{{ $sess->iniciado_at ? \Carbon\Carbon::parse($sess->iniciado_at)->format('d/m/Y H:i') : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        <div class="space-y-3">
            <div class="bg-gray-800/30 border border-white/5 rounded-lg p-3">
                <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Por tipo</h4>
                @forelse($questionsByType as $t)
                    @php $max = $questionsByType->max('count') ?: 1; $pct = $max ? round(100 * $t->count / $max) : 0; @endphp
                    <div class="mb-2">
                        <div class="flex justify-between text-[11px] text-gray-400 mb-1"><span class="font-medium text-white">{{ $t->type }}</span><span class="px-1.5 py-0.5 rounded-full bg-white/5 border border-white/5 text-[10px]">{{ $t->count }}</span></div>
                        <div class="w-full h-1.5 bg-white/10 rounded-full overflow-hidden"><div class="h-full bg-cyan-500 rounded-full" style="width: {{ $pct }}%"></div></div>
                    </div>
                @empty
                    <p class="text-xs text-gray-500">Sin datos.</p>
                @endforelse
            </div>
            <div class="bg-gray-800/30 border border-white/5 rounded-lg p-3">
                <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Por dificultad</h4>
                @forelse($questionsByDifficulty as $d)
                    @php $maxD = $questionsByDifficulty->max('count') ?: 1; $pctD = $maxD ? round(100 * $d->count / $maxD) : 0; $col = match(strtolower($d->difficulty ?? '')) { 'easy' => 'bg-emerald-500', 'medium' => 'bg-amber-500', 'hard' => 'bg-red-500', default => 'bg-gray-500' }; @endphp
                    <div class="mb-2">
                        <div class="flex justify-between text-[11px] text-gray-400 mb-1"><span class="font-medium text-white capitalize">{{ $d->difficulty ?? '—' }}</span><span class="px-1.5 py-0.5 rounded-full bg-white/5 border border-white/5 text-[10px]">{{ $d->count }}</span></div>
                        <div class="w-full h-1.5 bg-white/10 rounded-full overflow-hidden"><div class="h-full {{ $col }} rounded-full" style="width: {{ $pctD }}%"></div></div>
                    </div>
                @empty
                    <p class="text-xs text-gray-500">Sin datos.</p>
                @endforelse
            </div>
        </div>
        @endif
    </div>

    {{-- Resumen por área de formación — réplica planning (scoped is_leadership) --}}
    @if($pensumProgress && $pensumProgress->isNotEmpty())
        <div class="bg-gray-800/30 border border-white/5 rounded-lg overflow-hidden">
            <div class="px-3 py-2 border-b border-white/5 flex items-center justify-between">
                <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Resumen por área de formación</h4>
                <span class="text-[10px] text-gray-500">{{ $pensumProgress->count() }} área(s) en tu scope</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-white/[0.02] border-b border-white/5">
                        <tr class="text-[10px] font-bold uppercase tracking-widest text-gray-500">
                            <th class="text-left px-3 py-1.5">Área</th>
                            <th class="text-center px-3 py-1.5">Preg.</th>
                            <th class="text-center px-3 py-1.5">Ses.</th>
                            <th class="text-center px-3 py-1.5">Compl.</th>
                            <th class="text-center px-3 py-1.5">% Finalización</th>
                            <th class="text-center px-3 py-1.5">Precisión</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($pensumProgress as $pp)
                            <tr class="hover:bg-white/[0.02]">
                                <td class="px-3 py-1.5 text-white font-medium">{{ $pp->fullname }}</td>
                                <td class="px-3 py-1.5 text-center"><span class="px-1.5 py-0.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px]">{{ $pp->total_questions }}</span></td>
                                <td class="px-3 py-1.5 text-center"><span class="px-1.5 py-0.5 rounded-full bg-white/5 border border-white/5 text-gray-300 text-[10px]">{{ $pp->total_sessions }}</span></td>
                                <td class="px-3 py-1.5 text-center"><span class="px-1.5 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px]">{{ $pp->completed_sessions }}</span></td>
                                <td class="px-3 py-1.5">
                                    <div class="flex items-center gap-1.5 justify-center">
                                        <div class="w-12 h-1.5 bg-white/10 rounded-full overflow-hidden"><div class="h-full rounded-full {{ $pp->completion_percentage >= 80 ? 'bg-emerald-500' : ($pp->completion_percentage >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $pp->completion_percentage }}%"></div></div>
                                        <span class="text-[10px] text-gray-400">{{ number_format($pp->completion_percentage, 1) }}%</span>
                                    </div>
                                </td>
                                <td class="px-3 py-1.5 text-center">
                                    @if($pp->precision !== null)
                                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold border {{ $pp->precision >= 80 ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : ($pp->precision >= 60 ? 'bg-amber-500/10 border-amber-500/20 text-amber-400' : 'bg-red-500/10 border-red-500/20 text-red-400') }}">{{ $pp->precision }}%</span>
                                    @else
                                        <span class="text-[10px] text-gray-600">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-white/5">
                    <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Pregunta</th>
                    <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Pensum / Área</th>
                    <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Competencia</th>
                    <th class="text-center px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</th>
                    <th class="text-right px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Revisión</th>
                </tr></thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($questions as $q)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-4 py-3">
                                @php $displayPregunta = \Illuminate\Support\Str::limit($q->pregunta, 130); @endphp
                                <p class="text-white font-medium line-clamp-2 leading-snug" title="{{ $q->pregunta }}">{!! $search !== '' ? str_ireplace(e($search), '<mark class="bg-amber-500/30 text-amber-200 px-0.5 rounded">'.e($search).'</mark>', e($displayPregunta)) : e($displayPregunta) !!}</p>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $q->tipo_pregunta === 'multiple' ? 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20' }}">{{ $q->tipo_pregunta }}</span>
                                    <span class="text-[10px] text-gray-500">orden {{ $q->orden ?? '—' }} · {{ $q->difficulty ?? '—' }} · peso {{ $q->weighing ?? '—' }}</span>
                                    <span class="text-[10px] text-gray-500">· {{ $q->options->count() }} opc.</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <span class="text-xs text-white font-medium">{{ $q->pensum?->asignatura?->name ?? '—' }}</span>
                                        <span class="block text-[10px] text-gray-500">{{ $q->pensum?->grado?->name ?? $q->pensum_id }} · {{ $q->pensum?->pestudio?->code ?? '?' }}</span>
                                    </div>
                                    @php $campo = $q->pensum_id ? \App\Models\app\Academy\CampoConocimiento::where('pensum_id', $q->pensum_id)->with('area_conocimiento')->first() : null; @endphp
                                    @if($campo?->area_conocimiento)<span class="inline-flex shrink-0 self-start px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">{{ $campo->area_conocimiento->name }}</span>@endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400">
                                {{ $q->competency?->name ?? '—' }}
                                <span class="block text-[10px] text-gray-500">{{ $q->indicator?->name ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="toggleActivo({{ $q->id }})" class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold border transition-all {{ $q->activo ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-gray-500/10 text-gray-400 border-white/10' }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $q->activo ? 'bg-emerald-500' : 'bg-gray-500' }}"></span>{{ $q->activo ? 'Activa' : 'Inactiva' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center justify-end rounded-lg border border-amber-500/20 overflow-hidden divide-x divide-amber-500/20">
                                    <button wire:click="openDetail({{ $q->id }})" title="Ver detalle" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-bold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver
                                    </button>
                                    <button wire:click="openQuestionModal({{ $q->id }})" title="Editar pregunta" class="inline-flex items-center justify-center w-8 h-[30px] text-xs font-bold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        @if($areas->isEmpty())
                            <tr><td colspan="5" class="px-4 py-16 text-center">
                                <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p class="text-gray-500 font-medium">No tienes áreas asignadas</p>
                                <p class="text-gray-600 text-sm mt-1">Contacta al administrador para que te asigne como líder de un área de conocimiento.</p>
                                <p class="text-xs text-gray-500 mt-2">Se requiere <code class="text-gray-400">AreaConocimiento.leader_id = tu userId</code></p>
                            </td></tr>
                        @elseif($search !== '' || $filterAreaId !== '' || $filterPensumId !== '' || $filterTipo !== '' || $filterActive !== '' || $filterDiagMain !== '')
                            <tr><td colspan="5" class="px-4 py-12 text-center">
                                <p class="text-gray-500 font-medium">No hay resultados para los filtros</p>
                                <p class="text-gray-600 text-sm mt-1">Ajusta los filtros o limpia la búsqueda.</p>
                                <button wire:click="clearAllFilters" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Limpiar filtros
                                </button>
                            </td></tr>
                        @else
                            <tr><td colspan="5" class="px-4 py-16 text-center"><p class="text-gray-500 font-medium">No hay preguntas en tu scope</p><p class="text-gray-600 text-sm mt-1">Verifica <code class="text-gray-400">AreaConocimiento.leader_id</code> y <code class="text-gray-400">campo_conocimientos.pensum_id</code> (<code class="text-amber-400">php8.2 artisan campo:poblar-pensum --force</code>).</p></td></tr>
                        @endif
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($questions->hasPages())
            <x-pagination-wrapper :paginator="$questions" />
        @endif
    </div>

    @if($showDetail && $selected)
        @php
            $optCorrect = fn ($o) => ((int) ($o->valor ?? 0)) === 1;
            $tipoLabel = match ($selected->tipo_pregunta) {
                'multiple' => 'Múltiple opción',
                'open' => 'Abierta',
                'scale' => 'Escala',
                default => $selected->tipo_pregunta,
            };
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeDetail"></div>
            <div class="relative bg-gray-900 border border-white/10 rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden max-h-[88vh] flex flex-col">
                <div class="px-5 py-4 border-b border-white/5 flex items-start justify-between gap-3 shrink-0">
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-white truncate">Pregunta #{{ $selected->id }} · {{ $selected->pensum?->asignatura?->name ?? '—' }}</h3>
                        <p class="text-[11px] text-gray-500 mt-0.5">
                            {{ $selected->pensum?->pestudio?->code }} · {{ $selected->pensum?->grado?->name }} · orden {{ $selected->orden ?? '—' }}
                        </p>
                    </div>
                    <button wire:click="closeDetail" class="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto">
                    {{-- Pregunta --}}
                    <div class="rounded-xl border border-white/5 bg-white/[0.02] p-4">
                        <p class="text-sm text-white leading-relaxed">{{ $selected->pregunta }}</p>
                        <div class="flex flex-wrap items-center gap-1.5 mt-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-cyan-500/10 text-cyan-400 border-cyan-500/20">{{ $tipoLabel }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $selected->activo ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-gray-500/10 text-gray-400 border-white/10' }}">{{ $selected->activo ? 'Activa' : 'Inactiva' }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-white/5 text-gray-400 border-white/10">Dificultad: {{ $selected->difficulty ?? '—' }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-white/5 text-gray-400 border-white/10">Peso: {{ $selected->weighing ?? '—' }}</span>
                            @if($selected->diagMain)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-amber-500/10 text-amber-400 border-amber-500/20">{{ $selected->diagMain->name }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Clasificación curricular --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-xl border border-white/5 bg-white/[0.02] p-3">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Área de formación</p>
                            <p class="text-xs text-white font-medium">{{ $selected->pensum?->asignatura?->name ?? '—' }}</p>
                            <p class="text-[11px] text-gray-500">{{ $selected->pensum?->pestudio?->code }} · {{ $selected->pensum?->pestudio?->name }}</p>
                            <p class="text-[11px] text-gray-500">{{ $selected->pensum?->grado?->name }}</p>
                        </div>
                        <div class="rounded-xl border border-white/5 bg-white/[0.02] p-3">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Competencia</p>
                            <p class="text-xs text-white font-medium">{{ $selected->competency?->name ?? '—' }}</p>
                            @if($selected->competency?->description)<p class="text-[11px] text-gray-500 mt-0.5">{{ $selected->competency->description }}</p>@endif
                        </div>
                        <div class="rounded-xl border border-white/5 bg-white/[0.02] p-3 sm:col-span-2">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Indicador de logro</p>
                            @if($selected->indicator)
                                <p class="text-xs text-white font-medium">
                                    @if($selected->indicator->code)<span class="font-mono text-amber-400">{{ $selected->indicator->code }}</span> · @endif
                                    {{ $selected->indicator->description ?? '—' }}
                                </p>
                                @if($selected->indicator->expected_level)<p class="text-[11px] text-gray-500 mt-0.5">Nivel esperado: {{ $selected->indicator->expected_level }}</p>@endif
                            @else
                                <p class="text-xs text-gray-500 italic">Sin indicador asociado.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Opciones --}}
                    <div>
                        <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Opciones de respuesta ({{ $selected->options->count() }})</h4>
                        @forelse($selected->options->sortBy('orden') as $opt)
                            <div class="flex items-start gap-2 py-2 border-b border-white/5 last:border-0 {{ $optCorrect($opt) ? 'bg-emerald-500/[0.04] rounded-lg px-2' : '' }}">
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5 {{ $optCorrect($opt) ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-white/5 text-gray-500 border-white/10' }}">{{ chr(65 + $loop->index) }}</span>
                                <span class="text-xs text-gray-200 flex-1">{{ $opt->opcion }}</span>
                                @if($optCorrect($opt))<span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">Correcta</span>@endif
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 italic">
                                @if($selected->tipo_pregunta === 'open')
                                    Pregunta abierta: respuesta libre.
                                @elseif($selected->tipo_pregunta === 'scale')
                                    Pregunta de escala: valoración numérica.
                                @else
                                    Sin opciones cargadas.
                                @endif
                            </p>
                        @endforelse
                    </div>
                </div>

                <div class="px-5 py-3 bg-white/[0.02] border-t border-white/5 flex items-center justify-between gap-2 shrink-0">
                    <button wire:click="toggleActivo({{ $selected->id }})" class="px-3 py-1.5 rounded-lg text-xs font-bold border transition-all {{ $selected->activo ? 'bg-amber-500/10 text-amber-400 border-amber-500/20 hover:bg-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 hover:bg-emerald-500/20' }}">{{ $selected->activo ? 'Desactivar' : 'Activar' }}</button>
                    <button wire:click="closeDetail" class="px-4 py-2 rounded-lg text-sm font-medium bg-white/5 hover:bg-white/10 text-gray-300 border border-white/10">Cerrar</button>
                </div>
            </div>
        </div>
    @endif

    @include('livewire.leadership.partials.question-wizard')
</div>
