<div>
    {{-- Loading indicator --}}
    <div wire:loading class="mb-4">
        <div class="flex items-center gap-2 text-sm text-emerald-400 font-medium">
            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Procesando...
        </div>
    </div>

    {{-- Main card --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">

        {{-- Alert: Observación Coord.Eval. --}}
        <div class="border-b border-white/5 px-6 py-4 bg-amber-500/5">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 bg-amber-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Observación [Coord.Eval.]</span>
                    </div>
                    @if($pevaluacion->observations)
                        <p class="text-sm text-gray-300 leading-relaxed">{{ $pevaluacion->observations }}</p>
                    @else
                        <p class="text-sm text-gray-500">No hay observaciones registradas.</p>
                    @endif
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    {{-- S2526: Actividades periodo anterior --}}
                    <button wire:click="openS2526Modal"
                        title="Actividades de períodos anteriores"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-violet-500/10 text-violet-400 hover:bg-violet-500/20 border border-violet-500/20 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </button>
                    {{-- Competencias e Indicadores --}}
                    <button @click="window.Livewire.dispatch('openCompetenciasDialog', { pensumId: {{ $pevaluacion->pensum_id }} })"
                        title="Competencias e Indicadores"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </button>
                    {{-- PDF Resume --}}
                    <a href="{{ route('app.profesors.activities.resume', $pevaluacion->id) }}"
                        title="Resumen del Plan de Actividades"
                        target="_blank"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </a>
                    {{-- PDF Format --}}
                    <a href="{{ route('app.profesors.activities.format', $pevaluacion->id) }}"
                        title="Plan de Actividades Completo"
                        target="_blank"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 border border-purple-500/20 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </a>
                    {{-- Competencias e Indicadores --}}
                    <button 
                        @click="window.Livewire.dispatch('openCompetenciasDialog', { pensumId: {{ $pevaluacion->pensum_id }} })"
                        title="Competencias e Indicadores"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all duration-200"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path 
                                stroke-linecap="round" 
                                stroke-linejoin="round" 
                                stroke-width="2" 
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                            />
                        </svg>
                    </button>
                    {{-- Empty All Activities --}}
                    @php $disabled = (!empty($pevaluacion->observations) || !$enable_edit) ? true : false; @endphp
                    <button wire:click="emptyActivities"
                            title="Eliminar todas las actividades"
                            {{ $disabled ? 'disabled' : '' }}
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg {{ $disabled ? 'bg-gray-800/50 text-gray-600 cursor-not-allowed' : 'bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20' }} transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Activities List --}}
        <div class="p-6">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/5">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                    Actividades registradas
                    <span class="text-gray-500 font-normal normal-case">({{ $activities->total() }})</span>
                </h3>
                <div class="flex items-center gap-2">
                    {{-- Search --}}
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar..."
                            class="w-44 bg-gray-800/50 border border-white/10 rounded-lg pl-9 pr-3 py-1.5 text-xs text-gray-300 placeholder-gray-600 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                        @if($search)
                            <button wire:click="$set('search', '')" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        @endif
                    </div>

                    {{-- Per page --}}
                    <div class="relative w-16">
                        <select wire:model.live="paginate"
                            class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-2 pr-7 py-1.5 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200 appearance-none cursor-pointer">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                            <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    {{-- Clone selector (only if activities empty) --}}
                    @if($activities->isEmpty())
                        <select wire:model="seccion_id"
                            class="bg-gray-800/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-gray-300 focus:border-emerald-500/50">
                            <option value="">Seleccione sección</option>
                            @foreach($list_seccions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <button wire:click="clone"
                            {{ $enable_edit ? '' : 'disabled' }}
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200 {{ $enable_edit ? 'bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 border border-cyan-500/20' : 'bg-gray-800/50 text-gray-600 cursor-not-allowed' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Clonar
                        </button>
                    @endif

                    {{-- New Activity --}}
                    <button wire:click="setCreate"
                        {{ $enable_edit ? '' : 'disabled' }}
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold transition-all duration-200 {{ $enable_edit ? 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20' : 'bg-gray-800/50 text-gray-600 cursor-not-allowed' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        + Nuevo
                    </button>
                </div>
            </div>

            {{-- Activities --}}
            <div class="space-y-4">
                @forelse($activities as $item)
                    @php $achievements = $item->achievements; @endphp
                    <div class="bg-gray-800/30 border border-white/5 rounded-lg p-4 transition-all hover:border-white/10 {{ $item->id == $activity_id ? 'ring-1 ring-emerald-500/20' : '' }}">

                        {{-- Activity Header --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                {{-- Status badge --}}
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-400">
                                        {{ $activities->firstItem() + $loop->index }}
                                    </span>
                                    @if($item->status_resume)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            Act. Evaluación
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            Sin actividad de evaluación
                                        </span>
                                    @endif
                                    <span class="text-[11px] text-gray-500 font-mono">
                                        {{ \Carbon\Carbon::parse($item->finicial)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($item->ffinal)->format('d/m/Y') }}
                                    </span>
                                </div>

                                {{-- Content --}}
                                <div class="space-y-1 text-sm text-gray-300">
                                    @if($item->description)
                                        <div><span class="font-medium text-emerald-400">Act.Eval:</span> {{ $item->description }}</div>
                                    @endif
                                    <div><span class="font-medium text-gray-500">Tema:</span> {{ $item->topic }}</div>
                                    <div><span class="font-medium text-gray-500">T.Temático:</span> {{ $item->thematic }}</div>
                                    <div x-data="{ showTeaching: true }">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-gray-500">Enseñanza:</span>
                                            @if($item->hasTeachingStructure())
                                                <button @click="showTeaching = !showTeaching"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10 transition-all duration-200"
                                                    x-text="showTeaching ? 'Ver completo' : 'Ver estructura'">
                                                </button>
                                            @endif
                                        </div>
                                        {{-- Full text --}}
                                        <div x-show="!showTeaching" class="mt-1">
                                            {{ Str::limit($item->teaching, 120) }}
                                        </div>
                                        {{-- Structured view --}}
                                        @if($item->hasTeachingStructure())
                                            @php $sections = $item->getTeachingSections(); @endphp
                                            <div x-show="showTeaching" x-cloak x-transition:enter.duration.200ms class="mt-1 space-y-2">
                                                <div class="p-2 bg-cyan-500/5 border border-cyan-500/10 rounded-lg">
                                                    <div class="text-[10px] font-bold uppercase tracking-widest text-cyan-400 mb-0.5">INICIO</div>
                                                    <p class="text-xs text-gray-300">{{ $sections['INICIO'] ?? '' }}</p>
                                                </div>
                                                <div class="p-2 bg-emerald-500/5 border border-emerald-500/10 rounded-lg">
                                                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-400 mb-0.5">DESARROLLO</div>
                                                    <p class="text-xs text-gray-300">{{ $sections['DESARROLLO'] ?? '' }}</p>
                                                </div>
                                                <div class="p-2 bg-amber-500/5 border border-amber-500/10 rounded-lg">
                                                    <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400 mb-0.5">CIERRE</div>
                                                    <p class="text-xs text-gray-300">{{ $sections['CIERRE'] ?? '' }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div><span class="font-medium text-gray-500">Aprendizaje:</span> {{ Str::limit($item->learning, 120) }}</div>
                                </div>

                                {{-- Comment from J.Área --}}
                                @if($item->comments)
                                    <div class="mt-2 p-2 bg-cyan-500/5 border border-cyan-500/10 rounded-lg">
                                        <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider">Comentario [J.Área]</span>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $item->comments }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Action buttons --}}
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button wire:click="viewDetail({{ $item->id }})"
                                    title="Ver todos los detalles"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                {{-- LMS: Editar Contenido --}}
                                {{-- <a href="{{ route('app.profesors.lms.editor', $item->id) }}"
                                    title="Editar contenido LMS"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200"
                                    @if(!$enable_edit)
                                        onclick="event.preventDefault()"
                                        style="pointer-events:none; opacity:0.4;"
                                    @endif>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </a> --}}
                                <button wire:click="setEditActivity({{ $item->id }})"
                                    title="Editar actividad"
                                    {{ $enable_edit ? '' : 'disabled' }}
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold {{ $enable_edit ? 'bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20' : 'bg-gray-800/50 text-gray-600 cursor-not-allowed' }} transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                @php $disabled = ($achievements->count() > 0 || !$enable_edit); @endphp
                                <button wire:click="delActivity({{ $item->id }})"
                                    title="Eliminar actividad"
                                    {{ $disabled ? 'disabled' : '' }}
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold {{ $disabled ? 'bg-gray-800/50 text-gray-600 cursor-not-allowed' : 'bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20' }} transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Achievements Section --}}
                        <div class="mt-3 ml-8 pl-4 border-l border-white/5">
                            {{-- Achievement list header --}}
                            <div class="flex items-center justify-between py-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-600">Indicadores / Aprendizajes Esperados</span>
                                <button wire:click="addAchievement({{ $item->id }})"
                                    {{ $enable_edit ? '' : 'disabled' }}
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $enable_edit ? 'bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20' : 'bg-gray-800/50 text-gray-600 cursor-not-allowed' }} transition-all duration-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Agregar
                                </button>
                            </div>

                            {{-- Achievement items --}}
                            <div class="space-y-1">
                                @forelse($achievements as $ach)
                                    <div class="flex items-center justify-between py-1.5 px-3 rounded-lg {{ $achievement_id == $ach->id ? 'bg-gray-700/30 ring-1 ring-emerald-500/20' : 'hover:bg-white/[0.02]' }} transition-all">
                                        <span class="text-xs text-gray-400">
                                            - {{ $ach->name }}
                                            @if($ach->status_quantitative_weighting)
                                                <span class="text-emerald-400 font-mono font-medium">[{{ $ach->weighting }}]</span>
                                            @endif
                                        </span>
                                        <div class="flex items-center gap-1">
                                            @if($enable_edit)
                                                <button wire:click="setEditAchievement({{ $ach->id }})"
                                                    class="p-1 text-gray-500 hover:text-amber-400 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button wire:click="deleteAchievement({{ $ach->id }})"
                                                    class="p-1 text-gray-500 hover:text-red-400 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-[11px] text-gray-600 italic py-1">No hay indicadores</p>
                                @endforelse

                                {{-- Total weight --}}
                                @php $totalWeight = $achievements->sum(fn($a) => (int)($a->weighting ?? 0)); @endphp
                                @if($totalWeight > 0 && $achievements->count() > 0)
                                    <div class="flex items-center justify-between py-2 px-3 mt-2 bg-gray-800/30 border border-white/5 rounded-lg">
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Ponderaciones</span>
                                        <span class="text-sm font-bold text-emerald-400 font-mono">{{ $totalWeight }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-sm font-medium text-gray-400">
                            @if($search)
                                No se encontraron actividades
                            @else
                                No hay actividades registradas
                            @endif
                        </p>
                        @if($search)
                            <p class="text-xs text-gray-600 mt-1">No hay resultados para "<span class="text-emerald-400 font-medium">{{ $search }}</span>".</p>
                            <button wire:click="$set('search', '')"
                                class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                                Limpiar búsqueda
                            </button>
                        @else
                            <p class="text-xs text-gray-600 mt-1">Presione <span class="text-emerald-400 font-medium">"+ Nuevo"</span> para comenzar.</p>
                        @endif
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($activities->hasPages())
                <div class="mt-4 pt-4 border-t border-white/5">
                    {{ $activities->links('vendor.livewire.custom-tailwind') }}
                </div>
            @endif
        </div>
    </div>

    {{-- DETAIL MODAL --}}
    @if($showDetailModal && $detailActivity)
        <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="detail-modal-{{ $detailActivity->id }}">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeDetailModal"></div>

            {{-- Modal panel --}}
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative w-full max-w-6xl bg-gray-900 border border-white/10 rounded-lg shadow-2xl overflow-hidden">

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
                                    {{ $pevaluacion->pensum?->asignatura?->name }} —
                                    {{ $pevaluacion->pensum?->grado?->name }} {{ $pevaluacion->seccion?->name }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="closeDetailModal"
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
                            <div class="space-y-4" x-data="{ showTeaching: true }">
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
                        <button wire:click="closeDetailModal"
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

    {{-- EDIT MODAL --}}
    @if($modeEdit && $activity_id)
        <div class="fixed inset-0 z-[9998] overflow-y-auto" wire:key="edit-modal-{{ $activity_id }}">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="close"></div>

            {{-- Modal panel --}}
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative w-full max-w-6xl bg-gray-900 border border-white/10 rounded-lg shadow-2xl overflow-hidden">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-white/5 bg-gray-800/50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Editar Actividad</h3>
                                <p class="text-xs text-gray-500">
                                    {{ $pevaluacion->pensum?->asignatura?->name }} —
                                    {{ $pevaluacion->pensum?->grado?->name }} {{ $pevaluacion->seccion?->name }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="close"
                            class="p-1.5 text-gray-500 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Body: form fields --}}
                    <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                        @include('livewire.profesor.activity.form.fields')
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-3 border-t border-white/5 bg-gray-800/30 flex items-center justify-between">
                        <button wire:click="close"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancelar
                        </button>
                        <button wire:click="save"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-xs font-bold bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 hover:text-emerald-300 border border-emerald-500/20 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ACHIEVEMENT MODAL --}}
    @if($showAchievementModal)
        <div class="fixed inset-0 z-[9997] overflow-y-auto" wire:key="achievement-modal-{{ $activity->id }}-{{ $achievement_id ?? 'new' }}">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="close"></div>

            {{-- Modal panel --}}
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-gray-900 border border-white/10 rounded-lg shadow-2xl overflow-hidden">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-white/5 bg-gray-800/50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                                    {{ $achievement_id ? 'Editar Indicador' : 'Agregar Indicador' }}
                                </h3>
                                <p class="text-xs text-gray-500">
                                    {{ $pevaluacion->pensum?->asignatura?->name }} —
                                    {{ $activity->topic ? Str::limit($activity->topic, 40) : 'Nueva actividad' }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="close"
                            class="p-1.5 text-gray-500 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                        <div class="space-y-4">

                            {{-- Achievement Name --}}
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">
                                    {{ $list_comment['name'] ?? 'Nombre del indicador' }}
                                </label>
                                <input type="text" wire:model="achievementForm.name"
                                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200"
                                    placeholder="{{ $list_comment['name'] ?? 'Ej: Identifica los elementos...' }}">
                                @error('achievementForm.name')
                                    <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Quantitative Weighting Toggle --}}
                            <div class="flex items-center gap-3 pt-2">
                                <button type="button" wire:click="toggleWeighting"
                                    class="relative inline-flex items-center cursor-pointer focus:outline-none">
                                    <div class="w-9 h-5 rounded-full transition-colors duration-200 ease-in-out {{ $achievementForm->quantitative ? 'bg-emerald-500' : 'bg-gray-700 border border-white/10' }}">
                                        <div class="inline-block w-4 h-4 rounded-full bg-white shadow transform transition-transform duration-200 ease-in-out {{ $achievementForm->quantitative ? 'translate-x-[18px]' : 'translate-x-[2px]' }} mt-[2px]"></div>
                                    </div>
                                </button>
                                <span class="text-[11px] text-gray-400 font-medium">
                                    {{ $list_comment['status_quantitative_weighting'] ?? 'Indicador ponderado (cuantitativo)' }}
                                </span>
                            </div>

                            {{-- Weighting selector --}}
                            @if($achievementForm->quantitative)
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">
                                        {{ $list_comment['weighting'] ?? 'Ponderación' }}
                                    </label>
                                    <select wire:model="achievementForm.weighting"
                                        class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                                        <option value="">Seleccione ponderación</option>
                                        @for($i = 1; $i <= 20; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    @error('achievementForm.weighting')
                                        <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-3 border-t border-white/5 bg-gray-800/30 flex items-center justify-between">
                        <button wire:click="close"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancelar
                        </button>
                        <button wire:click="saveAchievement"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-xs font-bold bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 hover:text-emerald-300 border border-emerald-500/20 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $achievement_id ? 'Guardar Cambios' : 'Guardar Indicador' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- S2526 MODAL: Actividades de períodos anteriores              --}}
    {{-- ============================================================ --}}
    @if($showS2526Modal)
        <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="s2526-modal">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeS2526Modal"></div>

            <div class="relative min-h-screen flex items-start justify-center p-4 pt-8 pb-24">
                <div class="relative w-[95vw] max-w-[95vw] bg-gray-900 border border-white/10 rounded-lg shadow-2xl overflow-hidden" @click.away="closeS2526Modal">

                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between bg-violet-500/5 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-violet-500/10 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Actividades de períodos anteriores</h3>
                                <p class="text-[11px] text-gray-500 mt-0.5">
                                    {{ $this->pevaluacion->pensum?->asignatura?->name ?? 'Asignatura' }} · {{ $this->pevaluacion->pensum?->grado?->name ?? 'Grado' }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="closeS2526Modal"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Filters bar --}}
                    <div class="px-6 py-3 border-b border-white/5 flex items-center gap-3 bg-gray-800/20 shrink-0">
                        {{-- Search --}}
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" wire:model.live.debounce.300ms="s2526Search" placeholder="Buscar en actividades anteriores…"
                                class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-9 pr-3 py-1.5 text-xs text-gray-300 placeholder-gray-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all duration-200">
                            @if($s2526Search)
                                <button wire:click="$set('s2526Search', '')" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        {{-- Lapso filter --}}
                        <select wire:model.live="s2526Lapso"
                            class="bg-gray-800/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-gray-300 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all duration-200">
                            <option value="">Todos los lapsos</option>
                            @foreach($s2526Lapsos as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>

                        {{-- Sort controls --}}
                        <div class="flex items-center gap-1">
                            <button wire:click="sortS2526('finicial')"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold transition-all duration-200
                                    {{ $s2526SortField === 'finicial' ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($s2526SortField === 'finicial')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                                    @endif
                                </svg>
                                Fecha
                            </button>
                            <button wire:click="sortS2526('topic')"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold transition-all duration-200
                                    {{ $s2526SortField === 'topic' ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($s2526SortField === 'topic')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                                    @endif
                                </svg>
                                Tema
                            </button>
                        </div>

                        {{-- Results count --}}
                        <span class="text-[11px] text-gray-500 shrink-0">
                            {{ $s2526Total }} activ.
                        </span>
                    </div>

                    {{-- Grid de actividades --}}
                    <div class="overflow-y-auto overscroll-contain" style="max-height: calc(100vh - 220px);">
                        <div class="p-6">
                            @if(!empty($s2526Activities))
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($s2526Activities as $i => $act)
                                        <div class="bg-gray-800/30 border border-white/5 rounded-lg hover:border-violet-500/30 transition-all duration-200 flex flex-col"
                                            x-data="{ openMenu: false }"
                                            @click.away="openMenu = false">

                                            {{-- Card header --}}
                                            <div class="flex items-center justify-between px-4 pt-3 pb-2 border-b border-white/5">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-400 shrink-0">
                                                        {{ $s2526From + $loop->index }}
                                                    </span>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-violet-500/10 text-violet-400 border border-violet-500/20 shrink-0">
                                                        {{ $act['lapso_name'] }}
                                                    </span>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20 shrink-0">
                                                        {{ $act['seccion_name'] }}
                                                    </span>
                                                </div>

                                                {{-- Actions menu --}}
                                                <div class="relative">
                                                    <button @click="openMenu = !openMenu"
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-500 hover:text-gray-300 hover:bg-gray-700/50 transition-all duration-200">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                                        </svg>
                                                    </button>
                                                    <div x-show="openMenu" x-cloak
                                                        class="absolute right-0 top-full mt-1 w-48 bg-gray-800 border border-white/10 rounded-lg shadow-xl overflow-hidden z-10">
                                                        <div class="py-1">
                                                            <button wire:click="s2526ViewDetail({{ $i }})"
                                                                class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-gray-300 hover:bg-white/5 transition-all"
                                                                @click="openMenu = false">
                                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                </svg>
                                                                Ver detalle
                                                            </button>
                                                            <button wire:click="s2526CopyToPlan({{ $i }})"
                                                                class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-gray-300 hover:bg-white/5 transition-all"
                                                                @click="openMenu = false">
                                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                                                </svg>
                                                                Copiar a mi plan
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Card body --}}
                                            <div class="px-4 py-3 flex-1 space-y-1.5 text-sm text-gray-300">
                                                <div class="flex items-center gap-2 text-[11px] text-gray-500 font-mono mb-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse($act['finicial'])->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($act['ffinal'])->format('d/m/Y') }}
                                                </div>
                                                <div>
                                                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-600 block mb-0.5">Tema</span>
                                                    <p class="text-xs text-gray-200 leading-relaxed">{{ $act['topic'] }}</p>
                                                </div>
                                                @if(!empty($act['teaching']))
                                                <div>
                                                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-600 block mb-0.5">Enseñanza</span>
                                                    <p class="text-xs text-gray-400 leading-relaxed">{{ Str::limit($act['teaching'], 80) }}</p>
                                                </div>
                                                @endif
                                            </div>

                                            {{-- Card footer --}}
                                            <div class="px-4 py-2 border-t border-white/5 flex items-center justify-between bg-white/[0.015]">
                                                @if(!empty($act['description']))
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                        Act.Evaluativa
                                                    </span>
                                                @else
                                                    <span class="text-[10px] text-gray-600">—</span>
                                                @endif
                                                @if(!empty($act['comments']))
                                                    <span class="inline-flex items-center gap-1 text-[10px] text-cyan-400/70">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                                        </svg>
                                                        J.Área
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-16">
                                    <div class="w-14 h-14 bg-gray-800/50 rounded-lg flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-500">No se encontraron actividades en períodos anteriores</p>
                                    @if($s2526Search || $s2526Lapso)
                                        <p class="text-xs text-gray-600 mt-1">Intenta ajustar los filtros de búsqueda</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Pagination + Footer --}}
                    <div class="px-6 py-3 border-t border-white/5 bg-gray-800/30 flex items-center justify-between shrink-0">
                        {{-- Pagination --}}
                        @if($s2526LastPage > 1)
                            <div class="flex items-center gap-1">
                                {{-- Previous --}}
                                <button wire:click="gotoPageS2526({{ $s2526Page - 1 }})"
                                    {{ $s2526Page <= 1 ? 'disabled' : '' }}
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold transition-all duration-200
                                        {{ $s2526Page <= 1 ? 'bg-gray-800/50 text-gray-600 cursor-not-allowed' : 'bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>

                                {{-- Page numbers --}}
                                @for($page = 1; $page <= $s2526LastPage; $page++)
                                    @if($page == $s2526Page)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-violet-500/10 text-violet-400 border border-violet-500/20">
                                            {{ $page }}
                                        </span>
                                    @elseif($page >= $s2526Page - 2 && $page <= $s2526Page + 2)
                                        <button wire:click="gotoPageS2526({{ $page }})"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                                            {{ $page }}
                                        </button>
                                    @elseif($page == 1 || $page == $s2526LastPage)
                                        <button wire:click="gotoPageS2526({{ $page }})"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                                            {{ $page }}
                                        </button>
                                    @elseif($page == $s2526Page - 3 || $page == $s2526Page + 3)
                                        <span class="text-xs text-gray-600 px-1">...</span>
                                    @endif
                                @endfor

                                {{-- Next --}}
                                <button wire:click="gotoPageS2526({{ $s2526Page + 1 }})"
                                    {{ $s2526Page >= $s2526LastPage ? 'disabled' : '' }}
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold transition-all duration-200
                                        {{ $s2526Page >= $s2526LastPage ? 'bg-gray-800/50 text-gray-600 cursor-not-allowed' : 'bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>

                                <span class="text-[10px] text-gray-600 ml-2">
                                    Pág. {{ $s2526Page }} de {{ $s2526LastPage }}
                                </span>
                            </div>
                        @else
                            <div></div>
                        @endif

                        <button wire:click="closeS2526Modal"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- S2526 DETAIL MODAL --}}
    @if($showS2526DetailModal && !empty($s2526DetailActivity))
        @php $sAct = $s2526DetailActivity; @endphp
        <div class="fixed inset-0 overflow-y-auto" style="z-index: 99999 !important;" wire:key="s2526-detail-modal">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeS2526DetailModal"></div>

            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative w-full max-w-4xl bg-gray-900 border border-white/10 rounded-lg shadow-2xl overflow-hidden">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-white/5 bg-gray-800/50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Detalles de la Actividad</h3>
                                <p class="text-xs text-gray-500">
                                    {{ $sAct['lapso_name'] }} · {{ $sAct['seccion_name'] }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="closeS2526DetailModal"
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
                            @if(!empty($sAct['description']))
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
                                {{ \Carbon\Carbon::parse($sAct['finicial'])->format('d/m/Y') }}
                            </span>
                            <span class="text-gray-600">→</span>
                            <span class="text-xs text-gray-500 font-mono">
                                {{ \Carbon\Carbon::parse($sAct['ffinal'])->format('d/m/Y') }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-violet-500/10 text-violet-400 border border-violet-500/20">
                                {{ $sAct['lapso_name'] }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                {{ $sAct['seccion_name'] }}
                            </span>
                        </div>

                        {{-- 2-column grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- Col 1 --}}
                            <div class="space-y-4">
                                {{-- Actividad Evaluativa --}}
                                @if(!empty($sAct['description']))
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Actividad Evaluativa</label>
                                    <p class="text-sm text-gray-200 leading-relaxed">{{ $sAct['description'] }}</p>
                                </div>
                                @endif

                                {{-- Tema generador --}}
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Tema Generador y Énfasis</label>
                                    <p class="text-sm text-gray-200 leading-relaxed">{{ $sAct['topic'] }}</p>
                                </div>

                                {{-- Tejido temático --}}
                                @if(!empty($sAct['thematic']))
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Tejido Temático</label>
                                    <p class="text-sm text-gray-200 leading-relaxed">{{ $sAct['thematic'] }}</p>
                                </div>
                                @endif

                                {{-- Referentes --}}
                                @if(!empty($sAct['references']))
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Referentes Teórico-Prácticos</label>
                                    <p class="text-sm text-gray-200 leading-relaxed">{{ $sAct['references'] }}</p>
                                </div>
                                @endif
                            </div>

                            {{-- Col 2 --}}
                            <div class="space-y-4">
                                {{-- Enseñanza --}}
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Enseñanza / Actividad Globalizada</label>
                                    <p class="text-sm text-gray-200 leading-relaxed whitespace-pre-wrap">{{ $sAct['teaching'] }}</p>
                                </div>

                                {{-- Aprendizaje --}}
                                @if(!empty($sAct['learning']))
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Aprendizaje</label>
                                    <p class="text-sm text-gray-200 leading-relaxed">{{ $sAct['learning'] }}</p>
                                </div>
                                @endif

                                {{-- Observaciones / ODS --}}
                                @if(!empty($sAct['observations']))
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">ODS / Sistematización</label>
                                    <p class="text-sm text-gray-200 leading-relaxed">{{ $sAct['observations'] }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Comentario J.Área --}}
                        @if(!empty($sAct['comments']))
                        <div class="p-4 bg-cyan-500/5 border border-cyan-500/10 rounded-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider">Comentario [J.Área]</span>
                            </div>
                            <p class="text-sm text-gray-300 leading-relaxed">{{ $sAct['comments'] }}</p>
                        </div>
                        @endif

                        {{-- Achievements --}}
                        @if(!empty($s2526DetailAchievements))
                        <div class="border-t border-white/5 pt-5">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">
                                    Indicadores / Logros ({{ count($s2526DetailAchievements) }})
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($s2526DetailAchievements as $ach)
                                    <div class="flex items-center gap-2 p-3 rounded-lg bg-gray-800/50 border border-white/5">
                                        <div class="w-6 h-6 rounded-full bg-amber-500/10 flex items-center justify-center shrink-0">
                                            <span class="text-[10px] font-bold text-amber-400">{{ $loop->iteration }}</span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs text-gray-200 leading-relaxed truncate" title="{{ $ach['name'] ?? '' }}">{{ $ach['name'] ?? '' }}</p>
                                            @if(!empty($ach['weighting']))
                                                <span class="text-[10px] text-gray-500 font-mono">Pond.: {{ $ach['weighting'] }}</span>
                                            @endif
                                        </div>
                                        @if(!empty($ach['status_quantitative_weighting']) && $ach['status_quantitative_weighting'] !== 'false')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shrink-0">
                                                Cuant.
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-3 border-t border-white/5 bg-gray-800/30 flex items-center justify-between">
                        {{-- Left: Copy button --}}
                        <button wire:click="s2526CopyFromDetail"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold bg-violet-600 hover:bg-violet-500 text-white border border-violet-400/20 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Copiar a mi plan
                        </button>

                        {{-- Right: Close button --}}
                        <button wire:click="closeS2526DetailModal"
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

    {{-- Create Activity Modal (root level) --}}
    @if($modeCreator)
        @include('livewire.profesor.activity.partials.create')
    @endif
</div>
