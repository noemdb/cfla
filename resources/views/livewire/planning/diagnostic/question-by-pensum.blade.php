<div wire:key="q-by-pensum" class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
    {{-- Header: fila 1 título w-full + fila 2 filtros en una sola línea con pensum al final --}}
    <div class="px-4 sm:px-6 py-4 border-b border-white/5 space-y-3">
        <div class="flex items-center gap-3 w-full">
            <div class="w-9 h-9 bg-cyan-500/20 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-white uppercase tracking-widest">Preguntas por Pensum</h2>
                <p class="text-[11px] text-gray-400">
                    Explora las preguntas organizadas por área de formación y grupo.
                </p>
            </div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="flex flex-wrap items-center gap-2">
                {{-- Fila 1: pestudio → grado → pensum --}}
                <select wire:model.live="pestudioId"
                    class="bg-gray-800 text-gray-200 text-xs rounded-lg border border-white/5 px-3 py-2 min-h-[40px] focus:border-emerald-500/30 focus:ring-1 focus:ring-emerald-500/20 outline-none w-full sm:w-44 shrink-0">
                    <option value="">Pestudio: Todos</option>
                    @foreach($pestudiosOptions as $pest)
                        <option value="{{ $pest->id }}">{{ $pest->code }} — {{ $pest->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="gradoId"
                    class="bg-gray-800 text-gray-200 text-xs rounded-lg border border-white/5 px-3 py-2 min-h-[40px] focus:border-emerald-500/30 focus:ring-1 focus:ring-emerald-500/20 outline-none w-full sm:w-44 shrink-0 disabled:opacity-40 disabled:cursor-not-allowed"
                    @if(!$pestudioId) disabled @endif>
                    <option value="">Grado: Todos</option>
                    @foreach($gradosOptions as $gr)
                        <option value="{{ $gr->id }}">{{ $gr->code ?? $gr->name }} — {{ $gr->name }}</option>
                    @endforeach
                </select>

                {{-- Pensum anidado con gradoId (deshabilitado por defecto) --}}
                <select wire:model.live="pensumId"
                    class="bg-gray-800 text-gray-200 text-xs rounded-lg border border-white/5 px-3 py-2 min-h-[40px] focus:border-cyan-500/30 focus:ring-1 focus:ring-cyan-500/20 outline-none w-full sm:w-80 shrink-0 disabled:opacity-40 disabled:cursor-not-allowed"
                    @if(!$gradoId) disabled @endif>
                    <option value="">Seleccionar A.Formación</option>
                    @foreach($pensumOptions as $opt)
                        @php
                            $qCount = \App\Models\app\Instrument\DiagQuestion::where('pensum_id', $opt->id)->count();
                        @endphp
                        <option value="{{ $opt->id }}">
                            [#{{ $opt->id }}] {{ $opt->asignatura?->name ?? '?' }} — {{ $opt->grado?->name ?? '?' }} ({{ $opt->pestudio?->code ?? '?' }}) — {{ $qCount }} preg.
                        </option>
                    @endforeach
                </select>

            @if($pensumId || $pestudioId || $gradoId || $pevaluacionId || $grupoEstableId)
                <button wire:click="clearFilters"
                    class="px-3 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/5 text-xs font-bold uppercase tracking-widest transition-all shrink-0">
                    Limpiar
                </button>
            @endif
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Fila 2: pevaluación + Desactivar --}}
            {{-- Grupo estable — dropdown wireUI w-full con descripción por opción --}}
            <div class="w-full shrink-0">
                <x-select placeholder="Grupo: Todos" wire:model.live="grupoEstableId" searchable :disabled="!$gradoId && !$pestudioId && !$pensumId">
                    @foreach($grupoEstablesOptions as $g)
                        @php
                            $pevDesc = $pevaluacionsOptions->firstWhere('grupo_estable_id', $g->id);
                            $asigDesc = $pevDesc?->pensum?->asignatura?->name ?? '?';
                            $gradoSecDesc = $pevDesc?->seccion ? (($pevDesc->seccion->grado?->name ?? '?').'/'.$pevDesc->seccion->name) : ($pevDesc?->pensum?->grado?->name ?? '?');
                            $profDesc = $pevDesc?->profesor ? ($pevDesc->profesor->lastname.' '.$pevDesc->profesor->name) : '?';
                            $desc = $asigDesc.' · '.$gradoSecDesc.' · '.$profDesc;
                        @endphp
                        <x-select.option :label="$g->code.' — '.$g->name" :value="$g->id" :description="$desc" />
                    @endforeach
                </x-select>
            </div>

            {{-- Desactivar filtradas — deshabilitado por defecto, solo habilita con grupo estable --}}
            @php $hasGrupo = $grupoEstableId !== null; $selectedGrupo = $grupoEstableId ? $grupoEstablesOptions->firstWhere('id', $grupoEstableId) : null; @endphp
                <div x-data="{ open: false }" class="shrink-0">
                    <button type="button" @click="if ({{ $hasGrupo ? 'true' : 'false' }}) open = true"
                        @if(!$hasGrupo) disabled @endif
                        class="inline-flex items-center gap-1.5 px-3 py-2 min-h-[40px] rounded-lg border text-xs font-bold uppercase tracking-widest transition-all shrink-0 {{ $hasGrupo ? 'bg-red-500/10 hover:bg-red-500/20 text-red-400 border-red-500/20' : 'bg-white/5 text-gray-500 border-white/5 opacity-40 cursor-not-allowed' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 18M5.636 5.636L6 6"/></svg>
                        Desactivar
                    </button>
                    <template x-teleport="body">
                        <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                            <div @click="open = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                            <div class="relative bg-gray-900 border border-white/10 rounded-lg shadow-2xl w-full max-w-md overflow-hidden">
                                <div class="px-5 py-4 border-b border-white/5">
                                    <h3 class="text-sm font-bold text-white">¿Desactivar preguntas filtradas?</h3>
                                    <p class="text-xs text-gray-400 mt-1">Se desactivarán todas las preguntas de <span class="text-white font-medium">{{ $selectedGrupo?->code ?? '—' }}</span> para el pensum seleccionado. Esta acción puede revertirse activándolas de nuevo.</p>
                                </div>
                                <div class="px-5 py-3 bg-white/[0.02] border-t border-white/5 flex items-center justify-end gap-2">
                                    <button @click="open = false" class="px-4 py-2 rounded-lg text-xs font-bold bg-white/5 hover:bg-white/10 text-gray-300 border border-white/10 transition-all">Cancelar</button>
                                    <button @click="open = false; $wire.deactivateFiltered()" class="px-4 py-2 rounded-lg text-xs font-bold bg-red-500 hover:bg-red-600 text-white transition-all">Sí, desactivar</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-data="{ openAct: false }" class="shrink-0">
                    <button type="button" @click="if ({{ $hasGrupo ? 'true' : 'false' }}) openAct = true"
                        @if(!$hasGrupo) disabled @endif
                        class="inline-flex items-center gap-1.5 px-3 py-2 min-h-[40px] rounded-lg border text-xs font-bold uppercase tracking-widest transition-all shrink-0 {{ $hasGrupo ? 'bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border-emerald-500/20' : 'bg-white/5 text-gray-500 border-white/5 opacity-40 cursor-not-allowed' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Activar
                    </button>
                    <template x-teleport="body">
                        <div x-show="openAct" x-cloak x-transition.opacity class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                            <div @click="openAct = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                            <div class="relative bg-gray-900 border border-white/10 rounded-lg shadow-2xl w-full max-w-md overflow-hidden">
                                <div class="px-5 py-4 border-b border-white/5">
                                    <h3 class="text-sm font-bold text-white">¿Activar preguntas filtradas?</h3>
                                    <p class="text-xs text-gray-400 mt-1">Se activarán todas las preguntas de <span class="text-white font-medium">{{ $selectedGrupo?->code ?? '—' }}</span> para el pensum seleccionado.</p>
                                </div>
                                <div class="px-5 py-3 bg-white/[0.02] border-t border-white/5 flex items-center justify-end gap-2">
                                    <button @click="openAct = false" class="px-4 py-2 rounded-lg text-xs font-bold bg-white/5 hover:bg-white/10 text-gray-300 border border-white/10 transition-all">Cancelar</button>
                                    <button @click="openAct = false; $wire.activateFiltered()" class="px-4 py-2 rounded-lg text-xs font-bold bg-emerald-500 hover:bg-emerald-600 text-white transition-all">Sí, activar</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters row (only when pensum selected) --}}
    @if($selectedPensum)
        <div class="px-4 sm:px-6 py-3 bg-gray-800/20 border-b border-white/5 flex flex-col sm:flex-row gap-2 sm:items-center justify-between">
            <div class="flex items-center gap-2 text-[11px] text-gray-400">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-cyan-500/10 border border-cyan-500/20 rounded-full text-cyan-400 font-bold">
                    Pensum #{{ $selectedPensum->id }} · {{ $selectedPensum->asignatura?->name }} — {{ $selectedPensum->grado?->name }}
                </span>
                <span class="hidden sm:inline text-gray-500">{{ $selectedPensum->pestudio?->code }} · {{ $totalQuestions }} pregunta(s)</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Filtrar por texto de pregunta…"
                        class="w-full sm:w-64 bg-gray-800 text-gray-200 text-xs rounded-lg border border-white/5 pl-8 pr-3 py-2 focus:border-cyan-500/30 focus:ring-1 focus:ring-cyan-500/20 outline-none">
                    <svg class="w-3.5 h-3.5 text-gray-500 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <label class="flex items-center gap-1.5 cursor-pointer select-none text-[11px] text-gray-400">
                    <input type="checkbox" wire:model.live="showInactive" class="rounded border-white/10 bg-gray-800 text-cyan-500 focus:ring-cyan-500/20">
                    Inactivas
                </label>
            </div>
        </div>
    @endif

    {{-- Body --}}
    <div class="p-4 sm:p-6">
        @if(!$selectedPensum)
            {{-- Resumen sin filtro --}}
            @if($summary->isEmpty())
                <div class="text-center py-10">
                    <p class="text-gray-500 text-sm">No hay preguntas registradas en <code class="text-gray-400">diag_questions</code>.</p>
                </div>
            @else
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-3">Resumen por pensum ({{ $summary->count() }} pensum con preguntas)</p>
                <div class="overflow-x-auto border border-white/5 rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="bg-white/[0.02] border-b border-white/5">
                            <tr class="text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                <th class="text-left px-3 py-2">Pensum</th>
                                <th class="text-left px-3 py-2">Asignatura / Grado</th>
                                <th class="text-center px-3 py-2">Preguntas</th>
                                <th class="text-center px-3 py-2" title="Pevaluaciones que usan ese pensum">Cargas</th>
                                <th class="text-center px-3 py-2" title="Grupos estables distintos vía pevaluacion.grupo_estable_id">Grupos</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($summary as $row)
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-3 py-2">
                                        <span class="text-xs font-mono text-cyan-400">#{{ $row->pensum_id }}</span>
                                        <span class="block text-[10px] text-gray-500">{{ $row->pensum->pestudio?->code }} · {{ $row->pensum->pestudio?->name }}</span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="text-xs text-white font-medium">{{ $row->pensum->asignatura?->name }}</span>
                                        <span class="block text-[10px] text-gray-400">{{ $row->pensum->grado?->name }}</span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-cyan-500/10 border border-cyan-500/20 rounded-full text-xs font-bold text-cyan-400">{{ $row->total_q }}</span>
                                        <span class="block text-[9px] text-gray-500">{{ $row->activas }} activas</span>
                                    </td>
                                    <td class="px-3 py-2 text-center text-xs text-gray-300">{{ $row->pevsCount }}</td>
                                    <td class="px-3 py-2 text-center">
                                        @if($row->gruposCount > 0)
                                            <span class="inline-flex px-2 py-1 bg-amber-500/10 border border-amber-500/20 rounded-full text-xs font-bold text-amber-400">{{ $row->gruposCount }}</span>
                                        @else
                                            <span class="text-[11px] text-gray-600">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <button wire:click="$set('pensumId', {{ $row->pensum_id }})"
                                            class="px-3 py-1.5 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 rounded-lg border border-cyan-500/20 text-[11px] font-bold uppercase tracking-widest transition-all">
                                            Ver →
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-[10px] text-gray-600 mt-2">Tip: selecciona un pensum para ver sus preguntas agrupadas por <code class="text-amber-400">grupo_estable</code> (vía <code class="text-gray-400">pevaluacions.pensum_id → grupo_estable_id</code>).</p>
            @endif
        @else
            {{-- Detalle agrupado por grupo_estable --}}
            @if($grouped->isEmpty())
                <p class="text-sm text-gray-500 text-center py-8">Sin datos de agrupación.</p>
            @else
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-3">
                    {{ $grouped->count() }} grupo(s) estable(s) para este pensum · {{ $totalQuestions }} pregunta(s) en total
                    <span class="normal-case font-normal text-gray-600">— cada grupo muestra el mismo set de preguntas del pensum, contextualizado por sus pevaluaciones</span>
                </p>
                <div class="space-y-3">
                    @foreach($grouped as $g)
                        @php $grupo = $g->grupo; @endphp
                        <div class="border border-white/5 rounded-lg overflow-hidden bg-gray-800/20" wire:key="g-{{ $g->key }}">
                            {{-- Grupo header --}}
                            <button wire:click="toggleGroup({{ $g->key }})" class="w-full flex items-center justify-between gap-3 px-4 py-3 hover:bg-white/[0.02] transition-colors text-left">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $grupo ? 'bg-amber-500/20 text-amber-400' : 'bg-gray-700/50 text-gray-400' }}">
                                        @if($grupo)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                        @endif
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-white truncate">
                                            @if($grupo)
                                                {{ $grupo->code }} — {{ $grupo->name }}
                                            @else
                                                Sin grupo asignado
                                            @endif
                                        </p>
                                        <p class="text-[11px] text-gray-400">
                                            {{ $g->pevsCount }} carga(s) · {{ $totalQuestions }} pregunta(s) del pensum
                                            @if($grupo)
                                                <span class="text-gray-500">· {{ $grupo->hour_t_week ?? 0 }}hT / {{ $grupo->hour_p_week ?? 0 }}hP</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="hidden sm:inline text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded-full border {{ $grupo ? 'bg-amber-500/10 border-amber-500/20 text-amber-400' : 'bg-white/5 border-white/5 text-gray-400' }}">
                                        {{ $g->pevsCount }} pevs
                                    </span>
                                    <svg class="w-4 h-4 text-gray-500 transition-transform {{ $g->isExpanded ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </button>

                            @if($g->isExpanded)
                                {{-- Pevaluaciones del grupo --}}
                                @if($g->pevs->isNotEmpty())
                                    <div class="px-4 pb-2 flex flex-wrap items-center gap-2">
                                        <div class="flex flex-wrap gap-1.5 flex-1">
                                            @foreach($g->pevs as $pev)
                                                <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-white/5 border border-white/5 rounded-full text-[11px] text-gray-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    {{ $pev->seccion?->name ?? '?' }} ({{ $pev->seccion?->grado?->code ?? '?' }})
                                                    <span class="text-gray-500">·</span>
                                                    <span class="text-cyan-400">{{ $pev->profesor?->lastname }}, {{ $pev->profesor?->name }}</span>
                                                    <span class="text-gray-500">·</span>
                                                    <span class="text-gray-400">{{ $pev->lapso?->name ?? '—' }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <div x-data="{ openActG: false }">
                                                <button type="button" @click="openActG = true" title="Activar preguntas de este grupo" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                                <template x-teleport="body">
                                                    <div x-show="openActG" x-cloak x-transition.opacity class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                                                        <div @click="openActG = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                                                        <div class="relative bg-gray-900 border border-white/10 rounded-lg shadow-2xl w-full max-w-md overflow-hidden">
                                                            <div class="px-5 py-4 border-b border-white/5"><h3 class="text-sm font-bold text-white">¿Activar preguntas de este grupo?</h3><p class="text-xs text-gray-400 mt-1">Se activarán las preguntas asociadas a <span class="text-white font-medium">{{ $grupo?->code ?? 'Sin grupo' }}</span>.</p></div>
                                                            <div class="px-5 py-3 bg-white/[0.02] border-t border-white/5 flex items-center justify-end gap-2">
                                                                <button @click="openActG = false" class="px-4 py-2 rounded-lg text-xs font-bold bg-white/5 hover:bg-white/10 text-gray-300 border border-white/10">Cancelar</button>
                                                                <button @click="openActG = false; $wire.activateGroup({{ $g->key }})" class="px-4 py-2 rounded-lg text-xs font-bold bg-emerald-500 hover:bg-emerald-600 text-white">Sí, activar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                            <div x-data="{ openDesG: false }">
                                                <button type="button" @click="openDesG = true" title="Desactivar preguntas de este grupo" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 18M5.636 5.636L6 6"/></svg>
                                                </button>
                                                <template x-teleport="body">
                                                    <div x-show="openDesG" x-cloak x-transition.opacity class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                                                        <div @click="openDesG = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                                                        <div class="relative bg-gray-900 border border-white/10 rounded-lg shadow-2xl w-full max-w-md overflow-hidden">
                                                            <div class="px-5 py-4 border-b border-white/5"><h3 class="text-sm font-bold text-white">¿Desactivar preguntas de este grupo?</h3><p class="text-xs text-gray-400 mt-1">Se desactivarán las preguntas asociadas a <span class="text-white font-medium">{{ $grupo?->code ?? 'Sin grupo' }}</span>.</p></div>
                                                            <div class="px-5 py-3 bg-white/[0.02] border-t border-white/5 flex items-center justify-end gap-2">
                                                                <button @click="openDesG = false" class="px-4 py-2 rounded-lg text-xs font-bold bg-white/5 hover:bg-white/10 text-gray-300 border border-white/10">Cancelar</button>
                                                                <button @click="openDesG = false; $wire.deactivateGroup({{ $g->key }})" class="px-4 py-2 rounded-lg text-xs font-bold bg-red-500 hover:bg-red-600 text-white">Sí, desactivar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="px-4 pb-2 text-[11px] text-gray-500 italic">Este pensum no tiene cargas (pevaluacions) registradas para este grupo.</div>
                                @endif

                                {{-- Preguntas del pensum --}}
                                @if($g->questions->isEmpty())
                                    <div class="px-4 pb-4 text-center py-6 text-sm text-gray-500">
                                        @if($search !== '')
                                            Sin preguntas que coincidan con “{{ $search }}”.
                                        @else
                                            Este pensum no tiene preguntas registradas.
                                        @endif
                                    </div>
                                @else
                                    <div class="px-4 pb-4">
                                        <div class="overflow-x-auto border border-white/5 rounded-lg">
                                            <table class="w-full text-sm">
                                                <thead class="bg-white/[0.03] border-b border-white/5">
                                                    <tr class="text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                                        <th class="text-left px-3 py-2 w-10">#</th>
                                                        <th class="text-left px-3 py-2">Pregunta</th>
                                                        <th class="text-center px-3 py-2">Tipo</th>
                                                        <th class="text-center px-3 py-2">Dif.</th>
                                                        <th class="text-center px-3 py-2">Peso</th>
                                                        <th class="text-center px-3 py-2">Activa</th>
                                                        <th class="text-center px-3 py-2">Opciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-white/5">
                                                    @foreach($g->questions as $q)
                                                        <tr class="hover:bg-white/[0.02] transition-colors {{ $q->activo ? 'bg-emerald-500/[0.03] border-l-2 border-l-emerald-500/40' : 'opacity-60 bg-white/[0.01]' }}">
                                                            <td class="px-3 py-2 text-xs font-mono {{ $q->activo ? 'text-emerald-400' : 'text-gray-500' }}">{{ $q->orden ?? $loop->iteration }}</td>
                                                            <td class="px-3 py-2">
                                                                <div class="flex items-start gap-1.5">
                                                                    @if($q->activo)
                                                                        <span class="mt-0.5 w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_6px_rgba(16,185,129,0.5)] shrink-0" title="Activa"></span>
                                                                    @else
                                                                        <span class="mt-0.5 w-1.5 h-1.5 rounded-full bg-gray-600 shrink-0" title="Inactiva"></span>
                                                                    @endif
                                                                    <div class="min-w-0">
                                                                        <p class="text-xs {{ $q->activo ? 'text-white' : 'text-gray-400' }} leading-relaxed line-clamp-2" title="{{ $q->pregunta }}">{{ $q->pregunta }}</p>
                                                                        <span class="text-[10px] {{ $q->activo ? 'text-gray-400' : 'text-gray-600' }}">
                                                                            @if($q->competency) {{ $q->competency->name }} @endif
                                                                            @if($q->indicator) · {{ $q->indicator->name }} @endif
                                                                            @if($q->diagMain) · <span class="{{ $q->activo ? 'text-cyan-400' : 'text-gray-500' }}">{{ $q->diagMain->name }}</span> @endif
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="px-3 py-2 text-center">
                                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest border
                                                                    @if($q->tipo_pregunta === 'multiple') bg-cyan-500/10 border-cyan-500/20 text-cyan-400
                                                                    @elseif($q->tipo_pregunta === 'abierta') bg-amber-500/10 border-amber-500/20 text-amber-400
                                                                    @else bg-white/5 border-white/5 text-gray-400 @endif">
                                                                    {{ $q->tipo_pregunta }}
                                                                </span>
                                                            </td>
                                                            <td class="px-3 py-2 text-center text-xs text-gray-300">{{ $q->difficulty ?? '—' }}</td>
                                                            <td class="px-3 py-2 text-center text-xs text-gray-300">{{ $q->weighing ?? '—' }}</td>
                                                            <td class="px-3 py-2 text-center">
                                                                <button wire:click="toggleQuestion({{ $q->id }})" title="{{ $q->activo ? 'Desactivar' : 'Activar' }} pregunta"
                                                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full border transition-all duration-200 {{ $q->activo ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400 hover:bg-emerald-500/20' : 'bg-gray-700/50 border-white/10 text-gray-500 hover:bg-white/10 hover:text-gray-300' }}">
                                                                    @if($q->activo)
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                                    @else
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                    @endif
                                                                </button>
                                                            </td>
                                                            <td class="px-3 py-2 text-center text-xs text-gray-400">{{ $q->options->count() }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

    {{-- State loading — btn flotante abajo a la derecha con transparencia --}}
    <div wire:loading class="fixed bottom-6 right-6 z-50">
        <div class="flex items-center gap-2 rounded-full bg-gray-900/70 px-4 py-2.5 text-xs font-bold tracking-widest uppercase text-white backdrop-blur-md border border-white/10 shadow-xl shadow-black/30">
            <svg class="h-4 w-4 animate-spin text-emerald-400" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span>Cargando ...</span>
        </div>
    </div>
</div>
