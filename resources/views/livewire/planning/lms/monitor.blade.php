<div class="w-full mx-auto py-8 px-4 space-y-6"
     x-data="{ helpOpen: false }"
     x-cloak>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white">Seguimiento y Control para el Contenido LMS</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                Supervisa, controla y da seguimiento al contenido digital publicado por los docentes.
            </p>
        </div>
    </div>

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
        <div class="bg-white dark:bg-slate-800/40 border border-gray-200 dark:border-slate-700/50 rounded-lg p-3">
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 dark:text-slate-400">Total lecciones</p>
        </div>
        <div class="bg-emerald-50/50 dark:bg-emerald-500/5 border border-emerald-200 dark:border-emerald-500/20 rounded-lg p-3">
            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['published'] }}</p>
            <p class="text-xs text-emerald-600/70 dark:text-emerald-400/70">Publicadas</p>
        </div>
        <div class="bg-amber-50/50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/20 rounded-lg p-3">
            <p class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ $stats['scheduled'] }}</p>
            <p class="text-xs text-amber-600/70 dark:text-amber-400/70">Programadas</p>
        </div>
        <div class="bg-gray-100/50 dark:bg-slate-500/5 border border-slate-200 dark:border-slate-500/20 rounded-lg p-3">
            <p class="text-lg font-bold text-gray-500 dark:text-slate-400">{{ $stats['draft'] }}</p>
            <p class="text-xs text-gray-500/70 dark:text-slate-400/70">Borradores</p>
        </div>
        <div class="bg-red-50/50 dark:bg-red-500/5 border border-red-200 dark:border-red-500/20 rounded-lg p-3">
            <p class="text-lg font-bold text-red-600 dark:text-red-400">{{ $stats['archived'] }}</p>
            <p class="text-xs text-red-400/70">Archivadas</p>
        </div>
        <div class="bg-blue-50/50 dark:bg-blue-500/5 border border-blue-200 dark:border-blue-500/20 rounded-lg p-3">
            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $stats['withContent'] }}</p>
            <p class="text-xs text-blue-400/70">Con contenido</p>
        </div>
        <div class="bg-purple-50/50 dark:bg-purple-500/5 border border-purple-500/20 rounded-lg p-3">
            <p class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $stats['totalActivities'] }}</p>
            <p class="text-xs text-purple-400/70">Total actividades</p>
        </div>
    </div>

    {{-- View mode toggle --}}
    <div class="flex items-center justify-end">
        <div class="bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg p-0.5 flex">
            <button wire:click="$set('viewMode', 'table')"
                    @class([
                        'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold transition-all duration-200',
                        'bg-white dark:bg-slate-700 text-gray-900 dark:text-white shadow-sm' => $viewMode === 'table',
                        'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300' => $viewMode !== 'table',
                    ])>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span class="hidden sm:inline">Tabla</span>
            </button>
            <button wire:click="$set('viewMode', 'grid')"
                    @class([
                        'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold transition-all duration-200',
                        'bg-white dark:bg-slate-700 text-gray-900 dark:text-white shadow-sm' => $viewMode === 'grid',
                        'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300' => $viewMode !== 'grid',
                    ])>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="hidden sm:inline">Grid</span>
            </button>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-gray-50 dark:bg-slate-800/30 border border-gray-200 dark:border-slate-700/50 rounded-lg p-4 space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-7 gap-3">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Estado</label>
                <select wire:model.live="filterStatus"
                        class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-lg px-3 py-3 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                    <option value="">Todos</option>
                    <option value="DRAFT">Borrador</option>
                    <option value="SCHEDULED">Programado</option>
                    <option value="PUBLISHED">Publicado</option>
                    <option value="ARCHIVED">Archivado</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Profesor</label>
                <select wire:model.live="filterProfesor"
                        class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-lg px-3 py-3 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                    <option value="">Todos</option>
                    @foreach($profesores as $prof)
                        <option value="{{ $prof->id }}">{{ $prof->user?->name ?? $prof->lastname ?? 'Profesor #'.$prof->id }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Grado</label>
                <select wire:model.live="filterGrado"
                        class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-lg px-3 py-3 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                    <option value="">Todos</option>
                    @foreach($grados as $g)
                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Sección</label>
                <select wire:model.live="filterSeccion"
                        class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-lg px-3 py-3 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                    <option value="">Todas</option>
                    @foreach($secciones as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Asignatura</label>
                <select wire:model.live="filterAsignatura"
                        class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-lg px-3 py-3 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                    <option value="">Todas</option>
                    @foreach($asignaturas as $a)
                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">P.Estudio</label>
                <select wire:model.live="filterPestudio"
                        class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-lg px-3 py-3 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                    <option value="">Todos</option>
                    @foreach($pestudios as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Buscar</label>
                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Título actividad…"
                       class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-lg px-3 py-3 text-sm placeholder-slate-500 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none"/>
            </div>
        </div>
        @php $hasFilters = $search || $filterStatus || $filterProfesor || $filterGrado || $filterSeccion || $filterAsignatura || $filterPestudio; @endphp
        @if($hasFilters)
            <div class="flex justify-end pt-1">
                <button wire:click="clearFilters"
                        class="text-xs text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300 transition-colors inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Limpiar filtros
                </button>
            </div>
        @endif
    </div>

    @if($viewMode === 'table')

    {{-- Bulk action bar --}}
    @if(count($selectedIds) > 0)
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 p-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-lg">
            <span class="text-sm text-emerald-700 dark:text-emerald-300 font-medium">{{ count($selectedIds) }} seleccionado(s)</span>
            <div class="flex items-center gap-2 flex-wrap sm:ml-auto">
                <button wire:click="bulkPublish"
                        wire:confirm="¿Publicar {{ count($selectedIds) }} contenido(s)?"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-200 dark:hover:bg-emerald-500/30 border border-emerald-200 dark:border-emerald-500/30 transition-colors">
                    Publicar
                </button>
                <button wire:click="bulkUnpublish"
                        wire:confirm="¿Archivar {{ count($selectedIds) }} contenido(s)?"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-500/30 border border-red-200 dark:border-red-500/30 transition-colors">
                    Archivar
                </button>
                <button wire:click="bulkDelete"
                        wire:confirm="¿Eliminar permanentemente {{ count($selectedIds) }} publicación(es)? Esta acción no se puede deshacer."
                        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20 border border-red-200 dark:border-red-500/20 transition-colors">
                    Eliminar
                </button>
                <button wire:click="clearSelection"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-200 dark:bg-slate-700/50 text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white border border-gray-200 dark:border-slate-600/50 transition-colors">
                    Cancelar
                </button>
            </div>
        </div>
    @endif

    {{-- Tabla de publicaciones --}}
    <style>
        .scrollbar-fino::-webkit-scrollbar { width: 5px; height: 5px; }
        .scrollbar-fino::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-fino::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        .scrollbar-fino::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dark .scrollbar-fino::-webkit-scrollbar-thumb { background: #475569; }
        .dark .scrollbar-fino::-webkit-scrollbar-thumb:hover { background: #64748b; }
    </style>
    <div class="bg-gray-50 dark:bg-slate-800/30 border border-gray-200 dark:border-slate-700/50 rounded-lg">
        <div class="overflow-x-auto scrollbar-fino" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 dark:bg-slate-700/30">
                <tr>
                    <th class="text-center px-2 py-2.5 w-10">
                        <input type="checkbox" wire:model.live="selectAll"
                               class="rounded border-gray-200 dark:border-slate-600 bg-gray-200 dark:bg-slate-700 text-emerald-600 dark:text-emerald-500 focus:ring-emerald-500/50">
                    </th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Actividad</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Asignatura</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Grado/Sec.</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Profesor</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Estado</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Publicado</th>
                    <th class="text-center px-2 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400" title="Secciones">Sec.</th>
                    <th class="text-center px-2 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400" title="Recursos descargables">Rec.</th>
                    <th class="text-center px-2 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400" title="Enlaces">Lnks</th>
                    <th class="text-center px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-700/50">
                @forelse($publications as $pub)
                    @php
                        $profesor = $pub->pevaluacion?->profesor;
                        $grado = $pub->pevaluacion?->pensum?->grado?->name ?? '—';
                        $seccion = $pub->pevaluacion?->seccion?->name ?? '—';
                        $isSelected = in_array($pub->id, $selectedIds);
                        $pubStatus = $pub->lmsPublication?->status;
                        $rowBg = match($pubStatus) {
                            'PUBLISHED' => 'bg-emerald-200 dark:bg-emerald-950',
                            'SCHEDULED' => 'bg-amber-200 dark:bg-amber-950',
                            default     => '',
                        };
                    @endphp
                    <tr class="hover:bg-gray-100 dark:hover:bg-slate-700/20 {{ $isSelected ? 'bg-emerald-500/5' : ($rowBg ?: '') }}">
                        <td class="text-center px-2 py-2.5">
                            <input type="checkbox" value="{{ $pub->id }}"
                                   wire:change="toggleSelect({{ $pub->id }})"
                                   {{ $isSelected ? 'checked' : '' }}
                                   class="rounded border-gray-200 dark:border-slate-600 bg-gray-200 dark:bg-slate-700 text-emerald-600 dark:text-emerald-500 focus:ring-emerald-500/50">
                        </td>
                        <td class="px-4 py-2.5 text-gray-800 dark:text-slate-200 max-w-[200px] truncate" title="{{ $pub->topic ?? '' }}">
                            {{ $pub->topic ?? '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-gray-500 dark:text-slate-400">
                            {{ $pub->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-gray-500 dark:text-slate-400 text-xs">
                            {{ $grado }} {{ $seccion }}
                        </td>
                        <td class="px-4 py-2.5 text-gray-500 dark:text-slate-400">
                            {{ $profesor ? trim($profesor->lastname.' '.$profesor->name) : '—' }}
                        </td>
                        <td class="px-4 py-2.5">
                            <span @class([
                                'px-2 py-0.5 rounded text-xs font-medium',
                                'bg-emerald-500/10 text-emerald-400' => $pubStatus === 'PUBLISHED',
                                'bg-amber-500/10 text-amber-400'     => $pubStatus === 'SCHEDULED',
                                'bg-gray-100 dark:bg-slate-500/10 text-gray-600 dark:text-slate-400'     => $pubStatus === 'DRAFT',
                                'bg-red-500/10 text-red-400'         => $pubStatus === 'ARCHIVED',
                                'bg-stone-600/20 text-stone-400'     => $pubStatus === null,
                            ])>
                                {{ match($pubStatus) {
                                    'PUBLISHED' => 'Publicado',
                                    'SCHEDULED' => 'Programado',
                                    'ARCHIVED'  => 'Archivado',
                                    'DRAFT'     => 'Borrador',
                                    default     => 'Sin publicar',
                                } }}
                            </span>
                            @if($pubStatus === 'SCHEDULED' && $pub->lmsPublication?->publish_at)
                                <span class="block text-[11px] text-amber-600/70 dark:text-amber-500/70 mt-0.5">
                                    {{ $pub->lmsPublication->publish_at->format('d/m/Y H:i') }}
                                    @if($pub->lmsPublication->created_at && $pub->lmsPublication->created_at->gt(now()->subHours(48)))
                                        <span class="inline-block ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-500/15 text-sky-400 border border-sky-500/25">🆕 Nueva</span>
                                    @endif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-gray-400 dark:text-slate-500 text-xs">
                            @if($pub->lmsPublication?->published_at)
                                {{ $pub->lmsPublication->published_at->format('d/m/Y H:i') }}
                            @elseif($pubStatus === 'SCHEDULED' && $pub->lmsPublication?->publish_at)
                                <span class="text-amber-600/60 dark:text-amber-500/60">Pendiente</span>
                            @elseif($pubStatus === 'DRAFT')
                                —
                            @else
                                <span class="text-gray-300 dark:text-slate-600">—</span>
                            @endif
                        </td>
                        {{-- Conteos de contenido --}}
                        <td class="px-2 py-2.5 text-center text-xs {{ $pub->lms_sections_count > 0 ? 'text-sky-400' : 'text-slate-600' }}">
                            {{ $pub->lms_sections_count > 0 ? $pub->lms_sections_count : '—' }}
                        </td>
                        <td class="px-2 py-2.5 text-center text-xs {{ $pub->lms_resources_count > 0 ? 'text-amber-400' : 'text-slate-600' }}">
                            {{ $pub->lms_resources_count > 0 ? $pub->lms_resources_count : '—' }}
                        </td>
                        <td class="px-2 py-2.5 text-center text-xs {{ $pub->lms_links_count > 0 ? 'text-cyan-400' : 'text-slate-600' }}">
                            {{ $pub->lms_links_count > 0 ? $pub->lms_links_count : '—' }}
                        </td>
                        <td class="px-4 py-2.5">
                            <div class="flex items-center justify-center gap-1" x-data="{ actionsOpen: false }" @click.away="actionsOpen = false">
                                {{-- Preview — always visible --}}
                                <button wire:click="openPreview({{ $pub->id }})"
                                        class="p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-slate-700/30 hover:bg-gray-200 dark:hover:bg-slate-600/50 border border-gray-200 dark:border-slate-600/30 hover:border-slate-500/50 transition-all"
                                        title="Vista previa de lección">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>

                                {{-- Publicar ahora (SCHEDULED) — primary, always visible --}}
                                @if($pubStatus === 'SCHEDULED')
                                    <button wire:click="confirmPublish({{ $pub->id }})"
                                            class="px-2.5 py-1.5 rounded-lg text-white bg-emerald-600 hover:bg-emerald-500 shadow-sm border border-emerald-400/40 transition-all text-xs font-bold flex items-center gap-1"
                                            title="Publicar ahora (aprobar)">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                        </svg>
                                    </button>
                                @endif

                                {{-- Desktop group (hidden on mobile) --}}
                                <div class="hidden sm:flex items-center gap-1">
                                    {{-- Auditar --}}
                                    <a href="{{ route('app.planning.lms.activity.audit', $pub) }}"
                                       class="p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:text-cyan-700 dark:hover:text-cyan-300 bg-cyan-50 dark:bg-cyan-500/10 hover:bg-cyan-100 dark:hover:bg-cyan-500/20 border border-cyan-200 dark:border-cyan-500/20 hover:border-cyan-400/40 transition-all"
                                       title="Auditar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>

                                    {{-- Configuración --}}
                                    @if($pub->lmsPublication && $pubStatus !== 'DRAFT')
                                        <button wire:click="openSettings({{ $pub->id }})"
                                                class="p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:text-blue-700 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-500/20 border border-blue-200 dark:border-blue-500/20 hover:border-blue-400/40 transition-all"
                                                title="Configurar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- Programar / Publicar (incluye actividades sin publicación) --}}
                                    @if(is_null($pubStatus) || $pubStatus === 'DRAFT' || $pubStatus === 'ARCHIVED')
                                        <button wire:click="publish({{ $pub->id }})"
                                                wire:confirm="¿Publicar esta lección? Será visible para los estudiantes."
                                                class="p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-300 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/20 hover:border-emerald-400/40 transition-all"
                                                title="Publicar ahora">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                            </svg>
                                        </button>
                                        <button wire:click="openSchedule({{ $pub->id }})"
                                                class="p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:text-amber-700 dark:hover:text-amber-300 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 border border-amber-200 dark:border-amber-500/20 hover:border-amber-400/40 transition-all"
                                                title="Programar publicación">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    @endif
                                    @if($pubStatus === 'PUBLISHED' || $pubStatus === 'SCHEDULED')
                                        <button wire:click="unpublish({{ $pub->id }})"
                                                wire:confirm="¿Archivar esta lección? Dejará de ser visible para los estudiantes."
                                                class="p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:text-red-700 dark:hover:text-red-300 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 border border-red-200 dark:border-red-500/20 hover:border-red-400/40 transition-all"
                                                title="Archivar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                            </svg>
                                        </button>
                                    @endif
                                    @if($pubStatus === 'PUBLISHED' || $pubStatus === 'SCHEDULED')
                                        <button wire:click="setDraft({{ $pub->id }})"
                                                wire:confirm="¿Revertir a borrador? La lección dejará de estar {{ $pubStatus === 'SCHEDULED' ? 'programada' : 'publicada' }}."
                                                class="p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:text-orange-700 dark:hover:text-orange-300 bg-orange-50 dark:bg-orange-500/10 hover:bg-orange-100 dark:hover:bg-orange-500/20 border border-orange-200 dark:border-orange-500/20 hover:border-orange-400/40 transition-all"
                                                title="Revertir a borrador">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>

                                {{-- Mobile "···" dropdown (hidden on sm+) --}}
                                <div class="relative sm:hidden">
                                    <button @click="actionsOpen = !actionsOpen"
                                            class="p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-slate-700/30 hover:bg-gray-200 dark:hover:bg-slate-600/50 border border-gray-200 dark:border-slate-600/30 transition-all"
                                            title="Más acciones">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>
                                    <div x-show="actionsOpen"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute right-0 z-50 mt-1 min-w-[180px] bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-xl py-1"
                                         @click="actionsOpen = false">
                                        {{-- Audit --}}
                                        <a href="{{ route('app.planning.lms.activity.audit', $pub) }}"
                                           class="flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors">
                                            <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Auditar
                                        </a>
                                        {{-- Settings --}}
                                        @if($pub->lmsPublication && $pubStatus !== 'DRAFT')
                                            <button wire:click="openSettings({{ $pub->id }})"
                                                    class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                Configurar
                                            </button>
                                        @endif
                                        {{-- Publish / Schedule --}}
                                        @if(is_null($pubStatus) || $pubStatus === 'DRAFT' || $pubStatus === 'ARCHIVED')
                                            <button wire:click="publish({{ $pub->id }})"
                                                    wire:confirm="¿Publicar esta lección? Será visible para los estudiantes."
                                                    class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                                </svg>
                                                Publicar ahora
                                            </button>
                                            <button wire:click="openSchedule({{ $pub->id }})"
                                                    class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Programar
                                            </button>
                                        @endif
                                        {{-- Archive / Revert --}}
                                        @if($pubStatus === 'PUBLISHED' || $pubStatus === 'SCHEDULED')
                                            <button wire:click="unpublish({{ $pub->id }})"
                                                    wire:confirm="¿Archivar esta lección? Dejará de ser visible para los estudiantes."
                                                    class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                                </svg>
                                                Archivar
                                            </button>
                                            <button wire:click="setDraft({{ $pub->id }})"
                                                    wire:confirm="¿Revertir a borrador? La lección dejará de estar {{ $pubStatus === 'SCHEDULED' ? 'programada' : 'publicada' }}."
                                                    class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                Revertir a borrador
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-4 py-8 text-center text-gray-400 dark:text-slate-500">
                            No hay publicaciones que coincidan con los filtros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- Paginación --}}
    @if($publications->hasPages())
        <div class="mt-4">
            {{ $publications->links('vendor.pagination.custom-tailwind') }}
        </div>
    @endif

    @elseif($viewMode === 'grid')

    {{-- Grid de actividades --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($publications as $pub)
            @php
                $profesor = $pub->pevaluacion?->profesor;
                $grado = $pub->pevaluacion?->pensum?->grado?->name ?? '—';
                $seccion = $pub->pevaluacion?->seccion?->name ?? '—';
                $pubStatus = $pub->lmsPublication?->status;
                $isPublished = $pubStatus === 'PUBLISHED';
                $cardBg = match($pubStatus) {
                    'PUBLISHED' => 'bg-emerald-200 dark:bg-emerald-950',
                    'SCHEDULED' => 'bg-amber-200 dark:bg-amber-950',
                    default     => '',
                };
            @endphp
            <div class="relative bg-white dark:bg-slate-800/40 border border-gray-200 dark:border-slate-700/60 rounded-lg overflow-hidden transition-all duration-200 group hover:shadow-lg hover:shadow-black/5 dark:hover:shadow-black/10 {{ $cardBg }}">
                @if($pubStatus === 'PUBLISHED')
                    <span class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-emerald-500 to-emerald-400"></span>
                @elseif($pubStatus === 'SCHEDULED')
                    <span class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-amber-500 to-amber-400"></span>
                @endif

                {{-- Header: estado + fecha --}}
                <div class="flex items-start justify-between gap-2 px-4 pt-4 pb-2">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <span @class([
                            'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold tracking-wide',
                            'bg-emerald-500/12 text-emerald-400 border border-emerald-500/20' => $pubStatus === 'PUBLISHED',
                            'bg-amber-500/12 text-amber-400 border border-amber-500/20'        => $pubStatus === 'SCHEDULED',
                            'bg-gray-100 dark:bg-slate-700/40 text-gray-600 dark:text-slate-400 border border-gray-200 dark:border-slate-600/50' => $pubStatus === 'DRAFT' || is_null($pubStatus),
                            'bg-red-500/12 text-red-400 border border-red-500/20'              => $pubStatus === 'ARCHIVED',
                        ])>
                            @if($pubStatus === 'PUBLISHED')
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @elseif($pubStatus === 'SCHEDULED')
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @elseif($pubStatus === 'ARCHIVED')
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg>
                            @else
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            @endif
                            {{ match($pubStatus) {
                                'PUBLISHED' => 'Publicado',
                                'SCHEDULED' => 'Programado',
                                'ARCHIVED'  => 'Archivado',
                                'DRAFT'     => 'Borrador',
                                default     => 'Sin publicar',
                            } }}
                        </span>
                    </div>
                    <span class="shrink-0 text-[11px] font-mono text-gray-400 dark:text-slate-600">
                        {{ $pub->finicial ? \Carbon\Carbon::parse($pub->finicial)->format('d/m') : '' }}
                        <span class="text-gray-300 dark:text-slate-700">—</span>
                        {{ $pub->ffinal ? \Carbon\Carbon::parse($pub->ffinal)->format('d/m') : '' }}
                    </span>
                </div>

                {{-- Body --}}
                <div class="px-4 pb-2 space-y-2">
                    {{-- Título --}}
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white leading-snug group-hover:text-emerald-600 dark:group-hover:text-emerald-300 transition-colors duration-200">
                        {{ $pub->topic ?? 'Actividad sin título' }}
                    </h3>

                    {{-- Metadata: asignatura + grado --}}
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px]">
                        @if($pub->pevaluacion?->pensum?->asignatura?->name)
                            <span class="inline-flex items-center gap-1 text-gray-500 dark:text-slate-400">
                                <svg class="w-3.5 h-3.5 text-emerald-500/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                {{ $pub->pevaluacion->pensum->asignatura->name }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1 text-gray-500 dark:text-slate-400">
                            <svg class="w-3.5 h-3.5 text-blue-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            {{ $grado }} · Sec. {{ $seccion }}
                        </span>
                    </div>

                    {{-- Profesor --}}
                    <div class="flex items-center gap-1.5 text-[11px] text-gray-500 dark:text-slate-500">
                        <svg class="w-3.5 h-3.5 text-violet-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $profesor ? trim($profesor->lastname.' '.$profesor->name) : '—' }}
                    </div>

                    {{-- Content stats --}}
                    <div class="flex flex-wrap items-center gap-2 pt-0.5">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium {{ $pub->lms_sections_count > 0 ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'bg-gray-100 dark:bg-slate-700/40 text-gray-500 dark:text-slate-500 border border-gray-200 dark:border-slate-600/40' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            {{ $pub->lms_sections_count }} {{ Str::plural('sec', $pub->lms_sections_count) }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium {{ $pub->lms_resources_count > 0 ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-gray-100 dark:bg-slate-700/40 text-gray-500 dark:text-slate-500 border border-gray-200 dark:border-slate-600/40' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            {{ $pub->lms_resources_count }} {{ Str::plural('rec', $pub->lms_resources_count) }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium {{ $pub->lms_links_count > 0 ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-gray-100 dark:bg-slate-700/40 text-gray-500 dark:text-slate-500 border border-gray-200 dark:border-slate-600/40' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            {{ $pub->lms_links_count }} {{ Str::plural('link', $pub->lms_links_count) }}
                        </span>
                    </div>

                    {{-- Published date --}}
                    @if($pub->lmsPublication?->published_at)
                        <div class="text-[11px] text-gray-400 dark:text-slate-600">
                            Publicado {{ \Carbon\Carbon::parse($pub->lmsPublication->published_at)->format('d/m/Y H:i') }}
                        </div>
                    @elseif($pubStatus === 'SCHEDULED' && $pub->lmsPublication?->publish_at)
                        <div class="text-[11px] text-amber-600/70 dark:text-amber-500/70">
                            Programado {{ $pub->lmsPublication->publish_at->format('d/m/Y H:i') }}
                            @if($pub->lmsPublication->created_at && $pub->lmsPublication->created_at->gt(now()->subHours(48)))
                                <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-500/15 text-sky-400 border border-sky-500/25">🆕 Nueva</span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Acciones --}}
                <div class="mt-2 px-4 py-2 bg-gray-50 dark:bg-slate-900/40 border-t border-gray-200 dark:border-slate-700/40 flex items-center gap-1.5 flex-wrap" x-data="{ actionsOpen: false }" @click.away="actionsOpen = false">
                    {{-- Vista previa — always visible --}}
                    <button wire:click="openPreview({{ $pub->id }})"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-medium bg-gray-100 dark:bg-slate-700/40 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700/60 hover:text-gray-900 dark:hover:text-white border border-gray-200 dark:border-slate-600/40 transition-all"
                            title="Vista previa">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Vista
                    </button>

                    {{-- Publicar ahora (SCHEDULED) — primary, always visible --}}
                    @if($pubStatus === 'SCHEDULED')
                        <button wire:click="confirmPublish({{ $pub->id }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-sm border border-emerald-400/40 transition-all"
                                title="Publicar ahora">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            Publicar
                        </button>
                    @endif

                    <span class="hidden sm:block flex-1"></span>

                    {{-- Desktop group --}}
                    <div class="hidden sm:flex items-center gap-1.5">
                        {{-- Auditar --}}
                        <a href="{{ route('app.planning.lms.activity.audit', $pub) }}"
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-medium bg-cyan-50 dark:bg-cyan-500/10 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-100 dark:hover:bg-cyan-500/20 border border-cyan-200 dark:border-cyan-500/20 transition-all"
                           title="Auditar">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Auditar
                        </a>

                        {{-- Publicar / Programar --}}
                        @if(is_null($pubStatus) || $pubStatus === 'DRAFT' || $pubStatus === 'ARCHIVED')
                            <button wire:click="publish({{ $pub->id }})"
                                    wire:confirm="¿Publicar esta lección? Será visible para los estudiantes."
                                    class="inline-flex items-center gap-1 p-1.5 rounded-lg text-[10px] font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/20 transition-all"
                                    title="Publicar ahora">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            </button>
                            <button wire:click="openSchedule({{ $pub->id }})"
                                    class="inline-flex items-center gap-1 p-1.5 rounded-lg text-[10px] font-medium text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 border border-amber-200 dark:border-amber-500/20 transition-all"
                                    title="Programar publicación">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                        @endif

                        {{-- Archivar --}}
                        @if($pubStatus === 'PUBLISHED' || $pubStatus === 'SCHEDULED')
                            <button wire:click="unpublish({{ $pub->id }})"
                                    wire:confirm="¿Archivar esta lección? Dejará de ser visible para los estudiantes."
                                    class="inline-flex items-center gap-1 p-1.5 rounded-lg text-[10px] font-medium text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 border border-red-200 dark:border-red-500/20 transition-all"
                                    title="Archivar">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            </button>
                        @endif

                        {{-- Revertir a borrador --}}
                        @if($pubStatus === 'PUBLISHED' || $pubStatus === 'SCHEDULED')
                            <button wire:click="setDraft({{ $pub->id }})"
                                    wire:confirm="¿Revertir a borrador?"
                                    class="inline-flex items-center gap-1 p-1.5 rounded-lg text-[10px] font-medium text-orange-700 dark:text-orange-300 bg-orange-50 dark:bg-orange-500/10 hover:bg-orange-100 dark:hover:bg-orange-500/20 border border-orange-200 dark:border-orange-500/20 transition-all"
                                    title="Revertir a borrador">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </button>
                        @endif
                    </div>

                    {{-- Mobile ··· dropdown --}}
                    <div class="relative sm:hidden">
                        <button @click="actionsOpen = !actionsOpen"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-slate-700/40 hover:bg-gray-200 dark:hover:bg-slate-700/60 border border-gray-200 dark:border-slate-600/40 transition-all"
                                title="Más acciones">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                            </svg>
                        </button>
                        <div x-show="actionsOpen"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 z-50 mt-1 min-w-[180px] bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-xl py-1"
                             @click="actionsOpen = false">
                            {{-- Audit --}}
                            <a href="{{ route('app.planning.lms.activity.audit', $pub) }}"
                               class="flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors">
                                <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Auditar
                            </a>
                            {{-- Publish / Schedule --}}
                            @if(is_null($pubStatus) || $pubStatus === 'DRAFT' || $pubStatus === 'ARCHIVED')
                                <button wire:click="publish({{ $pub->id }})"
                                        wire:confirm="¿Publicar esta lección? Será visible para los estudiantes."
                                        class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                    Publicar ahora
                                </button>
                                <button wire:click="openSchedule({{ $pub->id }})"
                                        class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Programar
                                </button>
                            @endif
                            {{-- Archive --}}
                            @if($pubStatus === 'PUBLISHED' || $pubStatus === 'SCHEDULED')
                                <button wire:click="unpublish({{ $pub->id }})"
                                        wire:confirm="¿Archivar esta lección? Dejará de ser visible para los estudiantes."
                                        class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    Archivar
                                </button>
                                <button wire:click="setDraft({{ $pub->id }})"
                                        wire:confirm="¿Revertir a borrador?"
                                        class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Revertir a borrador
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16">
                <svg class="w-12 h-12 text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-sm font-medium text-slate-400">No hay actividades disponibles</p>
                <p class="text-xs text-gray-500 dark:text-slate-600 mt-1">Ajusta los filtros o crea una actividad primero.</p>
            </div>
        @endforelse
    </div>

    {{-- Paginación grid --}}
    @if($publications->hasPages())
        <div class="mt-4">
            {{ $publications->links('vendor.pagination.custom-tailwind') }}
        </div>
    @endif

    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Programar publicación                                 --}}
    {{-- ============================================================ --}}
    @if($showScheduleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
                {{-- Header --}}
                <div class="px-6 py-2 border-b border-gray-200 dark:border-slate-700/50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Programar publicación
                    </h3>
                    <button wire:click="$set('showScheduleModal', false)" class="text-gray-400 dark:text-slate-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                {{-- Body --}}
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Fecha de publicación *</label>
                        <input type="datetime-local" wire:model="schedulePublishAt"
                               class="w-full bg-gray-50 dark:bg-slate-900/50 border border-gray-200 dark:border-slate-600 text-gray-800 dark:text-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                        @error('schedulePublishAt') <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Despublicar automáticamente (opcional)</label>
                        <input type="datetime-local" wire:model="scheduleUnpublishAt"
                               class="w-full bg-gray-50 dark:bg-slate-900/50 border border-gray-200 dark:border-slate-600 text-gray-800 dark:text-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                        @error('scheduleUnpublishAt') <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-slate-300">
                            <input type="checkbox" wire:model="scheduleAllowComments"
                                   class="rounded border-gray-200 dark:border-slate-600 bg-gray-200 dark:bg-slate-700 text-emerald-600 dark:text-emerald-500 focus:ring-emerald-500/50">
                            Permitir comentarios
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-slate-300">
                            <input type="checkbox" wire:model="scheduleAllowDownloads"
                                   class="rounded border-gray-200 dark:border-slate-600 bg-gray-200 dark:bg-slate-700 text-emerald-600 dark:text-emerald-500 focus:ring-emerald-500/50">
                            Permitir descargas
                        </label>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Notas internas (opcional)</label>
                        <textarea wire:model="scheduleNotes" rows="2"
                                  class="w-full bg-gray-50 dark:bg-slate-900/50 border border-gray-200 dark:border-slate-600 text-gray-800 dark:text-slate-200 rounded-lg px-3 py-2 text-sm placeholder-slate-500 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none"
                                  placeholder="Notas para el coordinador…"></textarea>
                        @error('scheduleNotes') <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                {{-- Footer --}}
                <div class="px-6 py-2 border-t border-gray-200 dark:border-slate-700/50 flex justify-end gap-3">
                    <button wire:click="$set('showScheduleModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white bg-gray-200 dark:bg-slate-700/50 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="saveSchedule"
                            class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-white bg-amber-600 hover:bg-amber-500 rounded-lg transition-colors">
                        Programar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Configurar publicación                                --}}
    {{-- ============================================================ --}}
    @if($showSettingsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
                {{-- Header --}}
                <div class="px-6 py-2 border-b border-gray-200 dark:border-slate-700/50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Configurar publicación
                    </h3>
                    <button wire:click="$set('showSettingsModal', false)" class="text-gray-400 dark:text-slate-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                {{-- Body --}}
                <div class="px-6 py-5 space-y-4">
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-slate-400">
                        <span>Estado actual:</span>
                        <span @class([
                            'px-2 py-0.5 rounded text-xs font-medium',
                            'bg-emerald-500/10 text-emerald-400' => $settingsStatus === 'PUBLISHED',
                            'bg-amber-500/10 text-amber-400'     => $settingsStatus === 'SCHEDULED',
                            'bg-red-500/10 text-red-400'         => $settingsStatus === 'ARCHIVED',
                            'bg-gray-100 dark:bg-slate-500/10 text-gray-600 dark:text-slate-400'     => true,
                        ])>
                            {{ match($settingsStatus) {
                                'PUBLISHED' => 'Publicado',
                                'SCHEDULED' => 'Programado',
                                'ARCHIVED'  => 'Archivado',
                                default     => 'Borrador',
                            } }}
                        </span>
                    </div>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-slate-300">
                            <input type="checkbox" wire:model="settingsAllowComments"
                                   class="rounded border-gray-200 dark:border-slate-600 bg-gray-200 dark:bg-slate-700 text-emerald-600 dark:text-emerald-500 focus:ring-emerald-500/50">
                            Permitir comentarios
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-slate-300">
                            <input type="checkbox" wire:model="settingsAllowDownloads"
                                   class="rounded border-gray-200 dark:border-slate-600 bg-gray-200 dark:bg-slate-700 text-emerald-600 dark:text-emerald-500 focus:ring-emerald-500/50">
                            Permitir descargas
                        </label>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Notas internas</label>
                        <textarea wire:model="settingsNotes" rows="2"
                                  class="w-full bg-gray-50 dark:bg-slate-900/50 border border-gray-200 dark:border-slate-600 text-gray-800 dark:text-slate-200 rounded-lg px-3 py-2 text-sm placeholder-slate-500 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none"
                                  placeholder="Notas para el coordinador…"></textarea>
                        @error('settingsNotes') <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                {{-- Footer --}}
                <div class="px-6 py-2 border-t border-gray-200 dark:border-slate-700/50 flex justify-end gap-3">
                    <button wire:click="$set('showSettingsModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white bg-gray-200 dark:bg-slate-700/50 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="saveSettings"
                            class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-white bg-blue-600 hover:bg-blue-500 rounded-lg transition-colors">
                        Guardar cambios
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Confirmar publicación                                --}}
    {{-- ============================================================ --}}
    @if($showPublishModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" wire:key="publish-confirm">
            <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700/50 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        Publicar lección
                    </h3>
                    <button wire:click="cancelPublish" class="text-gray-400 dark:text-slate-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <p class="text-sm text-gray-600 dark:text-slate-300">
                        ¿Publicar la lección <strong class="text-gray-900 dark:text-white">{{ $publishActivityTitle }}</strong>?
                    </p>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-2">
                        Será visible inmediatamente para los estudiantes en su aula virtual.
                    </p>
                </div>
                <div class="px-6 py-3 bg-gray-50 dark:bg-slate-900/50 border-t border-gray-200 dark:border-slate-700/50 flex items-center justify-end gap-2">
                    <button wire:click="cancelPublish"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium text-gray-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 hover:bg-gray-100 dark:hover:bg-slate-700 transition-all">
                        Cancelar
                    </button>
                    <button wire:click="doPublish"
                            class="px-4 py-1.5 rounded-lg text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-sm border border-emerald-400/40 transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        Publicar ahora
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Vista previa de lección (student-preview component)  --}}
    {{-- ============================================================ --}}
    @if(($showPreviewModal ?? false) && ($previewData ?? null))
        <x-lms.student-preview :preview="$previewData" closeMethod="closePreview" wire:key="student-preview" />
    @endif
    {{-- ============================================================ --}}
    {{-- HELP BUTTON: Guía de estados de lecciones                  --}}
    {{-- ============================================================ --}}
        {{-- Floating button --}}
        <button @click="helpOpen = true"
                class="fixed bottom-6 right-6 z-40 w-12 h-12 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/30 hover:text-emerald-300 hover:scale-110 flex items-center justify-center shadow-lg backdrop-blur-sm transition-all duration-300 group"
                title="Guía de estados de lecciones"
                x-show="!helpOpen">
            <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M12 18h.01"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c1.256 0 2.47.202 3.612.586a9.044 9.044 0 012.907 1.895 8.997 8.997 0 011.896 2.908A8.95 8.95 0 0121 12a8.95 8.95 0 01-.585 3.611 8.997 8.997 0 01-1.896 2.908 9.044 9.044 0 01-2.907 1.895A8.98 8.98 0 0112 21a8.98 8.98 0 01-3.612-.586 9.044 9.044 0 01-2.907-1.895 8.997 8.997 0 01-1.896-2.908A8.95 8.95 0 013 12a8.95 8.95 0 01.585-3.611 8.997 8.997 0 011.896-2.908 9.044 9.044 0 012.907-1.895A8.98 8.98 0 0112 3z"/>
            </svg>
        </button>

        {{-- Backdrop --}}
        <div x-show="helpOpen"
             x-transition:enter="transition-opacity duration-300"
             x-transition:leave="transition-opacity duration-200"
             @click="helpOpen = false"
             class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm"></div>

        {{-- Slideover panel --}}
        <div x-show="helpOpen"
             x-transition:enter="transition-transform duration-300 ease-out"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition-transform duration-200 ease-in"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             @keydown.escape.window="helpOpen = false"
             class="fixed top-0 right-0 z-50 h-full w-full max-w-2xl bg-white dark:bg-slate-800 border-l border-gray-200 dark:border-slate-700/50 shadow-2xl overflow-y-auto">

            {{-- Sticky header --}}
            <div class="sticky top-0 bg-white/95 dark:bg-slate-800/95 backdrop-blur-sm border-b border-gray-200 dark:border-slate-700/50 z-10">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M12 18h.01"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c1.256 0 2.47.202 3.612.586a9.044 9.044 0 012.907 1.895 8.997 8.997 0 011.896 2.908A8.95 8.95 0 0121 12a8.95 8.95 0 01-.585 3.611 8.997 8.997 0 01-1.896 2.908 9.044 9.044 0 01-2.907 1.895A8.98 8.98 0 0112 21a8.98 8.98 0 01-3.612-.586 9.044 9.044 0 01-2.907-1.895 8.997 8.997 0 01-1.896-2.908A8.95 8.95 0 013 12a8.95 8.95 0 01.585-3.611 8.997 8.997 0 011.896-2.908 9.044 9.044 0 012.907-1.895A8.98 8.98 0 0112 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">Guía de Estados de Lecciones</h2>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Para usuarios de planificación, supervisión y seguimiento</p>
                        </div>
                    </div>
                    <button @click="helpOpen = false"
                            class="p-2 rounded-lg text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Content --}}
            <div class="p-6" x-data="{ tab: 'published' }">
                {{-- Intro text --}}
                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">
                        Cada lección en el LMS atraviesa un <strong class="text-gray-900 dark:text-white">ciclo de vida</strong> definido por su estado de publicación.
                        Como usuario de <strong class="text-gray-900 dark:text-white">planificación, supervisión y seguimiento</strong>, recibes <strong class="text-amber-500">notificaciones</strong>
                        cuando un docente programa una lección. Puedes auditar, aprobar y publicar desde este monitor.
                        Comprender estos estados te permite evaluar el avance docente, identificar oportunidades de mejora
                        y garantizar que el contenido digital esté disponible para los estudiantes cuando lo necesiten.
                    </p>
                </div>

                {{-- Tabs navigation --}}
                <div class="flex gap-1 bg-gray-100 dark:bg-slate-900/50 rounded-lg p-1 mb-6 overflow-x-auto" role="tablist">
                    <button @click="tab = 'published'"
                            :class="tab === 'published' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                            class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                        <span class="flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Publicado
                        </span>
                    </button>
                    <button @click="tab = 'scheduled'"
                            :class="tab === 'scheduled' ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                            class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                        <span class="flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Programado
                        </span>
                    </button>
                    <button @click="tab = 'draft'"
                            :class="tab === 'draft' ? 'bg-gray-200 dark:bg-slate-500/20 text-gray-800 dark:text-slate-300 border-gray-300 dark:border-slate-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                            class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                        <span class="flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Borrador
                        </span>
                    </button>
                    <button @click="tab = 'archived'"
                            :class="tab === 'archived' ? 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300 border-red-200 dark:border-red-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                            class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                        <span class="flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8M10 12h4"/></svg>
                            Archivado
                        </span>
                    </button>
                </div>

                {{-- ─── TAB: PUBLICADO ─────────────────────────────── --}}
                <div x-show="tab === 'published'" x-transition:enter="transition-opacity duration-200">
                    <div class="space-y-3">
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: true }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué significa "Publicado"?</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Estado: <span class="text-emerald-600 dark:text-emerald-400">PUBLISHED</span></p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4 space-y-3">
                                    <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Una lección en estado <strong class="text-emerald-600 dark:text-emerald-400">Publicado</strong> está completamente visible y accesible para los estudiantes en su aula virtual. Pueden ver su contenido, descargar recursos, acceder a enlaces externos, y participar en las actividades diseñadas por el docente.</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="px-2 py-1 rounded text-[10px] font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">✓ Visible para estudiantes</span>
                                        <span class="px-2 py-1 rounded text-[10px] font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">✓ Contenido completo</span>
                                        <span class="px-2 py-1 rounded text-[10px] font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">✓ Recursos descargables</span>
                                        <span class="px-2 py-1 rounded text-[10px] font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">✓ Participación activa</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Significado para la supervisión</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">¿Qué debes observar?</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4 space-y-3">
                                    <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">El estado <strong class="text-gray-900 dark:text-white">Publicado</strong> es el indicador principal de que el docente ha completado su flujo de trabajo y el contenido está siendo consumido por los estudiantes. Desde la supervisión debes:</p>
                                    <ul class="space-y-2 text-sm text-gray-700 dark:text-slate-300">
                                        <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Auditar la calidad</strong> del contenido publicado: ¿está completo? ¿tiene recursos? ¿las secciones están bien estructuradas?</span></li>
                                        <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Verificar cobertura curricular</strong>: ¿todas las asignaturas tienen lecciones publicadas? ¿hay algún grado o sección con rezago?</span></li>
                                        <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Monitorear la proporción</strong> entre borradores y publicaciones: un alto número de borradores puede indicar contenido estancado.</span></li>
                                        <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Recibir notificaciones</strong> de nuevas publicaciones para mantener el acompañamiento pedagógico.</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Transiciones posibles</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Hacia otros estados</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center gap-3 p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50"><span class="text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 px-2 py-1 rounded border border-red-200 dark:border-red-500/20">Archivar</span><svg class="w-4 h-4 text-gray-500 dark:text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg><span class="text-xs text-gray-500 dark:text-slate-400">La lección deja de ser visible para estudiantes. Útil al finalizar un lapso o cuando el contenido debe ser retirado.</span></div>
                                        <div class="flex items-center gap-3 p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50"><span class="text-xs font-bold text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-500/10 px-2 py-1 rounded border border-orange-200 dark:border-orange-500/20">Revertir a borrador</span><svg class="w-4 h-4 text-gray-500 dark:text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg><span class="text-xs text-gray-500 dark:text-slate-400">Permite al docente editar el contenido. La lección deja de estar visible hasta que se publique nuevamente.</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo en el aula</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Caso práctico</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4">
                                    <div class="bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50 p-3 space-y-1.5">
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-amber-400"></span><span>El profesor de <strong class="text-gray-800 dark:text-slate-200">Matemáticas</strong> completa la lección <strong class="text-gray-800 dark:text-slate-200">«Ecuaciones de primer grado»</strong> y la programa con fecha</span></div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-amber-400"></span><span>Planning recibe una <strong class="text-gray-800 dark:text-slate-200">notificación</strong> de que hay una lección pendiente de aprobación</span></div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-emerald-400"></span><span>Planning ingresa al monitor, filtra por <strong class="text-gray-800 dark:text-slate-200">Programado</strong>, audita el contenido y hace clic en <strong class="text-gray-800 dark:text-slate-200">Publicar</strong></span></div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-blue-400"></span><span>La lección se vuelve visible para <strong class="text-gray-800 dark:text-slate-200">5to año, sección A</strong> — 32 estudiantes</span></div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-violet-400"></span><span>El supervisor ingresa al monitor, ubica la lección y hace clic en <strong class="text-gray-800 dark:text-slate-200">Vista previa</strong> para auditar el contenido</span></div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-amber-400"></span><span>Tras la revisión, ajusta la <strong class="text-gray-800 dark:text-slate-200">configuración</strong> para permitir descargas y comentarios</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── TAB: PROGRAMADO ────────────────────────────── --}}
                <div x-show="tab === 'scheduled'" x-transition:enter="transition-opacity duration-200">
                    <div class="space-y-3">
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: true }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué significa "Programado"?</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Estado: <span class="text-amber-600 dark:text-amber-400">SCHEDULED</span></p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4 space-y-3">
                                    <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Una lección en estado <strong class="text-amber-600 dark:text-amber-400">Programado</strong> fue enviada por el docente para aprobación de Planificación. El contenido está listo pero permanece invisible para los estudiantes hasta que Planning lo <strong class="text-emerald-600 dark:text-emerald-400">publique</strong> o llegue la fecha programada. Opcionalmente puede tener una fecha de <strong class="text-red-600 dark:text-red-400">despublicación automática</strong>, permitiendo controlar su ciclo de visibilidad.</p>
                                    <div class="bg-amber-50/50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/10 rounded-lg p-3">
                                        <p class="text-xs text-amber-700/80 dark:text-amber-300/80 flex items-start gap-2"><svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>El docente <strong>programó</strong> la lección y se generó una <strong>notificación</strong> a los usuarios de Planificación. Puedes <strong>auditar</strong> el contenido con Vista previa y luego <strong>Publicar</strong> desde el monitor, o esperar a la publicación automática en la fecha programada.</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Significado para la supervisión</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">¿Qué debes observar?</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4 space-y-3">
                                    <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">El estado <strong class="text-gray-900 dark:text-white">Programado</strong> indica que el docente <strong class="text-gray-900 dark:text-white">solicitó la publicación</strong> de su lección y está esperando la revisión de Planificación. Desde la supervisión:</p>
                                    <ul class="space-y-2 text-sm text-gray-700 dark:text-slate-300">
                                        <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Recibes una notificación</strong> cuando un docente programa una lección. Revisa el monitor para ver las pendientes.</span></li>
                                        <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Audita el contenido</strong> con la vista previa <strong class="text-gray-800 dark:text-slate-200">antes</strong> de publicar. Verifica que esté completo y correcto.</span></li>
                                        <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Publica desde el monitor</strong> usando el botón en la columna de acciones. La lección se vuelve visible inmediatamente.</span></li>
                                        <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Verifica las fechas</strong>: ¿la publicación está alineada con el cronograma académico? ¿la fecha de despublicación (si aplica) es adecuada?</span></li>
                                        <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Monitorea la anticipación</strong>: contenidos programados con poca antelación pueden indicar planificación reactiva.</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Transiciones posibles</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Hacia otros estados</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center gap-3 p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50"><span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded border border-emerald-200 dark:border-emerald-500/20">Publicar (manual o automático)</span><svg class="w-4 h-4 text-gray-500 dark:text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg><span class="text-xs text-gray-500 dark:text-slate-400">Planning puede publicar manualmente desde el monitor, o esperar a la publicación automática en la fecha programada.</span></div>
                                        <div class="flex items-center gap-3 p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50"><span class="text-xs font-bold text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-500/10 px-2 py-1 rounded border border-orange-200 dark:border-orange-500/20">Revertir a borrador</span><svg class="w-4 h-4 text-gray-500 dark:text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg><span class="text-xs text-gray-500 dark:text-slate-400">Cancela la programación. El contenido vuelve a borrador y no se publicará en la fecha prevista.</span></div>
                                        <div class="flex items-center gap-3 p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50"><span class="text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 px-2 py-1 rounded border border-red-200 dark:border-red-500/20">Archivar</span><svg class="w-4 h-4 text-gray-500 dark:text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg><span class="text-xs text-gray-500 dark:text-slate-400">Descarta la programación y archiva el contenido. No se publicará en la fecha prevista.</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo en el aula</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Caso práctico</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4">
                                    <div class="bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50 p-3 space-y-1.5">
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-amber-400"></span><span>El profesor de <strong class="text-gray-800 dark:text-slate-200">Ciencias Naturales</strong> programa la lección <strong class="text-gray-800 dark:text-slate-200">«La Célula»</strong> para el viernes a las 8:00 AM</span></div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-amber-400"></span><span>Planning recibe una <strong class="text-gray-800 dark:text-slate-200">notificación</strong> y un registro aparece en el monitor con el badge 🆕 <strong class="text-sky-400">Nueva</strong></span></div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-emerald-400"></span><span>Planning ingresa al monitor, filtra por <strong class="text-gray-800 dark:text-slate-200">Programado</strong>, usa la <strong class="text-gray-800 dark:text-slate-200">Vista previa</strong> para auditar el contenido</span></div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-emerald-400"></span><span>Si el contenido está aprobado, hace clic en <strong class="text-gray-800 dark:text-slate-200">Publicar</strong> — la lección se vuelve visible inmediatamente</span></div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-amber-400"></span><span>Si no interviene, el sistema publica automáticamente el viernes a las 8:00 AM en la fecha programada</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── TAB: BORRADOR ──────────────────────────────── --}}
                <div x-show="tab === 'draft'" x-transition:enter="transition-opacity duration-200">
                    <div class="space-y-3">
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: true }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-gray-100 dark:bg-slate-500/15 border border-gray-200 dark:border-slate-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-gray-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué significa "Borrador"?</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Estado: <span class="text-gray-600 dark:text-slate-400">DRAFT</span></p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4 space-y-3">
                                    <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Una lección en estado <strong class="text-gray-700 dark:text-slate-300">Borrador</strong> está siendo creada o editada. Tiene un registro de publicación pero no es visible para los estudiantes. El docente puede estar agregando secciones, recursos, enlaces o ajustando el contenido. Es un estado <strong class="text-gray-900 dark:text-white">transitorio</strong>: la lección está en proceso de construcción.</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="px-2 py-1 rounded text-[10px] font-medium bg-gray-100 dark:bg-slate-500/10 text-gray-700 dark:text-slate-400 border border-gray-200 dark:border-slate-500/20">✗ No visible</span>
                                        <span class="px-2 py-1 rounded text-[10px] font-medium bg-gray-100 dark:bg-slate-500/10 text-gray-700 dark:text-slate-400 border border-gray-200 dark:border-slate-500/20">✗ Contenido parcial</span>
                                        <span class="px-2 py-1 rounded text-[10px] font-medium bg-gray-100 dark:bg-slate-500/10 text-gray-700 dark:text-slate-400 border border-gray-200 dark:border-slate-500/20">✗ Sin acceso estudiantes</span>
                                        <span class="px-2 py-1 rounded text-[10px] font-medium bg-gray-100 dark:bg-slate-500/10 text-gray-700 dark:text-slate-400 border border-gray-200 dark:border-slate-500/20">⟳ En edición</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Significado para la supervisión</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">¿Qué debes observar?</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4 space-y-3">
                                    <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">El estado <strong class="text-gray-900 dark:text-white">Borrador</strong> es el más común durante la fase de creación. Desde la supervisión representa una <strong class="text-gray-900 dark:text-white">oportunidad</strong> para acompañar el proceso antes de la publicación:</p>
                                    <ul class="space-y-2 text-sm text-gray-700 dark:text-slate-300">
                                        <li class="flex items-start gap-2"><span class="text-gray-500 dark:text-slate-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Identifica contenido estancado</strong>: lecciones en borrador por más de 1-2 semanas pueden indicar dificultades del docente o falta de tiempo.</span></li>
                                        <li class="flex items-start gap-2"><span class="text-gray-500 dark:text-slate-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Audita borradores</strong> para ofrecer retroalimentación <strong class="text-gray-800 dark:text-slate-200">temprana</strong>: es más fácil ajustar antes de publicar.</span></li>
                                        <li class="flex items-start gap-2"><span class="text-gray-500 dark:text-slate-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Mide el progreso general</strong>: un volumen alto de borradores frente a publicaciones indica que el contenido está en preparación pero aún no disponible.</span></li>
                                        <li class="flex items-start gap-2"><span class="text-gray-500 dark:text-slate-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Contacta al docente</strong> si observas borradores que deberían estar publicados según la planificación académica.</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Transiciones posibles</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Hacia otros estados</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center gap-3 p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50"><span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded border border-emerald-200 dark:border-emerald-500/20">Publicar ahora</span><svg class="w-4 h-4 text-gray-500 dark:text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg><span class="text-xs text-gray-500 dark:text-slate-400">La lección se vuelve visible inmediatamente para los estudiantes. Se crea un registro de publicación.</span></div>
                                        <div class="flex items-center gap-3 p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50"><span class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 px-2 py-1 rounded border border-amber-200 dark:border-amber-500/20">Programar</span><svg class="w-4 h-4 text-gray-500 dark:text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg><span class="text-xs text-gray-500 dark:text-slate-400">El docente programa con fecha y se genera una <strong class="text-amber-400">notificación</strong> a Planning para aprobación. Planificación puede publicar desde el monitor.</span></div>
                                    </div>
                                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-3 italic">Nota: Desde "Borrador" no se puede archivar directamente. Primero debe publicarse o programarse; luego sí puede archivarse.</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo en el aula</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Caso práctico</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4">
                                    <div class="bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50 p-3 space-y-1.5">
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-gray-400"></span><span>El profesor de <strong class="text-gray-800 dark:text-slate-200">Inglés</strong> comienza a crear la lección <strong class="text-gray-800 dark:text-slate-200">«Present Simple»</strong> — queda automáticamente en borrador</span></div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-gray-400"></span><span>Agrega 2 secciones, un video embebido y 3 ejercicios interactivos, pero aún no publica</span></div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-blue-400"></span><span>El supervisor revisa los borradores, encuentra la lección, la previsualiza y nota que le faltan recursos descargables</span></div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-amber-400"></span><span>Se comunica con el docente para sugerir la inclusión de una guía de estudio descargable <strong class="text-gray-800 dark:text-slate-200">antes de publicar</strong></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── TAB: ARCHIVADO ─────────────────────────────── --}}
                <div x-show="tab === 'archived'" x-transition:enter="transition-opacity duration-200">
                    <div class="space-y-3">
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: true }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-red-50 dark:bg-red-500/15 border border-red-200 dark:border-red-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8M10 12h4"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué significa "Archivado"?</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Estado: <span class="text-red-600 dark:text-red-400">ARCHIVED</span></p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4 space-y-3">
                                    <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Una lección en estado <strong class="text-red-600 dark:text-red-400">Archivado</strong> fue publicada previamente pero ha sido despublicada. Ya no es visible para los estudiantes. A diferencia de "Borrador" (que nunca fue visible), el contenido archivado tiene un <strong class="text-gray-900 dark:text-white">historial de publicación</strong> y la lección permanece en el sistema con todo su contenido intacto para consulta o republicación futura.</p>
                                    <div class="bg-red-50/50 dark:bg-red-500/5 border border-red-200 dark:border-red-500/10 rounded-lg p-3">
                                        <p class="text-xs text-red-700/80 dark:text-red-300/80 flex items-start gap-2"><svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span><strong>No se pierde nada.</strong> El contenido, secciones, recursos y configuraciones se conservan íntegramente. Puedes republicarlo en cualquier momento.</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Significado para la supervisión</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">¿Qué debes observar?</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4 space-y-3">
                                    <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">El estado <strong class="text-gray-900 dark:text-white">Archivado</strong> representa contenido que cumplió su ciclo o fue retirado por decisión pedagógica. Desde la supervisión:</p>
                                    <ul class="space-y-2 text-sm text-gray-700 dark:text-slate-300">
                                        <li class="flex items-start gap-2"><span class="text-red-600 dark:text-red-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Revisa el volumen de archivados</strong>: muchas lecciones archivadas repentinamente pueden indicar un problema (cambio de planificación, error masivo, etc.).</span></li>
                                        <li class="flex items-start gap-2"><span class="text-red-600 dark:text-red-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Consulta el historial</strong>: las lecciones archivadas mantienen su contenido. Úsalas como referencia para planificar el siguiente período.</span></li>
                                        <li class="flex items-start gap-2"><span class="text-red-600 dark:text-red-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Republica si es necesario</strong>: si un contenido archivado sigue siendo relevante (ej. lección de repaso transversal), puedes volver a publicarlo.</span></li>
                                        <li class="flex items-start gap-2"><span class="text-red-600 dark:text-red-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Identifica el contexto</strong>: ¿se archivó por fin de lapso? ¿por decisión del docente? ¿por error? Cada caso tiene implicaciones distintas.</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Transiciones posibles</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Hacia otros estados</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center gap-3 p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50"><span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded border border-emerald-200 dark:border-emerald-500/20">Publicar</span><svg class="w-4 h-4 text-gray-500 dark:text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg><span class="text-xs text-gray-500 dark:text-slate-400">Reactivación: el contenido vuelve a estar visible para los estudiantes. Útil para lecciones que siguen siendo relevantes.</span></div>
                                        <div class="flex items-center gap-3 p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50"><span class="text-xs font-bold text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-500/10 px-2 py-1 rounded border border-orange-200 dark:border-orange-500/20">Revertir a borrador</span><svg class="w-4 h-4 text-gray-500 dark:text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg><span class="text-xs text-gray-500 dark:text-slate-400">Permite editar el contenido archivado antes de republicar. Bueno si necesitas actualizarlo primero.</span></div>
                                        <div class="flex items-center gap-3 p-2.5 bg-red-50/50 dark:bg-red-500/5 rounded-lg border border-red-200 dark:border-red-500/20"><span class="text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 px-2 py-1 rounded border border-red-200 dark:border-red-500/20">Eliminar publicación</span><svg class="w-4 h-4 text-gray-500 dark:text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg><span class="text-xs text-gray-500 dark:text-slate-400">Elimina permanentemente el registro de publicación. La actividad base queda intacta pero pierde su estado de publicación. <strong class="text-red-600 dark:text-red-400">Esta acción no se puede deshacer.</strong></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo en el aula</h3>
                                        <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Caso práctico</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition-all duration-200">
                                <div class="px-4 pb-4">
                                    <div class="bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50 p-3 space-y-1.5">
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-emerald-400"></span><span>La lección <strong class="text-gray-800 dark:text-slate-200">«Geometría básica»</strong> del Lapso 1 estuvo publicada y visible durante todo el período</span></div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-red-400"></span><span>Al iniciar el Lapso 2, el docente archiva la lección para dejar espacio al nuevo contenido</span></div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-blue-400"></span><span>El supervisor puede consultarla en el monitor filtrando por "Archivado" para usarla como referencia o verificar su calidad</span></div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400"><span class="w-2 h-2 rounded-full bg-amber-400"></span><span>Si un estudiante necesita repasar ese contenido, el supervisor puede <strong class="text-gray-800 dark:text-slate-200">republicarlo temporalmente</strong> con un solo clic</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── FOOTER: Diagrama de ciclo de vida ──────────── --}}
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-slate-700/50">
                    <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-4 h-4 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <h3 class="text-sm font-bold text-gray-700 dark:text-slate-300">Ciclo de vida de una lección</h3>
                        </div>
                        <div class="flex items-center justify-center gap-2 py-3">
                            <div class="flex flex-col items-center">
                                <span class="w-10 h-10 rounded-full bg-gray-100 dark:bg-slate-500/20 border border-gray-200 dark:border-slate-500/40 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </span>
                                <span class="text-[10px] text-gray-500 dark:text-slate-500 mt-1 font-medium uppercase tracking-wider">Borrador</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <svg class="w-5 h-5 text-gray-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                <span class="text-[8px] text-gray-400 dark:text-slate-600">Publicar / Programar</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/40 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="text-[10px] text-emerald-600 dark:text-emerald-500 mt-1 font-medium uppercase tracking-wider">Publicado</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <svg class="w-5 h-5 text-gray-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                <span class="text-[8px] text-gray-400 dark:text-slate-600">Archivar</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-500/20 border border-red-200 dark:border-red-500/40 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8M10 12h4"/></svg>
                                </span>
                                <span class="text-[10px] text-red-600 dark:text-red-500 mt-1 font-medium uppercase tracking-wider">Archivado</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-center gap-1 mt-1 pb-2">
                            <div class="flex items-center gap-1 text-[10px] text-gray-400 dark:text-slate-500">
                                <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Programado</span>
                                <svg class="w-3 h-3 text-gray-400 dark:text-slate-600 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                <span class="text-emerald-600 dark:text-emerald-500">→ Publicado (automático)</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-400 dark:text-slate-600 text-center mt-1">
                            Las lecciones transitan de izquierda a derecha. Desde Publicado y Archivado se puede
                            <strong class="text-gray-600 dark:text-slate-500">revertir a Borrador</strong> para editar y reiniciar el ciclo.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
