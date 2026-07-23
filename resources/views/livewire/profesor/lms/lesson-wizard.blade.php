<div class="w-full mx-auto py-2 px-4 space-y-6">

    @if($mode === 'list')
        {{-- ═══════════ LISTADO DE ACTIVIDADES ═══════════ --}}
        <div wire:key="mode-list">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-lg font-bold text-gray-900 dark:text-white">Nueva Lección</h1>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Selecciona una actividad para crear su contenido LMS</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- View toggle: Grid / Lista --}}
                <div class="bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg p-0.5 flex">
                    <button wire:click="$set('viewMode', 'grid')"
                            @class([
                                'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold transition-all duration-200',
                                'bg-white dark:bg-slate-700 text-gray-900 dark:text-white shadow-sm' => $viewMode === 'grid',
                                'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300' => $viewMode !== 'grid',
                            ])>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        Grid
                    </button>
                    <button wire:click="$set('viewMode', 'table')"
                            @class([
                                'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold transition-all duration-200',
                                'bg-white dark:bg-slate-700 text-gray-900 dark:text-white shadow-sm' => $viewMode === 'table',
                                'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300' => $viewMode !== 'table',
                            ])>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Lista
                    </button>
                </div>
                <a href="{{ route('app.profesors.lms.editor', 0) }}"
                   class="text-xs text-slate-400 hover:text-emerald-400 transition-colors"
                   onclick="event.preventDefault()"
                   style="display:none;">
                </a>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="bg-gray-100/70 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-500 mb-1 block">Lapso</label>
                    <select wire:model.live="lapsoId"
                            class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 focus:border-emerald-500 focus:outline-none appearance-none">
                        <option value="">Todos</option>
                        @foreach($listLapso as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-500 mb-1 block">P.Estudio</label>
                    <select wire:model.live="pestudioId"
                            class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 focus:border-emerald-500 focus:outline-none appearance-none">
                        <option value="">Todos</option>
                        @foreach($listPestudio as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-500 mb-1 block">Grado</label>
                    <select wire:model.live="gradoId"
                            class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 focus:border-emerald-500 focus:outline-none appearance-none">
                        <option value="">Todos</option>
                        @foreach($listGrado as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-500 mb-1 block">Sección</label>
                    <select wire:model.live="seccionId"
                            class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 focus:border-emerald-500 focus:outline-none appearance-none">
                        <option value="">Todos</option>
                        @foreach($listSeccion as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-500 mb-1 block">Buscar</label>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Tema, descripción…"
                           class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none"/>
                </div>
            </div>
        </div>

        @if($viewMode === 'grid')
        {{-- Grid de actividades --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($activities as $item)
                @php
                    $pub = $item->lmsPublication;
                    $sections = $item->lmsSections ?? collect();
                    $resources = $item->lmsResources ?? collect();
                    $links = $item->lmsLinks ?? collect();
                    $totalContents = $sections->sum(fn($s) => $s->contents_count ?? 0);
                    $hasLmsContent = $sections->isNotEmpty() || $resources->isNotEmpty() || $links->isNotEmpty() || !is_null($pub);
                @endphp
                <div wire:key="activity-card-{{ $item->id }}"
                     class="relative bg-white dark:bg-slate-800/40 border border-gray-200 dark:border-slate-700/60 rounded-lg overflow-hidden mt-2
                            transition-all duration-200 group
                            hover:bg-gray-50 dark:hover:bg-slate-800/60 hover:border-gray-300 dark:hover:border-slate-600/80 hover:shadow-lg hover:shadow-black/5 dark:hover:shadow-black/10
                            {{ $hasLmsContent ? 'ring-1 ring-emerald-500/15' : '' }}">
                    @if($hasLmsContent)
                        <span class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-emerald-500 to-emerald-400"></span>
                    @endif

                    {{-- Badge de estado + fecha --}}
                    <div class="flex items-start justify-between gap-2 px-5 pt-5 pb-2">
                        <div class="flex items-center gap-1.5 min-w-0">
                            @if($pub)
                                <span @class([
                                    'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold tracking-wide',
                                    'bg-emerald-500/12 text-emerald-400 border border-emerald-500/20' => $pub->status === 'PUBLISHED',
                                    'bg-cyan-500/12 text-cyan-400 border border-cyan-500/20'        => $pub->status === 'SCHEDULED',
                                    'bg-slate-700/60 text-slate-500 border border-slate-600/50'      => $pub->status === 'ARCHIVED',
                                    'bg-amber-500/12 text-amber-400 border border-amber-500/20'      => true,
                                ])>
                                    @if($pub->status === 'PUBLISHED')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @elseif($pub->status === 'SCHEDULED')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @elseif($pub->status === 'ARCHIVED')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg>
                                    @else
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    @endif
                                    {{ match($pub->status) {
                                        'PUBLISHED' => 'Publicado',
                                        'SCHEDULED' => 'Programado',
                                        'ARCHIVED'  => 'Archivado',
                                        default     => 'Borrador',
                                    } }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium text-gray-500 dark:text-slate-500 bg-gray-100 dark:bg-slate-700/40 border border-gray-200 dark:border-slate-600/40">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    N.PUB
                                </span>
                            @endif
                        </div>
                        <span class="shrink-0 text-[11px] font-mono text-gray-400 dark:text-slate-600">
                            {{ \Carbon\Carbon::parse($item->finicial)->format('d/m') }}
                            <span class="text-gray-300 dark:text-slate-700">—</span>
                            {{ \Carbon\Carbon::parse($item->ffinal)->format('d/m') }}
                        </span>
                    </div>

                    {{-- Body --}}
                    <div class="px-5 pb-2 space-y-3">
                        {{-- Título --}}
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white leading-snug group-hover:text-emerald-600 dark:group-hover:text-emerald-300 transition-colors duration-200">
                            {{ $item->topic ?? 'Actividad sin título' }}
                        </h3>

                        {{-- Metadata: asignatura + grado --}}
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px]">
                            @if($item->pevaluacion?->pensum?->asignatura?->name)
                                <span class="inline-flex items-center gap-1.5 text-gray-500 dark:text-slate-400">
                                    <svg class="w-3.5 h-3.5 text-emerald-500/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    {{ $item->pevaluacion->pensum->asignatura->name }}
                                </span>
                            @endif
                            @if($item->pevaluacion?->pensum?->grado?->name)
                                <span class="inline-flex items-center gap-1.5 text-gray-500 dark:text-slate-400">
                                    <svg class="w-3.5 h-3.5 text-blue-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    {{ $item->pevaluacion->pensum->grado->name }}
                                    @if($item->pevaluacion?->seccion?->name)
                                        <span class="text-gray-400 dark:text-slate-600">·</span>
                                        Sec. {{ $item->pevaluacion->seccion->name }}
                                    @endif
                                </span>
                            @endif
                        </div>

                        {{-- Descripción --}}
                        @if($item->description)
                            <p class="text-xs text-gray-500 dark:text-slate-500 leading-relaxed line-clamp-2">{{ $item->description }}</p>
                        @endif

                        {{-- Indicadores LMS --}}
                        <div class="flex flex-wrap items-center gap-2 pt-0.5">
                            @if($hasLmsContent)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-emerald-500/8 text-emerald-400/80 border border-emerald-500/15">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    <span>{{ $sections->count() }} {{ Str::plural('sección', $sections->count()) }}</span>
                                    @if($totalContents > 0)
                                        <span class="text-gray-400 dark:text-slate-600">·</span>
                                        <span>{{ $totalContents }} {{ Str::plural('contenido', $totalContents) }}</span>
                                    @endif
                                </span>
                                @if($resources->count() > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-blue-500/8 text-blue-400/80 border border-blue-500/15">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        {{ $resources->count() }}
                                    </span>
                                @endif
                                @if($links->count() > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-cyan-500/8 text-cyan-400/80 border border-cyan-500/15">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        {{ $links->count() }}
                                    </span>
                                @endif
                                @if($pub?->published_at)
                                    <span class="ml-auto text-[10px] text-gray-400 dark:text-slate-600">
                                        {{ \Carbon\Carbon::parse($pub->published_at)->format('d/m/Y') }}
                                    </span>
                                @endif
                            @else
                                <span class="text-[10px] text-gray-400 dark:text-slate-600 italic">Sin contenido LMS</span>
                            @endif
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="mt-2 px-5 py-2 bg-gray-100 dark:bg-slate-900/40 border-t border-gray-200 dark:border-slate-700/40 flex items-center gap-2">
                        <button wire:click="showDetails({{ $item->id }})"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-medium
                                       bg-gray-100 dark:bg-slate-700/40 text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700/60 hover:text-gray-900 dark:hover:text-white border border-gray-200 dark:border-slate-600/40 hover:border-gray-300 dark:hover:border-slate-500/60 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Detalle
                        </button>
                        <a href="{{ route('app.profesors.lms.lesson.wizard') }}?activity_id={{ $item->id }}"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-medium transition-all duration-200
                                       {{ $hasLmsContent
                                           ? 'bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 hover:text-purple-300 border border-purple-500/20 hover:border-purple-500/40'
                                           : 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 hover:text-emerald-300 border border-emerald-500/20 hover:border-emerald-500/40' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            {{ $hasLmsContent ? 'Editar' : 'Asistente' }}
                        </a>

                        <div class="flex items-center gap-1 ml-1">
                            {{-- Exportar --}}
                            <button wire:click="showExport({{ $item->id }})"
                                    title="Exportar contenido a otra sección"
                                    class="p-2 rounded-lg text-slate-500 hover:text-emerald-400 hover:bg-emerald-500/10 border border-transparent hover:border-emerald-500/20 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            </button>

                            {{-- Importar --}}
                            <button wire:click="showImport({{ $item->id }})"
                                    title="{{ $hasLmsContent ? 'Esta actividad ya tiene contenido LMS' : 'Importar contenido de otra sección' }}"
                                    class="p-2 rounded-lg transition-all duration-200 border border-transparent
                                           {{ $hasLmsContent ? 'text-slate-600 cursor-not-allowed' : 'text-slate-500 hover:text-blue-400 hover:bg-blue-500/10 hover:border-blue-500/20' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            </button>

                            {{-- Vista estudiante --}}
                            @if($hasLmsContent)
                                <button wire:click="openListStudentPreview({{ $item->id }})"
                                        title="Vista del estudiante"
                                        class="p-2 rounded-lg text-slate-500 hover:text-amber-400 hover:bg-amber-500/10 border border-transparent hover:border-amber-500/20 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>

                                {{-- Eliminar lección --}}
                                <button wire:click="confirmDeleteLesson({{ $item->id }})"
                                        title="Eliminar lección"
                                        class="p-2 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/20 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <svg class="w-16 h-16 text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm font-medium text-slate-400">No hay actividades disponibles</p>
                    <p class="text-xs text-gray-500 dark:text-slate-600 mt-1">Ajusta los filtros o crea una actividad primero.</p>
                </div>
            @endforelse
        </div>
    @else
        {{-- ═══════════ ACTIVIDADES EN TABLA ═══════════ --}}
        <div wire:key="activity-table" class="overflow-x-auto rounded-lg border border-gray-200 dark:border-slate-700/60">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-100 dark:bg-slate-800/60 border-b border-gray-200 dark:border-slate-700/60">
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400">Actividad</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400">Asignatura / Sección</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400">Estado</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400">Contenido</th>
                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400">Fechas</th>
                        <th class="text-right px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $item)
                        @php
                            $pub = $item->lmsPublication;
                            $sections = $item->lmsSections ?? collect();
                            $resources = $item->lmsResources ?? collect();
                            $links = $item->lmsLinks ?? collect();
                            $totalContents = $sections->sum(fn($s) => $s->contents_count ?? 0);
                            $hasLmsContent = $sections->isNotEmpty() || $resources->isNotEmpty() || $links->isNotEmpty() || !is_null($pub);
                        @endphp
                        <tr wire:key="activity-row-{{ $item->id }}"
                            class="border-b border-gray-100 dark:border-slate-800/60 hover:bg-gray-50 dark:hover:bg-slate-800/30 transition-colors duration-150
                                   {{ $hasLmsContent ? 'bg-emerald-500/[0.02]' : '' }}">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg {{ $hasLmsContent ? 'bg-emerald-500/10' : 'bg-gray-100 dark:bg-slate-700/40' }} flex items-center justify-center shrink-0">
                                        @if($hasLmsContent)
                                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white leading-snug">{{ $item->topic ?? 'Actividad sin título' }}</p>
                                        @if($item->description)
                                            <p class="text-xs text-gray-500 dark:text-slate-500 mt-0.5 line-clamp-1">{{ $item->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-0.5">
                                    @if($item->pevaluacion?->pensum?->asignatura?->name)
                                        <p class="text-xs text-gray-700 dark:text-slate-300">{{ $item->pevaluacion->pensum->asignatura->name }}</p>
                                    @endif
                                    <p class="text-[11px] text-gray-500 dark:text-slate-500">
                                        {{ $item->pevaluacion?->pensum?->grado?->name ?? '' }}
                                        @if($item->pevaluacion?->seccion?->name)
                                            · Sec. {{ $item->pevaluacion->seccion->name }}
                                        @endif
                                    </p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($pub)
                                    <span @class([
                                        'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold tracking-wide',
                                        'bg-emerald-500/12 text-emerald-400 border border-emerald-500/20' => $pub->status === 'PUBLISHED',
                                        'bg-cyan-500/12 text-cyan-400 border border-cyan-500/20' => $pub->status === 'SCHEDULED',
                                        'bg-slate-700/60 text-slate-500 border border-slate-600/50' => $pub->status === 'ARCHIVED',
                                        'bg-amber-500/12 text-amber-400 border border-amber-500/20' => true,
                                    ])>
                                        {{ match($pub->status) {
                                            'PUBLISHED' => 'Publicado',
                                            'SCHEDULED' => 'Programado',
                                            'ARCHIVED'  => 'Archivado',
                                            default     => 'Borrador',
                                        } }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium text-gray-500 dark:text-slate-500 bg-gray-100 dark:bg-slate-700/40 border border-gray-200 dark:border-slate-600/40">N.PUB</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($hasLmsContent)
                                    <div class="flex items-center gap-2 text-[11px] text-gray-600 dark:text-slate-400">
                                        @if($sections->count() > 0)
                                            <span>{{ $sections->count() }} {{ Str::plural('sec.', $sections->count()) }}</span>
                                        @endif
                                        @if($totalContents > 0)
                                            <span>{{ $totalContents }} {{ Str::plural('cont.', $totalContents) }}</span>
                                        @endif
                                        @if($resources->count() > 0)
                                            <span>{{ $resources->count() }} {{ Str::plural('rec.', $resources->count()) }}</span>
                                        @endif
                                        @if($links->count() > 0)
                                            <span>{{ $links->count() }} {{ Str::plural('link', $links->count()) }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[11px] text-gray-400 dark:text-slate-600 italic">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-[11px] font-mono text-gray-500 dark:text-slate-500">
                                    {{ \Carbon\Carbon::parse($item->finicial)->format('d/m') }}
                                    <span class="text-gray-300 dark:text-slate-700">→</span>
                                    {{ \Carbon\Carbon::parse($item->ffinal)->format('d/m') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="showDetails({{ $item->id }})"
                                            title="Detalle"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-white hover:bg-slate-700/60 border border-transparent hover:border-slate-600/50 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <a href="{{ route('app.profesors.lms.lesson.wizard') }}?activity_id={{ $item->id }}"
                                       title="{{ $hasLmsContent ? 'Editar' : 'Asistente IA' }}"
                                       @class([
                                           'p-1.5 rounded-lg transition-all duration-200 border border-transparent',
                                           'text-purple-500 hover:text-purple-400 hover:bg-purple-500/10 hover:border-purple-500/20' => $hasLmsContent,
                                           'text-emerald-500 hover:text-emerald-400 hover:bg-emerald-500/10 hover:border-emerald-500/20' => !$hasLmsContent,
                                       ])>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </a>
                                    <button wire:click="showExport({{ $item->id }})"
                                            title="Exportar contenido"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-400 hover:bg-emerald-500/10 border border-transparent hover:border-emerald-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                    </button>
                                    <button wire:click="showImport({{ $item->id }})"
                                            title="{{ $hasLmsContent ? 'Ya tiene contenido' : 'Importar contenido' }}"
                                            @disabled($hasLmsContent)
                                            @class([
                                                'p-1.5 rounded-lg transition-all duration-200 border border-transparent',
                                                'text-slate-600 cursor-not-allowed' => $hasLmsContent,
                                                'text-slate-500 hover:text-blue-400 hover:bg-blue-500/10 hover:border-blue-500/20' => !$hasLmsContent,
                                            ])>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                    </button>
                                    @if($hasLmsContent)
                                        <button wire:click="openListStudentPreview({{ $item->id }})"
                                                title="Vista estudiante"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-amber-400 hover:bg-amber-500/10 border border-transparent hover:border-amber-500/20 transition-all duration-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <button wire:click="confirmDeleteLesson({{ $item->id }})"
                                                title="Eliminar lección"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/20 transition-all duration-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12">
                                <svg class="w-12 h-12 text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-sm font-medium text-slate-400">No hay actividades disponibles</p>
                                <p class="text-xs text-gray-500 dark:text-slate-600 mt-1">Ajusta los filtros o crea una actividad primero.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

        {{-- Paginación --}}
        @if($activities->hasPages())
            <div class="pt-4">
                {{ $activities->links('vendor.livewire.custom-tailwind') }}
            </div>
        @endif

        {{-- DETAIL MODAL (idéntico al de /app/profesors/activities/create/{pevaluacionId}) --}}
        @if($showDetailModal && $detailActivity)
            <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="detail-modal-{{ $detailActivity->id }}">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeDetails"></div>

                {{-- Modal panel --}}
                <div class="relative min-h-screen flex items-center justify-center p-4">
                    <div class="relative w-full max-w-6xl bg-gray-900 border border-white/10 rounded-lg shadow-2xl overflow-hidden">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 py-2 border-b border-white/5 bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Detalles de la Actividad</h3>
                                    <p class="text-xs text-gray-500">
                                        {{ $detailActivity->pevaluacion?->pensum?->asignatura?->name }} —
                                        {{ $detailActivity->pevaluacion?->pensum?->grado?->name }} {{ $detailActivity->pevaluacion?->seccion?->name }}
                                    </p>
                                </div>
                            </div>
                            <button wire:click="closeDetails"
                                class="p-1.5 text-gray-500 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="px-6 py-5 space-y-5 max-h-[70vh] overflow-y-auto">

                            {{-- Status + Fechas --}}
                            <div class="flex flex-wrap items-center gap-3 pb-4 border-b border-white/5">
                                @if($detailActivity->status_resume)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                        Act. Evaluación
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                        Sin actividad de evaluación
                                    </span>
                                @endif
                                <span class="text-xs text-gray-500 font-mono">
                                    {{ \Carbon\Carbon::parse($detailActivity->finicial)->format('d/m/Y') }}
                                </span>
                                <span class="text-gray-600">→</span>
                                <span class="text-xs text-gray-500 font-mono">
                                    {{ \Carbon\Carbon::parse($detailActivity->ffinal)->format('d/m/Y') }}
                                </span>
                            </div>

                            {{-- 2-column grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                                {{-- Col 1 --}}
                                <div class="space-y-4">
                                    {{-- Actividad Evaluativa --}}
                                    @if($detailActivity->description)
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Actividad Evaluativa</label>
                                        <p class="text-sm text-gray-200 leading-relaxed">{{ $detailActivity->description }}</p>
                                    </div>
                                    @endif

                                    {{-- Tema generador --}}
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Tema Generador y Énfasis</label>
                                        <p class="text-sm text-gray-200 leading-relaxed">{{ $detailActivity->topic }}</p>
                                    </div>

                                    {{-- Tejido temático --}}
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Tejido Temático</label>
                                        <p class="text-sm text-gray-200 leading-relaxed">{{ $detailActivity->thematic }}</p>
                                    </div>

                                    {{-- Referentes --}}
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Referentes Teórico-Prácticos</label>
                                        <p class="text-sm text-gray-200 leading-relaxed">{{ $detailActivity->references }}</p>
                                    </div>
                                </div>

                                {{-- Col 2 --}}
                                <div class="space-y-4" x-data="{ showFullTeaching: false }">
                                    {{-- Enseñanza / Actividad Globalizada --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500">Enseñanza / Actividad Globalizada</label>
                                            @if($detailActivity->hasTeachingStructure())
                                                <button @click="showFullTeaching = !showFullTeaching"
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10 transition-all duration-200"
                                                    x-text="showFullTeaching ? 'Ver estructura' : 'Ver completo'">
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Teaching structured view (default visible) --}}
                                        @if($detailActivity->hasTeachingStructure())
                                            @php $sections = $detailActivity->getTeachingSections(); @endphp
                                            <div x-show="!showFullTeaching" x-transition:enter.duration.200ms class="space-y-3">
                                                <div class="p-3 bg-cyan-500/5 border border-cyan-500/10 rounded-lg">
                                                    <div class="text-[10px] font-bold uppercase tracking-widest text-cyan-400 mb-1">INICIO</div>
                                                    <p class="text-sm text-gray-200">{{ $sections['INICIO'] ?? '' }}</p>
                                                </div>
                                                <div class="p-3 bg-emerald-500/5 border border-emerald-500/10 rounded-lg">
                                                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-400 mb-1">DESARROLLO</div>
                                                    <p class="text-sm text-gray-200">{{ $sections['DESARROLLO'] ?? '' }}</p>
                                                </div>
                                                <div class="p-3 bg-amber-500/5 border border-amber-500/10 rounded-lg">
                                                    <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400 mb-1">CIERRE</div>
                                                    <p class="text-sm text-gray-200">{{ $sections['CIERRE'] ?? '' }}</p>
                                                </div>
                                            </div>

                                            {{-- Teaching full text (toggle) --}}
                                            <p class="text-sm text-gray-200 leading-relaxed" x-show="showFullTeaching" x-cloak x-transition:enter.duration.200ms>{{ $detailActivity->teaching }}</p>
                                        @else
                                            {{-- No structure detected: show plain text --}}
                                            <p class="text-sm text-gray-200 leading-relaxed">{{ $detailActivity->teaching }}</p>
                                        @endif
                                    </div>

                                    {{-- Aprendizaje (full) --}}
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Aprendizaje</label>
                                        <p class="text-sm text-gray-200 leading-relaxed">{{ $detailActivity->learning }}</p>
                                    </div>

                                    {{-- Observaciones / ODS --}}
                                    @if($detailActivity->observations)
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">ODS / Sistematización</label>
                                        <p class="text-sm text-gray-200 leading-relaxed">{{ $detailActivity->observations }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Comentario J.Área --}}
                            @if($detailActivity->comments)
                            <div class="p-4 bg-cyan-500/5 border border-cyan-500/10 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                    </svg>
                                    <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider">Comentario [J.Área]</span>
                                </div>
                                <p class="text-sm text-gray-300 leading-relaxed">{{ $detailActivity->comments }}</p>
                            </div>
                            @endif

                            {{-- Achievements --}}
                            <div class="border-t border-white/5 pt-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                    <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Indicadores / Aprendizajes Esperados</span>
                                </div>
                                @php $detailAchievements = $detailActivity->achievements; @endphp
                                @if($detailAchievements->count() > 0)
                                    <div class="space-y-1.5">
                                        @foreach($detailAchievements as $ach)
                                            <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-gray-800/30 border border-white/5">
                                                <span class="text-sm text-gray-300">
                                                    <span class="text-gray-500 mr-1.5">—</span>
                                                    {{ $ach->name }}
                                                </span>
                                                @if($ach->status_quantitative_weighting)
                                                    <span class="text-xs font-bold font-mono text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md border border-emerald-500/20">
                                                        {{ $ach->weighting }} pts
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                        {{-- Total weight --}}
                                        @php $total = $detailAchievements->sum(fn($a) => (int)($a->weighting ?? 0)); @endphp
                                        @if($total > 0)
                                            <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-gray-800/50 border border-white/10">
                                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Ponderaciones</span>
                                                <span class="text-sm font-bold text-emerald-400 font-mono">{{ $total }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-sm text-gray-600 italic py-2">No hay indicadores registrados para esta actividad.</p>
                                @endif
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="px-6 py-2 border-t border-white/5 bg-gray-800/30 flex justify-end">
                            <button wire:click="closeDetails"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══════════ MODAL EXPORTAR (WIZARD 3 PASOS) ═══════════ --}}
        @if($showExportModal)
            <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="export-modal">
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeExportModal"></div>
                <div class="relative min-h-screen flex items-start justify-center p-4 pt-10">
                    <div class="relative w-full max-w-7xl bg-gray-900 border border-white/10 rounded-lg shadow-2xl overflow-hidden">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 py-2 border-b border-white/5 bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Exportar lección</h3>
                                    <p class="text-xs text-gray-500">Copiar contenido LMS a otra sección</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                {{-- Step indicators --}}
                                <div class="hidden sm:flex items-center gap-1.5 mr-2">
                                    @foreach(range(1, 3) as $s)
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold
                                            {{ $exportWizardStep === $s ? 'bg-emerald-500 text-white' : ($exportWizardStep > $s ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-700 text-slate-500') }}">
                                            @if($exportWizardStep > $s)
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                {{ $s }}
                                            @endif
                                        </span>
                                        @if($s < 3)
                                            <span class="w-4 h-px {{ $exportWizardStep > $s ? 'bg-emerald-500/40' : 'bg-slate-700' }}"></span>
                                        @endif
                                    @endforeach
                                </div>
                                <button wire:click="closeExportModal"
                                        class="p-1.5 text-gray-500 hover:text-white hover:bg-white/5 rounded-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- STEP 1: Destino --}}
                        @if($exportWizardStep === 1)
                            <div class="px-6 py-5 space-y-5">
                                <p class="text-xs text-slate-400 leading-relaxed">
                                    Selecciona la sección y la actividad destino donde se copiará el contenido
                                    LMS (secciones, recursos y enlaces) de esta lección.
                                </p>

                                {{-- Sección destino --}}
                                <div class="max-w-xs">
                                        <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1.5 block">
                                            Sección destino
                                        </label>
                                        <select wire:model.live="exportTargetSectionId"
                                                class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 focus:border-emerald-500 focus:outline-none appearance-none">
                                            <option value="">— Seleccionar —</option>
                                            @foreach($exportAvailableSections as $secId => $secName)
                                                <option value="{{ $secId }}">{{ $secName }}</option>
                                            @endforeach
                                        </select>
                                        @error('exportTargetSectionId')
                                            <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                {{-- Actividades destino (cards seleccionables) --}}
                                <div>
                                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-2 block">
                                        Actividad destino
                                        @if($exportAvailableActivities)
                                            <span class="text-slate-600 font-normal">({{ count($exportAvailableActivities) }})</span>
                                        @endif
                                    </label>

                                    @if($exportTargetSectionId && empty($exportAvailableActivities))
                                        <div class="text-center py-10 bg-slate-800/30 rounded-lg border border-dashed border-slate-700">
                                            <svg class="w-10 h-10 text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <p class="text-sm text-slate-500">No hay actividades disponibles en esta sección</p>
                                        </div>
                                    @elseif(!$exportTargetSectionId)
                                        <div class="text-center py-10 bg-slate-800/30 rounded-lg border border-dashed border-slate-700">
                                            <svg class="w-10 h-10 text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <p class="text-sm text-slate-500">Selecciona una sección para ver las actividades disponibles</p>
                                        </div>
                                    @else
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-72 overflow-y-auto pr-1">
                                            @foreach($exportAvailableActivities as $act)
                                                @php $selected = $exportTargetActivityId === $act['id']; @endphp
                                                <button wire:click="$set('exportTargetActivityId', {{ $act['id'] }})"
                                                        class="w-full text-left p-4 rounded-lg border transition-all duration-200
                                                               {{ $selected
                                                                   ? 'bg-emerald-500/10 border-emerald-500/40 ring-1 ring-emerald-500/30'
                                                                   : 'bg-slate-800/50 border-slate-700/60 hover:border-slate-600 hover:bg-slate-800' }}">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-sm font-medium text-slate-200 truncate {{ $selected ? 'text-emerald-200' : '' }}">
                                                                {{ $act['topic'] }}
                                                            </p>
                                                            @if($act['description'])
                                                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $act['description'] }}</p>
                                                            @endif
                                                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2">
                                                                <span class="text-[10px] text-slate-500 font-mono">
                                                                    {{ $act['start_date'] }} — {{ $act['end_date'] }}
                                                                </span>
                                                                <span class="text-[10px] text-slate-500">{{ $act['asignatura'] }}</span>
                                                                <span class="text-[10px] text-slate-500">{{ $act['grado'] }} {{ $act['seccion'] }}</span>
                                                            </div>
                                                        </div>
                                                        {{-- Check / LMS indicator --}}
                                                        <div class="shrink-0 flex flex-col items-center gap-1">
                                                            @if($selected)
                                                                <span class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center">
                                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                    </svg>
                                                                </span>
                                                            @endif
                                                            @if($act['has_lms'])
                                                                <span class="text-[10px] text-amber-400/70 flex items-center gap-0.5" title="Tiene contenido LMS">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                                    </svg>
                                                                    {{ $act['sections_count'] }} sec.
                                                                </span>
                                                            @else
                                                                <span class="text-[10px] text-slate-600">Sin LMS</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center justify-end gap-2 px-6 py-2 border-t border-white/5 bg-gray-800/30">
                                <button wire:click="closeExportModal"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-400 hover:text-white bg-slate-700/50 hover:bg-slate-700 border border-slate-600/50 transition-all">
                                    Cancelar
                                </button>
                                <button wire:click="loadExportPreview"
                                        wire:loading.attr="disabled"
                                        @disabled(!$exportTargetActivityId)
                                        class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold text-white transition-all disabled:opacity-40 disabled:cursor-not-allowed
                                               {{ $exportTargetActivityId ? 'bg-emerald-600 hover:bg-emerald-500 border border-emerald-500/50' : 'bg-slate-700 border border-slate-600/50' }}">
                                    Vista previa
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        @endif

                        {{-- STEP 2: Vista previa estudiante --}}
                        @if($exportWizardStep === 2 && $exportPreviewData)
                            <div class="px-8 py-6 space-y-6 max-h-[75vh] overflow-y-auto bg-white">
                                {{-- Encabezado de la lección --}}
                                <div class="border-b border-slate-200 pb-5">
                                    <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-1">
                                        {{ $exportPreviewData['subject'] }}
                                    </p>
                                    <h1 class="text-lg font-bold text-slate-900">{{ $exportPreviewData['title'] }}</h1>
                                    @if($exportPreviewData['description'])
                                        <p class="text-sm text-slate-600 mt-2 leading-relaxed">{{ $exportPreviewData['description'] }}</p>
                                    @endif
                                    @if($exportPreviewData['start_date'])
                                        <p class="text-xs text-slate-400 mt-2">
                                            {{ \Carbon\Carbon::parse($exportPreviewData['start_date'])->format('d/m/Y') }}
                                            — {{ \Carbon\Carbon::parse($exportPreviewData['end_date'])->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Secciones --}}
                                @forelse($exportPreviewData['sections'] as $section)
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1 h-6 bg-emerald-500 rounded-full"></span>
                                            <h2 class="text-lg font-bold text-slate-800">{{ $section['title'] }}</h2>
                                        </div>
                                        @foreach($section['contents'] as $content)
                                            @if(($content['title'] ?? null))
                                                <div class="flex items-start gap-2 mb-2">
                                                    <span class="w-0.5 h-5 bg-emerald-500 rounded-full mt-1 shrink-0"></span>
                                                    <h3 class="text-sm font-bold text-slate-800 leading-snug">{{ $content['title'] }}</h3>
                                                </div>
                                            @endif
                                            <x-lms-content-renderer :body="$content['body'] ?? ''" />
                                        @endforeach
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <svg class="w-16 h-16 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <p class="text-sm font-medium text-slate-500">No hay contenido disponible</p>
                                        <p class="text-xs text-slate-400 mt-1">Esta lección no tiene secciones visibles.</p>
                                    </div>
                                @endforelse

                                {{-- Recursos --}}
                                @if(count($exportPreviewData['resources'] ?? []) > 0)
                                    <div class="border-t border-slate-200 pt-5">
                                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            Recursos descargables
                                        </h3>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            @foreach($exportPreviewData['resources'] as $res)
                                                <div class="flex items-center gap-3 p-3 bg-slate-100 rounded-lg border border-slate-200">
                                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                        </svg>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-slate-700 truncate">{{ $res['display_name'] }}</p>
                                                        <p class="text-xs text-slate-400">{{ $res['media']['size_for_humans'] ?? '' }}</p>
                                                    </div>
                                                    @if($exportPreviewData['allow_downloads'])
                                                        <span class="ml-auto text-xs text-emerald-600 font-medium shrink-0">Descargar</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- HTML Embeds --}}
                                @if(count($exportPreviewData['html_embeds'] ?? []) > 0)
                                    <div class="border-t border-slate-200 pt-5">
                                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                            </svg>
                                            HTML Embeds
                                        </h3>
                                        <div class="space-y-3">
                                            @foreach($exportPreviewData['html_embeds'] as $embed)
                                                <div class="p-4 bg-fuchsia-50 border border-fuchsia-100 rounded-lg">
                                                    @if(!empty($embed['title']))
                                                        <h4 class="text-sm font-semibold text-fuchsia-800 mb-2">{{ $embed['title'] }}</h4>
                                                    @endif
                                                    <div class="text-sm text-slate-700 prose prose-sm max-w-none">
                                                        @if($embed['is_mermaid'] ?? false)
                                                            <div wire:ignore x-data="mermaidEmbed()"
                                                                 data-mermaid-code="{{ $embed['html_content'] }}"
                                                                 class="w-full">
                                                                <div x-ref="target" class="w-full"></div>
                                                            </div>
                                                        @else
                                                            {!! $embed['html_content'] !!}
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Enlaces --}}
                                @if(count($exportPreviewData['links'] ?? []) > 0)
                                    <div class="border-t border-slate-200 pt-5">
                                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            Enlaces de interés
                                        </h3>
                                        <div class="space-y-2">
                                            @foreach($exportPreviewData['links'] as $link)
                                                <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg border border-blue-100">
                                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-blue-800 truncate">{{ $link['title'] }}</p>
                                                        <p class="text-xs text-blue-500 truncate">{{ $link['url'] }}</p>
                                                    </div>
                                                    <span class="ml-auto text-[10px] font-medium text-blue-500 bg-blue-100 px-2 py-0.5 rounded shrink-0">{{ $link['link_type'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center justify-between px-8 py-2 bg-slate-100 border-t border-slate-200">
                                <p class="text-xs text-slate-400">
                                    <span class="font-medium">{{ count($exportPreviewData['sections']) }}</span> secciones ·
                                    <span class="font-medium">{{ collect($exportPreviewData['sections'])->sum(fn($s) => count($s['contents'])) }}</span> bloques ·
                                    <span class="font-medium">{{ count($exportPreviewData['resources'] ?? []) }}</span> recursos ·
                                    <span class="font-medium">{{ count($exportPreviewData['html_embeds'] ?? []) }}</span> embeds ·
                                    <span class="font-medium">{{ count($exportPreviewData['links'] ?? []) }}</span> enlaces
                                </p>
                                <div class="flex items-center gap-2">
                                    <button wire:click="goToExportStep(1)"
                                            class="px-4 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:text-slate-700 bg-slate-200 hover:bg-slate-300 transition-all">
                                        ← Anterior
                                    </button>
                                    <button wire:click="goToExportStep(3)"
                                            class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 border border-emerald-500/50 transition-all">
                                        Continuar →
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- STEP 3: Confirmar --}}
                        @if($exportWizardStep === 3)
                            <div class="px-6 py-5 space-y-5">
                                <div class="text-center pb-2">
                                    <div class="w-14 h-14 rounded-full bg-emerald-500/10 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-white">¿Exportar lección?</h3>
                                    <p class="text-xs text-slate-400 mt-1">El contenido LMS se copiará a la actividad seleccionada.</p>
                                </div>

                                {{-- Resumen origen --}}
                                <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50 space-y-2">
                                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Origen</h4>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-slate-300 font-medium">{{ $exportPreviewData['title'] ?? '—' }}</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-[11px] text-slate-400">
                                        <span>{{ count($exportPreviewData['sections'] ?? []) }} secciones</span>
                                        <span class="text-gray-400 dark:text-slate-600">·</span>
                                        <span>{{ count($exportPreviewData['resources'] ?? []) }} recursos</span>
                                        <span class="text-gray-400 dark:text-slate-600">·</span>
                                        <span>{{ count($exportPreviewData['html_embeds'] ?? []) }} embeds</span>
                                        <span class="text-gray-400 dark:text-slate-600">·</span>
                                        <span>{{ count($exportPreviewData['links'] ?? []) }} enlaces</span>
                                    </div>
                                </div>

                                {{-- Resumen destino --}}
                                @php $selectedAct = collect($exportAvailableActivities)->firstWhere('id', $exportTargetActivityId); @endphp
                                <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50 space-y-2">
                                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Destino</h4>
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="text-emerald-400 font-medium">
                                            {{ $exportAvailableSections[$exportTargetSectionId] ?? '—' }}
                                        </span>
                                        <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                        <span class="text-slate-300">
                                            {{ $selectedAct['topic'] ?? '—' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 text-[11px] text-slate-500">
                                        <span>{{ $selectedAct['asignatura'] ?? '—' }}</span>
                                        <span class="text-gray-400 dark:text-slate-600">·</span>
                                        <span>{{ $selectedAct['grado'] ?? '—' }} {{ $selectedAct['seccion'] ?? '' }}</span>
                                        <span class="text-gray-400 dark:text-slate-600">·</span>
                                        <span class="font-mono">{{ $selectedAct['start_date'] ?? '' }} — {{ $selectedAct['end_date'] ?? '' }}</span>
                                    </div>
                                </div>

                                <div class="bg-amber-500/5 border border-amber-500/10 rounded-lg p-3">
                                    <p class="text-[11px] text-amber-400/80 flex items-start gap-1.5">
                                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Esta acción copiará el contenido LMS a la actividad destino. El contenido existente en la actividad destino no se modificará, solo se añadirá el contenido copiado.</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between gap-2 px-6 py-2 border-t border-white/5 bg-gray-800/30">
                                <button wire:click="goToExportStep(2)"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-400 hover:text-white bg-slate-700/50 hover:bg-slate-700 border border-slate-600/50 transition-all">
                                    ← Anterior
                                </button>
                                <button wire:click="exportLesson"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-1.5 px-5 py-1.5 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 border border-emerald-500/50 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg wire:loading wire:target="exportLesson" class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    Exportar lección
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══════════ MODAL IMPORTAR (WIZARD 3 PASOS) ═══════════ --}}
        @if($showImportModal)
            <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="import-modal">
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeImportModal"></div>
                <div class="relative min-h-screen flex items-start justify-center p-4 pt-10">
                    <div class="relative w-full max-w-7xl bg-gray-900 border border-white/10 rounded-lg shadow-2xl overflow-hidden">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 py-2 border-b border-white/5 bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Importar lección</h3>
                                    <p class="text-xs text-gray-500">Copiar contenido LMS desde otra sección</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                {{-- Step indicators --}}
                                <div class="hidden sm:flex items-center gap-1.5 mr-2">
                                    @foreach(range(1, 3) as $s)
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold
                                            {{ $importWizardStep === $s ? 'bg-blue-500 text-white' : ($importWizardStep > $s ? 'bg-blue-500/20 text-blue-400' : 'bg-slate-700 text-slate-500') }}">
                                            @if($importWizardStep > $s)
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                {{ $s }}
                                            @endif
                                        </span>
                                        @if($s < 3)
                                            <span class="w-4 h-px {{ $importWizardStep > $s ? 'bg-blue-500/40' : 'bg-slate-700' }}"></span>
                                        @endif
                                    @endforeach
                                </div>
                                <button wire:click="closeImportModal"
                                        class="p-1.5 text-gray-500 hover:text-white hover:bg-white/5 rounded-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- STEP 1: Origen --}}
                        @if($importWizardStep === 1)
                            <div class="px-6 py-5 space-y-5">
                                <p class="text-xs text-slate-400 leading-relaxed">
                                    Selecciona la sección y la actividad origen desde donde se copiará el contenido
                                    LMS (secciones, recursos y enlaces) hacia esta lección.
                                </p>

                                {{-- Sección origen --}}
                                <div class="max-w-xs">
                                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1.5 block">
                                        Sección origen
                                    </label>
                                    <select wire:model.live="importSourceSectionId"
                                            class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 focus:border-emerald-500 focus:outline-none appearance-none">
                                        <option value="">— Seleccionar —</option>
                                        @foreach($importAvailableSections as $secId => $secName)
                                            <option value="{{ $secId }}">{{ $secName }}</option>
                                        @endforeach
                                    </select>
                                    @error('importSourceSectionId')
                                        <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Actividades origen (cards seleccionables) --}}
                                <div>
                                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-2 block">
                                        Actividad origen
                                        @if($importAvailableActivities)
                                            <span class="text-slate-600 font-normal">({{ count($importAvailableActivities) }})</span>
                                        @endif
                                    </label>

                                    @if($importSourceSectionId && empty($importAvailableActivities))
                                        <div class="text-center py-10 bg-slate-800/30 rounded-lg border border-dashed border-slate-700">
                                            <svg class="w-10 h-10 text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <p class="text-sm text-slate-500">No hay actividades disponibles en esta sección</p>
                                        </div>
                                    @elseif(!$importSourceSectionId)
                                        <div class="text-center py-10 bg-slate-800/30 rounded-lg border border-dashed border-slate-700">
                                            <svg class="w-10 h-10 text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <p class="text-sm text-slate-500">Selecciona una sección para ver las actividades disponibles</p>
                                        </div>
                                    @else
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-72 overflow-y-auto pr-1">
                                            @foreach($importAvailableActivities as $act)
                                                @php $selected = $importSourceActivityId === $act['id']; @endphp
                                                <button wire:click="$set('importSourceActivityId', {{ $act['id'] }})"
                                                        class="w-full text-left p-4 rounded-lg border transition-all duration-200
                                                               {{ $selected
                                                                   ? 'bg-blue-500/10 border-blue-500/40 ring-1 ring-blue-500/30'
                                                                   : 'bg-slate-800/50 border-slate-700/60 hover:border-slate-600 hover:bg-slate-800' }}">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-sm font-medium text-slate-200 truncate {{ $selected ? 'text-blue-200' : '' }}">
                                                                {{ $act['topic'] }}
                                                            </p>
                                                            @if($act['description'])
                                                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $act['description'] }}</p>
                                                            @endif
                                                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2">
                                                                <span class="text-[10px] text-slate-500 font-mono">
                                                                    {{ $act['start_date'] }} — {{ $act['end_date'] }}
                                                                </span>
                                                                <span class="text-[10px] text-slate-500">{{ $act['asignatura'] }}</span>
                                                                <span class="text-[10px] text-slate-500">{{ $act['grado'] }} {{ $act['seccion'] }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="shrink-0 flex flex-col items-center gap-1">
                                                            @if($selected)
                                                                <span class="w-5 h-5 rounded-full bg-blue-500 flex items-center justify-center">
                                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                    </svg>
                                                                </span>
                                                            @endif
                                                            @if($act['has_lms'])
                                                                <span class="text-[10px] text-amber-400/70 flex items-center gap-0.5" title="Tiene contenido LMS">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                                    </svg>
                                                                    {{ $act['sections_count'] }} sec.
                                                                </span>
                                                            @else
                                                                <span class="text-[10px] text-slate-600">Sin LMS</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center justify-end gap-2 px-6 py-2 border-t border-white/5 bg-gray-800/30">
                                <button wire:click="closeImportModal"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-400 hover:text-white bg-slate-700/50 hover:bg-slate-700 border border-slate-600/50 transition-all">
                                    Cancelar
                                </button>
                                <button wire:click="loadImportPreview"
                                        wire:loading.attr="disabled"
                                        @disabled(!$importSourceActivityId)
                                        class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold text-white transition-all disabled:opacity-40 disabled:cursor-not-allowed
                                               {{ $importSourceActivityId ? 'bg-blue-600 hover:bg-blue-500 border border-blue-500/50' : 'bg-slate-700 border border-slate-600/50' }}">
                                    Vista previa
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        @endif

                        {{-- STEP 2: Vista previa estudiante --}}
                        @if($importWizardStep === 2 && $importPreviewData)
                            <div class="px-8 py-6 space-y-6 max-h-[75vh] overflow-y-auto bg-white">
                                {{-- Encabezado de la lección --}}
                                <div class="border-b border-slate-200 pb-5">
                                    <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-1">
                                        {{ $importPreviewData['subject'] }}
                                    </p>
                                    <h1 class="text-lg font-bold text-slate-900">{{ $importPreviewData['title'] }}</h1>
                                    @if($importPreviewData['description'])
                                        <p class="text-sm text-slate-600 mt-2 leading-relaxed">{{ $importPreviewData['description'] }}</p>
                                    @endif
                                    @if($importPreviewData['start_date'])
                                        <p class="text-xs text-slate-400 mt-2">
                                            {{ \Carbon\Carbon::parse($importPreviewData['start_date'])->format('d/m/Y') }}
                                            — {{ \Carbon\Carbon::parse($importPreviewData['end_date'])->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Secciones --}}
                                @forelse($importPreviewData['sections'] as $section)
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1 h-6 bg-emerald-500 rounded-full"></span>
                                            <h2 class="text-lg font-bold text-slate-800">{{ $section['title'] }}</h2>
                                        </div>
                                        @foreach($section['contents'] as $content)
                                            @if(($content['title'] ?? null))
                                                <div class="flex items-start gap-2 mb-2">
                                                    <span class="w-0.5 h-5 bg-emerald-500 rounded-full mt-1 shrink-0"></span>
                                                    <h3 class="text-sm font-bold text-slate-800 leading-snug">{{ $content['title'] }}</h3>
                                                </div>
                                            @endif
                                            <x-lms-content-renderer :body="$content['body'] ?? ''" />
                                        @endforeach
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <svg class="w-16 h-16 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <p class="text-sm font-medium text-slate-500">No hay contenido disponible</p>
                                        <p class="text-xs text-slate-400 mt-1">Esta lección no tiene secciones visibles.</p>
                                    </div>
                                @endforelse

                                {{-- Recursos --}}
                                @if(count($importPreviewData['resources'] ?? []) > 0)
                                    <div class="border-t border-slate-200 pt-5">
                                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            Recursos descargables
                                        </h3>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            @foreach($importPreviewData['resources'] as $res)
                                                <div class="flex items-center gap-3 p-3 bg-slate-100 rounded-lg border border-slate-200">
                                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                        </svg>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-slate-700 truncate">{{ $res['display_name'] }}</p>
                                                        <p class="text-xs text-slate-400">{{ $res['media']['size_for_humans'] ?? '' }}</p>
                                                    </div>
                                                    @if($importPreviewData['allow_downloads'])
                                                        <span class="ml-auto text-xs text-emerald-600 font-medium shrink-0">Descargar</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- HTML Embeds --}}
                                @if(count($importPreviewData['html_embeds'] ?? []) > 0)
                                    <div class="border-t border-slate-200 pt-5">
                                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                            </svg>
                                            HTML Embeds
                                        </h3>
                                        <div class="space-y-3">
                                            @foreach($importPreviewData['html_embeds'] as $embed)
                                                <div class="p-4 bg-fuchsia-50 border border-fuchsia-100 rounded-lg">
                                                    @if(!empty($embed['title']))
                                                        <h4 class="text-sm font-semibold text-fuchsia-800 mb-2">{{ $embed['title'] }}</h4>
                                                    @endif
                                                    <div class="text-sm text-slate-700 prose prose-sm max-w-none">
                                                        @if($embed['is_mermaid'] ?? false)
                                                            <div wire:ignore x-data="mermaidEmbed()"
                                                                 data-mermaid-code="{{ $embed['html_content'] }}"
                                                                 class="w-full">
                                                                <div x-ref="target" class="w-full"></div>
                                                            </div>
                                                        @else
                                                            {!! $embed['html_content'] !!}
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Enlaces --}}
                                @if(count($importPreviewData['links'] ?? []) > 0)
                                    <div class="border-t border-slate-200 pt-5">
                                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            Enlaces de interés
                                        </h3>
                                        <div class="space-y-2">
                                            @foreach($importPreviewData['links'] as $link)
                                                <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg border border-blue-100">
                                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-blue-800 truncate">{{ $link['title'] }}</p>
                                                        <p class="text-xs text-blue-500 truncate">{{ $link['url'] }}</p>
                                                    </div>
                                                    <span class="ml-auto text-[10px] font-medium text-blue-500 bg-blue-100 px-2 py-0.5 rounded shrink-0">{{ $link['link_type'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center justify-between px-8 py-2 bg-slate-100 border-t border-slate-200">
                                <p class="text-xs text-slate-400">
                                    <span class="font-medium">{{ count($importPreviewData['sections']) }}</span> secciones ·
                                    <span class="font-medium">{{ collect($importPreviewData['sections'])->sum(fn($s) => count($s['contents'])) }}</span> bloques ·
                                    <span class="font-medium">{{ count($importPreviewData['resources'] ?? []) }}</span> recursos ·
                                    <span class="font-medium">{{ count($importPreviewData['html_embeds'] ?? []) }}</span> embeds ·
                                    <span class="font-medium">{{ count($importPreviewData['links'] ?? []) }}</span> enlaces
                                </p>
                                <div class="flex items-center gap-2">
                                    <button wire:click="goToImportStep(1)"
                                            class="px-4 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:text-slate-700 bg-slate-200 hover:bg-slate-300 transition-all">
                                        ← Anterior
                                    </button>
                                    <button wire:click="goToImportStep(3)"
                                            class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 border border-blue-500/50 transition-all">
                                        Continuar →
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- STEP 3: Confirmar --}}
                        @if($importWizardStep === 3)
                            @php $selectedAct = collect($importAvailableActivities)->firstWhere('id', $importSourceActivityId); @endphp
                            <div class="px-6 py-5 space-y-5">
                                <div class="text-center pb-2">
                                    <div class="w-14 h-14 rounded-full bg-blue-500/10 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-white">¿Importar lección?</h3>
                                    <p class="text-xs text-slate-400 mt-1">El contenido LMS se copiará desde la actividad seleccionada hacia esta lección.</p>
                                </div>

                                {{-- Resumen origen --}}
                                <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50 space-y-2">
                                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Origen</h4>
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="text-blue-400 font-medium">
                                            {{ $importAvailableSections[$importSourceSectionId] ?? '—' }}
                                        </span>
                                        <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                        <span class="text-slate-300">
                                            {{ $selectedAct['topic'] ?? '—' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 text-[11px] text-slate-500">
                                        <span>{{ $selectedAct['asignatura'] ?? '—' }}</span>
                                        <span class="text-gray-400 dark:text-slate-600">·</span>
                                        <span>{{ $selectedAct['grado'] ?? '—' }} {{ $selectedAct['seccion'] ?? '' }}</span>
                                        <span class="text-gray-400 dark:text-slate-600">·</span>
                                        <span class="font-mono">{{ $selectedAct['start_date'] ?? '' }} — {{ $selectedAct['end_date'] ?? '' }}</span>
                                    </div>
                                    @if($importPreviewData)
                                        <div class="flex items-center gap-3 text-[11px] text-slate-500 pt-1 border-t border-slate-700/50 mt-2">
                                            <span>{{ count($importPreviewData['sections'] ?? []) }} secciones</span>
                                            <span class="text-gray-400 dark:text-slate-600">·</span>
                                            <span>{{ count($importPreviewData['resources'] ?? []) }} recursos</span>
                                            <span class="text-gray-400 dark:text-slate-600">·</span>
                                            <span>{{ count($importPreviewData['html_embeds'] ?? []) }} embeds</span>
                                            <span class="text-gray-400 dark:text-slate-600">·</span>
                                            <span>{{ count($importPreviewData['links'] ?? []) }} enlaces</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Resumen destino --}}
                                <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50 space-y-2">
                                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Destino (lección actual)</h4>
                                    <p class="text-xs text-slate-300">El contenido importado se agregará al contenido LMS existente de esta actividad.</p>
                                </div>

                                <div class="bg-amber-500/5 border border-amber-500/10 rounded-lg p-3">
                                    <p class="text-[11px] text-amber-400/80 flex items-start gap-1.5">
                                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Esta acción copiará el contenido LMS de la actividad origen hacia esta lección. El contenido existente no se modificará, solo se añadirá el contenido importado.</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between gap-2 px-6 py-2 border-t border-white/5 bg-gray-800/30">
                                <button wire:click="goToImportStep(2)"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-400 hover:text-white bg-slate-700/50 hover:bg-slate-700 border border-slate-600/50 transition-all">
                                    ← Anterior
                                </button>
                                <button wire:click="importLesson"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-1.5 px-5 py-1.5 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 border border-blue-500/50 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg wire:loading wire:target="importLesson" class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                    Importar lección
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        </div>{{-- /mode-list --}}

    @elseif($mode === 'wizard')
        {{-- ═══════════ ASISTENTE PASO A PASO ═══════════ --}}
        <div wire:key="mode-wizard">

        {{-- Overlay bloqueante con competencias e indicadores de la actividad --}}
        @php
            $act = $selectedActivity;
            $pe = $act?->pevaluacion;
            $pensum = $pe?->pensum;
            $competencias = $pensum?->diagCompetencies()->with(['indicators'])->get();
            $indicadoresLogro = $act?->achievements ?? collect();
            $gradoName = $pensum?->grado?->name ?? '—';
            $asignaturaName = $pensum?->asignatura?->name ?? '—';
            $seccionName = $pe?->seccion?->name ?? '—';
        @endphp

        <div wire:loading.flex
             wire:target="generateStep1Content,generateStep2Sections,generateSectionContent,generateSlideText,generateSlideImage,generateSlideDiagram,generateSectionIllustration,generateReviewQuestions,generateSlideHtmlTags,generateSlideMath"
             class="fixed inset-0 z-[9999] items-center justify-center bg-white/95 dark:bg-slate-900/90 backdrop-blur-md"
             id="llm-loading-overlay"
             x-data="{
                 startTime: Date.now(),
                 elapsed: '00:00',
                 __timer: null,
                 __obs: null,
                 init() {
                     this.__timer = setInterval(() => {
                         if (this.$el.style.display === 'none') return;
                         var diff = Math.floor((Date.now() - this.startTime) / 1000);
                         var m = String(Math.floor(diff / 60)).padStart(2, '0');
                         var s = String(diff % 60).padStart(2, '0');
                         this.elapsed = m + ':' + s;
                     }, 1000);
                     this.__obs = new MutationObserver(function () {
                         if (this.$el.style.display !== 'none' && this.$el.style.display !== '') {
                             this.startTime = Date.now();
                             this.elapsed = '00:00';
                         }
                     }.bind(this));
                     this.__obs.observe(this.$el, { attributes: true, attributeFilter: ['style'] });
                 },
                 destroy() {
                     if (this.__timer) clearInterval(this.__timer);
                     if (this.__obs) this.__obs.disconnect();
                 }
             }">
            <div class="max-w-4xl py-8 mx-auto px-6 space-y-5">

                {{-- Header --}}
                <div class="text-center space-y-3">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-purple-500/20 to-emerald-500/20 border-2 border-purple-500/30 mx-auto relative">
                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                        </svg>
                        {{-- Spinner ring around the icon --}}
                        <svg class="absolute inset-0 w-full h-full animate-spin text-purple-400/40" viewBox="0 0 64 64" fill="none">
                            <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="3" stroke-dasharray="44 132" stroke-linecap="round" class="opacity-80"/>
                        </svg>
                    </div>
                    <p class="text-lg font-bold text-purple-200">Generando contenido con IA</p>
                    {{-- Contador de tiempo --}}
                    <p class="text-xs font-mono text-purple-300/70 flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-text="elapsed"></span>
                    </p>
                    @if($act)
                        <p class="text-sm text-gray-500 dark:text-slate-400">{{ $asignaturaName }} · {{ $gradoName }} · Sec. {{ $seccionName }}</p>
                    @endif
                </div>

                <div class="space-y-4 text-left max-h-[60vh] overflow-y-auto"
                     x-data="{ openCompetencias: false, openIndicadores: false }">

                    {{-- Competencias (acordeón, cerrado por defecto) --}}
                    <div class="w-full bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/50 rounded-lg overflow-hidden">
                        {{-- Header clickeable --}}
                        <button @click="openCompetencias = !openCompetencias"
                                class="w-full flex items-center gap-3 px-5 py-2.5 bg-gray-100 dark:bg-slate-800/40 border-b border-gray-200 dark:border-slate-700/30 hover:bg-gray-200 dark:hover:bg-slate-800/60 transition-colors text-left">
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-purple-500/20 to-purple-600/10 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-bold text-purple-200">Competencias</h3>
                                <p class="text-[11px] text-gray-400 dark:text-slate-500 truncate">Competencias fundamentales del pensum</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if($competencias?->isNotEmpty())
                                    <span class="text-xs font-medium text-purple-300 bg-purple-500/10 border border-purple-500/20 px-2.5 py-0.5 rounded-full">
                                        {{ $competencias->count() }}
                                    </span>
                                @endif
                                <svg class="w-4 h-4 text-gray-400 dark:text-slate-500 transition-transform duration-200"
                                     :class="openCompetencias ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>

                        {{-- Body colapsable --}}
                        <div x-show="openCompetencias"
                             x-cloak
                             x-transition:enter.duration.150ms>
                            @if($competencias?->isNotEmpty())
                                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($competencias as $comp)
                                        @php $indCount = $comp->indicators?->count() ?? 0; @endphp
                                        <div class="bg-white dark:bg-slate-800/70 border border-purple-500/20 rounded-lg overflow-hidden">
                                            <div class="h-1 bg-gradient-to-r from-purple-500/60 to-purple-400/30 shrink-0"></div>
                                            <div class="p-4 flex flex-col gap-2">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white leading-snug">{{ $comp->name }}</p>
                                                @if($indCount > 0)
                                                    <div class="space-y-1">
                                                        @foreach($comp->indicators as $ind)
                                                            <div class="flex items-start gap-1.5">
                                                                <span class="w-1 h-1 rounded-full bg-purple-400/40 mt-1.5 shrink-0"></span>
                                                                <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">{{ $ind->description }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-8 px-4 text-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-200 dark:bg-slate-700/30 flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-400 dark:text-slate-500 italic">No hay competencias asociadas</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actividad de referencia --}}
                    @if($act)
                        <div class="bg-gray-50 dark:bg-slate-800/30 border border-gray-200 dark:border-slate-700/30 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Actividad</span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-slate-400">{{ $act->topic }}</p>
                        </div>
                    @endif
                </div>

                {{-- Bouncing dots --}}
                <div class="flex items-center justify-center gap-1.5 pt-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-400 animate-bounce" style="animation-delay:0s"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-400 animate-bounce" style="animation-delay:0.15s"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-400 animate-bounce" style="animation-delay:0.3s"></span>
                </div>
            </div>
        </div>

        {{-- Overlay de resultado (sin animación, se muestra inmediatamente al completar) --}}
        @if($showGenerationResult)
            <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-white/95 dark:bg-slate-900/90 backdrop-blur-md">
                <div class="text-center space-y-5 max-w-2xl py-8 mx-auto px-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500/20 border-2 border-emerald-500/40 mx-auto">
                        <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    <p class="text-lg font-bold text-emerald-300">
                        @if($generationType === 'step1')
                            Título y descripción generados
                        @elseif($generationType === 'step2')
                            Secciones generadas
                        @else
                            Contenido generado
                        @endif
                    </p>

                    @if($generationType === 'step1')
                        <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/50 rounded-lg p-6 text-left space-y-3 min-h-[80px]">
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ $lessonTitle }}</h2>
                            <p class="text-sm text-gray-600 dark:text-slate-300 leading-relaxed">{{ $lessonDescription }}</p>
                        </div>
                    @elseif($generationType === 'section')
                        <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/50 rounded-lg p-6 text-left">
                            <p class="text-sm text-gray-600 dark:text-slate-300 leading-relaxed">
                                {{ \Illuminate\Support\Str::limit(
                                    ($wizardSections[array_key_last($wizardSections)]['contents'][0]['body'] ?? ''),
                                    300
                                ) }}
                            </p>
                        </div>
                    @elseif($generationType === 'step2')
                        <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/50 rounded-lg p-6 text-left max-h-80 overflow-y-auto space-y-3">
                            @foreach($wizardSections as $section)
                                <div class="flex items-start gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mt-1.5 shrink-0"></span>
                                    <div>
                                        <p class="text-sm font-bold text-emerald-300">{{ $section['title'] ?? '' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed mt-0.5">
                                            {{ \Illuminate\Support\Str::limit($section['contents'][0]['body'] ?? '', 150) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex items-center justify-center mt-2">
                        <button wire:click="dismissGenerationResult"
                                class="px-6 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-lg transition-all">
                            Continuar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Encabezado del wizard --}}
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-3 min-w-0 shrink">
                <button wire:click="backToList"
                        class="p-2 text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700/50 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white">{{ $lessonTitle ?: 'Nueva Lección' }}</h1>
                    <p class="text-xs text-gray-500 dark:text-slate-400">{{ $selectedActivity?->pevaluacion?->pensum?->asignatura?->name ?? '—' }} · {{ $selectedActivity?->pevaluacion?->pensum?->grado?->name ?? '—' }} Sec.{{ $selectedActivity?->pevaluacion?->seccion?->name ?? '—' }}</p>
                </div>
            </div>

        </div>

        {{-- Mensaje de guardado exitoso --}}
        @if($saved)
            <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-lg p-4 text-center">
                <svg class="w-12 h-12 text-emerald-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-base font-bold text-emerald-400 mb-1">¡Lección publicada exitosamente!</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-2">El contenido ya está disponible para los estudiantes.</p>
                <div class="flex items-center justify-center gap-3">
                    {{-- <button wire:click="openListStudentPreview({{ $selectedActivityId }})"
                            class="px-4 py-2 bg-fuchsia-600 hover:bg-fuchsia-500 text-white text-sm rounded-lg font-medium transition-all">
                        👁️ Vista estudiante
                    </button> --}}
                    <button wire:click="goToStep(1)"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm rounded-lg font-medium transition-all">
                        Editar contenido completo
                    </button>
                    <button wire:click="backToList"
                            class="px-4 py-2 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-300 text-sm rounded-lg font-medium transition-all">
                        Volver al listado
                    </button>
                </div>
            </div>
        @else
            <div x-on:keydown.window="
                if (event.ctrlKey || event.metaKey) {
                    if (event.key === 'ArrowRight') {
                        event.preventDefault();
                        $wire.call('goToStep', Math.min(5, {{ $currentStep }} + 1));
                    } else if (event.key === 'ArrowLeft') {
                        event.preventDefault();
                        $wire.call('goToStep', Math.max(1, {{ $currentStep }} - 1));
                    }
                }
                if ({{ $currentStep === 2 ? 'true' : 'false' }}) {
                    if (event.key === 'ArrowDown' && !event.ctrlKey && !event.metaKey) {
                        event.preventDefault();
                        $wire.call('nextSlide');
                    } else if (event.key === 'ArrowUp' && !event.ctrlKey && !event.metaKey) {
                        event.preventDefault();
                        $wire.call('prevSlide');
                    }
                }
            ">

            {{-- Navegación entre pasos --}}
            <div class="flex items-center justify-between my-4 border border-gray-200 dark:border-slate-700 rounded-lg px-4 py-2">
                <button wire:click="goToStep({{ $currentStep - 1 }})"
                        class="px-4 py-2 text-sm text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors {{ $currentStep <= 1 ? 'invisible' : '' }}">
                    ← Anterior
                </button>

                {{-- Barra de progreso --}}
                <div class="flex-1 flex justify-center overflow-x-auto min-w-0 px-2 mx-2">
                    <div class="flex items-center gap-0.5 sm:gap-1 min-w-max">
                        @php $stepLabels = ['', 'Información', 'Secciones', 'Recursos', 'Repaso', 'Publicar']; @endphp
                        @foreach(range(1, 5) as $step)
                            <button wire:click="goToStep({{ $step }})" type="button" class="flex items-center gap-1 group shrink-0">
                                <div class="flex flex-col items-center min-w-0">
                                    <span class="inline-flex items-center justify-center rounded-full text-[11px] font-bold transition-all duration-200
                                        {{ $currentStep === $step ? 'w-7 h-7 bg-emerald-500 text-white shadow-md shadow-emerald-500/40 ring-2 ring-emerald-400/40 scale-110' : ($currentStep > $step ? 'w-6 h-6 bg-emerald-500/20 text-emerald-400' : 'w-6 h-6 bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-slate-500') }}
                                        hover:ring-2 hover:ring-emerald-400/30 hover:ring-offset-1 hover:ring-offset-white dark:hover:ring-offset-slate-900">
                                        @if($currentStep > $step)
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                            {{ $step }}
                                        @endif
                                    </span>
                                    <span class="text-[9px] mt-0.5 whitespace-nowrap {{ $currentStep === $step ? 'text-emerald-400 font-semibold' : 'text-gray-400 dark:text-slate-500' }}
                                        {{ $step === $currentStep ? '' : 'hidden sm:inline' }}">
                                        {{ $stepLabels[$step] }}
                                    </span>
                                </div>
                                @if($step < 5)
                                    <span class="w-4 sm:w-8 h-px mb-4 {{ $currentStep > $step ? 'bg-emerald-500/40' : 'bg-gray-200 dark:bg-slate-700' }}"></span>
                                @endif
                            </button>
                        @endforeach
                        @php
                            $completedSteps = collect(range(1,5))->filter(fn($s) => $s < $currentStep)->count();
                        @endphp
                        <span class="ml-3 text-[10px] font-mono text-gray-400 dark:text-slate-500 shrink-0 whitespace-nowrap">{{ $completedSteps }}/5</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if($currentStep < 5)
                        <button wire:click="goToStep({{ $currentStep + 1 }})"
                                class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-lg transition-all">
                            Siguiente →
                        </button>
                    @endif
                </div>
            </div>

            {{-- Atajos de teclado (hint sutil) --}}
            <div class="flex items-center justify-end gap-3 px-1 -mt-2 mb-1">
                <span class="text-[10px] text-gray-400 dark:text-slate-600 font-mono flex items-center gap-1.5">
                    <kbd class="px-1 py-0.5 rounded text-[9px] bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-slate-400 border border-gray-300 dark:border-slate-600">Ctrl</kbd>
                    <span class="text-gray-300 dark:text-slate-700">+</span>
                    <kbd class="px-1 py-0.5 rounded text-[9px] bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-slate-400 border border-gray-300 dark:border-slate-600">&larr;</kbd>
                    <kbd class="px-1 py-0.5 rounded text-[9px] bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-slate-400 border border-gray-300 dark:border-slate-600">&rarr;</kbd>
                    <span class="text-gray-400 dark:text-slate-600 mx-0.5">pasos</span>
                </span>
                @if($currentStep === 2)
                <span class="text-[10px] text-gray-400 dark:text-slate-600 font-mono flex items-center gap-1.5">
                    <kbd class="px-1 py-0.5 rounded text-[9px] bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-slate-400 border border-gray-300 dark:border-slate-600">&uarr;</kbd>
                    <kbd class="px-1 py-0.5 rounded text-[9px] bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-slate-400 border border-gray-300 dark:border-slate-600">&darr;</kbd>
                    <span class="text-gray-400 dark:text-slate-600 mx-0.5">diapositivas</span>
                </span>
                @endif
            </div>

            {{-- Grid: formulario a la izquierda, preview a la derecha --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- ═══ COLUMNA IZQUIERDA: FORMULARIO (3/5) ═══ --}}
                <div class="lg:col-span-5 space-y-4 transition-all duration-300 min-w-0">

                    {{-- STEP 1: Información de la Lección --}}
                    @if($currentStep === 1)
                        <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg p-5 space-y-4">
                            <div class="flex items-center gap-2 pb-3 border-b border-gray-200 dark:border-slate-700">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">1</span>
                                <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Información de la Lección</h2>
                                <div class="ml-auto">
                                    <button wire:click="generateStep1Content"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-medium
                                                   text-purple-400 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/20
                                                   transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        Generar con IA
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Título de la lección</label>
                                <input type="text" wire:model="lessonTitle"
                                       class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none"
                                       placeholder="Título de la lección"/>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Descripción</label>
                                <textarea wire:model="lessonDescription" rows="3"
                                          class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none"
                                          placeholder="Breve descripción de la lección…"></textarea>
                            </div>

                            @if($selectedActivity?->learning)
                                <div class="bg-white dark:bg-slate-900/30 rounded-lg p-3 border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Aprendizaje esperado</span>
                                    <p class="text-sm text-gray-600 dark:text-slate-300 mt-1">{{ $selectedActivity->learning }}</p>
                                </div>
                            @endif

                            {{-- Referentes normativos con competencias e indicadores --}}
                            @if($wizardReferents && count($wizardReferents) > 0)
                                <div class="border-t border-gray-200 dark:border-slate-700 pt-4 mt-2"
                                     x-data="{ expandedReferent: null }">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Referentes Normativos</span>
                                        <span class="text-[10px] text-gray-400 dark:text-slate-600 font-mono">({{ count($wizardReferents) }})</span>
                                    </div>

                                    <div class="space-y-2">
                                        @foreach($wizardReferents as $rIdx => $referent)
                                            <div class="bg-gray-50 dark:bg-slate-900/40 border border-gray-200 dark:border-slate-700/60 rounded-lg overflow-hidden">
                                                {{-- Cabecera del referente (click para expandir) --}}
                                                <button @click="expandedReferent = expandedReferent === {{ $rIdx }} ? null : {{ $rIdx }}"
                                                        class="w-full flex items-center justify-between px-3 py-2 text-left hover:bg-gray-100 dark:hover:bg-slate-800/40 transition-colors group">
                                                    <div class="flex items-center gap-2 min-w-0">
                                                        <svg class="w-3.5 h-3.5 text-amber-400/70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span class="text-sm font-medium text-gray-700 dark:text-slate-200 truncate">{{ $referent['name'] }}</span>
                                                        @if($referent['code'])
                                                            <span class="text-[10px] text-gray-400 dark:text-slate-500 font-mono shrink-0">({{ $referent['code'] }})</span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-2 shrink-0">
                                                        <span class="text-[10px] text-gray-400 dark:text-slate-600">
                                                            {{ count($referent['competencies'] ?? []) }} comp.
                                                        </span>
                                                        <svg class="w-3.5 h-3.5 text-gray-400 dark:text-slate-500 transition-transform duration-200"
                                                             :class="expandedReferent === {{ $rIdx }} ? 'rotate-180' : ''"
                                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                    </div>
                                                </button>

                                                {{-- Competencias e indicadores (accordion body) --}}
                                                <div x-show="expandedReferent === {{ $rIdx }}"
                                                     x-cloak
                                                     x-transition:enter.duration.150ms>
                                                    <div class="px-3 pb-3 space-y-2 border-t border-gray-200 dark:border-slate-700/50 pt-2">
                                                        @forelse($referent['competencies'] ?? [] as $competency)
                                                            <div class="pl-3 border-l-2 border-emerald-500/30">
                                                                <div class="flex items-center gap-1.5 mb-1">
                                                                    <svg class="w-3 h-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                                                    </svg>
                                                                    <span class="text-xs font-medium text-emerald-300">{{ $competency['name'] }}</span>
                                                                </div>
                                                                @if(count($competency['indicators'] ?? []) > 0)
                                                                    <ul class="ml-5 space-y-0.5">
                                                                        @foreach($competency['indicators'] as $indicator)
                                                                            <li class="text-[11px] text-gray-500 dark:text-slate-400 flex items-start gap-1.5">
                                                                                <span class="text-gray-400 dark:text-slate-600 mt-0.5 select-none">•</span>
                                                                                <span>{{ $indicator['description'] }}</span>
                                                                                @if($indicator['code'])
                                                                                    <span class="text-[10px] text-gray-400 dark:text-slate-600 font-mono shrink-0">[{{ $indicator['code'] }}]</span>
                                                                                @endif
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @else
                                                                    <p class="ml-5 text-[11px] text-gray-400 dark:text-slate-600 italic">Sin indicadores asociados</p>
                                                                @endif
                                                            </div>
                                                        @empty
                                                            <p class="text-xs text-gray-400 dark:text-slate-600 italic">Sin competencias asociadas</p>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="border-t border-gray-200 dark:border-slate-700 pt-4 mt-2">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-xs font-bold text-gray-400 dark:text-slate-600 uppercase tracking-wider">Referentes Normativos</span>
                                    </div>
                                    <p class="text-xs text-gray-400 dark:text-slate-600 italic">No hay referentes normativos registrados para este plan de estudio.</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- STEP 2: Editor de Diapositivas (Slide Editor) --}}
                    @if($currentStep === 2)
                        {{-- ===== SLIDE EDITOR CON SIDEBAR PERSISTENTE ===== --}}
                        @php
                            $totalSlides = count($wizardSections);
                            $currentSlide = $wizardSections[$currentSlideIndex] ?? null;
                        @endphp

                        <div class="slide-editor-root bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg"
                             x-data="{
                                showSlideList: false,
                                sidebarCompact: $wire.entangle('sidebarCompact'),
                                dragIndex: null,
                                dragOverIndex: null,
                                editSlideTitle: false,
                                slideTitleBuffer: '{{ addslashes($wizardSections[$currentSlideIndex]['title'] ?? '') }}',

                                startDrag(idx) { this.dragIndex = idx; },
                                dragOver(idx) { if (this.dragIndex !== null && this.dragIndex !== idx) this.dragOverIndex = idx; },
                                endDrag() {
                                    if (this.dragIndex !== null && this.dragOverIndex !== null && this.dragIndex !== this.dragOverIndex) {
                                        $wire.call('moveSlide', this.dragIndex, this.dragOverIndex);
                                    }
                                    this.dragIndex = null; this.dragOverIndex = null;
                                },
                                cancelDrag() { this.dragIndex = null; this.dragOverIndex = null; }
                             }">

                            {{-- Slide Navigation Bar — compacta --}}
                            <div class="flex items-center justify-between gap-2 px-4 py-2 bg-gray-100 dark:bg-slate-800/40 border-b border-gray-200 dark:border-slate-700/30">
                                <div class="flex items-center gap-1">
                                    <button wire:click="prevSlide"
                                            class="flex items-center gap-1 px-2 py-1.5 text-[11px] font-medium text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-slate-700/50 rounded-lg transition-all {{ $totalSlides <= 1 || $currentSlideIndex <= 0 ? 'opacity-40 pointer-events-none' : '' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                        <span class="hidden sm:inline">Anterior</span>
                                    </button>
                                    <span class="text-gray-300 dark:text-slate-600 mx-0.5">|</span>
                                    <button wire:click="nextSlide"
                                            class="flex items-center gap-1 px-2 py-1.5 text-[11px] font-medium text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-slate-700/50 rounded-lg transition-all {{ $totalSlides <= 1 || $currentSlideIndex >= $totalSlides - 1 ? 'opacity-40 pointer-events-none' : '' }}">
                                        <span class="hidden sm:inline">Siguiente</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                    <span class="text-[11px] text-gray-400 dark:text-slate-500 font-mono ml-2">
                                        <span class="text-emerald-400 font-bold">{{ $currentSlideIndex + 1 }}</span>/{{ max(0, $totalSlides) }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button @click="showSlideList = !showSlideList"
                                            class="lg:hidden p-1.5 text-gray-400 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700/50 rounded-lg transition-all"
                                            title="Lista de diapositivas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Mobile: Navegación rápida de diapositivas (collapsed) --}}
                            <div class="flex lg:hidden items-center gap-1 px-3 py-1.5 border-b border-gray-200 dark:border-slate-700/30 overflow-x-auto bg-gray-50/50 dark:bg-slate-800/20" x-show="!showSlideList">
                                <button @click="showSlideList = !showSlideList"
                                        class="flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700/50 shrink-0 transition-all"
                                        title="Lista de diapositivas">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                </button>
                                <span class="w-px h-4 bg-gray-200 dark:bg-slate-700 shrink-0"></span>
                                @if($totalSlides > 0)
                                    @foreach(range(0, $totalSlides - 1) as $sIdx)
                                        <button wire:click="goToSlide({{ $sIdx }})"
                                                @class([
                                                    'flex items-center justify-center w-7 h-7 rounded-full text-[10px] font-bold shrink-0 transition-all',
                                                    'bg-emerald-500 text-white shadow-sm ring-2 ring-emerald-400/30' => $sIdx === $currentSlideIndex,
                                                    'bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-slate-400 hover:bg-gray-300 dark:hover:bg-slate-600' => $sIdx !== $currentSlideIndex,
                                                ])>
                                            {{ $sIdx + 1 }}
                                        </button>
                                    @endforeach
                                @endif
                            </div>

                            {{-- 🔲 Sidebar + Contenido en flex row --}}
                            <div class="flex flex-col lg:flex-row">
                                {{-- ═══ SIDEBAR: Lista persistente de secciones (desktop) ═══ --}}
                                <aside :style="`width: ${sidebarCompact ? '3rem' : '14rem'}`" class="hidden lg:flex flex-col shrink-0 border-r border-gray-200 dark:border-slate-700/30 bg-gray-50/70 dark:bg-slate-900/40 transition-all duration-200" style="width: 14rem">
                                    <div class="flex items-center justify-between px-3 py-2 border-b border-gray-200 dark:border-slate-700/30">
                                        <span x-show="!sidebarCompact" class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500">Secciones</span>
                                        <span x-show="!sidebarCompact" class="text-[10px] font-mono text-gray-400 dark:text-slate-600">{{ $totalSlides }}</span>
                                        <button wire:click="toggleSidebar"
                                                class="p-1 rounded-lg text-gray-400 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700/50 transition-all ml-auto"
                                                title="Toggle sidebar">
                                            <svg x-show="!sidebarCompact" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                                            <svg x-show="sidebarCompact" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                                        </button>
                                    </div>
                                    <div class="flex-1 overflow-y-auto p-2 space-y-0.5 min-h-[200px] max-h-[55vh]">
                                        @forelse($wizardSections as $sIdx2 => $sec)
                                            @php
                                                $secContent = $sec['contents'][0]['body'] ?? '';
                                                $hasSecContent = !empty($secContent);
                                                $isActive = $sIdx2 === $currentSlideIndex;
                                            @endphp
                                            <div wire:key="slide-list-{{ $sIdx2 }}"
                                                 @dragover.prevent="dragOver({{ $sIdx2 }})"
                                                 @dragleave.prevent="if (dragOverIndex === {{ $sIdx2 }}) dragOverIndex = null"
                                                 @drop.prevent="endDrag()">
                                                <button wire:click="goToSlide({{ $sIdx2 }})"
                                                        draggable="true"
                                                        @dragstart="startDrag({{ $sIdx2 }})"
                                                        @dragend="cancelDrag()"
                                                        @class([
                                                            'w-full flex items-center py-2 rounded-lg transition-all text-[11px] border cursor-grab active:cursor-grabbing group',
                                                            'bg-emerald-500/15 text-emerald-300 border-emerald-500/20' => $isActive,
                                                            'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/40 border-transparent' => !$isActive,
                                                        ])
                                                        :class="{
                                                            'opacity-40': dragIndex === {{ $sIdx2 }},
                                                            'border-t-2 border-t-emerald-400/40': dragOverIndex === {{ $sIdx2 }},
                                                            'px-2.5 gap-2 text-left': !sidebarCompact,
                                                            'px-1 justify-center': sidebarCompact
                                                        }">
                                                    <span class="flex items-center justify-center w-5 h-5 rounded shrink-0 text-[10px] font-mono {{ $hasSecContent ? 'bg-emerald-500/10 text-emerald-400' : 'bg-gray-200 dark:bg-slate-700/60 text-gray-500 dark:text-slate-500' }}">
                                                        {{ $sIdx2 + 1 }}
                                                    </span>
                                                    <span x-show="!sidebarCompact" class="truncate flex-1">{{ $sec['title'] ?: 'Sin título' }}</span>
                                                    @if($hasSecContent)
                                                        <span x-show="!sidebarCompact" class="w-1.5 h-1.5 rounded-full bg-emerald-400/60 shrink-0"></span>
                                                    @else
                                                        <span x-show="!sidebarCompact" class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-slate-600/40 shrink-0"></span>
                                                    @endif
                                                    <svg x-show="!sidebarCompact" class="w-3 h-3 text-gray-400 dark:text-slate-600 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm8 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm-8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm8 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm-8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm8 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/></svg>
                                                </button>
                                            </div>
                                        @empty
                                            <div class="text-center py-8">
                                                <p class="text-[11px] text-gray-400 dark:text-slate-600">Sin secciones</p>
                                                <p class="text-[10px] text-gray-500 dark:text-slate-500 mt-1">Agrega una abajo</p>
                                            </div>
                                        @endforelse
                                    </div>
                                    <div x-show="!sidebarCompact" class="p-2 border-t border-gray-200 dark:border-slate-700/30 space-y-1">
                                        <button wire:click="addWizardSection"
                                                class="w-full flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-[11px] font-medium text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Nueva diapositiva
                                        </button>
                                        @if($totalSlides > 0)
                                            <button wire:click="confirmResetWizardSections"
                                                    class="w-full flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-[11px] font-medium text-gray-400 dark:text-slate-500 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/20 transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Limpiar todo
                                            </button>
                                        @endif
                                    </div>
                                </aside>

                                {{-- ═══ CONTENIDO PRINCIPAL (slide actual) ═══ --}}
                                <div class="flex-1 min-w-0">
                                    @if($currentSlide)
                                        <div class="px-4 py-2" wire:key="slide-{{ $currentSlideIndex }}">
                                    {{-- Slide Title (editable inline) --}}
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 text-emerald-400 text-xs font-bold shrink-0">
                                            {{ $currentSlideIndex + 1 }}
                                        </span>
                                        <input wire:model="wizardSections.{{ $currentSlideIndex }}.title"
                                               class="flex-1 bg-transparent border-b border-transparent hover:border-gray-400 dark:hover:border-slate-600 focus:border-emerald-500 text-sm font-bold text-gray-900 dark:text-white px-0 py-0.5 focus:outline-none transition-colors"
                                               placeholder="Titulo de la diapositiva"/>
                                        <button wire:click="toggleWizardSectionVisibility({{ $currentSlideIndex }})"
                                                class="p-1.5 rounded-lg transition-all {{ ($currentSlide['is_visible'] ?? true) ? 'text-emerald-400/60 hover:text-emerald-400 hover:bg-emerald-500/10' : 'text-gray-400 dark:text-slate-600 hover:text-gray-600 dark:hover:text-slate-400 hover:bg-gray-200 dark:hover:bg-slate-700/50' }}"
                                                title="{{ ($currentSlide['is_visible'] ?? true) ? 'Visible' : 'Oculto' }}">
                                            @if($currentSlide['is_visible'] ?? true)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l22 22"/></svg>
                                            @endif
                                        </button>
                                    </div>

                                    {{-- Tab: Editor / Preview --}}
                                    <div x-data="{ editorTab: 'preview' }"
                                         @show-preview.window="editorTab = 'preview'">
                                        {{-- Tab buttons --}}
                                        <div class="flex gap-0.5 mb-2">
                                            <button @click="editorTab = 'edit'"
                                                    :class="editorTab === 'edit' ? 'text-emerald-300 bg-emerald-500/10 border-emerald-500/30' : 'text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300 border-transparent'"
                                                    class="flex-1 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-t-lg border border-b-0 transition-all text-center">
                                                <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Editor
                                            </button>
                                            <button @click="editorTab = 'preview'"
                                                    :class="editorTab === 'preview' ? 'text-fuchsia-300 bg-fuchsia-500/10 border-fuchsia-500/30' : 'text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300 border-transparent'"
                                                    class="flex-1 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-t-lg border border-b-0 transition-all text-center">
                                                <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Vista previa
                                            </button>
                                        </div>

                                        {{-- EDIT TAB: HTML Content Editor --}}
                                        <div x-show="editorTab === 'edit'" x-transition:enter.duration.150ms>
                                            @if(isset($wizardSections[$currentSlideIndex]['contents'][0]))
                                                <textarea wire:model="wizardSections.{{ $currentSlideIndex }}.contents.0.body"
                                                          rows="12"
                                                          class="w-full bg-white dark:bg-slate-950/80 border border-gray-300 dark:border-slate-700/50 rounded-lg px-4 py-2 text-xs text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-600 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all resize-y font-mono leading-relaxed"
                                                          placeholder="<!-- Escribe o pega el contenido HTML de esta diapositiva -->"
                                                          spellcheck="false"></textarea>
                                            @else
                                                <div class="text-center py-10 bg-gray-50 dark:bg-slate-900/50 border border-dashed border-gray-200 dark:border-slate-700/50 rounded-lg">
                                                    <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-gray-200 dark:bg-slate-700/30 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                                    </div>
                                                    <p class="text-xs text-gray-400 dark:text-slate-500 font-medium mb-1">Esta diapositiva esta vacia</p>
                                                    <p class="text-[10px] text-gray-400 dark:text-slate-600">Usa los botones de abajo para generar contenido o escribe HTML directamente</p>
                                                </div>
                                            @endif

                                            {{-- Block list with delete buttons --}}
                                            @php
                                                $allContents = $currentSlide['contents'] ?? [];
                                                $blockCount = count($allContents);
                                            @endphp
                                            @if($blockCount > 0)
                                                <div class="mt-3 space-y-1.5"
                                                     x-data="{ previewIndex: null }">
                                                    @foreach($allContents as $cIdx => $content)
                                                        <div class="flex items-start gap-2 px-3 py-2 rounded-lg transition-all text-xs
                                                                    {{ $cIdx === 0 ? 'bg-emerald-500/8 border border-emerald-500/10' : 'bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/30 hover:bg-gray-50 dark:hover:bg-slate-800/80' }}">
                                                            <div class="flex-1 min-w-0">
                                                                <div class="flex items-center gap-1.5 mb-0.5">
                                                                    @if($cIdx === 0)
                                                                        <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-400/60">Editor principal</span>
                                                                    @endif
                                                                    <span class="text-[9px] font-mono uppercase px-1.5 py-0.5 rounded
                                                                                {{ ($content['type'] ?? 'TEXT') === 'TEXT' ? 'bg-sky-500/10 text-sky-400' : '' }}
                                                                                {{ ($content['type'] ?? '') === 'HTML' ? 'bg-amber-500/10 text-amber-400' : '' }}
                                                                                {{ ($content['type'] ?? '') === 'MEDIA' ? 'bg-fuchsia-500/10 text-fuchsia-400' : '' }}">
                                                                        {{ $content['type'] ?? 'TEXT' }}
                                                                    </span>
                                                                    <span class="text-gray-400 dark:text-slate-500 truncate max-w-[200px]"
                                                                          x-data="{ editing: false }"
                                                                          x-cloak>
                                                                        {{-- Display mode --}}
                                                                        <span x-show="!editing"
                                                                              class="inline-flex items-center gap-1 cursor-pointer hover:text-gray-700 dark:hover:text-slate-300 transition-colors"
                                                                              @click="editing = true">
                                                                            <span class="truncate max-w-[160px]">{{ $content['title'] ? Str::limit($content['title'], 40) : 'sin título' }}</span>
                                                                            <svg class="w-3 h-3 shrink-0 text-slate-600 hover:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                                        </span>
                                                                        {{-- Edit mode --}}
                                                                        <span x-show="editing"
                                                                              x-cloak
                                                                              class="inline-flex items-center gap-1">
                                                                            <input x-ref="titleInput"
                                                                                   wire:model.blur="wizardSections.{{ $currentSlideIndex }}.contents.{{ $cIdx }}.title"
                                                                                   @keydown.escape="editing = false"
                                                                                   @keydown.enter="$wire.set('wizardSections.{{ $currentSlideIndex }}.contents.{{ $cIdx }}.title', $refs.titleInput.value).then(() => { editing = false })"
                                                                                   x-init="$nextTick(() => $refs.titleInput?.focus())"
                                                                                   class="w-full bg-white dark:bg-slate-900/60 border border-emerald-500/40 rounded px-1.5 py-0.5 text-xs text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-emerald-400"/>
                                                                            <button @click="$wire.set('wizardSections.{{ $currentSlideIndex }}.contents.{{ $cIdx }}.title', $refs.titleInput.value).then(() => { editing = false })"
                                                                                    class="p-1 rounded transition-all text-emerald-400 hover:text-emerald-300 hover:bg-emerald-500/15"
                                                                                    title="Guardar título">
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                                            </button>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                                <div class="text-[10px] text-gray-400 dark:text-slate-600 leading-relaxed line-clamp-1">
                                                                    @php
                                                                        $bodyText = strip_tags($content['body'] ?? '');
                                                                        $bodyText = preg_replace('/\s+/', ' ', $bodyText);
                                                                    @endphp
                                                                    {{ $bodyText ? Str::limit(trim($bodyText), 100) : '(vacio)' }}
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center gap-1 shrink-0">
                                                                <button @click.prevent="previewIndex = {{ $cIdx }}"
                                                                        class="p-1.5 rounded-lg transition-all
                                                                               text-gray-400 dark:text-slate-600 hover:text-gray-700 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-slate-600/50">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                                </button>
                                                                @if($blockCount > 1)
                                                                    <button wire:click="removeWizardContent({{ $currentSlideIndex }}, {{ $cIdx }})"
                                                                            wire:confirm="Eliminar este bloque de contenido?"
                                                                            class="p-1.5 rounded-lg transition-all
                                                                                   text-gray-400 dark:text-slate-600 hover:text-red-400 hover:bg-red-500/10">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        {{-- Preview modal for this block --}}
                                                        <div x-show="previewIndex === {{ $cIdx }}"
                                                             x-cloak
                                                             x-transition:enter.duration.200
                                                             class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
                                                             @keydown.escape.window="previewIndex = null">
                                                            {{-- Overlay --}}
                                                            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"
                                                                 @click="previewIndex = null"></div>
                                                            {{-- Modal card --}}
                                                            <div class="relative w-full max-w-3xl max-h-[85vh] bg-white rounded-lg shadow-2xl flex flex-col overflow-hidden">
                                                                {{-- Header --}}
                                                                <div class="flex items-center justify-between px-5 py-2 border-b border-gray-200 shrink-0">
                                                                    <div class="flex items-center gap-2">
                                                                        <span class="text-[10px] font-mono font-bold uppercase px-2 py-0.5 rounded
                                                                                    {{ ($content['type'] ?? 'TEXT') === 'TEXT' ? 'bg-sky-100 text-sky-700' : '' }}
                                                                                    {{ ($content['type'] ?? '') === 'HTML' ? 'bg-amber-100 text-amber-700' : '' }}
                                                                                    {{ ($content['type'] ?? '') === 'MEDIA' ? 'bg-fuchsia-100 text-fuchsia-700' : '' }}">
                                                                            {{ $content['type'] ?? 'TEXT' }}
                                                                        </span>
                                                                        <span class="text-sm font-semibold text-gray-800">
                                                                            {{ $content['title'] ?? 'Sin titulo' }}
                                                                        </span>
                                                                    </div>
                                                                    <button @click="previewIndex = null"
                                                                            class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                    </button>
                                                                </div>
                                                                {{-- Body --}}
                                                                <div class="flex-1 overflow-y-auto p-5">
                                                                    <div class="prose prose-sm prose-slate max-w-none !text-gray-800"
                                                                         style="color: #1e293b !important;">
                                                                        {!! $this->renderPreviewContent($content['body'] ?? '') !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        {{-- PREVIEW TAB: HTML Rendered --}}
                                        {{-- NOTA: slidePreviewContent() ya envuelve cada bloque en su        --}}
                                        {{-- estrategia de renderizado (mathContent() para texto,             --}}
                                        {{-- mermaidEmbed() para diagramas). Aquí solo se renderiza raw.      --}}
                                        <div x-show="editorTab === 'preview'" x-cloak x-transition:enter.duration.150ms>
                                            <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-6 min-h-[200px] overflow-x-auto shadow-sm">
                                                @php $previewContent = trim($this->slidePreviewContent()); @endphp
                                                @if(!empty($previewContent))
                                                    <div class="slide-preview-wrapper" style="color: #1e293b; line-height: 1.7;">
                                                        {!! $previewContent !!}
                                                    </div>
                                                @else
                                                    <div class="text-center py-12 text-gray-400 dark:text-slate-400">
                                                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <p class="text-sm font-medium">Sin contenido para previsualizar</p>
                                                        <p class="text-xs mt-1">Genera contenido o escribe HTML en la pestana Editor</p>
                                                    </div>
                                                @endif
                                            </div>
                                            @php
                                                $hasMermaid = str_contains($previewContent, 'x-data="mermaidEmbed()"');
                                            @endphp
                                            @if($hasMermaid)
                                                <div class="flex items-center gap-1.5 mt-2 px-3 py-2 bg-fuchsia-500/10 border border-fuchsia-500/20 rounded-lg">
                                                    <svg class="w-4 h-4 text-fuchsia-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span class="text-[11px] text-fuchsia-300">Esta diapositiva contiene un diagrama Mermaid</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                @php $blockCount = count($wizardSections[$currentSlideIndex]['contents'] ?? []); @endphp
                                <div class="px-4 py-2 border-t border-gray-200 dark:border-slate-700/30 bg-gray-50 dark:bg-slate-900/30">
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if($blockCount >= 2)
                                            <span class="text-[10px] text-gray-400 dark:text-slate-500 italic px-1 mr-1">Máx. 2 bloques</span>
                                        @endif
                                        <button wire:click="generateSlideText"
                                                @click="editorTab = 'preview'"
                                                wire:loading.attr="disabled"
                                                wire:target="generateSlideText"
                                                {{ $blockCount >= 2 ? 'disabled' : '' }}
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[11px] font-medium transition-all duration-200
                                                       text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 hover:border-emerald-500/40 active:scale-[0.97]
                                                       {{ $blockCount >= 2 ? 'opacity-40 cursor-not-allowed pointer-events-none' : '' }}">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Generar Texto
                                        </button>
                                        <button wire:click="generateSlideImage"
                                                wire:loading.attr="disabled"
                                                wire:target="generateSlideImage"
                                                {{ $blockCount >= 2 ? 'disabled' : '' }}
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[11px] font-medium transition-all duration-200
                                                       text-amber-400 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20 hover:border-amber-500/40 active:scale-[0.97]
                                                       {{ $blockCount >= 2 ? 'opacity-40 cursor-not-allowed pointer-events-none' : '' }}">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                            Generar Imagen
                                        </button>
                                        <button wire:click="generateSectionIllustration"
                                                @click="editorTab = 'preview'"
                                                disabled
                                                wire:loading.attr="disabled"
                                                wire:target="generateSectionIllustration"
                                                {{ $blockCount >= 2 ? 'disabled' : '' }}
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[11px] font-medium transition-all duration-200
                                                       text-sky-400 bg-sky-500/10 hover:bg-sky-500/20 border border-sky-500/20 hover:border-sky-500/40 active:scale-[0.97]
                                                       disabled:opacity-40 disabled:cursor-not-allowed disabled:active:scale-100
                                                       {{ $blockCount >= 2 ? 'opacity-40 cursor-not-allowed pointer-events-none' : '' }}">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/></svg>
                                            Generar Ilustración
                                        </button>
                                        <button wire:click="generateSlideDiagram"
                                                @click="editorTab = 'preview'"
                                                wire:loading.attr="disabled"
                                                wire:target="generateSlideDiagram"
                                                {{ $blockCount >= 2 ? 'disabled' : '' }}
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[11px] font-medium transition-all duration-200
                                                       text-fuchsia-400 bg-fuchsia-500/10 hover:bg-fuchsia-500/20 border border-fuchsia-500/20 hover:border-fuchsia-500/40 active:scale-[0.97]
                                                       {{ $blockCount >= 2 ? 'opacity-40 cursor-not-allowed pointer-events-none' : '' }}">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                            Generar Diagrama
                                        </button>

                                        <button wire:click="generateSlideHtmlTags"
                                                @click="editorTab = 'preview'"
                                                wire:loading.attr="disabled"
                                                wire:target="generateSlideHtmlTags"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[11px] font-medium transition-all duration-200
                                                       text-indigo-400 bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/20 hover:border-indigo-500/40 active:scale-[0.97]">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v4a1 1 0 001 1h4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14h6m-3-3v6"/></svg>
                                            Etiquetar HTML
                                        </button>

                                        {{-- Detectar y convertir expresiones matemáticas a LaTeX --}}
                                        <button title="Etiquetar con Notación matemática" wire:click="generateSlideMath"
                                                @click="editorTab = 'preview'"
                                                wire:loading.attr="disabled"
                                                wire:target="generateSlideMath"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[11px] font-medium transition-all duration-200
                                                       text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 hover:border-emerald-500/40 active:scale-[0.97]">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM8 12h8m-4-4v8"/></svg>
                                            Etiquetar Not. Mat. 
                                        </button>

                                        <span class="w-px h-5 bg-slate-700/50 mx-1 ml-auto"></span>

                                        <button wire:click="removeWizardSection({{ $currentSlideIndex }})"
                                                wire:confirm="Eliminar esta diapositiva?"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-medium transition-all
                                                       text-red-400/70 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/20">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Eliminar
                                        </button>

                                        <span class="w-px h-5 bg-red-900/50 mx-1"></span>

                                        <button wire:click="confirmResetWizardSections"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-medium transition-all
                                                       text-red-400/50 hover:text-red-300 hover:bg-red-500/15 border border-red-900/30 hover:border-red-500/30">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3 3m0 0l3-3m-3 3V8"/></svg>
                                            Limpiar todo
                                        </button>
                                    </div>
                                </div>

                                {{-- Debug: raw LLM response (solo visible si hay debugRawContent) --}}
                                @if($debugRawContent)
                                    <details class="border-t border-gray-200 dark:border-slate-700/30 bg-gray-50/50 dark:bg-slate-900/30">
                                        <summary class="px-4 py-2 text-[10px] font-mono text-gray-400 dark:text-slate-500 cursor-pointer hover:text-gray-600 dark:hover:text-slate-400 transition-colors select-none">
                                            🔍 Debug: respuesta cruda del LLM
                                        </summary>
                                        <pre class="px-4 py-3 text-[10px] font-mono text-gray-500 dark:text-slate-400 leading-relaxed whitespace-pre-wrap max-h-60 overflow-y-auto border-t border-gray-200 dark:border-slate-700/20">{{ $debugRawContent }}</pre>
                                    </details>
                                @endif

                                {{-- Generation Error --}}
                                @if($generatingSection === $currentSlideIndex && $generationError)
                                    <div class="px-4 py-2.5 bg-red-500/10 border-t border-red-500/20">
                                        <p class="text-xs text-red-400 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $generationError }}
                                        </p>
                                    </div>
                                @endif
                            @else
                                {{-- Empty State: No slides --}}
                                <div class="p-8 text-center">
                                    <div class="w-16 h-16 mx-auto mb-2 rounded-full bg-gray-200 dark:bg-slate-700/30 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-500 dark:text-slate-400 mb-2">No hay diapositivas</h3>
                                    <p class="text-xs text-gray-400 dark:text-slate-500 mb-2">Agrega una seccion o genera la estructura con IA para empezar.</p>
                                    <div class="flex items-center justify-center gap-3">
                                        <button wire:click="generateStep2Sections"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-medium
                                                       text-purple-400 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/20 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            Generar estructura con IA
                                        </button>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                            {{-- Add Section --}}
                            <div class="flex gap-2 px-4 py-2 border-t border-gray-200 dark:border-slate-700/30 bg-gray-50 dark:bg-slate-800/20">
                                <input wire:model="newSectionTitle" wire:keydown.enter="addWizardSection"
                                       placeholder="Nueva diapositiva (ej: Introduccion)..."
                                       class="flex-1 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none"/>
                                <button wire:click="addWizardSection"
                                        class="px-4 py-2 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-700 dark:text-white text-xs rounded-lg font-medium transition-all whitespace-nowrap">
                                    + Diapositiva
                                </button>
                            </div>

                            {{-- ═══ Mobile: Bottom Sheet con lista de secciones ═══ --}}
                            <div x-show="showSlideList" x-cloak x-transition:enter.duration.200ms
                                 class="fixed inset-0 z-50 lg:hidden" @click.away="showSlideList = false">
                                {{-- Backdrop --}}
                                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                                     @click="showSlideList = false"></div>
                                {{-- Sheet --}}
                                <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-slate-800 rounded-t-2xl shadow-xl border-t border-gray-200 dark:border-slate-700 max-h-[60vh] overflow-hidden"
                                     @click.stop>
                                    {{-- Handle visual --}}
                                    <div class="flex items-center justify-center pt-2 pb-1">
                                        <span class="w-8 h-1 bg-gray-300 dark:bg-slate-600 rounded-full"></span>
                                    </div>
                                    <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 dark:border-slate-700/50">
                                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Secciones</span>
                                        <span class="text-[10px] font-mono text-gray-400">{{ $totalSlides }} diapositivas</span>
                                    </div>
                                    <div class="overflow-y-auto p-2 space-y-0.5 max-h-[calc(60vh-80px)]">
                                        @forelse($wizardSections as $sIdx2 => $sec)
                                            @php
                                                $secContent = $sec['contents'][0]['body'] ?? '';
                                                $hasSecContent = !empty($secContent);
                                            @endphp
                                            <button wire:click="goToSlide({{ $sIdx2 }}); showSlideList = false"
                                                    @class([
                                                        'w-full flex items-center gap-2 px-3 py-2.5 rounded-lg text-left transition-all text-xs',
                                                        'bg-emerald-500/15 text-emerald-300 border border-emerald-500/20' => $sIdx2 === $currentSlideIndex,
                                                        'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/40 border border-transparent' => $sIdx2 !== $currentSlideIndex,
                                                    ])>
                                                <span class="flex items-center justify-center w-5 h-5 rounded shrink-0 text-[10px] font-mono {{ $hasSecContent ? 'bg-emerald-500/10 text-emerald-400' : 'bg-gray-200 dark:bg-slate-700/60 text-gray-500 dark:text-slate-500' }}">
                                                    {{ $sIdx2 + 1 }}
                                                </span>
                                                <span class="truncate flex-1">{{ $sec['title'] ?: 'Sin título' }}</span>
                                                @if($hasSecContent)
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400/60 shrink-0"></span>
                                                @else
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-slate-600/40 shrink-0"></span>
                                                @endif
                                            </button>
                                        @empty
                                            <p class="text-center text-[11px] text-gray-400 dark:text-slate-600 py-6">Sin secciones aún</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif

                                        {{-- STEP 3: Recursos y Enlaces — Tabbed interface --}}
                    @if($currentStep === 3)
                        <div wire:key="step3-recursos"
                             class="w-full bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg overflow-hidden"
                             x-data="{ activeTab: 'resources', showConfirmDeleteResources: false }">
                            {{-- Header --}}
                            <div class="flex items-center gap-3 px-5 py-2.5 bg-gray-100 dark:bg-slate-800/40 border-b border-gray-200 dark:border-slate-700/30">
                                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-sm font-bold text-gray-900 dark:text-white tracking-wide">Recursos y Enlaces</h2>
                                    <p class="text-[11px] text-gray-400 dark:text-slate-500 truncate">Material descargable, HTML embeds y enlaces de interés para la lección</p>
                                </div>
                                <button @click="showConfirmDeleteResources = true"
                                        class="text-[11px] text-red-400/60 hover:text-red-300 bg-red-500/5 hover:bg-red-500/10 px-2 py-1 rounded-lg transition-all inline-flex items-center gap-1.5 shrink-0 {{ count($wizardResources) === 0 && count($wizardLinks) === 0 && count($wizardHtmlEmbeds) === 0 ? 'hidden' : '' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Eliminar todos
                                </button>
                            </div>

                            {{-- Tabs de navegación (tab-fill: ancho completo) --}}
                            <div class="flex items-stretch gap-0.5 border-b border-gray-200 dark:border-slate-700/50 bg-gray-50 dark:bg-slate-900/30 px-5">
                                <button @click="activeTab = 'resources'"
                                        :class="activeTab === 'resources' ? 'text-emerald-300 bg-emerald-500/10 border-emerald-500/40' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 border-transparent hover:border-gray-300 dark:hover:border-slate-600/40'"
                                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 text-[11px] font-bold uppercase tracking-wider border-b-2 transition-all duration-200">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="hidden sm:inline">Archivos</span> descargables
                                    <span class="text-[10px] font-mono ml-1 px-1.5 py-0.5 rounded-full"
                                          :class="activeTab === 'resources' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-700/50 text-slate-500'"
                                          x-text="{{ count($wizardResources) }}"></span>
                                </button>
                                <button @click="activeTab = 'embeds'"
                                        :class="activeTab === 'embeds' ? 'text-fuchsia-300 bg-fuchsia-500/10 border-fuchsia-500/40' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 border-transparent hover:border-gray-300 dark:hover:border-slate-600/40'"
                                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 text-[11px] font-bold uppercase tracking-wider border-b-2 transition-all duration-200">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                    </svg>
                                    HTML Embeds
                                    <span class="text-[10px] font-mono ml-1 px-1.5 py-0.5 rounded-full"
                                          :class="activeTab === 'embeds' ? 'bg-fuchsia-500/20 text-fuchsia-400' : 'bg-gray-200 dark:bg-slate-700/50 text-gray-500 dark:text-slate-500'"
                                          x-text="{{ count($wizardHtmlEmbeds) }}"></span>
                                </button>
                                <button @click="activeTab = 'links'"
                                        :class="activeTab === 'links' ? 'text-sky-300 bg-sky-500/10 border-sky-500/40' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 border-transparent hover:border-gray-300 dark:hover:border-slate-600/40'"
                                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 text-[11px] font-bold uppercase tracking-wider border-b-2 transition-all duration-200">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                    <span class="hidden sm:inline">Enlaces</span> externos
                                    <span class="text-[10px] font-mono ml-1 px-1.5 py-0.5 rounded-full"
                                          :class="activeTab === 'links' ? 'bg-sky-500/20 text-sky-400' : 'bg-gray-200 dark:bg-slate-700/50 text-gray-500 dark:text-slate-500'"
                                          x-text="{{ count($wizardLinks) }}"></span>
                                </button>
                            </div>

                            {{-- Body: Tab panels --}}
                            <div class="p-5">

                                {{-- ═══ Tab: Archivos descargables ═══ --}}
                                <div x-show="activeTab === 'resources'" x-cloak x-transition:enter.duration.200ms>
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <h3 class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Archivos descargables</h3>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[11px] text-gray-400 dark:text-slate-500 bg-gray-200 dark:bg-slate-700/40 px-2 py-0.5 rounded-full">{{ count($wizardResources) }} archivos</span>
                                        </div>
                                    </div>

                                    @if(count($wizardResources) > 0)
                                        <div class="space-y-1.5 mb-2">
                                            @foreach($wizardResources as $rIdx => $res)
                                                @php
                                                    $ext = strtolower(pathinfo($res['media']['original_name'] ?? '', PATHINFO_EXTENSION));
                                                    $icon = match($ext) {
                                                        'pdf' => 'pdf',
                                                        'jpg','jpeg','png','gif','webp' => 'image',
                                                        'mp4','webm','mov' => 'video',
                                                        'mp3','wav','ogg' => 'audio',
                                                        'doc','docx' => 'word',
                                                        'xls','xlsx' => 'excel',
                                                        'ppt','pptx' => 'powerpoint',
                                                        default => 'file'
                                                    };

                                                    $iconStyles = [
                                                        'pdf' => ['bg' => 'bg-red-500/15', 'text' => 'text-red-400'],
                                                        'image' => ['bg' => 'bg-blue-500/15', 'text' => 'text-blue-400'],
                                                        'video' => ['bg' => 'bg-purple-500/15', 'text' => 'text-purple-400'],
                                                        'audio' => ['bg' => 'bg-amber-500/15', 'text' => 'text-amber-400'],
                                                        'word' => ['bg' => 'bg-blue-600/15', 'text' => 'text-blue-400'],
                                                        'excel' => ['bg' => 'bg-emerald-500/15', 'text' => 'text-emerald-400'],
                                                        'powerpoint' => ['bg' => 'bg-orange-500/15', 'text' => 'text-orange-400'],
                                                        'file' => ['bg' => 'bg-slate-600/30', 'text' => 'text-slate-400'],
                                                    ];
                                                    $is = $iconStyles[$icon] ?? $iconStyles['file'];
                                                @endphp
                                                <div wire:key="resource-{{ $res['id'] }}" class="flex items-center gap-3 px-3 py-2.5 bg-white dark:bg-slate-800/40 border border-gray-200 dark:border-slate-700/40 rounded-lg hover:border-gray-300 dark:hover:border-slate-600/60 hover:bg-gray-50 dark:hover:bg-slate-800/60 transition-all group">
                                                    @if($icon === 'image')
                                                        <button wire:click="previewResourceImage({{ $rIdx }})"
                                                                class="w-9 h-9 rounded-lg overflow-hidden shrink-0 border border-slate-600/30 hover:ring-2 hover:ring-emerald-500/50 transition-all cursor-pointer"
                                                                title="Ver previsualización">
                                                            <img src="{{ $res['media']['public_url'] }}" alt=""
                                                                 class="w-full h-full object-cover">
                                                        </button>
                                                    @else
                                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $is['bg'] }}">
                                                            <svg class="w-4 h-4 {{ $is['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                @switch($icon)
                                                                    @case('pdf')
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11v4m0 0l-2-2m2 2l2-2" opacity="0.5"/>
                                                                        @break
                                                                    @case('image')
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                        @break
                                                                    @case('video')
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                                        @break
                                                                    @case('audio')
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                                                        @break
                                                                    @default
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                                @endswitch
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-xs font-medium text-gray-700 dark:text-slate-200 truncate">{{ $res['display_name'] }}</p>
                                                        <p class="text-[10px] text-gray-400 dark:text-slate-500 truncate">{{ $res['media']['original_name'] ?? '' }} <span class="text-gray-300 dark:text-slate-600">·</span> {{ $res['media']['size_for_humans'] ?? '' }}
                                                            @if($res['section_id'])
                                                                <span class="text-gray-300 dark:text-slate-600">·</span>
                                                                <span class="text-emerald-400/70">{{ collect($wizardSections)->firstWhere('id', $res['section_id'])['title'] ?? 'Sección' }}</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <button wire:click="editWizardResource({{ $rIdx }})"
                                                                class="text-gray-400 dark:text-slate-400/60 hover:text-sky-300 transition-all text-xs p-1 rounded hover:bg-sky-500/10"
                                                                title="Editar recurso">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </button>
                                                        <button wire:click="removeWizardResource({{ $rIdx }})"
                                                                class="text-red-400/60 hover:text-red-300 transition-all text-xs p-1 rounded hover:bg-red-500/10"
                                                                title="Eliminar recurso">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center gap-3 px-4 py-5 bg-gray-50 dark:bg-slate-800/20 border border-dashed border-gray-200 dark:border-slate-700/30 rounded-lg mb-2">
                                            <svg class="w-5 h-5 text-gray-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-xs text-gray-400 dark:text-slate-500">Sin archivos aún. Agrega recursos descargables para la lección.</p>
                                        </div>
                                    @endif

                                    {{-- Add resource form --}}
                                    <div class="space-y-2">
                                        {{-- Edit mode indicator --}}
                                        @if($editingResourceIndex !== null && isset($wizardResources[$editingResourceIndex]))
                                            <div class="flex items-center gap-2 px-3 py-1.5 bg-sky-500/10 border border-sky-500/20 rounded-lg">
                                                <span class="w-1.5 h-1.5 rounded-full bg-sky-400 shrink-0"></span>
                                                <span class="text-[11px] font-medium text-sky-300">
                                                    Editando: <span class="text-sky-200">{{ $wizardResources[$editingResourceIndex]['display_name'] }}</span>
                                                </span>
                                                <button wire:click="cancelEditResource"
                                                        class="ml-auto text-[10px] text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700/50 px-2 py-0.5 rounded transition-colors">
                                                    Cancelar
                                                </button>
                                            </div>
                                        @endif

                                        {{-- Row 1: Name + Section --}}
                                        <div class="flex gap-2">
                                            <div class="flex-1">
                                                <input wire:model="resourceName" placeholder="Nombre del recurso"
                                                       class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none transition-colors @error('resourceName') border-red-500/50 @enderror"/>
                                                @error('resourceName')
                                                    <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            @if(count($wizardSections) > 0)
                                                <div class="flex-none">
                                                    <select wire:model="resourceSectionId"
                                                            class="bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-2.5 py-2 text-xs text-gray-900 dark:text-slate-200 focus:border-emerald-500 focus:outline-none transition-colors min-w-[130px]">
                                                        <option value="">Sin sección</option>
                                                        @foreach($wizardSections as $sec)
                                                            <option value="{{ $sec['id'] }}">{{ $sec['title'] }} {{ !$sec['is_visible'] ? '(oculta)' : '' }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Row 2: File + Upload + Preview --}}
                                        <div x-data="{ previewUrl: window._filePreviewUrl || null, previewType: window._filePreviewType || null }"
                                             x-on:file-preview-reset.window="previewUrl = null; previewType = null; window._filePreviewUrl = null; window._filePreviewType = null">
                                            <div class="flex gap-2 items-start">
                                                <div class="relative flex-none">
                                                    <input wire:model="resourceFile" type="file" id="resourceFile"
                                                           class="absolute inset-0 opacity-0 cursor-pointer @error('resourceFile') border-2 border-red-500/50 @enderror"
                                                           @change="const f = $event.target.files[0]; if (f && f.type.startsWith('image/')) { const r = new FileReader(); r.onload = e => { window._filePreviewUrl = e.target.result; window._filePreviewType = f.type; previewUrl = e.target.result; previewType = f.type }; r.readAsDataURL(f) } else { window._filePreviewUrl = null; window._filePreviewType = null; previewUrl = null; previewType = null }"/>
                                                    <label for="resourceFile"
                                                           class="flex items-center gap-1.5 px-3 py-2 @error('resourceFile') bg-red-800/40 border-red-500/50 @else bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 border-gray-300 dark:border-slate-600/50 hover:border-gray-400 dark:hover:border-slate-500/50 @enderror text-gray-600 dark:text-slate-300 text-xs rounded-lg cursor-pointer transition-colors whitespace-nowrap border">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                        </svg>
                                                        {{ $editingResourceIndex !== null ? 'Cambiar archivo' : 'Adjuntar' }}
                                                    </label>
                                                    @error('resourceFile')
                                                        <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <p class="flex-1 text-[10px] text-gray-400 dark:text-slate-500 leading-[36px]">Máx. 2 MB por archivo · 10 MB total por lección</p>
                                                <button wire:click="addWizardResource"
                                                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-medium rounded-lg transition-colors whitespace-nowrap flex items-center gap-1.5 shrink-0">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                    {{ $editingResourceIndex !== null ? 'Actualizar' : 'Subir' }}
                                                </button>
                                            </div>
                                            <template x-if="previewUrl">
                                                <div class="mt-2 rounded-xl overflow-hidden border border-emerald-500/30 bg-white dark:bg-slate-800/50"
                                                     title="Vista previa del archivo seleccionado">
                                                    <div class="relative w-full max-w-[200px] mx-auto">
                                                        <img :src="previewUrl" alt="Preview"
                                                             class="w-full h-auto object-contain max-h-48">
                                                    </div>
                                                    <div class="px-3 py-1.5 border-t border-emerald-500/20 flex items-center gap-2">
                                                        <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span class="text-[10px] text-gray-500 dark:text-slate-400">Archivo seleccionado — listo para subir</span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- ═══ IMAGE PROMPT (como en paso 2, con selector de sección) ═══ --}}
                                    @php
                                        $step3ImageSection = $step3ImageSectionId
                                            ? collect($wizardSections)->firstWhere('id', (int) $step3ImageSectionId)
                                            : null;

                                        $step3SectionContentForPrompt = '';
                                        if ($step3ImageSection) {
                                            $step3Text = collect($step3ImageSection['contents'] ?? [])
                                                ->pluck('body')
                                                ->map(fn($b) => strip_tags($b))
                                                ->implode("\n");
                                            $step3SectionContentForPrompt = \Illuminate\Support\Str::limit($step3Text, 500) ?: 'Describe un recurso visual genérico que complemente esta sección.';
                                        } else {
                                            $step3SectionContentForPrompt = 'No hay sección específica seleccionada. Crea un recurso visual genérico para la lección.';
                                        }

                                        $step3ImagePrompt = "## Rol\n"
                                            ."Eres un ilustrador educativo senior y diseñador instruccional con 15 años de experiencia creando recursos visuales pedagógicamente efectivos para entornos de aprendizaje presencial y digital. Dominas principios de comunicación visual, psicología cognitiva del aprendizaje y diseño universal para el aprendizaje (DUA).\n\n"
                                            ."## Contexto pedagógico\n"
                                            ."- **Grado/Nivel:** {$gradoName}\n"
                                            ."- **Asignatura:** {$asignaturaName}\n"
                                            ."- **Sección escolar:** {$seccionName}\n"
                                            ."- **Título de la lección:** {$lessonTitle}\n"
                                            ."- **Sección destino:** ".($step3ImageSection ? $step3ImageSection['title'] : 'Sin sección específica')."\n"
                                            ."- **Contenido de la sección:** {$step3SectionContentForPrompt}\n"
                                            ."- **Tipo de recurso:** Imagen descargable / recurso visual complementario\n\n"
                                            ."## Especificaciones técnicas del recurso visual\n"
                                            ."- **Estilo gráfico:** Ilustración educativa profesional en estilo «flat design» con paleta de color armónica, saturada pero no fluorescente. Trazos vectoriales definidos sin sombras complejas ni degradados extensos. Composición ordenada con jerarquía visual clara (tamaño, color, posición).\n"
                                            ."- **Proporción:** 16:9 horizontal. La imagen debe funcionar tanto en pantalla proyectada como en impresión tamaño carta (margen de 1\").\n"
                                            ."- **Resolución:** Mínimo 1920×1080px, 300 DPI si es vectorial.\n"
                                            ."- **Tipografía:** NO incluir texto ni etiquetas en la imagen. Todo el texto debe poder añadirse por separado como capa independiente.\n"
                                            ."- **Paleta de color:** Accesible para daltonismo (evitar rojo/verde como único contraste). Usar azul, naranja, amarillo, verde azulado como colores principales de distinción.\n"
                                            ."- **Público objetivo:** Estudiantes de {$gradoName}. El nivel de abstracción, las metáforas visuales y el vocabulario gráfico deben ser apropiados para esta etapa educativa.\n\n"
                                            ."## Instrucciones de contenido didáctico\n"
                                            ."1. **Concepto central:** Representa visualmente la idea o proceso fundamental de la sección de manera concreta, evitando abstracciones que requieran texto explicativo.\n"
                                            ."2. **Metáfora visual:** Usa una analogía visual que conecte el nuevo conocimiento con experiencias cotidianas del estudiante (si aplica).\n"
                                            ."3. **Secuencia didáctica:** Si el contenido describe un proceso (causa-efecto, línea de tiempo, ciclo), represéntalo en 3-4 viñetas o pasos dentro de una misma composición.\n"
                                            ."4. **Punto focal:** La composición debe tener un único elemento visual dominante que capture la atención primero, con elementos secundarios que amplíen o contextualicen.\n"
                                            ."5. **Inclusión y diversidad:** Cualquier figura humana debe representar diversidad étnica, de género y funcional de manera natural y no estereotipada.\n"
                                            ."6. **Fondo:** Neutro o contextual mínimo (sin texturas distractoras). El fondo no debe competir con el contenido pedagógico.\n\n"
                                            ."## Restricciones\n"
                                            ."- ❌ Sin texto renderizado en la imagen (ni títulos, ni etiquetas, ni pies de foto).\n"
                                            ."- ❌ Sin elementos decorativos que no tengan función pedagógica directa.\n"
                                            ."- ❌ Sin violencia, estereotipos de género/raza, representaciones inexactas científicamente.\n"
                                            ."- ❌ Sin marcas de agua, logos o referencias a la herramienta generadora.\n"
                                            ."- ✅ La imagen debe mantener legibilidad y contraste si se imprime en escala de grises.\n"
                                            ."- ✅ El estilo debe ser coherente con otras imágenes didácticas de la misma lección (mantener misma paleta y nivel de detalle).\n\n"
                                            ."## Formato de salida\n"
                                            ."Genera ÚNICAMENTE la imagen solicitada. Sin descripciones adicionales, sin explicaciones, sin variantes. Entrega la imagen en el formato y proporción especificados.";
                                    @endphp

                                    <div class="mt-3 border-t border-gray-200 dark:border-slate-700/30 pt-3"
                                         x-data="{ showPrompt: false }">
                                        <button @click="showPrompt = !showPrompt"
                                                class="flex items-center gap-2 w-full px-3 py-2 rounded-lg text-[11px] font-medium transition-colors
                                                       text-gray-500 dark:text-slate-400 hover:text-amber-300 hover:bg-amber-500/5">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                            </svg>
                                            Imagen IA — Prompt para recurso visual
                                            <svg class="w-3.5 h-3.5 ml-auto transition-transform" :class="showPrompt ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        <div x-show="showPrompt" x-cloak x-transition:enter.duration.200ms
                                             class="mt-3 p-4 bg-gradient-to-br from-amber-500/5 via-slate-900/80 to-slate-900 border border-amber-500/20 rounded-lg space-y-3">
                                            {{-- Selector de sección --}}
                                            <div class="flex items-center gap-3">
                                                <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-slate-400 shrink-0">
                                                    <svg class="w-3.5 h-3.5 text-amber-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                                    Sección:
                                                </div>
                                                <select wire:model.live="step3ImageSectionId"
                                                        class="flex-1 bg-white dark:bg-slate-800/80 border border-gray-300 dark:border-slate-600/50 rounded-lg px-3 py-1.5 text-xs text-gray-900 dark:text-slate-200 focus:border-amber-500/50 focus:outline-none">
                                                    <option value="">— Seleccionar sección —</option>
                                                    @foreach($wizardSections as $sec)
                                                        <option value="{{ $sec['id'] }}">{{ $sec['title'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Header --}}
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex items-start gap-3">
                                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-500/20 to-orange-500/10 flex items-center justify-center shrink-0 mt-0.5">
                                                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-sm font-bold text-amber-300">Prompt — Imagen didáctica</h4>
                                                        <p class="text-[11px] text-gray-500 dark:text-slate-400 leading-relaxed">
                                                            Copia este prompt y pégalo en un generador de imágenes con IA
                                                            (<span class="text-gray-600 dark:text-slate-300">DALL·E, Midjourney, Stable Diffusion, Copilot</span>)
                                                            para crear un recurso visual descargable para la lección.
                                                        </p>
                                                    </div>
                                                </div>
                                                <button @click="showPrompt = false"
                                                        class="p-1 hover:bg-gray-200 dark:hover:bg-slate-700/50 rounded-lg transition-colors shrink-0">
                                                    <svg class="w-4 h-4 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>

                                            {{-- Prompt text --}}
                                            <div class="relative" x-data="{}">
                                                <pre class="bg-white dark:bg-slate-950/80 border border-gray-200 dark:border-slate-700/50 rounded-lg p-4 text-[11px] text-gray-600 dark:text-slate-300 leading-relaxed font-mono whitespace-pre-wrap overflow-x-auto max-h-96 overflow-y-auto">{{ $step3ImagePrompt }}</pre>
                                                <button @click="
                                                    const btn = $event.currentTarget;
                                                    navigator.clipboard.writeText(btn.parentElement.querySelector('pre')?.textContent || '');
                                                    btn.textContent = '✓ Copiado';
                                                    setTimeout(() => btn.textContent = 'Copiar prompt', 2000);
                                                "
                                                        class="absolute top-3 right-3 px-2.5 py-1 text-[10px] font-medium text-amber-300 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20 rounded-lg transition-all"
                                                        type="button">
                                                    Copiar prompt
                                                </button>
                                            </div>

                                            {{-- Footer --}}
                                            <div class="flex items-center justify-between text-[10px] text-gray-400 dark:text-slate-500">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    La imagen generada podrás asociarla como recurso descargable a la lección.
                                                </span>
                                                <span>{{ strlen($step3ImagePrompt) }} caracteres</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ═══ Tab: HTML Embeds ═══ --}}
                                <div x-show="activeTab === 'embeds'" x-cloak x-transition:enter.duration.200ms>
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                            </svg>
                                            <h3 class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">HTML Embeds</h3>
                                            @if($editingEmbedIndex !== null)
                                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                                    Editando #{{ $editingEmbedIndex + 1 }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[11px] text-gray-400 dark:text-slate-500 bg-gray-200 dark:bg-slate-700/40 px-2 py-0.5 rounded-full">{{ count($wizardHtmlEmbeds) }} embeds</span>
                                    </div>

                                    @if(count($wizardHtmlEmbeds) > 0)
                                        <div class="space-y-1.5 mb-2">
                                            @foreach($wizardHtmlEmbeds as $eIdx => $embed)
                                                <div class="flex items-start gap-3 px-3 py-2.5 bg-white dark:bg-slate-800/40 border border-gray-200 dark:border-slate-700/40 rounded-lg hover:border-gray-300 dark:hover:border-slate-600/60 hover:bg-gray-50 dark:hover:bg-slate-800/60 transition-all group">
                                                    <div class="w-9 h-9 rounded-lg bg-fuchsia-500/10 flex items-center justify-center shrink-0 mt-0.5">
                                                        <svg class="w-4 h-4 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2">
                                                            <p class="text-xs font-medium text-gray-700 dark:text-slate-200 truncate">{{ $embed['title'] ?? 'Embed HTML' }}</p>
                                                            @if(!empty($embed['section_id']))
                                                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded border text-amber-300 bg-amber-500/10 border-amber-500/20 shrink-0">
                                                                    Sección {{ collect($wizardSections)->firstWhere('id', $embed['section_id'])['title'] ?? '' }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="text-[10px] text-gray-400 dark:text-slate-500 font-mono mt-1 line-clamp-2">{{ Str::limit(strip_tags($embed['html_content'] ?? ''), 120) }}</div>
                                                    </div>
                                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-all">
                                                        <button wire:click="previewExistingEmbed({{ $eIdx }})"
                                                                class="text-gray-400 dark:text-slate-400 hover:text-fuchsia-300 transition-all text-xs p-1 rounded hover:bg-fuchsia-500/10"
                                                                title="Vista previa">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                            </svg>
                                                        </button>
                                                        <button wire:click="editWizardHtmlEmbed({{ $eIdx }})"
                                                                class="text-gray-400 dark:text-slate-400 hover:text-amber-300 transition-all text-xs p-1 rounded hover:bg-amber-500/10"
                                                                title="Editar embed">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </button>
                                                        <button wire:click="removeWizardHtmlEmbed({{ $eIdx }})"
                                                                class="text-red-400/60 hover:text-red-300 transition-all text-xs p-1 rounded hover:bg-red-500/10"
                                                                title="Eliminar embed">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center gap-3 px-4 py-5 bg-gray-50 dark:bg-slate-800/20 border border-dashed border-gray-200 dark:border-slate-700/30 rounded-lg mb-2">
                                            <svg class="w-5 h-5 text-gray-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                            </svg>
                                            <p class="text-xs text-gray-400 dark:text-slate-500">Sin código HTML embebido aún. Agrega contenido HTML para la lección.</p>
                                        </div>
                                    @endif

                                    {{-- Add HTML embed form --}}
                                    <div class="space-y-2">
                                        <div class="flex gap-2">
                                            <div class="flex-1">
                                                <input wire:model="embedTitle" placeholder="Título del embed (opcional)"
                                                       class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none transition-colors"/>
                                            </div>
                                            @if(count($wizardSections) > 0)
                                                <div class="flex-none">
                                                    <select wire:model.live="embedSectionId"
                                                            class="bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-2.5 py-2 text-xs text-gray-900 dark:text-slate-200 focus:border-emerald-500 focus:outline-none transition-colors min-w-[130px]">
                                                        <option value="">Sin sección</option>
                                                        @foreach($wizardSections as $sec)
                                                            <option value="{{ $sec['id'] }}">{{ $sec['title'] }} {{ !$sec['is_visible'] ? '(oculta)' : '' }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </div>

                                        <div x-data="{ showHelpModal: false }">
                                            <div class="flex justify-end">
                                                <button @click="showHelpModal = true"
                                                        class="text-[11px] text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 px-2 py-1 -mb-1 rounded-lg transition-colors flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    ¿Cómo se usa?
                                                </button>
                                            </div>

                                            {{-- Modal de ayuda --}}
                                            <div x-show="showHelpModal" x-cloak
                                                 @keydown.escape.window="showHelpModal = false"
                                                 class="fixed inset-0 z-[9999] overflow-y-auto">
                                                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="showHelpModal = false"></div>
                                                <div class="relative min-h-screen flex items-center justify-center p-4">
                                                    <div class="relative w-full max-w-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-slate-700 rounded-lg shadow-2xl overflow-hidden">
                                                        {{-- Header --}}
                                                        <div class="flex items-center justify-between px-6 py-3 border-b border-gray-200 dark:border-slate-700">
                                                            <div class="flex items-center gap-2">
                                                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Guía: HTML Embeds</h3>
                                                            </div>
                                                            <button @click="showHelpModal = false"
                                                                    class="p-1.5 text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-all">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </div>

                                                        {{-- Body del modal --}}
                                                        <div class="p-6 space-y-4">
                                                            <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                                                                Pega código HTML (<em>iframe</em>, <em>script</em>, tablas, etc.) para enriquecer la lección con contenido interactivo externo. Los iframes se renderizan en vivo dentro de la lección.
                                                            </p>

                                                            {{-- YouTube --}}
                                                            <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/50 rounded-lg overflow-hidden">
                                                                <div class="p-3 border-b border-gray-200 dark:border-slate-700/50">
                                                                    <p class="text-xs font-semibold text-emerald-400/80 mb-1">📺 YouTube — Video</p>
                                                                    <code class="text-[11px] text-gray-500 dark:text-slate-400 font-mono break-all select-all">&lt;iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Video explicativo" frameborder="0" allowfullscreen&gt;&lt;/iframe&gt;</code>
                                                                </div>
                                                            </div>

                                                            {{-- Google Maps + Google Drive --}}
                                                            <div class="grid grid-cols-2 gap-3">
                                                                <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/50 rounded-lg p-3">
                                                                    <p class="text-[11px] font-semibold text-blue-400/80 mb-1">🗺️ Google Maps — Ubicación</p>
                                                                    <code class="text-[10px] text-gray-500 dark:text-slate-400 font-mono break-all select-all">&lt;iframe src="https://www.google.com/maps/embed?pb=..." width="600" height="450" allowfullscreen&gt;&lt;/iframe&gt;</code>
                                                                </div>
                                                                <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/50 rounded-lg p-3">
                                                                    <p class="text-[11px] font-semibold text-fuchsia-400/80 mb-1">📁 Google Drive — Archivo</p>
                                                                    <code class="text-[10px] text-gray-500 dark:text-slate-400 font-mono break-all select-all">&lt;iframe src="https://drive.google.com/file/d/FILE_ID/preview" width="640" height="480" allowfullscreen&gt;&lt;/iframe&gt;</code>
                                                                </div>
                                                            </div>

                                                            {{-- CORS warning --}}
                                                            <div class="flex items-start gap-2 bg-amber-500/10 border border-amber-500/20 rounded-lg p-3">
                                                                <svg class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                                </svg>
                                                                <p class="text-[11px] text-amber-400/80 leading-relaxed">
                                                                    Algunos servicios bloquean iframes por políticas CORS. Siempre verifica que el embed funcione antes de publicar la lección.
                                                                </p>
                                                            </div>
                                                        </div>

                                                        {{-- Footer --}}
                                                        <div class="flex items-center justify-end px-6 py-3 bg-gray-50 dark:bg-slate-800/50 border-t border-gray-200 dark:border-slate-700">
                                                            <button @click="showHelpModal = false"
                                                                    class="px-4 py-2 text-xs font-medium text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg transition-all">
                                                                Cerrar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <textarea wire:model="embedHtml" rows="4"
                                                      placeholder="Pega aquí el código HTML (iframe, script, etc.)"
                                                      class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none transition-colors font-mono resize-y"></textarea>
                                        </div>
                                        <div x-data="{ showEmbedPreview: false }">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="flex items-center gap-2">
                                                    @if(trim($embedHtml))
                                                    <button @click="showEmbedPreview = true"
                                                            class="px-3 py-2 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white text-xs font-medium rounded-lg transition-all whitespace-nowrap flex items-center gap-1.5 border border-gray-300 dark:border-slate-600/50">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Vista previa
                                                    </button>
                                                    @endif
                                                    <button wire:click="addWizardHtmlEmbed"
                                                            class="px-4 py-2 {{ $editingEmbedIndex !== null ? 'bg-amber-600 hover:bg-amber-500' : 'bg-fuchsia-600 hover:bg-fuchsia-500' }} text-white text-xs font-medium rounded-lg transition-colors whitespace-nowrap flex items-center gap-1.5">
                                                        @if($editingEmbedIndex !== null)
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                            Actualizar cambios
                                                        @else
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                            Agregar Embed
                                                        @endif
                                                    </button>
                                                </div>
                                                @if($editingEmbedIndex !== null)
                                                    <button wire:click="cancelEditEmbed"
                                                            class="px-3 py-2 text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white text-xs font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors">
                                                        Cancelar
                                                    </button>
                                                @endif
                                            </div>

                                            {{-- Modal vista previa del embed --}}
                                            <div x-show="showEmbedPreview" x-cloak
                                                 @keydown.escape.window="showEmbedPreview = false"
                                                 class="fixed inset-0 z-[9999] overflow-y-auto">
                                                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="showEmbedPreview = false"></div>
                                                <div class="relative min-h-screen flex items-center justify-center p-4">
                                                    <div class="relative w-full max-w-4xl bg-gray-900 border border-slate-700 rounded-lg shadow-2xl overflow-hidden">
                                                        <div class="flex items-center justify-between px-6 py-3 border-b border-slate-700">
                                                            <div class="flex items-center gap-2">
                                                                <svg class="w-5 h-5 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                </svg>
                                                                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Vista previa del Embed</h3>
                                                                @if($embedTitle)
                                                                    <span class="text-xs text-slate-400 font-normal">— {{ $embedTitle }}</span>
                                                                @endif
                                                            </div>
                                                            <button @click="showEmbedPreview = false"
                                                                    class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-all">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        <div class="p-6">
                                                            <div class="prose prose-sm max-w-none prose-invert">{!! $embedHtml !!}</div>
                                                        </div>
                                                        <div class="flex items-center justify-end px-6 py-3 bg-slate-800/50 border-t border-slate-700">
                                                            <button @click="showEmbedPreview = false"
                                                                    class="px-4 py-2 text-xs font-medium text-slate-300 hover:text-white bg-slate-700 hover:bg-slate-600 rounded-lg transition-all">
                                                                Cerrar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-[10px] text-gray-400 dark:text-slate-500 leading-relaxed">
                                            El código HTML se renderizará en la vista del estudiante. Usa con precaución: iframes, tablas, formularios, etc.
                                        </p>
                                    </div>
                                </div>

                                {{-- ═══ Tab: Enlaces externos ═══ --}}
                                <div x-show="activeTab === 'links'" x-cloak x-transition:enter.duration.200ms>
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                            <h3 class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Enlaces externos</h3>
                                        </div>
                                        <span class="text-[11px] text-gray-400 dark:text-slate-500 bg-gray-200 dark:bg-slate-700/40 px-2 py-0.5 rounded-full">{{ count($wizardLinks) }} enlaces</span>
                                    </div>

                                    @if(count($wizardLinks) > 0)
                                        <div class="space-y-1.5 mb-2">
                                            @foreach($wizardLinks as $lIdx => $link)
                                                @php
                                                    $badge = match($link['link_type']) {
                                                        'REFERENCE' => ['label' => 'Ref', 'color' => 'text-amber-300 bg-amber-500/10 border-amber-500/20'],
                                                        'VIDEO'     => ['label' => 'Video', 'color' => 'text-purple-300 bg-purple-500/10 border-purple-500/20'],
                                                        'TOOL'      => ['label' => 'Tool', 'color' => 'text-sky-300 bg-sky-500/10 border-sky-500/20'],
                                                        'DOCUMENT'  => ['label' => 'Doc', 'color' => 'text-blue-300 bg-blue-500/10 border-blue-500/20'],
                                                        default     => ['label' => 'Otro', 'color' => 'text-slate-300 bg-slate-500/10 border-slate-500/20'],
                                                    };
                                                    $displayUrl = parse_url($link['url'], PHP_URL_HOST) ?: $link['url'];
                                                @endphp
                                                <div class="flex items-center gap-3 px-3 py-2.5 bg-white dark:bg-slate-800/40 border border-gray-200 dark:border-slate-700/40 rounded-lg hover:border-gray-300 dark:hover:border-slate-600/60 hover:bg-gray-50 dark:hover:bg-slate-800/60 transition-all group">
                                                    <div class="w-9 h-9 rounded-lg bg-sky-500/10 flex items-center justify-center shrink-0">
                                                        <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2">
                                                            <p class="text-xs font-medium text-gray-700 dark:text-slate-200 truncate">{{ $link['title'] }}</p>
                                                            <span class="text-[10px] font-medium px-1.5 py-0.5 rounded border {{ $badge['color'] }} shrink-0">{{ $badge['label'] }}</span>
                                                        </div>
                                                        <p class="text-[10px] text-gray-400 dark:text-slate-500 truncate">{{ $displayUrl }}</p>
                                                    </div>
                                                    <button wire:click="removeWizardLink({{ $lIdx }})"
                                                            class="opacity-0 group-hover:opacity-100 text-red-400/60 hover:text-red-300 transition-all text-xs p-1 rounded hover:bg-red-500/10"
                                                            title="Eliminar enlace">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center gap-3 px-4 py-5 bg-gray-50 dark:bg-slate-800/20 border border-dashed border-gray-200 dark:border-slate-700/30 rounded-lg mb-2">
                                            <svg class="w-5 h-5 text-gray-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                            <p class="text-xs text-gray-400 dark:text-slate-500">Sin enlaces aún. Agrega enlaces de referencia, videos o herramientas.</p>
                                        </div>
                                    @endif

                                    {{-- Add link form --}}
                                    <div class="flex flex-wrap gap-2 items-end">
                                        <div class="flex-1 min-w-[140px]">
                                            <input wire:model="linkTitle" placeholder="Título del enlace"
                                                   class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none transition-colors"/>
                                        </div>
                                        <div class="flex-1 min-w-[140px]">
                                            <input wire:model="linkUrl" placeholder="https://…"
                                                   class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none transition-colors"/>
                                        </div>
                                        <select wire:model="linkType"
                                                class="bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-2.5 py-2 text-xs text-gray-900 dark:text-slate-200 focus:border-emerald-500 focus:outline-none transition-colors">
                                            <option value="REFERENCE">Referencia</option>
                                            <option value="VIDEO">Video</option>
                                            <option value="TOOL">Herramienta</option>
                                            <option value="DOCUMENT">Documento</option>
                                            <option value="OTHER">Otro</option>
                                        </select>
                                        @if(count($wizardSections) > 0)
                                            <select wire:model="linkSectionId"
                                                    class="bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-2.5 py-2 text-xs text-gray-900 dark:text-slate-200 focus:border-emerald-500 focus:outline-none transition-colors min-w-[120px]">
                                                <option value="">Sin sección</option>
                                                @foreach($wizardSections as $sec)
                                                    <option value="{{ $sec['id'] }}">{{ $sec['title'] }} {{ !$sec['is_visible'] ? '(oculta)' : '' }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                        <button wire:click="addWizardLink"
                                                class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-medium rounded-lg transition-colors whitespace-nowrap flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Agregar
                                        </button>
                                    </div>
                                </div>

                            </div>{{-- /body tabs --}}

                            {{-- ═══════ CONFIRM DELETE ALL RESOURCES MODAL ═══════ --}}
                            <div x-show="showConfirmDeleteResources" x-cloak
                                 class="fixed inset-0 z-[9999] overflow-y-auto"
                                 wire:key="confirm-delete-all-resources">
                                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm"
                                     @click="showConfirmDeleteResources = false"></div>
                                <div class="relative min-h-screen flex items-center justify-center p-4">
                                    <div class="relative w-full max-w-md bg-slate-800 border border-slate-600/50 rounded-xl shadow-2xl overflow-hidden"
                                         @click.outside="showConfirmDeleteResources = false">
                                        {{-- Header --}}
                                        <div class="flex items-center gap-3 px-6 pt-6 pb-2">
                                            <div class="w-10 h-10 rounded-xl bg-red-500/15 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-base font-bold text-white">Eliminar todos los recursos</h3>
                                                <p class="text-xs text-slate-400 leading-relaxed mt-0.5">
                                                    Se eliminarán <strong class="text-slate-300">todos</strong> los archivos descargables,
                                                    HTML embeds y enlaces externos de esta lección.
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Body: summary counts --}}
                                        <div class="px-6 py-3 space-y-1.5">
                                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                                <svg class="w-3.5 h-3.5 text-emerald-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span><strong class="text-slate-300" x-text="{{ count($wizardResources) }}"></strong> archivos descargables</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                                <svg class="w-3.5 h-3.5 text-fuchsia-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                                </svg>
                                                <span><strong class="text-slate-300" x-text="{{ count($wizardHtmlEmbeds) }}"></strong> HTML embeds</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                                <svg class="w-3.5 h-3.5 text-sky-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                                </svg>
                                                <span><strong class="text-slate-300" x-text="{{ count($wizardLinks) }}"></strong> enlaces externos</span>
                                            </div>
                                        </div>

                                        {{-- Warning --}}
                                        <div class="mx-6 mb-2 p-3 bg-amber-500/10 border border-amber-500/20 rounded-lg">
                                            <p class="text-[11px] text-amber-300/80 leading-relaxed flex items-start gap-2">
                                                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                </svg>
                                                <span>Esta acción no se puede deshacer. Los recursos eliminados se perderán permanentemente al guardar la lección.</span>
                                            </p>
                                        </div>

                                        {{-- Footer actions --}}
                                        <div class="flex items-center justify-end gap-2 px-6 py-4 bg-slate-800/80 border-t border-slate-700/30">
                                            <button @click="showConfirmDeleteResources = false"
                                                    class="px-4 py-2 text-xs font-medium text-slate-300 hover:text-white bg-slate-700 hover:bg-slate-600 rounded-lg transition-all">
                                                Cancelar
                                            </button>
                                            <button wire:click="removeAllWizardResources"
                                                    @click="showConfirmDeleteResources = false"
                                                    class="px-4 py-2 text-xs font-medium text-white bg-red-600 hover:bg-red-500 rounded-lg transition-all flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Eliminar todo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- ═══════ MODAL PREVIEW EMBED EXISTENTE (global a los tabs) ═══════ --}}
                        @php $previewEmbed = $previewEmbedIndex !== null ? ($wizardHtmlEmbeds[$previewEmbedIndex] ?? null) : null; @endphp
                        @if($previewEmbed)
                        <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="existing-embed-preview-modal">
                            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeExistingEmbedPreview"></div>
                            <div class="relative min-h-screen flex items-center justify-center p-4">
                                <div class="relative w-full max-w-4xl bg-gray-900 border border-slate-700 rounded-lg shadow-2xl overflow-hidden">
                                    <div class="flex items-center justify-between px-6 py-2 border-b border-slate-700">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                                            </svg>
                                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Vista previa del embed</h3>
                                            @if(!empty($previewEmbed['title']))
                                                <span class="text-xs text-slate-400 font-normal">— {{ $previewEmbed['title'] }}</span>
                                            @endif
                                        </div>
                                        <button wire:click="closeExistingEmbedPreview"
                                                class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="p-6">
                                        @php
                                            $previewContent = $previewEmbed['html_content'] ?? '';
                                            $isMermaid = preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/', trim($previewContent)) === 1;
                                        @endphp
                                        @if($isMermaid)
                                            <div wire:ignore x-data="mermaidEmbed()"
                                                 data-mermaid-code="{{ $previewContent }}"
                                                 class="w-full bg-white rounded-lg p-4 overflow-x-auto">
                                                <div x-ref="target" class="w-full"></div>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-3 text-center">Diagrama Mermaid renderizado en vivo</p>
                                        @else
                                            <div class="prose prose-sm max-w-none prose-invert">{!! $previewContent !!}</div>
                                            <p class="text-xs text-amber-400 mt-3 text-center">ℹ️ Contenido HTML embebido</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-end gap-2 px-6 py-2 bg-slate-800/50 border-t border-slate-700">
                                        <button wire:click="closeExistingEmbedPreview"
                                                class="px-4 py-2 text-xs font-medium text-slate-300 hover:text-white bg-slate-700 hover:bg-slate-600 rounded-lg transition-all">
                                            Cerrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- ═══════ MODAL PREVIEW DIAGRAMA (global a los tabs) ═══════ --}}
                        @if($showEmbedPreview)
                        <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="embed-preview-modal">
                            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeEmbedPreview"></div>
                            <div class="relative min-h-screen flex items-center justify-center p-4">
                                <div class="relative w-full max-w-4xl bg-gray-900 border border-slate-700 rounded-lg shadow-2xl overflow-hidden">
                                    <div class="flex items-center justify-between px-6 py-2 border-b border-slate-700">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                                            </svg>
                                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Vista previa del diagrama</h3>
                                            @if($embedTitle)
                                                <span class="text-xs text-slate-400 font-normal">— {{ $embedTitle }}</span>
                                            @endif
                                        </div>
                                        <button wire:click="closeEmbedPreview"
                                                class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="p-6">
                                        @php
                                            $isMermaid = preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/', trim($embedHtml)) === 1;
                                        @endphp
                                        @if($isMermaid)
                                            <div wire:ignore x-data="mermaidEmbed()"
                                                 data-mermaid-code="{{ $embedHtml }}"
                                                 class="w-full bg-white rounded-lg p-4 overflow-x-auto">
                                                <div x-ref="target" class="w-full"></div>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-3 text-center">Diagrama Mermaid renderizado en vivo</p>
                                        @else
                                            <div class="prose prose-sm max-w-none prose-invert">{!! $embedHtml !!}</div>
                                            <p class="text-xs text-amber-400 mt-3 text-center">ℹ️ Este contenido no se reconoce como diagrama Mermaid. Se muestra como HTML.</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-end gap-2 px-6 py-2 bg-slate-800/50 border-t border-slate-700">
                                        <button wire:click="closeEmbedPreview"
                                                class="px-4 py-2 text-xs font-medium text-slate-300 hover:text-white bg-slate-700 hover:bg-slate-600 rounded-lg transition-all">
                                            Cerrar
                                        </button>
                                        <button wire:click="addWizardHtmlEmbed"
                                                class="px-4 py-2 text-xs font-medium text-white bg-fuchsia-600 hover:bg-fuchsia-500 rounded-lg transition-all">
                                            Agregar Embed
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- ═══════ MODAL PREVIEW IMAGEN ═══════ --}}
                        @php $previewResource = $previewResourceIndex !== null ? ($wizardResources[$previewResourceIndex] ?? null) : null; @endphp
                        @if($previewResource)
                        <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="resource-image-preview-modal">
                            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeResourcePreview"></div>
                            <div class="relative min-h-screen flex items-center justify-center p-4">
                                <div class="relative bg-gray-900 border border-slate-700 rounded-lg shadow-2xl overflow-hidden"
                                     x-data="{ imgWidth: 0, imgHeight: 0 }">
                                    {{-- Header --}}
                                    <div class="flex items-center justify-between gap-3 px-6 py-2 border-b border-slate-700">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <h3 class="text-sm font-bold text-white uppercase tracking-wider truncate">{{ $previewResource['display_name'] }}</h3>
                                            <span class="text-xs text-slate-500 shrink-0">({{ $previewResource['media']['size_for_humans'] ?? '' }})</span>
                                        </div>
                                        <button wire:click="closeResourcePreview"
                                                class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-all shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    {{-- Body: imagen con dimensiones naturales --}}
                                    <div class="p-4 flex items-center justify-center bg-black/40">
                                        <img src="{{ $previewResource['media']['public_url'] }}"
                                             alt="{{ $previewResource['display_name'] }}"
                                             @load="imgWidth = $el.naturalWidth; imgHeight = $el.naturalHeight"
                                             class="max-w-full h-auto rounded-lg shadow-lg object-contain"
                                             :style="`max-height: min(80vh, ${imgHeight}px); max-width: min(90vw, ${imgWidth}px)`"
                                             style="max-height: 80vh"/>
                                    </div>
                                    {{-- Footer: dimensiones --}}
                                    <div class="flex items-center justify-between px-6 py-2 bg-slate-800/50 border-t border-slate-700">
                                        <span class="text-[11px] text-slate-400">
                                            <span x-text="imgWidth ? imgWidth + ' × ' + imgHeight + ' px' : 'Cargando dimensiones…'"></span>
                                        </span>
                                        <button wire:click="closeResourcePreview"
                                                class="px-4 py-1.5 text-xs font-medium text-slate-300 hover:text-white bg-slate-700 hover:bg-slate-600 rounded-lg transition-all">
                                            Cerrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif

                    {{-- STEP 4: Preguntas de Repaso --}}
                    @if($currentStep === 4)
                        <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg p-5 space-y-4">
                            <div class="flex items-center gap-2 pb-3 border-b border-gray-200 dark:border-slate-700">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">4</span>
                                <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Preguntas de Repaso</h2>
                                <span class="text-[10px] text-gray-400 dark:text-slate-600 font-mono">Markdown</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                                Escribe preguntas de repaso en formato Markdown. Puedes usar <code class="text-emerald-400/80">##</code> títulos,
                                <code class="text-emerald-400/80">**negritas**</code>, listas, tablas y más.
                            </p>
                            <textarea wire:model="reviewQuestions"
                                      rows="10"
                                      class="w-full bg-white dark:bg-slate-950/80 border border-gray-300 dark:border-slate-700/50 rounded-lg px-4 py-2.5 text-sm text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-600 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all resize-y font-mono leading-relaxed"
                                      placeholder="## Preguntas de Repaso

