<div class="w-full mx-auto py-2 px-4 space-y-6">

    @if($mode === 'list')
        {{-- ═══════════ LISTADO DE ACTIVIDADES ═══════════ --}}
        <div wire:key="mode-list">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">Nueva Lección</h1>
                <p class="text-sm text-slate-400 mt-1">Selecciona una actividad para crear su contenido LMS</p>
            </div>
            <a href="{{ route('app.profesors.lms.editor', 0) }}"
               class="text-xs text-slate-400 hover:text-emerald-400 transition-colors"
               onclick="event.preventDefault()"
               style="display:none;">
            </a>
        </div>

        {{-- Filtros --}}
        <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1 block">Lapso</label>
                    <select wire:model.live="lapsoId"
                            class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none appearance-none">
                        <option value="">Todos</option>
                        @foreach($listLapso as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1 block">P.Estudio</label>
                    <select wire:model.live="pestudioId"
                            class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none appearance-none">
                        <option value="">Todos</option>
                        @foreach($listPestudio as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1 block">Grado</label>
                    <select wire:model.live="gradoId"
                            class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none appearance-none">
                        <option value="">Todos</option>
                        @foreach($listGrado as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1 block">Sección</label>
                    <select wire:model.live="seccionId"
                            class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none appearance-none">
                        <option value="">Todos</option>
                        @foreach($listSeccion as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1 block">Buscar</label>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Tema, descripción…"
                           class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none"/>
                </div>
            </div>
        </div>

        {{-- Grid de actividades --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($activities as $item)
                @php
                    $pub = $item->lmsPublication;
                    $sections = $item->lmsSections ?? collect();
                    $resources = $item->lmsResources ?? collect();
                    $links = $item->lmsLinks ?? collect();
                    $totalContents = $sections->sum(fn($s) => $s->contents_count ?? 0);
                    $hasLmsContent = $sections->isNotEmpty() || $resources->isNotEmpty() || $links->isNotEmpty() || !is_null($pub);
                @endphp
                <div wire:key="activity-card-{{ $item->id }}" class="bg-slate-800/50 border border-slate-700 rounded-xl overflow-hidden hover:border-slate-600 transition-all duration-200 group {{ $hasLmsContent ? 'ring-1 ring-emerald-500/10' : '' }}">
                    {{-- Cabecera de la card --}}
                    <div class="p-4 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold inline-flex items-center gap-1
                                {{ !$pub ? 'bg-slate-700 text-slate-400' : match($pub->status) {
                                    'PUBLISHED' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                                    'SCHEDULED' => 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20',
                                    'ARCHIVED' => 'bg-slate-700 text-slate-400',
                                    default => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                                } }}">
                                @if($pub?->status === 'PUBLISHED')
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                @elseif($pub?->status === 'SCHEDULED')
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                @endif
                                {{ $pub ? match($pub->status) { 'PUBLISHED' => 'Publicado', 'SCHEDULED' => 'Programado', 'ARCHIVED' => 'Archivado', default => 'Borrador' } : 'Sin LMS' }}
                            </span>
                            <span class="text-[11px] text-slate-500 font-mono shrink-0">
                                {{ \Carbon\Carbon::parse($item->finicial)->format('d/m') }} — {{ \Carbon\Carbon::parse($item->ffinal)->format('d/m') }}
                            </span>
                        </div>

                        <h3 class="text-sm font-semibold text-white leading-snug">
                            {{ $item->topic ?? 'Actividad sin título' }}
                        </h3>

                        <div class="text-xs text-slate-400 space-y-0.5">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                {{ $item->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ $item->pevaluacion?->pensum?->grado?->name ?? '—' }} Sec. {{ $item->pevaluacion?->seccion?->name ?? '—' }}
                            </div>
                        </div>

                        @if($item->description)
                            <p class="text-xs text-slate-500 line-clamp-2">{{ $item->description }}</p>
                        @endif

                        {{-- Indicadores LMS --}}
                        <div class="flex flex-wrap items-center gap-2 pt-1">
                            @if($hasLmsContent)
                                <div class="flex items-center gap-1.5 text-[10px] text-emerald-400/70 font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    <span>{{ $sections->count() }} sec.</span>
                                    <span class="text-slate-600">·</span>
                                    <span>{{ $totalContents }} cont.</span>
                                </div>
                                @if($resources->count() > 0)
                                    <span class="inline-flex items-center gap-0.5 text-[10px] text-blue-400/70">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $resources->count() }}
                                    </span>
                                @endif
                                @if($links->count() > 0)
                                    <span class="inline-flex items-center gap-0.5 text-[10px] text-cyan-400/70">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        {{ $links->count() }}
                                    </span>
                                @endif
                                @if($pub?->published_at)
                                    <span class="text-[10px] text-slate-600 ml-auto">
                                        {{ \Carbon\Carbon::parse($pub->published_at)->format('d/m/Y') }}
                                    </span>
                                @endif
                            @else
                                <span class="text-[10px] text-slate-600 italic">Sin contenido LMS</span>
                            @endif
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="px-4 py-3 bg-slate-900/30 border-t border-slate-700/50 flex items-center gap-1.5">
                        <button wire:click="showDetails({{ $item->id }})"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-medium
                                       bg-slate-700/50 text-slate-300 hover:bg-slate-700 border border-slate-600/50 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Detalle
                        </button>
                        <a href="{{ route('app.profesors.lms.lesson.wizard') }}?activity_id={{ $item->id }}"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-medium
                                       {{ $hasLmsContent ? 'bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 border border-purple-500/20' : 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20' }} transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            {{ $hasLmsContent ? 'Editar' : 'Asistente' }}
                        </a>

                        {{-- Exportar (siempre visible) --}}
                        <button wire:click="showExport({{ $item->id }})"
                                title="Exportar contenido a otra sección"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-400 hover:bg-emerald-500/10 border border-transparent hover:border-emerald-500/20 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                        </button>

                        {{-- Importar (deshabilitado si ya tiene contenido LMS) --}}
                        <button wire:click="showImport({{ $item->id }})"
                                title="{{ $hasLmsContent ? 'Esta actividad ya tiene contenido LMS' : 'Importar contenido de otra sección' }}"
                                class="p-1.5 rounded-lg border border-transparent transition-all duration-200
                                       {{ $hasLmsContent ? 'text-slate-600 cursor-not-allowed' : 'text-slate-400 hover:text-blue-400 hover:bg-blue-500/10 hover:border-blue-500/20' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </button>

                        {{-- Vista estudiante (solo si hay contenido LMS) --}}
                        @if($hasLmsContent)
                            <button wire:click="openListStudentPreview({{ $item->id }})"
                                    title="Vista del estudiante"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-amber-400 hover:bg-amber-500/10 border border-transparent hover:border-amber-500/20 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>

                            {{-- Eliminar lección --}}
                            <button wire:click="confirmDeleteLesson({{ $item->id }})"
                                    title="Eliminar lección"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/20 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <svg class="w-16 h-16 text-slate-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm font-medium text-slate-400">No hay actividades disponibles</p>
                    <p class="text-xs text-slate-600 mt-1">Ajusta los filtros o crea una actividad primero.</p>
                </div>
            @endforelse
        </div>

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
                    <div class="relative w-full max-w-6xl bg-gray-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-white/5 bg-gray-800/50">
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
                                <div class="space-y-4" x-data="{ showTeaching: false }">
                                    {{-- Enseñanza with structure toggle --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500">Enseñanza / Actividad Globalizada</label>
                                            @if($detailActivity->hasTeachingStructure())
                                                <button @click="showTeaching = !showTeaching"
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10 transition-all duration-200"
                                                    x-text="showTeaching ? 'Ver completo' : 'Ver estructura'">
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Teaching full text (default visible) --}}
                                        <p class="text-sm text-gray-200 leading-relaxed" x-show="!showTeaching">{{ $detailActivity->teaching }}</p>

                                        {{-- Teaching structured view (toggle) --}}
                                        @if($detailActivity->hasTeachingStructure())
                                            @php $sections = $detailActivity->getTeachingSections(); @endphp
                                            <div x-show="showTeaching" x-cloak x-transition:enter.duration.200ms class="space-y-3">
                                                <div class="p-3 bg-cyan-500/5 border border-cyan-500/10 rounded-xl">
                                                    <div class="text-[10px] font-bold uppercase tracking-widest text-cyan-400 mb-1">INICIO</div>
                                                    <p class="text-sm text-gray-200">{{ $sections['INICIO'] ?? '' }}</p>
                                                </div>
                                                <div class="p-3 bg-emerald-500/5 border border-emerald-500/10 rounded-xl">
                                                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-400 mb-1">DESARROLLO</div>
                                                    <p class="text-sm text-gray-200">{{ $sections['DESARROLLO'] ?? '' }}</p>
                                                </div>
                                                <div class="p-3 bg-amber-500/5 border border-amber-500/10 rounded-xl">
                                                    <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400 mb-1">CIERRE</div>
                                                    <p class="text-sm text-gray-200">{{ $sections['CIERRE'] ?? '' }}</p>
                                                </div>
                                            </div>
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
                            <div class="p-4 bg-cyan-500/5 border border-cyan-500/10 rounded-xl">
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
                                <div class="flex items-center gap-2 mb-3">
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
                        <div class="px-6 py-3 border-t border-white/5 bg-gray-800/30 flex justify-end">
                            <button wire:click="closeDetails"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
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
                    <div class="relative w-full max-w-7xl bg-gray-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-white/5 bg-gray-800/50">
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
                                                class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none appearance-none">
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
                                        <div class="text-center py-10 bg-slate-800/30 rounded-xl border border-dashed border-slate-700">
                                            <svg class="w-10 h-10 text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <p class="text-sm text-slate-500">No hay actividades disponibles en esta sección</p>
                                        </div>
                                    @elseif(!$exportTargetSectionId)
                                        <div class="text-center py-10 bg-slate-800/30 rounded-xl border border-dashed border-slate-700">
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
                                                        class="w-full text-left p-4 rounded-xl border transition-all duration-200
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
                            <div class="flex items-center justify-end gap-2 px-6 py-3 border-t border-white/5 bg-gray-800/30">
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
                                    <h1 class="text-2xl font-bold text-slate-900">{{ $exportPreviewData['title'] }}</h1>
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
                                                <h3 class="text-sm font-semibold text-slate-700">{{ $content['title'] }}</h3>
                                            @endif
                                            <div class="text-sm text-slate-700 leading-relaxed prose prose-sm max-w-none">
                                                {!! nl2br(e($content['body'] ?? '')) !!}
                                            </div>
                                        @endforeach
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <svg class="w-16 h-16 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <p class="text-sm font-medium text-slate-500">No hay contenido disponible</p>
                                        <p class="text-xs text-slate-400 mt-1">Esta lección no tiene secciones visibles.</p>
                                    </div>
                                @endforelse

                                {{-- Recursos --}}
                                @if(count($exportPreviewData['resources'] ?? []) > 0)
                                    <div class="border-t border-slate-200 pt-5">
                                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-3">
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

                                {{-- Enlaces --}}
                                @if(count($exportPreviewData['links'] ?? []) > 0)
                                    <div class="border-t border-slate-200 pt-5">
                                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-3">
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
                            <div class="flex items-center justify-between px-8 py-4 bg-slate-100 border-t border-slate-200">
                                <p class="text-xs text-slate-400">
                                    <span class="font-medium">{{ count($exportPreviewData['sections']) }}</span> secciones ·
                                    <span class="font-medium">{{ collect($exportPreviewData['sections'])->sum(fn($s) => count($s['contents'])) }}</span> bloques ·
                                    <span class="font-medium">{{ count($exportPreviewData['resources'] ?? []) }}</span> recursos ·
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
                                    <div class="w-14 h-14 rounded-full bg-emerald-500/10 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-white">¿Exportar lección?</h3>
                                    <p class="text-xs text-slate-400 mt-1">El contenido LMS se copiará a la actividad seleccionada.</p>
                                </div>

                                {{-- Resumen origen --}}
                                <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50 space-y-2">
                                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Origen</h4>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-slate-300 font-medium">{{ $exportPreviewData['title'] ?? '—' }}</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-[11px] text-slate-400">
                                        <span>{{ count($exportPreviewData['sections'] ?? []) }} secciones</span>
                                        <span class="text-slate-600">·</span>
                                        <span>{{ count($exportPreviewData['resources'] ?? []) }} recursos</span>
                                        <span class="text-slate-600">·</span>
                                        <span>{{ count($exportPreviewData['links'] ?? []) }} enlaces</span>
                                    </div>
                                </div>

                                {{-- Resumen destino --}}
                                @php $selectedAct = collect($exportAvailableActivities)->firstWhere('id', $exportTargetActivityId); @endphp
                                <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50 space-y-2">
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
                                        <span class="text-slate-600">·</span>
                                        <span>{{ $selectedAct['grado'] ?? '—' }} {{ $selectedAct['seccion'] ?? '' }}</span>
                                        <span class="text-slate-600">·</span>
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
                            <div class="flex items-center justify-between gap-2 px-6 py-3 border-t border-white/5 bg-gray-800/30">
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
                    <div class="relative w-full max-w-7xl bg-gray-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-white/5 bg-gray-800/50">
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
                                            class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none appearance-none">
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
                                        <div class="text-center py-10 bg-slate-800/30 rounded-xl border border-dashed border-slate-700">
                                            <svg class="w-10 h-10 text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <p class="text-sm text-slate-500">No hay actividades disponibles en esta sección</p>
                                        </div>
                                    @elseif(!$importSourceSectionId)
                                        <div class="text-center py-10 bg-slate-800/30 rounded-xl border border-dashed border-slate-700">
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
                                                        class="w-full text-left p-4 rounded-xl border transition-all duration-200
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
                            <div class="flex items-center justify-end gap-2 px-6 py-3 border-t border-white/5 bg-gray-800/30">
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
                                    <h1 class="text-2xl font-bold text-slate-900">{{ $importPreviewData['title'] }}</h1>
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
                                                <h3 class="text-sm font-semibold text-slate-700">{{ $content['title'] }}</h3>
                                            @endif
                                            <div class="text-sm text-slate-700 leading-relaxed prose prose-sm max-w-none">
                                                {!! nl2br(e($content['body'] ?? '')) !!}
                                            </div>
                                        @endforeach
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <svg class="w-16 h-16 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <p class="text-sm font-medium text-slate-500">No hay contenido disponible</p>
                                        <p class="text-xs text-slate-400 mt-1">Esta lección no tiene secciones visibles.</p>
                                    </div>
                                @endforelse

                                {{-- Recursos --}}
                                @if(count($importPreviewData['resources'] ?? []) > 0)
                                    <div class="border-t border-slate-200 pt-5">
                                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-3">
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

                                {{-- Enlaces --}}
                                @if(count($importPreviewData['links'] ?? []) > 0)
                                    <div class="border-t border-slate-200 pt-5">
                                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-3">
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
                            <div class="flex items-center justify-between px-8 py-4 bg-slate-100 border-t border-slate-200">
                                <p class="text-xs text-slate-400">
                                    <span class="font-medium">{{ count($importPreviewData['sections']) }}</span> secciones ·
                                    <span class="font-medium">{{ collect($importPreviewData['sections'])->sum(fn($s) => count($s['contents'])) }}</span> bloques ·
                                    <span class="font-medium">{{ count($importPreviewData['resources'] ?? []) }}</span> recursos ·
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
                                    <div class="w-14 h-14 rounded-full bg-blue-500/10 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-white">¿Importar lección?</h3>
                                    <p class="text-xs text-slate-400 mt-1">El contenido LMS se copiará desde la actividad seleccionada hacia esta lección.</p>
                                </div>

                                {{-- Resumen origen --}}
                                <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50 space-y-2">
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
                                        <span class="text-slate-600">·</span>
                                        <span>{{ $selectedAct['grado'] ?? '—' }} {{ $selectedAct['seccion'] ?? '' }}</span>
                                        <span class="text-slate-600">·</span>
                                        <span class="font-mono">{{ $selectedAct['start_date'] ?? '' }} — {{ $selectedAct['end_date'] ?? '' }}</span>
                                    </div>
                                    @if($importPreviewData)
                                        <div class="flex items-center gap-3 text-[11px] text-slate-500 pt-1 border-t border-slate-700/50 mt-2">
                                            <span>{{ count($importPreviewData['sections'] ?? []) }} secciones</span>
                                            <span class="text-slate-600">·</span>
                                            <span>{{ count($importPreviewData['resources'] ?? []) }} recursos</span>
                                            <span class="text-slate-600">·</span>
                                            <span>{{ count($importPreviewData['links'] ?? []) }} enlaces</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Resumen destino --}}
                                <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50 space-y-2">
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
                            <div class="flex items-center justify-between gap-2 px-6 py-3 border-t border-white/5 bg-gray-800/30">
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

        {{-- ═══════════ MODAL VISTA ESTUDIANTE (LISTA) ═══════════ --}}
        @if($showListStudentPreview && $listPreviewData)
            <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="list-student-preview-{{ $listPreviewData['activity_id'] }}">
                <div class="fixed inset-0 bg-black/80 backdrop-blur-md" wire:click="closeListStudentPreview"></div>
                <div class="relative min-h-screen flex items-start justify-center p-4 pt-10">
                    <div class="relative w-full max-w-7xl bg-white rounded-2xl shadow-2xl overflow-hidden">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-8 py-5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-base font-bold uppercase tracking-wider">Vista del Estudiante</h2>
                                    <p class="text-xs text-emerald-100/80">Así verán los estudiantes la lección</p>
                                </div>
                            </div>
                            <button wire:click="closeListStudentPreview"
                                    class="p-2 hover:bg-white/10 rounded-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="px-8 py-6 space-y-6 max-h-[75vh] overflow-y-auto bg-slate-50">
                            {{-- Encabezado de la lección --}}
                            <div class="border-b border-slate-200 pb-5">
                                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-1">
                                    {{ $listPreviewData['subject'] }}
                                </p>
                                <h1 class="text-2xl font-bold text-slate-900">{{ $listPreviewData['title'] }}</h1>
                                @if($listPreviewData['description'])
                                    <p class="text-sm text-slate-600 mt-2 leading-relaxed">{{ $listPreviewData['description'] }}</p>
                                @endif
                                @if($listPreviewData['start_date'])
                                    <p class="text-xs text-slate-400 mt-2">
                                        {{ \Carbon\Carbon::parse($listPreviewData['start_date'])->format('d/m/Y') }}
                                        — {{ \Carbon\Carbon::parse($listPreviewData['end_date'])->format('d/m/Y') }}
                                    </p>
                                @endif
                            </div>

                            {{-- Secciones --}}
                            @forelse($listPreviewData['sections'] as $section)
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1 h-6 bg-emerald-500 rounded-full"></span>
                                        <h2 class="text-lg font-bold text-slate-800">{{ $section['title'] }}</h2>
                                    </div>
                                    @foreach($section['contents'] as $content)
                                        @if(($content['title'] ?? null))
                                            <h3 class="text-sm font-semibold text-slate-700">{{ $content['title'] }}</h3>
                                        @endif
                                        <div class="text-sm text-slate-700 leading-relaxed prose prose-sm max-w-none">
                                            {!! nl2br(e($content['body'] ?? '')) !!}
                                        </div>
                                    @endforeach
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <p class="text-sm font-medium text-slate-500">No hay contenido disponible</p>
                                    <p class="text-xs text-slate-400 mt-1">Esta actividad no tiene secciones visibles para estudiantes.</p>
                                </div>
                            @endforelse

                            {{-- Recursos --}}
                            @if(count($listPreviewData['resources'] ?? []) > 0)
                                <div class="border-t border-slate-200 pt-5">
                                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-3">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        Recursos descargables
                                    </h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach($listPreviewData['resources'] as $res)
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
                                                @if($listPreviewData['allow_downloads'])
                                                    <span class="ml-auto text-xs text-emerald-600 font-medium shrink-0">Descargar</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Enlaces --}}
                            @if(count($listPreviewData['links'] ?? []) > 0)
                                <div class="border-t border-slate-200 pt-5">
                                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-3">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        Enlaces de interés
                                    </h3>
                                    <div class="space-y-2">
                                        @foreach($listPreviewData['links'] as $link)
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

                        {{-- Footer --}}
                        <div class="px-8 py-4 bg-slate-100 border-t border-slate-200 flex items-center justify-between">
                            <p class="text-xs text-slate-400">
                                <span class="font-medium">{{ count($listPreviewData['sections']) }}</span> secciones ·
                                <span class="font-medium">{{ collect($listPreviewData['sections'])->sum(fn($s) => count($s['contents'])) }}</span> bloques ·
                                <span class="font-medium">{{ count($listPreviewData['resources'] ?? []) }}</span> recursos ·
                                <span class="font-medium">{{ count($listPreviewData['links'] ?? []) }}</span> enlaces
                            </p>
                            <button wire:click="closeListStudentPreview"
                                    class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-lg transition-all">
                                Cerrar vista
                            </button>
                        </div>
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
            $twIdx = 0;
        @endphp

        <div wire:loading.flex
             wire:target="generateStep1Content,generateStep2Sections,generateSectionContent"
             class="fixed inset-0 z-[9999] items-center justify-center bg-slate-900/90 backdrop-blur-md"
             id="llm-loading-overlay">
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
                    @if($act)
                        <p class="text-sm text-slate-400">{{ $asignaturaName }} · {{ $gradoName }} · Sec. {{ $seccionName }}</p>
                    @endif
                </div>

                <div class="space-y-4 text-left max-h-[60vh] overflow-y-auto">

                    {{-- Competencias (siempre visibles) --}}
                    <div class="w-full bg-slate-800/50 border border-slate-700/50 rounded-xl overflow-hidden">
                        {{-- Header --}}
                        <div class="flex items-center gap-3 px-5 py-3.5 bg-slate-800/40 border-b border-slate-700/30">
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-purple-500/20 to-purple-600/10 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-bold text-purple-200">Competencias</h3>
                                <p class="text-[11px] text-slate-500 truncate">Competencias fundamentales del pensum</p>
                            </div>
                            @if($competencias?->isNotEmpty())
                                <span class="shrink-0 text-xs font-medium text-purple-300 bg-purple-500/10 border border-purple-500/20 px-2.5 py-0.5 rounded-full">
                                    {{ $competencias->count() }}
                                </span>
                            @endif
                        </div>

                        {{-- Body: Grid con efecto máquina de escribir --}}
                        @if($competencias?->isNotEmpty())
                            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($competencias as $comp)
                                    @php $indCount = $comp->indicators?->count() ?? 0; @endphp
                                    <div class="bg-slate-800/70 border border-purple-500/20 rounded-lg overflow-hidden">
                                        {{-- Top accent bar --}}
                                        <div class="h-1 bg-gradient-to-r from-purple-500/60 to-purple-400/30 shrink-0"></div>
                                        {{-- Content --}}
                                        <div class="p-4 flex flex-col gap-2">
                                            <p class="text-sm font-semibold text-white leading-snug"
                                               x-ref="tw-{{ $twIdx }}">{{ $comp->name }}</p>
                                            @php $twIdx++; @endphp
                                            {{-- Indicators --}}
                                            @if($indCount > 0)
                                                <div class="space-y-1">
                                                    @foreach($comp->indicators as $ind)
                                                        <div class="flex items-start gap-1.5">
                                                            <span class="w-1 h-1 rounded-full bg-purple-400/40 mt-1.5 shrink-0"></span>
                                                            <p class="text-xs text-slate-400 leading-relaxed"
                                                               x-ref="tw-{{ $twIdx }}">{{ $ind->description }}</p>
                                                            @php $twIdx++; @endphp
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
                                <div class="w-12 h-12 rounded-full bg-slate-700/30 flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-slate-500 italic"
                                   x-ref="tw-{{ $twIdx }}">No hay competencias asociadas</p>
                                @php $twIdx++; @endphp
                            </div>
                        @endif
                    </div>

                    {{-- Indicadores de Logro (siempre visibles) --}}
                    {{-- <div class="bg-slate-800/60 border border-slate-700/50 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Indicadores de Logro</span>
                        </div>
                        @if($indicadoresLogro->isNotEmpty())
                            <div class="space-y-1.5">
                                @foreach($indicadoresLogro as $ind)
                                    <div class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-1.5 shrink-0"></span>
                                        <span class="text-sm text-slate-300"
                                              x-ref="tw-{{ $twIdx }}">{{ $ind->name }}</span>
                                        @php $twIdx++; @endphp
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-500 italic"
                               x-ref="tw-{{ $twIdx }}">No hay indicadores de logro asociados</p>
                            @php $twIdx++; @endphp
                        @endif
                    </div> --}}

                    {{-- Actividad de referencia --}}
                    @if($act)
                        <div class="bg-slate-800/30 border border-slate-700/30 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Actividad</span>
                            </div>
                            <p class="text-sm text-slate-400">{{ $act->topic }}</p>
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

        @script
            <script>
                (function() {
                    var overlay = document.getElementById('llm-loading-overlay');
                    if (!overlay) return;

                    var started = false;

                    var observer = new MutationObserver(function() {
                        if (started) return;
                        if (overlay.style.display === 'flex') {
                            started = true;
                            observer.disconnect();
                            startTypewriter();
                        }
                    });

                    observer.observe(overlay, { attributes: true, attributeFilter: ['style'] });

                    function startTypewriter() {
                        if (typeof Typed === 'undefined') {
                            console.warn('Typed.js not loaded');
                            return;
                        }

                        var items = [];
                        overlay.querySelectorAll('[x-ref^="tw-"]').forEach(function(el) {
                            items.push({ el: el, text: el.textContent });
                        });

                        if (!items.length) return;

                        items.forEach(function(it) { it.el.textContent = ''; });

                        var i = 0;
                        var next = function() {
                            if (i >= items.length) return;
                            new Typed(items[i].el, {
                                strings: [items[i].text],
                                typeSpeed: 18,
                                showCursor: false,
                                contentType: 'text',
                                onComplete: function() { i++; setTimeout(next, 50); }
                            });
                        };
                        next();
                    }
                })();
            </script>
        @endscript

        {{-- Overlay de resultado (sin animación, se muestra inmediatamente al completar) --}}
        @if($showGenerationResult)
            <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/90 backdrop-blur-md">
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
                        <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl p-6 text-left space-y-3 min-h-[80px]">
                            <h2 class="text-base font-bold text-white">{{ $lessonTitle }}</h2>
                            <p class="text-sm text-slate-300 leading-relaxed">{{ $lessonDescription }}</p>
                        </div>
                    @elseif($generationType === 'section')
                        <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl p-6 text-left">
                            <p class="text-sm text-slate-300 leading-relaxed">
                                {{ \Illuminate\Support\Str::limit(
                                    ($wizardSections[array_key_last($wizardSections)]['contents'][0]['body'] ?? ''),
                                    300
                                ) }}
                            </p>
                        </div>
                    @elseif($generationType === 'step2')
                        <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl p-6 text-left max-h-80 overflow-y-auto space-y-3">
                            @foreach($wizardSections as $section)
                                <div class="flex items-start gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mt-1.5 shrink-0"></span>
                                    <div>
                                        <p class="text-sm font-bold text-emerald-300">{{ $section['title'] ?? '' }}</p>
                                        <p class="text-xs text-slate-400 leading-relaxed mt-0.5">
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
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button wire:click="backToList"
                        class="p-2 text-slate-400 hover:text-white hover:bg-slate-700/50 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-white">{{ $lessonTitle ?: 'Nueva Lección' }}</h1>
                    <p class="text-xs text-slate-400">{{ $selectedActivity?->pevaluacion?->pensum?->asignatura?->name ?? '—' }} · {{ $selectedActivity?->pevaluacion?->pensum?->grado?->name ?? '—' }} Sec.{{ $selectedActivity?->pevaluacion?->seccion?->name ?? '—' }}</p>
                </div>
            </div>

            {{-- Indicador de pasos --}}
            <div class="hidden md:flex items-center gap-1">
                @foreach(range(1, 4) as $step)
                    <div class="flex items-center">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-[11px] font-bold
                            {{ $currentStep === $step ? 'bg-emerald-500 text-white' : ($currentStep > $step ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-700 text-slate-500') }}">
                            @if($currentStep > $step)
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @else
                                {{ $step }}
                            @endif
                        </span>
                        @if($step < 4)
                            <span class="w-6 h-px mx-1 {{ $currentStep > $step ? 'bg-emerald-500/40' : 'bg-slate-700' }}"></span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Mensaje de guardado exitoso --}}
        @if($saved)
            <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 text-center">
                <svg class="w-12 h-12 text-emerald-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-base font-bold text-emerald-400 mb-1">¡Lección publicada exitosamente!</h3>
                <p class="text-sm text-slate-400 mb-4">El contenido ya está disponible para los estudiantes.</p>
                <div class="flex items-center justify-center gap-3">
                    <a href="{{ route('app.profesors.lms.editor', $selectedActivityId) }}"
                       class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm rounded-lg font-medium transition-all">
                        Editar contenido completo
                    </a>
                    <button wire:click="backToList"
                            class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 text-sm rounded-lg font-medium transition-all">
                        Volver al listado
                    </button>
                </div>
            </div>
        @else
            {{-- Grid: formulario a la izquierda, preview a la derecha --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- ═══ COLUMNA IZQUIERDA: FORMULARIO (3/5) ═══ --}}
                <div class="lg:col-span-3 space-y-4">

                    {{-- STEP 1: Información de la Lección --}}
                    @if($currentStep === 1)
                        <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-5 space-y-4">
                            <div class="flex items-center gap-2 pb-3 border-b border-slate-700">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">1</span>
                                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Información de la Lección</h2>
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
                                <label class="block text-xs font-medium text-slate-400 mb-1">Título de la lección</label>
                                <input type="text" wire:model="lessonTitle"
                                       class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none"
                                       placeholder="Título de la lección"/>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Descripción</label>
                                <textarea wire:model="lessonDescription" rows="3"
                                          class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none"
                                          placeholder="Breve descripción de la lección…"></textarea>
                            </div>

                            @if($selectedActivity?->learning)
                                <div class="bg-slate-900/30 rounded-lg p-3 border border-slate-700/50">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Aprendizaje esperado</span>
                                    <p class="text-sm text-slate-300 mt-1">{{ $selectedActivity->learning }}</p>
                                </div>
                            @endif

                            {{-- Referentes normativos con competencias e indicadores --}}
                            @if($wizardReferents && count($wizardReferents) > 0)
                                <div class="border-t border-slate-700 pt-4 mt-2"
                                     x-data="{ expandedReferent: null }">
                                    <div class="flex items-center gap-2 mb-3">
                                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Referentes Normativos</span>
                                        <span class="text-[10px] text-slate-600 font-mono">({{ count($wizardReferents) }})</span>
                                    </div>

                                    <div class="space-y-2">
                                        @foreach($wizardReferents as $rIdx => $referent)
                                            <div class="bg-slate-900/40 border border-slate-700/60 rounded-lg overflow-hidden">
                                                {{-- Cabecera del referente (click para expandir) --}}
                                                <button @click="expandedReferent = expandedReferent === {{ $rIdx }} ? null : {{ $rIdx }}"
                                                        class="w-full flex items-center justify-between px-3 py-2 text-left hover:bg-slate-800/40 transition-colors group">
                                                    <div class="flex items-center gap-2 min-w-0">
                                                        <svg class="w-3.5 h-3.5 text-amber-400/70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span class="text-sm font-medium text-slate-200 truncate">{{ $referent['name'] }}</span>
                                                        @if($referent['code'])
                                                            <span class="text-[10px] text-slate-500 font-mono shrink-0">({{ $referent['code'] }})</span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-2 shrink-0">
                                                        <span class="text-[10px] text-slate-600">
                                                            {{ count($referent['competencies'] ?? []) }} comp.
                                                        </span>
                                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform duration-200"
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
                                                    <div class="px-3 pb-3 space-y-2 border-t border-slate-700/50 pt-2">
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
                                                                            <li class="text-[11px] text-slate-400 flex items-start gap-1.5">
                                                                                <span class="text-slate-600 mt-0.5 select-none">•</span>
                                                                                <span>{{ $indicator['description'] }}</span>
                                                                                @if($indicator['code'])
                                                                                    <span class="text-[10px] text-slate-600 font-mono shrink-0">[{{ $indicator['code'] }}]</span>
                                                                                @endif
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @else
                                                                    <p class="ml-5 text-[11px] text-slate-600 italic">Sin indicadores asociados</p>
                                                                @endif
                                                            </div>
                                                        @empty
                                                            <p class="text-xs text-slate-600 italic">Sin competencias asociadas</p>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="border-t border-slate-700 pt-4 mt-2">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Referentes Normativos</span>
                                    </div>
                                    <p class="text-xs text-slate-600 italic">No hay referentes normativos registrados para este plan de estudio.</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- STEP 2: Secciones y Contenido --}}
                    @if($currentStep === 2)
                        <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-5 space-y-4">
                            <div class="flex items-center gap-2 pb-3 border-b border-slate-700">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">2</span>
                                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Contenido de la Lección</h2>
                                <div class="ml-auto">
                                    <button wire:click="generateStep2Sections"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-medium
                                                   text-purple-400 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/20
                                                   transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        Generar estructura
                                    </button>
                                </div>
                            </div>

                            {{-- Secciones existentes --}}
                            @foreach($wizardSections as $sIdx => $section)
                                <div class="bg-slate-900/30 border border-slate-700 rounded-lg overflow-hidden">
                                    <div class="flex items-center justify-between px-3 py-2 bg-slate-800/50">
                                        <span class="text-sm font-medium text-slate-200">{{ $section['title'] }}</span>
                                        <div class="flex items-center gap-1">
                                            <button wire:click="toggleWizardSectionVisibility({{ $sIdx }})"
                                                    class="text-[10px] px-2 py-0.5 rounded {{ $section['is_visible'] ? 'text-emerald-400 bg-emerald-500/10' : 'text-slate-500 bg-slate-700/50' }}">
                                                {{ $section['is_visible'] ? 'Visible' : 'Oculto' }}
                                            </button>
                                            <button wire:click="generateSectionContent({{ $sIdx }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="generateSectionContent({{ $sIdx }})"
                                                    class="text-[10px] px-2 py-0.5 rounded inline-flex items-center gap-1
                                                           text-purple-400 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/20
                                                           transition-all duration-200"
                                                    title="Generar contenido con IA">
                                                @if($generatingSection === $sIdx)
                                                    <svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                    </svg>
                                                    Generando...
                                                @else
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                    </svg>
                                                    IA
                                                @endif
                                            </button>
                                            <button wire:click="$set('editingSectionIndex', {{ $sIdx }})"
                                                    class="text-[10px] px-2 py-0.5 rounded text-slate-400 hover:text-white bg-slate-700/50 hover:bg-slate-700">
                                                + Bloque
                                            </button>
                                            <button wire:click="removeWizardSection({{ $sIdx }})"
                                                    wire:confirm="¿Eliminar esta sección?"
                                                    class="text-[10px] px-2 py-0.5 rounded text-red-400 hover:text-red-300 bg-red-500/10 hover:bg-red-500/20">
                                                ×
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Error de generación --}}
                                    @if($generatingSection === $sIdx && $generationError)
                                        <div class="px-3 py-2 bg-red-500/10 border-b border-red-500/20">
                                            <p class="text-xs text-red-400 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $generationError }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="divide-y divide-slate-700/50">
                                        @foreach($section['contents'] as $cIdx => $content)
                                            <div class="px-3 py-2 flex items-start justify-between gap-2">
                                                <div class="min-w-0">
                                                    @if($content['title'])
                                                        <p class="text-xs font-medium text-slate-200">{{ $content['title'] }}</p>
                                                    @endif
                                                    <p class="text-xs text-slate-500 truncate">{{ \Illuminate\Support\Str::limit(strip_tags($content['body'] ?? ''), 60) }}</p>
                                                </div>
                                                <button wire:click="removeWizardContent({{ $sIdx }}, {{ $cIdx }})"
                                                        class="text-red-400 hover:text-red-300 shrink-0">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Formulario de contenido (inline) --}}
                                    @if($editingSectionIndex === $sIdx)
                                        <div class="px-3 py-3 bg-slate-900/50 border-t border-slate-700 space-y-2">
                                            <input wire:model="contentTitle" placeholder="Título (opcional)"
                                                   class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none"/>
                                            <textarea wire:model="contentBody" rows="3" placeholder="Escribe el contenido…"
                                                      class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none"></textarea>
                                            <div class="flex gap-2">
                                                <button wire:click="addWizardContent({{ $sIdx }})"
                                                        class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white text-xs rounded-lg">
                                                    Agregar
                                                </button>
                                                <button wire:click="$set('editingSectionIndex', null)"
                                                        class="px-3 py-1 text-xs text-slate-400 hover:text-white">
                                                    Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            {{-- Agregar sección --}}
                            <div class="flex gap-2 pt-2">
                                <input wire:model="newSectionTitle" wire:keydown.enter="addWizardSection"
                                       placeholder="Nueva sección (ej: Introducción)…"
                                       class="flex-1 bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none"/>
                                <button wire:click="addWizardSection"
                                        class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-xs rounded-lg font-medium transition-all">
                                    + Sección
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- STEP 3: Recursos y Enlaces --}}
                    @if($currentStep === 3)
                        <div class="w-full bg-slate-800/50 border border-slate-700 rounded-xl overflow-hidden">
                            {{-- Header --}}
                            <div class="flex items-center gap-3 px-5 py-3.5 bg-slate-800/40 border-b border-slate-700/30">
                                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-sm font-bold text-white tracking-wide">Recursos y Enlaces</h2>
                                    <p class="text-[11px] text-slate-500 truncate">Material descargable y enlaces de interés para la lección</p>
                                </div>
                            </div>

                            {{-- Body --}}
                            <div class="p-5 space-y-6">

                                {{-- ═══════ RECURSOS ═══════ --}}
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Archivos descargables</h3>
                                        </div>
                                        <span class="text-[11px] text-slate-500 bg-slate-700/40 px-2 py-0.5 rounded-full">{{ count($wizardResources) }} archivos</span>
                                    </div>

                                    @if(count($wizardResources) > 0)
                                        <div class="space-y-1.5 mb-3">
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
                                                @endphp
                                                <div class="flex items-center gap-3 px-3 py-2.5 bg-slate-800/40 border border-slate-700/40 rounded-lg hover:border-slate-600/60 hover:bg-slate-800/60 transition-all group">
                                                    {{-- Icon --}}
                                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0
                                                        @switch($icon)
                                                            @case('pdf') bg-red-500/15 @break
                                                            @case('image') bg-blue-500/15 @break
                                                            @case('video') bg-purple-500/15 @break
                                                            @case('audio') bg-amber-500/15 @break
                                                            @case('word') bg-blue-600/15 @break
                                                            @case('excel') bg-emerald-500/15 @break
                                                            @case('powerpoint') bg-orange-500/15 @break
                                                            @default bg-slate-600/30
                                                        @endswitch">
                                                        <svg class="w-4 h-4
                                                            @switch($icon)
                                                                @case('pdf') text-red-400 @break
                                                                @case('image') text-blue-400 @break
                                                                @case('video') text-purple-400 @break
                                                                @case('audio') text-amber-400 @break
                                                                @case('word') text-blue-400 @break
                                                                @case('excel') text-emerald-400 @break
                                                                @case('powerpoint') text-orange-400 @break
                                                                @default text-slate-400
                                                            @endswitch"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                                    {{-- Info --}}
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-xs font-medium text-slate-200 truncate">{{ $res['display_name'] }}</p>
                                                        <p class="text-[10px] text-slate-500 truncate">{{ $res['media']['original_name'] ?? '' }} <span class="text-slate-600">·</span> {{ $res['media']['size_for_humans'] ?? '' }}</p>
                                                    </div>
                                                    {{-- Remove --}}
                                                    <button wire:click="removeWizardResource({{ $rIdx }})"
                                                            class="opacity-0 group-hover:opacity-100 text-red-400/60 hover:text-red-300 transition-all text-xs p-1 rounded hover:bg-red-500/10"
                                                            title="Eliminar recurso">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center gap-3 px-4 py-5 bg-slate-800/20 border border-dashed border-slate-700/30 rounded-lg mb-3">
                                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-xs text-slate-500">Sin archivos aún. Agrega recursos descargables para la lección.</p>
                                        </div>
                                    @endif

                                    {{-- Add resource form --}}
                                    <div class="flex gap-2 items-end">
                                        <div class="flex-1">
                                            <input wire:model="resourceName" placeholder="Nombre del recurso"
                                                   class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none transition-colors"/>
                                        </div>
                                        <div class="relative">
                                            <input wire:model="resourceFile" type="file" id="resourceFile"
                                                   class="absolute inset-0 opacity-0 cursor-pointer"/>
                                            <label for="resourceFile"
                                                   class="block px-3 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 text-xs rounded-lg cursor-pointer transition-colors whitespace-nowrap border border-slate-600/50 hover:border-slate-500/50">
                                                <span class="flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                    </svg>
                                                    Adjuntar
                                                </span>
                                            </label>
                                        </div>
                                        <button wire:click="addWizardResource"
                                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-medium rounded-lg transition-colors whitespace-nowrap flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Subir
                                        </button>
                                    </div>
                                </div>

                                {{-- Divider --}}
                                <div class="border-t border-slate-700/40"></div>

                                {{-- ═══════ ENLACES ═══════ --}}
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                            <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Enlaces externos</h3>
                                        </div>
                                        <span class="text-[11px] text-slate-500 bg-slate-700/40 px-2 py-0.5 rounded-full">{{ count($wizardLinks) }} enlaces</span>
                                    </div>

                                    @if(count($wizardLinks) > 0)
                                        <div class="space-y-1.5 mb-3">
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
                                                <div class="flex items-center gap-3 px-3 py-2.5 bg-slate-800/40 border border-slate-700/40 rounded-lg hover:border-slate-600/60 hover:bg-slate-800/60 transition-all group">
                                                    <div class="w-9 h-9 rounded-lg bg-sky-500/10 flex items-center justify-center shrink-0">
                                                        <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2">
                                                            <p class="text-xs font-medium text-slate-200 truncate">{{ $link['title'] }}</p>
                                                            <span class="text-[10px] font-medium px-1.5 py-0.5 rounded border {{ $badge['color'] }} shrink-0">{{ $badge['label'] }}</span>
                                                        </div>
                                                        <p class="text-[10px] text-slate-500 truncate">{{ $displayUrl }}</p>
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
                                        <div class="flex items-center justify-center gap-3 px-4 py-5 bg-slate-800/20 border border-dashed border-slate-700/30 rounded-lg mb-3">
                                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                            <p class="text-xs text-slate-500">Sin enlaces aún. Agrega enlaces de referencia, videos o herramientas.</p>
                                        </div>
                                    @endif

                                    {{-- Add link form --}}
                                    <div class="flex gap-2 items-end">
                                        <div class="flex-1">
                                            <input wire:model="linkTitle" placeholder="Título del enlace"
                                                   class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none transition-colors"/>
                                        </div>
                                        <div class="flex-1">
                                            <input wire:model="linkUrl" placeholder="https://…"
                                                   class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none transition-colors"/>
                                        </div>
                                        <select wire:model="linkType"
                                                class="bg-slate-800 border border-slate-600 rounded-lg px-2.5 py-2 text-xs text-slate-200 focus:border-emerald-500 focus:outline-none transition-colors">
                                            <option value="REFERENCE">Referencia</option>
                                            <option value="VIDEO">Video</option>
                                            <option value="TOOL">Herramienta</option>
                                            <option value="DOCUMENT">Documento</option>
                                            <option value="OTHER">Otro</option>
                                        </select>
                                        <button wire:click="addWizardLink"
                                                class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-medium rounded-lg transition-colors whitespace-nowrap flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Agregar
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif

                    {{-- STEP 4: Publicar --}}
                    @if($currentStep === 4)
                        <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-5 space-y-4">
                            <div class="flex items-center gap-2 pb-3 border-b border-slate-700">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">4</span>
                                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Publicar Lección</h2>
                            </div>

                            <div class="flex items-center gap-3">
                                <label class="text-sm text-slate-300">Programar publicación:</label>
                                <input wire:model="publishAt" type="datetime-local"
                                       class="bg-slate-800 border border-slate-600 rounded-lg px-3 py-1.5 text-sm text-slate-200 focus:border-emerald-500 focus:outline-none"/>
                            </div>

                            <label class="flex items-center gap-2 text-sm text-slate-300 cursor-pointer">
                                <input wire:model="allowDownloads" type="checkbox"
                                       class="rounded border-slate-600 bg-slate-800 text-emerald-500"/>
                                Permitir descarga de recursos
                            </label>

                            <div class="bg-slate-900/30 rounded-lg p-3 border border-slate-700/50">
                                <p class="text-xs text-slate-400">
                                    <span class="text-emerald-400 font-medium">{{ count($this->previewSections) }}</span> secciones visibles ·
                                    <span class="text-emerald-400 font-medium">{{ collect($this->previewSections)->sum(fn($s) => count($s['contents'])) }}</span> bloques de contenido ·
                                    <span class="text-emerald-400 font-medium">{{ count($wizardResources) }}</span> recursos ·
                                    <span class="text-emerald-400 font-medium">{{ count($wizardLinks) }}</span> enlaces
                                </p>
                            </div>

                            <button wire:click="$toggle('showStudentPreview')"
                                    class="w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 text-sm font-medium rounded-xl transition-all duration-200 flex items-center justify-center gap-2 border border-slate-600/50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Vista estudiante
                            </button>

                            <button wire:click="confirmPublish"
                                    wire:loading.attr="disabled"
                                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 disabled:bg-slate-700 disabled:text-slate-500 text-white text-sm font-bold rounded-xl transition-all duration-200 flex items-center justify-center gap-2">
                                <svg wire:loading wire:target="confirmPublish" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Publicar lección
                            </button>
                        </div>
                    @endif

                    {{-- Navegación entre pasos --}}
                    <div class="flex items-center justify-between">
                        <button wire:click="goToStep({{ $currentStep - 1 }})"
                                class="px-4 py-2 text-sm text-slate-400 hover:text-white transition-colors {{ $currentStep <= 1 ? 'invisible' : '' }}">
                            ← Anterior
                        </button>

                        <div class="flex items-center gap-2">
                            @if($currentStep < 4)
                                <button wire:click="goToStep({{ $currentStep + 1 }})"
                                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-lg transition-all">
                                    Siguiente →
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ═══ COLUMNA DERECHA: VISTA PREVIA (2/5) ═══ --}}
                <div class="lg:col-span-2">
                    <div class="bg-slate-800/50 border border-slate-700 rounded-xl overflow-hidden sticky top-24">
                        <div class="px-4 py-3 bg-slate-700/30 border-b border-slate-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">Vista Previa</span>
                            <span class="text-[10px] text-slate-500 ml-auto">Modo estudiante</span>
                        </div>

                        <div class="p-4 space-y-4 max-h-[70vh] overflow-y-auto bg-white/5">
                            {{-- Header preview --}}
                            <div class="border-b border-slate-700 pb-3">
                                <p class="text-[10px] text-slate-500 uppercase tracking-wider mb-1">
                                    {{ $selectedActivity?->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura' }}
                                </p>
                                <h2 class="text-base font-bold text-white">{{ $lessonTitle ?: 'Título de la lección' }}</h2>
                                @if($lessonDescription)
                                    <p class="text-xs text-slate-400 mt-1">{{ $lessonDescription }}</p>
                                @endif
                                @if($selectedActivity)
                                    <p class="text-[10px] text-slate-500 mt-1">
                                        {{ \Carbon\Carbon::parse($selectedActivity->finicial)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($selectedActivity->ffinal)->format('d/m/Y') }}
                                    </p>
                                @endif
                            </div>

                            {{-- Secciones preview --}}
                            @forelse($this->previewSections as $section)
                                <div class="space-y-2">
                                    <h3 class="text-sm font-semibold text-slate-100">{{ $section['title'] }}</h3>
                                    @foreach($section['contents'] as $content)
                                        @if($content['title'])
                                            <h4 class="text-xs font-medium text-slate-300">{{ $content['title'] }}</h4>
                                        @endif
                                        <div class="text-xs text-slate-400 leading-relaxed prose prose-invert max-w-none">
                                            {!! $content['body'] ?? '' !!}
                                        </div>
                                    @endforeach
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <svg class="w-10 h-10 text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <p class="text-xs text-slate-600">Agrega secciones y contenido en el paso 2</p>
                                </div>
                            @endforelse

                            {{-- Recursos preview --}}
                            @if(count($wizardResources) > 0)
                                <div class="border-t border-slate-700 pt-3 space-y-2">
                                    <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Recursos</h4>
                                    @foreach($wizardResources as $res)
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-xs text-slate-300">{{ $res['display_name'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Enlaces preview --}}
                            @if(count($wizardLinks) > 0)
                                <div class="border-t border-slate-700 pt-3 space-y-2">
                                    <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Enlaces</h4>
                                    @foreach($wizardLinks as $link)
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            <span class="text-xs text-slate-300">{{ $link['title'] }}</span>
                                            <span class="text-[10px] text-slate-500">({{ $link['link_type'] }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════ MODAL VISTA ESTUDIANTE (7xl) ═══════════ --}}
            @if($showStudentPreview)
                <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="student-preview-modal">
                    {{-- Backdrop --}}
                    <div class="fixed inset-0 bg-black/80 backdrop-blur-md"
                         wire:click="$toggle('showStudentPreview')"></div>

                    {{-- Modal panel 7xl --}}
                    <div class="relative min-h-screen flex items-start justify-center p-4 pt-10">
                        <div class="relative w-full max-w-7xl bg-white rounded-2xl shadow-2xl overflow-hidden">
                            {{-- Header --}}
                            <div class="flex items-center justify-between px-8 py-5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-base font-bold uppercase tracking-wider">Vista del Estudiante</h2>
                                        <p class="text-xs text-emerald-100/80">Así verán los estudiantes la lección</p>
                                    </div>
                                </div>
                                <button wire:click="$toggle('showStudentPreview')"
                                        class="p-2 hover:bg-white/10 rounded-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Body --}}
                            <div class="px-8 py-6 space-y-6 max-h-[75vh] overflow-y-auto bg-slate-50">
                                {{-- Encabezado de la lección --}}
                                <div class="border-b border-slate-200 pb-5">
                                    <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-1">
                                        {{ $selectedActivity?->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura' }}
                                    </p>
                                    <h1 class="text-2xl font-bold text-slate-900">{{ $lessonTitle ?: 'Título de la lección' }}</h1>
                                    @if($lessonDescription)
                                        <p class="text-sm text-slate-600 mt-2 leading-relaxed">{{ $lessonDescription }}</p>
                                    @endif
                                    @if($selectedActivity)
                                        <p class="text-xs text-slate-400 mt-2">
                                            {{ \Carbon\Carbon::parse($selectedActivity->finicial)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($selectedActivity->ffinal)->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Secciones --}}
                                @forelse($this->previewSections as $section)
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1 h-6 bg-emerald-500 rounded-full"></span>
                                            <h2 class="text-lg font-bold text-slate-800">{{ $section['title'] }}</h2>
                                        </div>
                                        @foreach($section['contents'] as $content)
                                            @if($content['title'])
                                                <h3 class="text-sm font-semibold text-slate-700">{{ $content['title'] }}</h3>
                                            @endif
                                            <div class="text-sm text-slate-700 leading-relaxed prose prose-sm max-w-none">
                                                {!! nl2br(e($content['body'] ?? '')) !!}
                                            </div>
                                        @endforeach
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <svg class="w-16 h-16 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <p class="text-sm font-medium text-slate-500">No hay contenido disponible</p>
                                        <p class="text-xs text-slate-400 mt-1">Agrega secciones y contenido en el paso 2 del asistente.</p>
                                    </div>
                                @endforelse

                                {{-- Recursos --}}
                                @if(count($wizardResources) > 0)
                                    <div class="border-t border-slate-200 pt-5">
                                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-3">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            Recursos descargables
                                        </h3>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            @foreach($wizardResources as $res)
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
                                                    @if($allowDownloads)
                                                        <span class="ml-auto text-xs text-emerald-600 font-medium shrink-0">Descargar</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Enlaces --}}
                                @if(count($wizardLinks) > 0)
                                    <div class="border-t border-slate-200 pt-5">
                                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-3">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            Enlaces de interés
                                        </h3>
                                        <div class="space-y-2">
                                            @foreach($wizardLinks as $link)
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

                            {{-- Footer --}}
                            <div class="px-8 py-4 bg-slate-100 border-t border-slate-200 flex items-center justify-between">
                                <p class="text-xs text-slate-400">
                                    <span class="font-medium">{{ count($this->previewSections) }}</span> secciones ·
                                    <span class="font-medium">{{ collect($this->previewSections)->sum(fn($s) => count($s['contents'])) }}</span> bloques ·
                                    <span class="font-medium">{{ count($wizardResources) }}</span> recursos ·
                                    <span class="font-medium">{{ count($wizardLinks) }}</span> enlaces
                                </p>
                                <button wire:click="$toggle('showStudentPreview')"
                                        class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-lg transition-all">
                                    Cerrar vista
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ═══════════ MODAL CONFIRMACIÓN PUBLICAR SIN FECHA ═══════════ --}}
            @if($showPublishConfirm)
                <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="publish-confirm-modal">
                    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm"></div>
                    <div class="relative min-h-screen flex items-center justify-center p-4">
                        <div class="relative w-full max-w-md bg-gray-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden">
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
        @endif
</div>
    @endif
</div>
