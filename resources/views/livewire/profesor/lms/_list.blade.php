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
                    $isPublished = $pub?->status === 'PUBLISHED';
                @endphp
                <div wire:key="activity-card-{{ $item->id }}"
                     class="relative {{ $isPublished ? 'bg-emerald-200 dark:bg-emerald-950' : 'bg-white dark:bg-slate-800/40' }} border border-gray-200 dark:border-slate-700/60 rounded-lg overflow-hidden mt-2
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
                                        class="p-2 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/20 transition-all duration-200" @disabled($isPublished)>
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
                            $isPublished = $pub?->status === 'PUBLISHED';
                        @endphp
                        <tr wire:key="activity-row-{{ $item->id }}"
                            class="border-b border-gray-100 dark:border-slate-800/60 hover:bg-gray-50 dark:hover:bg-slate-800/30 transition-colors duration-150
                                   {{ $isPublished ? 'bg-emerald-200 dark:bg-emerald-950' : ($hasLmsContent ? 'bg-emerald-500/[0.02]' : '') }}">
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
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/20 transition-all duration-200" @disabled($isPublished)>
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
                                        class="inline-flex items-center gap-1.5 px-5 py-1.5 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 border border-emerald-500/50 transition-all disabled:opacity-50 disabled:cursor-not-allowed" @disabled($isPublished)>
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
                                        class="inline-flex items-center gap-1.5 px-5 py-1.5 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 border border-blue-500/50 transition-all disabled:opacity-50 disabled:cursor-not-allowed" @disabled($isPublished)>
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