1. Cuál es...?
2. Explica...

### Sección 2
Cómo...?"
                                      spellcheck="false"></textarea>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400 dark:text-slate-600">{{ strlen($reviewQuestions) }} caracteres</span>
                                <div class="flex items-center gap-2">
                                    <button wire:click="generateReviewQuestions"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                                   text-purple-400 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/20 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        Generar con IA
                                    </button>
                                    @if(!empty($reviewQuestions))
                                        <button wire:click="$set('showReviewPreview', true)"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-medium text-cyan-400 bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/20 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Vista previa
                                        </button>
                                    @endif
                                    <button wire:click="$set('reviewQuestions', '')"
                                            wire:confirm="Limpiar las preguntas de repaso?"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-medium text-gray-400 dark:text-slate-500 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/20 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- ═══ MODAL: Vista Previa de Preguntas de Repaso ═══ --}}
                        @if($showReviewPreview && !empty($reviewQuestions))
                            <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="review-preview-modal">
                                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="$set('showReviewPreview', false)"></div>
                                <div class="relative min-h-screen flex items-center justify-center p-4">
                                    <div class="relative w-full max-w-3xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-2xl shadow-2xl overflow-hidden">
                                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800/90">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Vista Previa — Preguntas de Repaso</h3>
                                            </div>
                                            <button wire:click="$set('showReviewPreview', false)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700/50 text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6L18 18"/></svg>
                                            </button>
                                        </div>
                                        <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                                            <x-lms.math-text
                                                :content="Str::markdown($reviewQuestions)"
                                                class="prose prose-sm prose-invert max-w-none
                                                       prose-headings:text-white prose-headings:font-bold
                                                       prose-h2:text-lg prose-h2:border-b prose-h2:border-slate-700 prose-h2:pb-2 prose-h2:mb-4
                                                       prose-h3:text-base prose-h3:mt-6 prose-h3:mb-2
                                                       prose-p:text-slate-300 prose-p:leading-relaxed
                                                       prose-strong:text-emerald-300
                                                       prose-ul:text-slate-300 prose-ol:text-slate-300
                                                       prose-li:marker:text-emerald-500
                                                       prose-code:text-cyan-300 prose-code:bg-slate-700/50 prose-code:px-1 prose-code:py-0.5 prose-code:rounded prose-code:text-[13px]
                                                       prose-hr:border-slate-700" />
                                        </div>
                                        <div class="flex items-center justify-end gap-3 px-6 py-3 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/60">
                                            <button wire:click="$set('showReviewPreview', false)"
                                                    class="px-4 py-2 text-xs font-medium text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-slate-700/50 hover:bg-gray-200 dark:hover:bg-slate-700 rounded-lg transition-all">
                                                Cerrar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- STEP 5: Publicar --}}
                    @if($currentStep === 5)
                        <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg p-5 space-y-4">
                            <div class="flex items-center gap-2 pb-3 border-b border-gray-200 dark:border-slate-700">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold">5</span>
                                <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Publicar Lección</h2>
                            </div>

                            {{-- ═══ Estados de publicación ═══ --}}
                            <div class="bg-amber-500/5 border border-amber-500/10 rounded-lg p-3 space-y-2">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-amber-400">Estados de publicación</span>
                                </div>

                                {{-- Flow visual --}}
                                <div class="flex items-center gap-1.5 text-[11px]">
                                    <span class="px-2 py-0.5 rounded bg-slate-700/60 text-slate-400 border border-slate-600/50 font-medium whitespace-nowrap">📋 Borrador</span>
                                    <svg class="w-3 h-3 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>

                                    @if(blank($publishAt))
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-400 border border-emerald-500/25 font-medium ring-1 ring-emerald-500/30 whitespace-nowrap">🟢 Publicado</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-cyan-500/15 text-cyan-400 border border-cyan-500/25 font-medium ring-1 ring-cyan-500/30 whitespace-nowrap">⏰ Programado</span>
                                    @endif

                                    <span class="text-slate-600 mx-1">·</span>

                                    @if(blank($publishAt))
                                        <span class="text-amber-400/70">{{-- El estado "Programado" aparece si completas la fecha --}}o programa con fecha</span>
                                    @else
                                        <span class="text-slate-500">Publicado</span>
                                    @endif
                                </div>

                                {{-- Glosario rápido de estados --}}
                                <div class="grid grid-cols-3 gap-1.5 pt-0.5">
                                    <div class="text-center px-1.5 py-1 rounded bg-slate-800/40 border border-slate-700/40">
                                        <p class="text-[10px] font-semibold text-slate-400">📋 Borrador</p>
                                        <p class="text-[9px] text-slate-500 leading-tight">No visible</p>
                                    </div>
                                    <div class="text-center px-1.5 py-1 rounded @if(blank($publishAt)) bg-emerald-500/8 border border-emerald-500/15 @else bg-slate-800/40 border border-slate-700/40 @endif">
                                        <p class="text-[10px] font-semibold @if(blank($publishAt)) text-emerald-400 @else text-slate-400 @endif">🟢 Publicado</p>
                                        <p class="text-[9px] @if(blank($publishAt)) text-emerald-400/60 @else text-slate-500 @endif leading-tight">Visible ahora</p>
                                    </div>
                                    <div class="text-center px-1.5 py-1 rounded @if(!blank($publishAt)) bg-cyan-500/8 border border-cyan-500/15 @else bg-slate-800/40 border border-slate-700/40 @endif">
                                        <p class="text-[10px] font-semibold @if(!blank($publishAt)) text-cyan-400 @else text-slate-400 @endif">⏰ Programado</p>
                                        <p class="text-[9px] @if(!blank($publishAt)) text-cyan-400/60 @else text-slate-500 @endif leading-tight">Pub. automática</p>
                                    </div>
                                </div>

                                {{-- Mensaje contextual según acción --}}
                                <p class="text-[11px] text-amber-400/70 leading-relaxed">
                                    @auth
                                        @if(auth()->user()->isPlanner)
                                            {{-- Mensaje para Planner/Admin --}}
                                            @if(blank($publishAt))
                                                Al publicar <strong class="text-amber-300">sin fecha</strong>, la lección será <strong class="text-emerald-400">visible para los estudiantes</strong> inmediatamente.
                                            @else
                                                Al publicar, la lección será <strong class="text-emerald-400">visible para los estudiantes</strong> y la programación planificada se aplicará el <strong class="text-cyan-400">{{ \Carbon\Carbon::parse($publishAt)->format('d/m/Y H:i') }}</strong>.
                                            @endif
                                        @else
                                            {{-- Mensaje para Profesor --}}
                                            @if(blank($publishAt))
                                                Al programar con una fecha, la lección quedará <strong class="text-amber-300">pendiente de aprobación</strong> por Planificación.
                                            @else
                                                La lección se programará para el <strong class="text-cyan-400">{{ \Carbon\Carbon::parse($publishAt)->format('d/m/Y H:i') }}</strong> y quedará <strong class="text-amber-300">pendiente de aprobación</strong> por Planificación.
                                            @endif
                                        @endif
                                    @endauth
                                    Si aún no está lista, usa el botón flotante <strong class="text-blue-400">Guardar</strong> para mantenerla en borrador.
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <label class="text-sm text-gray-600 dark:text-slate-300">Programar publicación:</label>
                                <input wire:model="publishAt" type="datetime-local"
                                       class="bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-1.5 text-sm text-gray-900 dark:text-slate-200 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 focus:outline-none"/>
                            </div>

                            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-slate-300 cursor-pointer">
                                <input wire:model="allowDownloads" type="checkbox"
                                       class="rounded border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-500"/>
                                Permitir descarga de recursos
                            </label>

                            <div class="bg-gray-50 dark:bg-slate-900/30 rounded-lg p-3 border border-gray-200 dark:border-slate-700/50">
                                <p class="text-xs text-gray-500 dark:text-slate-400">
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ count($this->previewSections) }}</span> secciones visibles ·
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ collect($this->previewSections)->sum(fn($s) => count($s['contents'])) }}</span> bloques de contenido ·
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ count($wizardResources) }}</span> recursos ·
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ count($wizardHtmlEmbeds) }}</span> embeds ·
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ count($wizardLinks) }}</span> enlaces
                                </p>
                            </div>


                            @php $isPlanner = auth()->user()->isPlanner; @endphp

                            @if($isPlanner)
                                {{-- Planner/Admin: botón "Publicar lección" (comportamiento actual) --}}
                                <button wire:click="confirmPublish"
                                        wire:loading.attr="disabled"
                                        class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 disabled:bg-gray-200 dark:disabled:bg-slate-700 disabled:text-gray-400 dark:disabled:text-slate-500 text-white text-sm font-bold rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                                    <svg wire:loading wire:target="confirmPublish" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span wire:loading.remove wire:target="confirmPublish">Publicar lección</span>
                                    <span wire:loading wire:target="confirmPublish">Publicando…</span>
                                </button>
                            @else
                                {{-- Profesor: botón "Programar lección" --}}
                                <button wire:click="confirmPublish"
                                        wire:loading.attr="disabled"
                                        @if(blank($publishAt)) disabled @endif
                                        class="w-full py-2 @if(blank($publishAt)) bg-gray-300 dark:bg-slate-700 text-gray-500 dark:text-slate-500 cursor-not-allowed @else bg-amber-600 hover:bg-amber-500 text-white @endif disabled:bg-gray-200 dark:disabled:bg-slate-700 disabled:text-gray-400 dark:disabled:text-slate-500 text-sm font-bold rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 @if(!blank($publishAt)) text-amber-200 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span wire:loading.remove wire:target="confirmPublish">
                                        @if(blank($publishAt))
                                            Programar lección
                                        @else
                                            Programar lección
                                        @endif
                                    </span>
                                    <span wire:loading wire:target="confirmPublish">Programando…</span>
                                </button>
                                @if(blank($publishAt))
                                    <p class="text-[10px] text-amber-400 text-center -mt-1">
                                        Establece una fecha de programación primero
                                    </p>
                                @endif
                            @endif
                        </div>
                    @endif

                </div>

            </div>

            {{-- Botón flotante: abrir preview full-screen --}}
            {{-- <button wire:click="$set('showFullPreview', true)"
                    class="hidden lg:flex items-center gap-1.5 text-xs text-slate-400 hover:text-emerald-400 transition-all bg-slate-800/90 border border-slate-700 rounded-l-xl pl-3 pr-2.5 py-2 fixed right-0 top-1/2 -translate-y-1/2 z-40 cursor-pointer"
                    title="Vista previa">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                </svg>
                <span>Previa</span>
            </button> --}}

            {{-- ═══════════ MODAL VISTA PREVIA (full-screen) ═══════════ --}}
            @if($showFullPreview)
                <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="full-preview-modal">
                    <div class="fixed inset-0 bg-slate-900/95 backdrop-blur-md"
                         wire:click="$set('showFullPreview', false)"></div>

                    <div class="relative min-h-screen flex items-start justify-center p-4 pt-10">
                        <div class="relative w-full max-w-5xl bg-slate-800 border border-slate-700 rounded-lg shadow-2xl overflow-hidden">

                            {{-- Header --}}
                            <div class="flex items-center justify-between px-6 py-2 bg-slate-700/50 border-b border-slate-700">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <div>
                                        <h2 class="text-sm font-bold text-white uppercase tracking-wider">Vista Previa</h2>
                                        <p class="text-[11px] text-slate-400">Así se verá la lección al publicarse</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-700 text-slate-300 border border-slate-600/50 ml-auto">
                                    {{ count($this->previewSections) }} secciones
                                </span>
                                <button wire:click="$set('showFullPreview', false)"
                                        class="p-2 hover:bg-white/10 rounded-lg transition-all text-slate-400 hover:text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Body: 2 columnas --}}
                            <div class="flex flex-1 overflow-hidden min-h-0" x-data="tocNavigation()">
                                {{-- ═══ SIDEBAR TOC ═══ --}}
                                <aside class="hidden lg:block w-56 shrink-0 border-r border-slate-700/50 bg-slate-800/80 overflow-y-auto p-3 sticky top-0 self-start max-h-[calc(100vh-12rem)]">
                                    <div class="flex items-center gap-1.5 mb-2 px-1">
                                        <span class="text-xs text-slate-400">📑</span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Índice</span>
                                        <span class="ml-auto text-[10px] font-mono text-slate-600">{{ count($this->previewSections) }} sec.</span>
                                    </div>
                                    <div class="space-y-0.5">
                                        @foreach($this->previewSections as $sIdx => $section)
                                            @php
                                                $hasContent = !empty(array_filter($section['contents'] ?? [], fn($c) => !empty($c['body'])));
                                            @endphp
                                            <button @click="scrollTo({{ $sIdx }})"
                                                    :class="activeSection === {{ $sIdx }} ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-700/40 border-transparent'"
                                                    class="w-full flex items-center gap-2 px-2.5 py-2 rounded-lg text-left transition-all text-xs border">
                                                <span class="flex items-center justify-center w-5 h-5 rounded {{ $hasContent ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-700/60 text-slate-500' }} text-[10px] font-mono shrink-0">
                                                    {{ str_pad($sIdx + 1, 2, '0', STR_PAD_LEFT) }}
                                                </span>
                                                <span class="truncate flex-1">{{ $section['title'] }}</span>
                                                <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $hasContent ? 'bg-emerald-400/60' : 'bg-slate-600/40' }}"></span>
                                            </button>
                                        @endforeach
                                    </div>
                                </aside>

                                {{-- ═══ CONTENIDO ═══ --}}
                                <div class="flex-1 overflow-y-auto p-6 space-y-5" x-ref="contentArea">
                                {{-- Header preview --}}
                                <div class="border-b border-slate-700 pb-4">
                                    <p class="text-xs font-semibold text-emerald-400 uppercase tracking-wider mb-1">
                                        {{ $selectedActivity?->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura' }}
                                    </p>
                                    <h2 class="text-lg font-bold text-white">{{ $lessonTitle ?: 'Título de la lección' }}</h2>
                                    @if($lessonDescription)
                                        <p class="text-sm text-slate-400 mt-1">{{ $lessonDescription }}</p>
                                    @endif
                                    @if($selectedActivity)
                                        <p class="text-xs text-slate-500 mt-2">
                                            {{ \Carbon\Carbon::parse($selectedActivity->finicial)->format('d/m/Y') }} &mdash; {{ \Carbon\Carbon::parse($selectedActivity->ffinal)->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Secciones --}}
                                @forelse($this->previewSections as $sIdx => $section)
                                    <div data-section-index="{{ $sIdx }}" x-data="{ expanded: true }" class="space-y-3 scroll-mt-20">
                                        <div class="flex items-center gap-2">
                                            <button @click="expanded = !expanded"
                                                    class="p-1 -ml-1 rounded-lg hover:bg-slate-700/50 text-slate-500 hover:text-slate-300 transition-all"
                                                    :class="expanded ? 'rotate-90' : ''">
                                                <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </button>
                                            <span class="w-1 h-5 bg-emerald-500 rounded-full shrink-0"></span>
                                            <h3 class="text-base font-bold text-slate-100">{{ $section['title'] }}</h3>
                                            <span class="text-[10px] text-slate-600 font-mono ml-auto">
                                                {{ count($section['contents'] ?? []) }} bloques
                                            </span>
                                        </div>
                                        <div x-show="expanded" x-transition:enter.duration.200 x-collapse>
                                        @foreach($section['contents'] as $content)
                                            @php
                                                $rawBody = $content['body'] ?? '';
                                                $isMermaid = preg_match('/class="[^"]*\bmermaid\b[^"]*"/', $rawBody) === 1;
                                                if (!$isMermaid) {
                                                    $isMermaid = preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/m', trim($rawBody)) === 1;
                                                }
                                            @endphp
                                            @if($content['title'])
                                                <h4 class="text-sm font-semibold text-slate-300">{{ $content['title'] }}</h4>
                                            @endif
                                            @if($isMermaid)
                                                @php
                                                    preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $rawBody, $m);
                                                    $mermaidCode = trim(strip_tags($m[1] ?? ''));
                                                    if (empty($mermaidCode)) {
                                                        $mermaidCode = trim(strip_tags($rawBody));
                                                    }
                                                @endphp
                                                <div wire:ignore x-data="mermaidEmbed()"
                                                     data-mermaid-code="{{ $mermaidCode }}"
                                                     class="w-full bg-slate-800 rounded-lg p-4 overflow-x-auto border border-slate-700/50">
                                                    <div x-ref="target" class="w-full"></div>
                                                </div>
                                            @else
                                                <x-lms.math-text
                                                :content="$this->renderContentBody($rawBody)"
                                                class="text-sm text-slate-400 leading-relaxed prose prose-invert prose-sm max-w-none" />
                                            @endif
                                        @endforeach

                                        {{-- Recursos vinculados a esta sección --}}
                                        @php
                                            $secResources = collect($wizardResources)->where('section_id', $section['id'])->values()->all();
                                            $secLinks = collect($wizardLinks)->where('section_id', $section['id'])->values()->all();
                                            $secEmbeds = collect($wizardHtmlEmbeds)->where('section_id', $section['id'])->values()->all();
                                            $hasSecResources = count($secResources) > 0 || count($secLinks) > 0 || count($secEmbeds) > 0;
                                        @endphp
                                        @if($hasSecResources)
                                            <div class="border-t border-slate-700/40 pt-3 mt-2 space-y-2">
                                                @foreach($secResources as $res)
                                                    <div class="flex items-center gap-3 px-3 py-2 bg-slate-700/30 border border-slate-700/20 rounded-lg">
                                                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span class="text-sm text-slate-300 truncate flex-1">{{ $res['display_name'] }}</span>
                                                        <span class="text-xs text-slate-500">{{ $res['media']['size_for_humans'] ?? '' }}</span>
                                                    </div>
                                                @endforeach
                                                @foreach($secLinks as $link)
                                                    <div class="flex items-center gap-3 px-3 py-2 bg-slate-700/30 border border-slate-700/20 rounded-lg">
                                                        <svg class="w-4 h-4 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                        <span class="text-sm text-slate-300 truncate flex-1">{{ $link['title'] }}</span>
                                                        <span class="text-xs text-slate-500">({{ $link['link_type'] }})</span>
                                                    </div>
                                                @endforeach
                                                @foreach($secEmbeds as $embed)
                                                    <div class="flex items-center gap-3 px-3 py-2 bg-slate-700/30 border border-slate-700/20 rounded-lg">
                                                        <svg class="w-4 h-4 text-fuchsia-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                                        </svg>
                                                        <span class="text-sm text-slate-300 truncate flex-1">{{ $embed['title'] ?? 'Embed HTML' }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <svg class="w-16 h-16 text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <p class="text-sm font-medium text-slate-500">No hay contenido disponible</p>
                                        <p class="text-xs text-slate-600 mt-1">Agrega secciones y contenido en el paso 2 del asistente.</p>
                                    </div>
                                @endforelse

                                {{-- Recursos no vinculados (aparecen al final) --}}
                                @php
                                    $unlinkedResources = collect($wizardResources)->filter(fn($r) => empty($r['section_id']))->values()->all();
                                    $unlinkedLinks = collect($wizardLinks)->filter(fn($l) => empty($l['section_id']))->values()->all();
                                    $unlinkedEmbeds = collect($wizardHtmlEmbeds)->filter(fn($e) => empty($e['section_id']))->values()->all();
                                @endphp
                                @if(count($unlinkedResources) > 0)
                                    <div class="border-t border-slate-700 pt-4 space-y-2">
                                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            Recursos
                                        </h4>
                                        @foreach($unlinkedResources as $res)
                                            <div class="flex items-center gap-3 px-3 py-2 bg-slate-700/30 rounded-lg">
                                                <span class="text-sm text-slate-300">{{ $res['display_name'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @if(count($unlinkedLinks) > 0)
                                    <div class="border-t border-slate-700 pt-4 space-y-2">
                                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            Enlaces
                                        </h4>
                                        @foreach($unlinkedLinks as $link)
                                            <div class="flex items-center gap-3 px-3 py-2 bg-slate-700/30 rounded-lg">
                                                <span class="text-sm text-slate-300">{{ $link['title'] }}</span>
                                                <span class="text-xs text-slate-500">({{ $link['link_type'] }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @if(count($unlinkedEmbeds) > 0)
                                    <div class="border-t border-slate-700 pt-4 space-y-2">
                                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                            </svg>
                                            HTML Embeds
                                        </h4>
                                        @foreach($unlinkedEmbeds as $embed)
                                            <div class="flex items-center gap-3 px-3 py-2 bg-slate-700/30 rounded-lg">
                                                <span class="text-sm text-slate-300">{{ $embed['title'] ?? 'Embed HTML' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div> {{-- Cierra content area --}}
                            </div> {{-- Cierra flex container --}}
                        </div>
                    </div>
                </div>

                {{-- Botón TOC flotante (mobile) --}}
                <div x-data="{ mobileTocOpen: false }"
                     x-init="$watch('mobileTocOpen', val => document.body.classList.toggle('overflow-hidden', val))">
                    {{-- Floating button --}}
                    <button @click="mobileTocOpen = true"
                            class="lg:hidden fixed bottom-6 right-6 z-50 w-12 h-12 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white shadow-xl shadow-emerald-900/30 flex items-center justify-center transition-all active:scale-90">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    {{-- Overlay TOC --}}
                    <div x-show="mobileTocOpen" x-cloak
                         x-transition:enter.duration.200
                         class="lg:hidden fixed inset-0 z-[9999]">
                        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="mobileTocOpen = false"></div>
                        <div class="absolute bottom-0 left-0 right-0 bg-slate-800 border-t border-slate-700 rounded-t-2xl p-4 max-h-[50vh] overflow-y-auto">
                            <div class="flex items-center justify-between mb-2 px-1">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Índice</span>
                                <button @click="mobileTocOpen = false" class="p-1.5 text-slate-500 hover:text-white rounded-lg hover:bg-slate-700/50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="space-y-0.5">
                                @foreach($this->previewSections as $sIdx => $section)
                                    @php
                                        $hasContent = !empty(array_filter($section['contents'] ?? [], fn($c) => !empty($c['body'])));
                                    @endphp
                                    <button @click="mobileTocOpen = false; document.querySelector('[x-data=\'tocNavigation()\']')?.__x.$data.scrollTo({{ $sIdx }})"
                                            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left transition-all text-sm hover:bg-slate-700/50">
                                        <span class="flex items-center justify-center w-6 h-6 rounded {{ $hasContent ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-700/60 text-slate-500' }} text-xs font-mono shrink-0">
                                            {{ $sIdx + 1 }}
                                        </span>
                                        <span class="text-slate-300 font-medium truncate">{{ $section['title'] }}</span>
                                        <span class="w-1.5 h-1.5 rounded-full shrink-0 ml-auto {{ $hasContent ? 'bg-emerald-400/60' : 'bg-slate-600/40' }}"></span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ═══════════ MODAL VISTA ESTUDIANTE (wizard → unificado) ═══════════ --}}
            {{-- openWizardStudentPreview() normaliza los datos del wizard y activa el componente unificado --}}

            {{-- ═══════════ MODAL CONFIRMACIÓN PUBLICAR SIN FECHA ═══════════ --}}
            @if($showPublishConfirm)
                <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="publish-confirm-modal">
                    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm"></div>
                    <div class="relative min-h-screen flex items-center justify-center p-4">
                        <div class="relative w-full max-w-md bg-gray-900 border border-slate-700 rounded-lg shadow-2xl overflow-hidden">
                            <div class="p-6 space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Sin fecha de publicación</h3>
                                        <p class="text-xs text-slate-400 mt-1">
                                            No has establecido una fecha de publicación. La lección se guardará como borrador.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-2">
                                    <button wire:click="$set('showPublishConfirm', false)"
                                            class="px-4 py-2 text-sm text-slate-400 hover:text-white transition-colors">
                                        Cancelar
                                    </button>
                                    <button wire:click="saveAndPublish"
                                            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-lg transition-all">
                                        Guardar de todas formas
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ═══ BOTONES FLOTANTES: Vista estudiante + Guardar (group button) ═══ --}}
            <div class="fixed bottom-6 right-6 z-50 flex">
                <button wire:click="openWizardStudentPreview"
                        title="Vista estudiante"
                        class="inline-flex items-center justify-center w-11 h-11 rounded-l-xl text-sm font-semibold transition-all duration-200
                               text-fuchsia-300 bg-fuchsia-500/10 hover:bg-fuchsia-500/20
                               border border-fuchsia-500/20 hover:border-fuchsia-500/40
                               active:scale-[0.95]
                               border-r-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>

                <button wire:click="saveStep2"
                        wire:loading.attr="disabled"
                        wire:target="saveStep2"
                        title="Guardar lección"
                        class="inline-flex items-center justify-center w-11 h-11 rounded-r-xl text-sm font-semibold transition-all duration-200
                               shadow-lg shadow-blue-500/20
                               text-white bg-gradient-to-br from-blue-500 to-blue-600
                               hover:from-blue-400 hover:to-blue-500
                               active:scale-[0.95] active:shadow-blue-500/30
                               disabled:opacity-60 disabled:cursor-not-allowed disabled:active:scale-100
                               border border-blue-400/30">
                    <svg wire:loading wire:target="saveStep2" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <svg wire:loading.remove wire:target="saveStep2" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                </button>
            </div>

        @endif
    </div>
</div>
    @endif

    {{-- ═══════════ MODAL VISTA ESTUDIANTE (componente unificado) ═══════════ --}}
    @if($showListStudentPreview && $listPreviewData)
        <x-lms.student-preview :preview="$listPreviewData" closeMethod="closeListStudentPreview" />
    @endif

</div>

{{-- ═══ Mermaid.js — bundled via Vite (resources/js/app.js) ═══ --}}
@assets
    <style>
        .student-preview-modal:fullscreen {
            background: #f1f5f9;
            overflow-y: auto;
        }
        .student-preview-modal:fullscreen > .bg-black\/80 {
            opacity: 0;
            pointer-events: none;
        }
        /* ── Slide Preview: Enhanced Styling (shared: preview tab + student modal) ── */
        .slide-preview-wrapper,
        .slide-block {
            font-size: 0.9375rem;
            line-height: 1.75;
            color: #1e293b;
        }
        /* Card: applies to both .slide-block standalone and .slide-preview-wrapper > .slide-block */
        .slide-block {
            margin-bottom: 1.25rem;
            padding: 1.25rem 1.5rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.625rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.04);
            transition: box-shadow 0.2s ease;
        }
        .slide-block:last-child {
            margin-bottom: 0;
        }
        .slide-block:hover {
            box-shadow: 0 2px 8px 0 rgb(0 0 0 / 0.06);
        }
        .slide-block-even {
            background: #ffffff;
        }
        .slide-block-odd {
            background: #fafbfc;
        }
        /* Content styling within any slide container */
        .slide-preview-wrapper h2,
        .slide-block h2 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 0.75rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
            letter-spacing: -0.01em;
        }
        .slide-preview-wrapper h3,
        .slide-block h3 {
            font-size: 1.05rem;
            font-weight: 600;
            color: #1e293b;
            margin: 1rem 0 0.5rem 0;
        }
        .slide-preview-wrapper h4,
        .slide-block h4 {
            font-size: 0.975rem;
            font-weight: 600;
            color: #334155;
            margin: 0.75rem 0 0.4rem 0;
        }
        .slide-preview-wrapper p,
        .slide-block p {
            margin: 0 0 0.75rem 0;
            color: #334155;
        }
        .slide-preview-wrapper p:last-child,
        .slide-block p:last-child {
            margin-bottom: 0;
        }
        .slide-preview-wrapper ul,
        .slide-preview-wrapper ol,
        .slide-block ul,
        .slide-block ol {
            margin: 0.5rem 0 0.75rem 0;
            padding-left: 1.5rem;
        }
        .slide-preview-wrapper li,
        .slide-block li {
            margin-bottom: 0.3rem;
            color: #334155;
        }
        .slide-preview-wrapper li::marker,
        .slide-block li::marker {
            color: #14b8a6;
        }
        .slide-preview-wrapper table,
        .slide-block table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0.75rem 0;
            font-size: 0.875rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .slide-preview-wrapper thead,
        .slide-block thead {
            background: #f1f5f9;
        }
        .slide-preview-wrapper th,
        .slide-block th {
            padding: 0.625rem 0.875rem;
            font-weight: 600;
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        .slide-preview-wrapper td,
        .slide-block td {
            padding: 0.625rem 0.875rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }
        .slide-preview-wrapper tbody tr:last-child td,
        .slide-block tbody tr:last-child td {
            border-bottom: none;
        }
        .slide-preview-wrapper tbody tr:nth-child(even),
        .slide-block tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .slide-preview-wrapper tbody tr:hover,
        .slide-block tbody tr:hover {
            background: #f1f5f9;
        }
        /* Bold / Italic */
        .slide-preview-wrapper strong,
        .slide-block strong {
            font-weight: 600;
            color: #0f172a;
        }
        .slide-preview-wrapper em,
        .slide-block em {
            font-style: italic;
            color: #475569;
        }
        /* Inline code */
        .slide-preview-wrapper code,
        .slide-block code {
            background: #f1f5f9;
            padding: 0.15rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.8125em;
            color: #be185d;
            border: 1px solid #e2e8f0;
        }
        /* Blockquotes */
        .slide-preview-wrapper blockquote,
        .slide-block blockquote {
            border-left: 3px solid #14b8a6;
            padding: 0.5rem 1rem;
            margin: 0.75rem 0;
            background: #f0fdfa;
            border-radius: 0 0.375rem 0.375rem 0;
            color: #475569;
        }
        /* Horizontal rule */
        .slide-preview-wrapper hr,
        .slide-block hr {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 1rem 0;
        }

        /* Mermaid zoom & fullscreen */
        .mermaid-zoom-toolbar {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        [x-data="mermaidEmbed()"]:fullscreen {
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow: auto;
        }
        [x-data="mermaidEmbed()"]:fullscreen .mermaid-zoom-toolbar {
            opacity: 1 !important;
            position: fixed;
            top: 1rem;
            right: 1rem;
        }
        .zoom-act {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        /* ── Review Questions: cards for each Q&A pair ── */
        .review-questions {
            counter-reset: rq-counter;
        }
        .review-questions h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #d97706;
            margin: 1.5rem 0 0.75rem 0;
            padding: 0.5rem 0.75rem;
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            border-radius: 0 0.5rem 0.5rem 0;
        }
        .review-questions h3:first-child {
            margin-top: 0;
        }
        .review-questions ol,
        .review-questions ul {
            list-style: none;
            padding: 0;
            margin: 0 0 1rem 0;
        }
        .review-questions ol > li,
        .review-questions ul > li {
            counter-increment: rq-counter;
            position: relative;
            padding: 1rem 1rem 1rem 3rem;
            margin-bottom: 0.75rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.625rem;
            box-shadow: 0 1px 2px rgb(0 0 0 / 0.04);
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
            color: #334155;
            font-size: 0.9375rem;
            line-height: 1.7;
        }
        .review-questions ol > li:hover,
        .review-questions ul > li:hover {
            border-color: #fcd34d;
            box-shadow: 0 2px 8px rgb(251 191 36 / 0.1);
        }
        .review-questions ol > li::before,
        .review-questions ul > li::before {
            content: counter(rq-counter);
            position: absolute;
            left: 0.625rem;
            top: 0.875rem;
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f59e0b;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 999px;
            line-height: 1;
        }
        .review-questions ul > li::before {
            content: '';
            width: 0.625rem;
            height: 0.625rem;
            background: #f59e0b;
            top: 1.15rem;
            left: 1.125rem;
        }
        .review-questions ol > li strong,
        .review-questions ul > li strong {
            display: block;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.25rem;
            font-size: 0.975rem;
        }
        .review-questions ol > li em,
        .review-questions ul > li em {
            color: #64748b;
            font-style: italic;
        }
        .review-questions ol > li > p,
        .review-questions ul > li > p {
            margin: 0;
        }
        .review-questions ol > li > p:first-of-type,
        .review-questions ul > li > p:first-of-type {
            display: inline;
        }
        .review-questions ol > li > *:last-child,
        .review-questions ul > li > *:last-child {
            margin-bottom: 0;
        }
    </style>
@endassets
@script
<script>
    Alpine.data('tocNavigation', () => ({
        activeSection: 0,
        observer: null,

        init() {
            this.$nextTick(() => {
                const sections = this.$el.querySelectorAll('[data-section-index]');
                if (!sections.length) return;

                this.observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.activeSection = parseInt(entry.target.dataset.sectionIndex);
                        }
                    });
                }, { rootMargin: '-80px 0px -60% 0px' });

                sections.forEach(el => this.observer.observe(el));
            });
        },

        scrollTo(index) {
            const el = this.$el.querySelector(`[data-section-index="${index}"]`);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                this.activeSection = index;
            }
        },

        destroy() {
            if (this.observer) {
                this.observer.disconnect();
            }
        }
    }));
</script>
@endscript
