<div class="fade-in space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Lecciones LMS</h1>
            <p class="text-amber-600 dark:text-amber-400 font-medium text-sm">Monitoreo de contenido publicado</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-2 sm:p-5 rounded-lg mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por tema..."
                    class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none transition-all placeholder:text-gray-600">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Plan Estudio</label>
                <select wire:model.live="pestudio_id"
                    class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($list_pestudio as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Profesor</label>
                <select wire:model.live="profesor_id"
                    class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($list_profesors as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Grado/Año</label>
                <select wire:model.live="grado_id"
                    class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($list_grado as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Sección</label>
                <select wire:model.live="seccion_id"
                    class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none transition-all">
                    <option value="">Todas</option>
                    @foreach($list_seccion as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Resultados</label>
                <select wire:model.live="paginate"
                    class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none transition-all">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="9999">Todos</option>
                </select>
            </div>
            <div class="flex items-end gap-3">
                <label class="relative inline-flex items-center gap-2 cursor-pointer min-h-[44px] select-none group">
                    <input type="checkbox" wire:model.live="filter_published" class="sr-only peer">
                    <div class="relative w-10 h-6 rounded-full transition-all duration-500 peer-checked:bg-gradient-to-r peer-checked:from-amber-500 peer-checked:to-yellow-500 bg-gray-300 dark:bg-white/10 peer-checked:shadow-lg peer-checked:shadow-amber-500/30 after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:duration-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:shadow-md after:border after:border-gray-200 dark:after:border-white/10 peer-checked:after:shadow-amber-500/30 group-hover:after:scale-110 peer-checked:group-hover:after:scale-110"></div>
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 peer-checked:text-amber-600 dark:peer-checked:text-amber-400 transition-all duration-300 peer-checked:drop-shadow-[0_1px_2px_rgba(217,119,6,0.15)]">
                        Solo publicadas
                    </span>
                </label>
                <label class="relative inline-flex items-center gap-2 cursor-pointer min-h-[44px] select-none group">
                    <input type="checkbox" wire:model.live="filter_scheduled" class="sr-only peer">
                    <div class="relative w-10 h-6 rounded-full transition-all duration-500 peer-checked:bg-gradient-to-r peer-checked:from-amber-500 peer-checked:to-yellow-500 bg-gray-300 dark:bg-white/10 peer-checked:shadow-lg peer-checked:shadow-amber-500/30 after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:duration-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:shadow-md after:border after:border-gray-200 dark:after:border-white/10 peer-checked:after:shadow-amber-500/30 group-hover:after:scale-110 peer-checked:group-hover:after:scale-110"></div>
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 peer-checked:text-amber-600 dark:peer-checked:text-amber-400 transition-all duration-300 peer-checked:drop-shadow-[0_1px_2px_rgba(217,119,6,0.15)]">
                        Solo programadas
                    </span>
                </label>
            </div>
        </div>
    </div>

    {{-- View Mode Toggle (Grid / Table) — seccion pattern --}}
    <div class="flex items-center justify-end mb-4"
         x-data="{ mode: localStorage.getItem('lessons-view-mode') || 'table' }"
         x-init="$watch('mode', val => {
             localStorage.setItem('lessons-view-mode', val);
             window.dispatchEvent(new CustomEvent('lessons-view-mode-changed', { detail: { mode: val } }))
         })">
        <div class="inline-flex items-center bg-gray-900/40 border border-white/5 rounded-lg p-0.5 gap-0.5">
            <button @click="mode = 'grid'"
                :class="mode === 'grid' ? 'bg-amber-500/15 text-amber-400 border-amber-500/30' : 'bg-transparent text-gray-500 hover:text-gray-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-transparent transition-all duration-200 text-[11px] font-bold"
                title="Vista Grid">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="hidden sm:inline">Grid</span>
            </button>
            <button @click="mode = 'table'"
                :class="mode === 'table' ? 'bg-amber-500/15 text-amber-400 border-amber-500/30' : 'bg-transparent text-gray-500 hover:text-gray-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-transparent transition-all duration-200 text-[11px] font-bold"
                title="Vista Tabla">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span class="hidden sm:inline">Tabla</span>
            </button>
        </div>
    </div>

    {{-- View Container --}}
    <div x-cloak
         x-data="{ mode: localStorage.getItem('lessons-view-mode') || 'table' }"
         x-init="() => { if (!localStorage.getItem('lessons-view-mode')) localStorage.setItem('lessons-view-mode', 'table') }"
         x-on:lessons-view-mode-changed.window="mode = $event.detail.mode"
         wire:key="lessons-tab-content-{{ $lapso_id }}-{{ $pestudio_id ?? 'all' }}-{{ $filter_published ? 'pub' : 'all' }}-{{ $filter_scheduled ? 'sch' : 'all' }}">

        {{-- ===== TAB NAVIGATION (Lapso tabs) ===== --}}
        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden mb-6">
            <div class="border-b border-white/5">
                <nav class="flex overflow-x-auto [&::-webkit-scrollbar]:h-1" style="scrollbar-width: thin;">
                    @foreach($tabsLapsos as $index => $lapsoItem)
                        @php $isActive = $lapsoItem->id == $lapso_id; @endphp
                        <button wire:click="selectLapso({{ $lapsoItem->id }})"
                            title="{{ $lapsoItem->name }}"
                            class="flex-1 px-2 sm:px-3 lg:px-6 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap
                                   {{ $isActive ? 'text-amber-400 border-amber-500 bg-amber-500/5' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600' }}">
                            <svg class="w-4 h-4 inline sm:mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="hidden sm:inline">{{ $lapsoItem->name }}</span>
                            <span class="hidden sm:block text-[9px] font-normal text-gray-500 normal-case">{{ $lapsoItem->code }}</span>
                        </button>
                    @endforeach
                </nav>
            </div>

            <div class="p-2 sm:p-4 lg:p-6">

        {{-- ═══ GRID MODE ═══ --}}
        <div x-show="mode === 'grid'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="bg-gray-900/60 border border-white/5 rounded-2xl p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($lessons as $lesson)
                        @php
                            $pubStatus = $lesson->lmsPublication?->status;
                            $statusBorder = match($pubStatus) {
                                'PUBLISHED' => 'border-t-emerald-500',
                                'SCHEDULED' => 'border-t-amber-500',
                                default => 'border-t-gray-600',
                            };
                        @endphp
                        <div class="rounded-2xl border border-white/5 border-t-4 bg-gray-900 hover:border-amber-500/30 transition-all duration-200 flex flex-col overflow-hidden h-full {{ $statusBorder }}
                            {{ $pubStatus === 'PUBLISHED' ? '' : 'opacity-85' }}">
                            {{-- Header: status dot + code --}}
                            <div class="flex items-center justify-between px-4 pt-4 pb-3 border-b border-white/5 gap-3">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    @if($pubStatus === 'PUBLISHED')
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                    @elseif($pubStatus === 'SCHEDULED')
                                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-gray-500 shrink-0"></span>
                                    @endif
                                    <span class="text-[10px] font-bold uppercase tracking-widest @if($pubStatus === 'PUBLISHED') text-emerald-400 @elseif($pubStatus === 'SCHEDULED') text-amber-400 @else text-gray-500 @endif">
                                        @if($pubStatus === 'PUBLISHED') Publicada
                                        @elseif($pubStatus === 'SCHEDULED') Programada
                                        @else Borrador @endif
                                    </span>
                                </div>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/12 text-amber-400 border border-amber-500/20 shrink-0">
                                    LMS
                                </span>
                            </div>

                            {{-- Body --}}
                            <div class="px-4 py-3 space-y-2.5 flex-1">
                                {{-- Topic --}}
                                <h3 class="text-sm font-bold text-white leading-snug line-clamp-2" title="{{ $lesson->topic }}">
                                    {{ $lesson->topic }}
                                </h3>

                                {{-- Asignatura --}}
                                <div class="flex items-center gap-2 text-[11px]">
                                    <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <span class="text-gray-400 truncate">{{ $lesson->pevaluacion?->pensum?->asignatura?->name ?? '—' }}</span>
                                </div>

                                {{-- Profesor --}}
                                <div class="flex items-center gap-2 text-[11px]">
                                    <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-gray-400 truncate">{{ $lesson->pevaluacion?->profesor?->fullname ?? '—' }}</span>
                                </div>

                                {{-- Lapso --}}
                                <div class="flex items-center gap-2 text-[11px]">
                                    <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-gray-500 font-mono text-[10px]">{{ $lesson->pevaluacion?->lapso?->name ?? '—' }}</span>
                                </div>
                            </div>

                            {{-- Footer: action --}}
                            <div class="px-4 py-3 border-t border-white/5 bg-white/[0.03] flex flex-col gap-2">
                                <button type="button" wire:click="previewLesson({{ $lesson->id }})"
                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-bold bg-amber-500/12 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver lección
                                </button>
                                @if($lesson->lmsPublication && $lesson->lmsPublication->status === 'SCHEDULED')
                                    <button type="button" wire:click="confirmPublishLesson({{ $lesson->id }})"
                                        class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-bold bg-emerald-500/12 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Publicar ahora
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <svg class="w-14 h-14 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-gray-500 font-medium mb-2">No se encontraron lecciones</p>
                            <p class="text-gray-600 text-sm">Ajusta los filtros o verifica que existan lecciones en tus áreas asignadas.</p>
                        </div>
                    @endforelse
                </div>

                @if($lessons->hasPages())
                    <div class="mt-6">
                        {{ $lessons->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ═══ TABLE MODE ═══ --}}
        <div x-show="mode === 'table'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
                <div class="overflow-x-auto [&::-webkit-scrollbar]:h-1.5" style="scrollbar-width: thin;">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-white/5">
                                <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Tema</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Asignatura</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Profesor</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Lapso</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</th>
                                <th class="text-right px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($lessons as $lesson)
                                @php
                                    $pubStatus = $lesson->lmsPublication?->status;
                                    $rowBorder = match($pubStatus) {
                                        'PUBLISHED' => 'border-l-emerald-500',
                                        'SCHEDULED' => 'border-l-amber-500',
                                        default => 'border-l-gray-600',
                                    };
                                @endphp
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-5 py-3 border-l-4 {{ $rowBorder }}">
                                        <span class="text-white font-medium">{{ $lesson->topic }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-400">
                                        {{ $lesson->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-400">
                                        {{ $lesson->pevaluacion?->profesor?->fullname ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-400">
                                        {{ $lesson->pevaluacion?->lapso?->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($pubStatus === 'PUBLISHED')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/12 text-emerald-400 border border-emerald-500/20">
                                                Publicada
                                            </span>
                                        @elseif($pubStatus === 'SCHEDULED')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/12 text-amber-400 border border-amber-500/20">
                                                Programada
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-gray-500/12 text-gray-400 border border-gray-500/20">
                                                Borrador
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <button type="button" wire:click="previewLesson({{ $lesson->id }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold bg-amber-500/12 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all duration-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Ver
                                            </button>
                                            @if($lesson->lmsPublication && $lesson->lmsPublication->status === 'SCHEDULED')
                                                <button type="button" wire:click="confirmPublishLesson({{ $lesson->id }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold bg-emerald-500/12 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Publicar
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <div>
                                            <svg class="w-14 h-14 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <p class="text-gray-500 font-medium mb-1">No se encontraron lecciones</p>
                                            <p class="text-gray-600 text-sm">Ajusta los filtros o verifica que existan lecciones en tus áreas asignadas.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($lessons->hasPages())
                    <x-pagination-wrapper :paginator="$lessons" />
                @endif
            </div>
        </div>

            </div>{{-- /tab-content padding --}}
        </div>{{-- /tab wrapper --}}

        {{-- ── MODAL: Vista previa de lección (student-preview) ── --}}
        @if($previewData)
            <x-lms.student-preview :preview="$previewData" closeMethod="closeLessonPreview" wire:key="student-preview-{{ $previewLessonId }}" />
        @endif

        {{-- ── MODAL: Confirmar publicación ── --}}
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
                        <button wire:click="cancelPublishLesson" class="p-2 min-w-[44px] min-h-[44px] rounded-lg text-gray-400 dark:text-slate-500 hover:text-gray-900 dark:hover:text-white transition-colors">
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
                        <button wire:click="cancelPublishLesson"
                                class="px-4 py-1.5 rounded-lg text-sm font-medium text-gray-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 hover:bg-gray-100 dark:hover:bg-slate-700 transition-all">
                            Cancelar
                        </button>
                        <button wire:click="doPublishLesson"
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
    </div>{{-- /tab outer --}}

    @include('leadership.help-lessons')
</div>{{-- /root --}}