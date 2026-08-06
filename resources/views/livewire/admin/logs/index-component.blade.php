<div class="fade-in" x-data="{ confirmAction: @entangle('confirmAction'), drawerOpen: false }" {!! $autoRefresh ? 'wire:poll.3s' : '' !!}
    @keydown.window="
        const el = document.activeElement;
        const tag = el ? el.tagName : '';
        const isField = tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || (el && el.isContentEditable);
        if (event.key === 'Escape') {
            if (drawerOpen) { drawerOpen = false; return; }
            if (confirmAction) { confirmAction = null; }
            return;
        }
        if (event.key === '/' && !isField) {
            event.preventDefault();
            document.getElementById('log-search-input')?.focus();
        }
        if ((event.key === '+' || event.key === '=' ) && !isField) { $wire.nudgeDateRange(1); }
        if (event.key === '-' && !isField) { $wire.nudgeDateRange(-1); }
    ">
    <!-- Header -->
    <div class="mb-6 space-y-3">
        <div>
            <h1 class="text-lg font-extrabold text-white">Log del Sistema</h1>
            @if(! $tooLarge && isset($stats['total']) && empty($diffResult))
                <p class="text-xs text-gray-400 mt-1">
                    {{ number_format($stats['total']) }} entradas mostradas
                    @if($search || $filterLevel || $dateFrom || $dateTo)
                        <span class="text-gray-500">· con filtros</span>
                    @endif
                </p>
            @endif
        </div>
        {{-- Fila del texto descriptivo --}}
        <p class="text-emerald-400 font-medium text-sm">Inspecciona, filtra y administra los registros de la aplicación.</p>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            {{-- Fila de botones --}}
            @if($fileInfo)
                <div class="flex flex-wrap items-center gap-2 justify-start sm:justify-end">
                    @if($fileInfo['size'] > 0)
                        @if(! $tooLarge && ! $uploadedLogName)
                            <button wire:click="toggleAutoRefresh"
                            class="inline-flex items-center justify-center gap-2 px-4 h-10 overflow-hidden whitespace-nowrap rounded-lg border text-sm font-medium transition-all duration-300 {{ $autoRefresh ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white border-white/5' }}"
                            title="Actualizar automáticamente cada 3 segundos">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M4 9a8 8 0 0112.5-2.5M20 15a8 8 0 01-12.5 2.5"></path>
                            </svg>
                            <span class="inline-flex items-center gap-1.5">{{ $autoRefresh ? 'EN VIVO' : 'Auto' }}
                            @if($autoRefresh)
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
                            @endif
                            </span>
                        </button>
                    @endif
                    <button wire:click="download"
                        class="inline-flex items-center justify-center gap-2 px-4 h-10 overflow-hidden whitespace-nowrap bg-white/5 hover:bg-emerald-500/20 text-gray-300 hover:text-emerald-300 rounded-lg border border-white/5 transition-all duration-300 text-sm font-medium">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Descargar
                    </button>
                    <button wire:click="exportJson"
                        class="inline-flex items-center justify-center gap-2 px-4 h-10 overflow-hidden whitespace-nowrap bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 rounded-lg border border-sky-500/20 transition-all duration-300 text-sm font-medium disabled:opacity-50 disabled:cursor-wait">
                        <svg wire:loading.remove wire:target="exportJson" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        <svg wire:loading wire:target="exportJson" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span wire:loading.remove wire:target="exportJson">Exportar JSON</span>
                        <span wire:loading wire:target="exportJson">Generando…</span>
                    </button>
                    <button wire:click="{{ $diffMode ? 'exitDiff' : 'enterDiff' }}"
                        class="inline-flex items-center justify-center gap-2 px-4 h-10 overflow-hidden whitespace-nowrap rounded-lg border text-sm font-medium transition-all duration-300 {{ $diffMode ? 'bg-violet-500/20 text-violet-300 border-violet-500/30' : 'bg-white/5 hover:bg-violet-500/20 text-gray-300 hover:text-violet-300 border-white/5' }}"
                        title="{{ $diffMode ? 'Salir de la comparación de fechas' : 'Comparar entradas entre dos rangos de fechas' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        <span>{{ $diffMode ? 'Salir de comparar' : 'Comparar fechas' }}</span>
                    </button>
                    @if(! $uploadedLogName)
                    <button wire:click="confirmClean"
                        class="inline-flex items-center justify-center gap-2 px-4 h-10 overflow-hidden whitespace-nowrap bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 rounded-lg border border-amber-500/20 transition-all duration-300 text-sm font-medium">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Limpiar
                    </button>
                    <button wire:click="confirmDelete"
                        class="inline-flex items-center justify-center gap-2 px-4 h-10 overflow-hidden whitespace-nowrap bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg border border-red-500/20 transition-all duration-300 text-sm font-medium">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Eliminar
                    </button>
                    <button wire:click="confirmDeleteAll"
                        class="inline-flex items-center justify-center gap-2 px-4 h-10 overflow-hidden whitespace-nowrap bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg border border-red-500/20 transition-all duration-300 text-sm font-medium">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Eliminar todos
                    </button>
                    <button wire:click="confirmPrune"
                        class="inline-flex items-center justify-center gap-2 px-4 h-10 overflow-hidden whitespace-nowrap bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 rounded-lg border border-indigo-500/20 transition-all duration-300 text-sm font-medium">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Limpiar antiguos
                    </button>
                    @endif
                @endif
            </div>
        @endif

    </div>

    {{-- Archivo actual y tamaño --}}
    @if($fileInfo)
        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mb-6 px-5 py-3 bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg">
            <div class="flex items-center gap-2 text-xs text-gray-400">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="font-mono text-emerald-400 font-semibold">{{ $selectedFolder ? $selectedFolder . '/' : '' }}{{ $fileInfo['name'] }}</span>
            </div>
            <span class="text-[11px] text-gray-400">Tamaño: <span class="font-mono text-gray-300">{{ number_format($fileInfo['size'] / 1024, 1) }} KB</span></span>
            <span class="text-[11px] text-gray-400">Modificado: <span class="font-mono text-gray-300">{{ \Carbon\Carbon::createFromTimestamp($fileInfo['modified'])->diffForHumans() }}</span></span>

            @php
                $levelOrder = ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG'];
                $levelCounts = collect($stats ?? ['total' => 0])->except('total');
                $levelTotal = (int) ($stats['total'] ?? $levelCounts->sum());
                $levelColor = [
                    'EMERGENCY' => 'bg-fuchsia-500', 'ALERT' => 'bg-fuchsia-500',
                    'CRITICAL'  => 'bg-red-600',     'ERROR'    => 'bg-rose-500',
                    'WARNING'   => 'bg-amber-500',    'NOTICE'   => 'bg-cyan-500',
                    'INFO'      => 'bg-emerald-500',   'DEBUG'    => 'bg-gray-600',
                ];
                $bars = collect($levelOrder)->map(fn ($l) => ['level' => $l, 'count' => (int) ($levelCounts[$l] ?? 0), 'color' => $levelColor[$l] ?? 'bg-gray-600'])->filter(fn ($b) => $b['count'] > 0)->values();
            @endphp
            @if($bars->isNotEmpty() && $levelTotal > 0)
                <div class="w-full mt-3 pt-3 border-t border-white/5" x-data="{ hover: null }">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Severidad</span>
                        <div class="flex items-center gap-2 flex-wrap">
                            @foreach($bars as $bar)
                                <span x-on:mouseenter="hover = '{{ $bar['level'] }}'" x-on:mouseleave="hover = null" class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded cursor-default transition-opacity" :class="hover === null || hover === '{{ $bar['level'] }}' ? 'opacity-100' : 'opacity-40'">
                                    <span class="w-2 h-2 rounded-full {{ $bar['color'] }}"></span>
                                    <span class="text-gray-400">{{ $bar['level'] }} <span class="text-gray-200 font-semibold">{{ number_format($bar['count']) }}</span></span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex h-2 rounded-full overflow-hidden bg-gray-800/60" role="img" aria-label="Distribución por severidad">
                        @foreach($bars as $bar)
                            <div class="{{ $bar['color'] }} transition-all duration-300" x-bind:style="'width: '+(({{ $bar['count'] }} / {{ $levelTotal }}) * 100)+'%'" x-bind:class="hover === null || hover === '{{ $bar['level'] }}' ? 'opacity-100' : 'opacity-30'"></div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- B4 — drag & drop local --}}
    @if($uploadedLogName)
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6 px-5 py-3 bg-emerald-500/10 border border-emerald-500/30 rounded-lg">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-500/15 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-emerald-300">Archivo local cargado</p>
                    <p class="text-xs text-gray-400">Inspeccionando <span class="font-mono text-gray-200">{{ $uploadedLogName }}</span> — copia temporal, no está en <span class="font-mono">storage/logs</span>.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap shrink-0">
                <button wire:click="removeUploadedLog"
                    class="px-3 py-1.5 text-xs font-medium text-gray-300 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/5 transition-all duration-300">
                    Volver a storage/logs
                </button>
                <button wire:click="removeUploadedLog; enterDiff()"
                    class="px-3 py-1.5 text-xs font-medium text-violet-300 hover:text-violet-200 bg-violet-500/10 hover:bg-violet-500/20 rounded-lg border border-violet-500/20 transition-all duration-300">
                    Comparar fechas
                </button>
            </div>
        </div>
    @endif

    <div class="mb-6"
        x-data="{ drag: false }"
        @dragover.prevent="drag = true"
        @dragleave.prevent="drag = false"
        @drop.prevent="drag = false">
        <label for="local-log-input"
            class="flex items-center gap-3 px-5 py-4 border-2 border-dashed rounded-lg cursor-pointer transition-all duration-300 text-sm {{ $uploadedLogName ? 'border-gray-700 bg-gray-900/20 hover:border-emerald-500/40' : 'border-emerald-500/30 bg-emerald-500/[0.03] hover:bg-emerald-500/[0.06]' }}"
            :class="drag ? 'border-emerald-400 bg-emerald-500/10' : ''">
            <svg class="w-5 h-5 shrink-0 {{ $uploadedLogName ? 'text-emerald-400' : 'text-emerald-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <div class="min-w-0">
                <p class="text-emerald-300 font-medium">Arrastra un archivo <span class="font-mono">.log</span> aquí o haz clic para seleccionarlo</p>
                <p class="text-xs text-gray-500">Se inspecciona en memoria; no se guarda en <span class="font-mono">storage/logs</span>. Máx. 50 MB.</p>
            </div>
            <input id="local-log-input" type="file" wire:model="uploadedLog" accept=".log,.txt" class="sr-only">
        </label>
    </div>

    {{-- Alerta archivo demasiado grande --}}
    @if($tooLarge)
        <div class="bg-amber-500/10 border border-amber-500/30 rounded-lg p-6 text-center mb-6">
            <svg class="w-12 h-12 mx-auto text-amber-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h2 class="text-sm font-bold text-amber-400 mb-1">El archivo supera el límite de visualización</h2>
            <p class="text-sm text-gray-400 mb-4">Este log es demasiado grande para renderizarlo en el navegador (más de 50 MB). Descárgalo o límpialo desde el panel.</p>
            <button wire:click="download"
                class="px-5 py-2.5 text-sm font-bold uppercase tracking-widest text-white bg-emerald-500/20 hover:bg-emerald-500/30 rounded-lg border border-emerald-500/30 transition-all duration-300">
                Descargar archivo
            </button>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Carpeta</label>
                <select wire:model.live="selectedFolder" class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-colors">
                    <option value="">(raíz — storage/logs)</option>
                    @foreach($folderList as $folder)
                        <option value="{{ $folder }}">{{ $folder }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Archivo</label>
                <select wire:model.live="selectedFile" class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-colors">
                    @forelse($fileList as $name => $file)
                        <option value="{{ $name }}">{{ $name }} ({{ number_format($file['size'] / 1024, 1) }} KB)</option>
                    @empty
                        <option value="">Sin logs</option>
                    @endforelse
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Nivel</label>
                <select wire:model.live="filterLevel" class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-colors">
                    @foreach($levelOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
        </div>
        <div class="flex flex-col sm:flex-row sm:items-start gap-3 mt-3">
            
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Buscar en mensajes</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" id="log-search-input" wire:model.live.debounce.300ms="search" placeholder="Buscar... (atajo /)"
                        class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-10 pr-4 py-2.5 text-sm text-white placeholder-gray-400 focus:outline-none focus:border-emerald-500/50 transition-colors">
                </div>
                <label class="inline-flex items-center gap-2 mt-2 text-xs text-gray-400 hover:text-white cursor-pointer select-none">
                    <input type="checkbox" wire:model.live="searchIncludeStack" class="rounded border-white/10 bg-gray-800/50 text-emerald-500 shadow-sm focus:ring-emerald-500/50 focus:ring-offset-0">
                    Incluir stack &amp; contexto en la búsqueda
                </label>
            </div>

            <div class="sm:w-72 shrink-0">
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Rango de fechas</label>
                <div class="grid grid-cols-2 gap-2 w-full">
                    <div>
                        <input type="date" wire:model.live="dateFrom" class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-colors [color-scheme:dark]" aria-label="Desde">
                    </div>
                    <div>
                        <input type="date" wire:model.live="dateTo" class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-colors [color-scheme:dark]" aria-label="Hasta">
                    </div>
                </div>
            </div>

            <div class="gap-2 sm:pt-0">
                <label class="block text-xs font-medium text-gray-400 mb-1.5">..</label>
                <button wire:click="clearFilters"
                    class="px-4 py-2.5 text-sm text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/5 transition-all duration-300">
                    Limpiar filtros
                </button>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 -mt-3 mb-5 text-[11px] text-gray-400">
        <span class="text-gray-500 font-medium mr-1">Atajos:</span>
        <span class="inline-flex items-center gap-1"><kbd class="px-1.5 py-0.5 rounded bg-gray-800 border border-white/10 font-mono text-gray-300">/</kbd> enfocar búsqueda</span>
        <span class="inline-flex items-center gap-1"><kbd class="px-1.5 py-0.5 rounded bg-gray-800 border border-white/10 font-mono text-gray-300">Esc</kbd> cerrar diálogo</span>
        <span class="inline-flex items-center gap-1"><kbd class="px-1.5 py-0.5 rounded bg-gray-800 border border-white/10 font-mono text-gray-300">+</kbd>/<kbd class="px-1.5 py-0.5 rounded bg-gray-800 border border-white/10 font-mono text-gray-300">-</kbd> mover rango de fechas</span>
    </div>

    @if(!$tooLarge && isset($stats['total']) && $stats['total'] > 0 && empty($diffResult))
        {{-- Estadísticas por nivel --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 mb-6">
            @foreach([
                'ERROR' => ['rose', 'bg-rose-500/10 border-rose-500/20 text-rose-400'],
                'CRITICAL' => ['rose', 'bg-rose-500/10 border-rose-500/20 text-rose-400'],
                'WARNING' => ['amber', 'bg-amber-500/10 border-amber-500/20 text-amber-400'],
                'NOTICE' => ['cyan', 'bg-cyan-500/10 border-cyan-500/20 text-cyan-400'],
                'INFO' => ['emerald', 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400'],
                'DEBUG' => ['gray', 'bg-gray-500/10 border-gray-500/20 text-gray-400'],
            ] as $level => $style)
                @if(isset($stats[$level]))
                    <div class="border rounded-lg px-3 py-2.5 {{ $style[1] }}">
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-70">{{ $level }}</p>
                        <p class="text-xl font-extrabold mt-0.5">{{ $stats[$level] }}</p>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    {{-- Tabla de logs + panel de detalle (ocultos en modo diff) --}}
    @if(empty($diffResult))
    @php
        $selectedDetail = $selectedIndex !== null ? ($pageEntries[$selectedIndex] ?? null) : null
    @endphp
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/5 bg-gray-800/30">
                        <th class="px-4 py-2 text-left w-28">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Nivel</span>
                        </th>
                        <th class="px-4 py-2 text-left w-40">
                            <button wire:click="toggleSort"
                                class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-emerald-300 transition-colors group"
                                title="Alternar orden por fecha ({{ $sortDirection === 'desc' ? 'más reciente primero' : 'más antiguo primero' }})"
                                aria-pressed="{{ $sortDirection === 'desc' ? 'true' : 'false' }}">
                                Fecha
                                <svg class="w-3 h-3 transition-transform {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            </button>
                        </th>
                        <th class="px-4 py-2 text-left">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Mensaje</span>
                        </th>
                        <th class="px-4 py-2 text-center w-24">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Stack</span>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($logs as $log)
                        @php
                        $levelStyle = [
                            'EMERGENCY' => 'bg-red-500/15 text-red-400 border-red-500/20',
                            'ALERT'     => 'bg-red-500/15 text-red-400 border-red-500/20',
                            'CRITICAL'  => 'bg-rose-500/15 text-rose-400 border-rose-500/20',
                            'ERROR'     => 'bg-rose-500/15 text-rose-400 border-rose-500/20',
                            'WARNING'   => 'bg-amber-500/15 text-amber-400 border-amber-500/20',
                            'NOTICE'    => 'bg-cyan-500/15 text-cyan-400 border-cyan-500/20',
                            'INFO'      => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/20',
                            'DEBUG'     => 'bg-gray-500/15 text-gray-400 border-gray-500/20',
                        ][$log['level']] ?? 'bg-gray-500/15 text-gray-400 border-gray-500/20'
                        @endphp

                        <tbody class="group" x-data="{ expanded: false, copied: false, copyLog: function(text){ var self = this; self.copied = false; (navigator.clipboard && navigator.clipboard.writeText) ? navigator.clipboard.writeText(text).then(function(){ self.copied = true; setTimeout(function(){ self.copied = false; }, 1500); }).catch(function(){ self.copyLegacy(text); }) : self.copyLegacy(text); }, copyLegacy: function(text){ var ta = document.createElement('textarea'); ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0'; document.body.appendChild(ta); ta.select(); try { document.execCommand('copy'); } catch(e) {} document.body.removeChild(ta); this.copied = true; setTimeout(function(){ this.copied = false; }.bind(this), 1500); } }">
                        <tr @click="drawerOpen = true; $wire.selectEntry({{ $loop->index }})"
                            class="border-b border-white/5 transition-colors cursor-pointer {{ $selectedIndex === $loop->index ? 'bg-emerald-500/[0.06] border-l-2 border-l-emerald-500/40' : 'hover:bg-white/[0.02]' }}">
                            <td class="px-4 py-2 align-top pt-3">
                                <button wire:click.stop="filterByLevel('{{ $log['level'] }}')"
                                    title="{{ $filterLevel === $log['level'] ? 'Quitar filtro de nivel' : 'Filtrar por nivel '.$log['level'] }}"
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border transition-all duration-200 cursor-pointer hover:ring-2 hover:ring-white/10 {{ $filterLevel === $log['level'] ? 'ring-2 ring-emerald-400/40' : '' }} {{ $levelStyle }}">
                                    @php
                                        $icon = match ($log['level']) {
                                        'EMERGENCY', 'ALERT' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>',
                                        'CRITICAL', 'ERROR' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                        'WARNING' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M9 3.93a3 3 0 016 0l5.2 11.7A2 2 0 0118.46 19H5.54a2 2 0 01-1.74-3.36L9 3.93z"/>',
                                        'NOTICE', 'INFO' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                        default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>', // DEBUG / desconocido
                                        };
                                    @endphp
                                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24">{!! $icon !!}</svg>
                                    {{ $log['level'] }}
                                </button>
                            </td>
                            <td class="px-4 py-2 align-top pt-3">
                                <p class="text-xs font-mono text-gray-400">{{ $log['date'] }}</p>
                                @if($log['env'])
                                    <p class="text-[10px] text-gray-500 mt-0.5">[{{ $log['env'] }}]</p>
                                @endif
                            </td>
                            <td class="px-4 py-2 align-top pt-2">
                                <p class="text-sm text-gray-200 break-words leading-snug">
                                    {{ Str::limit($log['message'], 220) }}
                                </p>
                                @if($log['context'])
                                    <div class="mt-1">
                                        <button @click="expanded = true" x-show="!expanded"
                                            :aria-expanded="expanded ? 'true' : 'false'"
                                            aria-controls="ctx-{{ $loop->index }}"
                                            class="text-[10px] text-gray-500 hover:text-cyan-400 font-medium underline underline-offset-2">
                                            Ver contexto (JSON)
                                        </button>
                                        <pre id="ctx-{{ $loop->index }}" x-show="expanded" x-cloak x-transition
                                            class="mt-1 text-[11px] text-cyan-300/80 bg-gray-950/60 border border-white/5 rounded-lg p-3 overflow-x-auto whitespace-pre-wrap break-words max-h-48">{{ $log['context'] }}</pre>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-end align-top pt-3">
                                <div class="flex items-end justify-end gap-1">
                                    @if($log['stack'])
                                        <button @click.stop="expanded = !expanded"
                                            :aria-expanded="expanded ? 'true' : 'false'"
                                            aria-controls="stack-{{ $loop->index }}"
                                            class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-medium text-gray-400 hover:text-emerald-300 bg-white/5 hover:bg-emerald-500/20 rounded-md border border-white/5 transition-all duration-200"
                                            title="Expandir stack trace">
                                            <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                            trace
                                        </button>
                                    @else
                                        <span class="text-[10px] text-gray-500">—</span>
                                    @endif

                                    <button @click.stop="copyLog({{ json_encode(strip_tags($log['message'] . ($log['context'] ? '\n' . $log['context'] : '') . ($log['stack'] ? '\n\n' . $log['stack'] : '')), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }})"
                                        class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-medium transition-all duration-200 rounded-md border border-white/5"
                                        :class="copied ? 'text-emerald-300 bg-emerald-500/20 border-emerald-500/20' : 'text-gray-400 hover:text-cyan-300 bg-white/5 hover:bg-cyan-500/20'"
                                        :title="copied ? 'Copiado!' : 'Copiar mensaje, contexto y stack'">
                                        <svg x-show="!copied" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <svg x-show="copied" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span x-text="copied ? 'copiado' : 'copiar'"></span>
                                    </button>
                                    <button type="button" @click.stop="drawerOpen = true; $wire.selectEntry({{ $loop->index }})"
                                        class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-medium transition-all duration-200 rounded-md border border-white/5 text-gray-400 hover:text-violet-300 bg-white/5 hover:bg-violet-500/20"
                                        title="Ver detalle completo en panel">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        {{-- <span>Ver detalle</span> --}}
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @if($log['stack'])
                            <tr id="stack-{{ $loop->index }}" x-show="expanded" x-cloak x-transition class="odd:bg-transparent hover:bg-white/[0.02]">
                                <td colspan="4" class="px-4 pb-3">
                                    <div class="ml-0 sm:ml-28">
                                        <pre class="text-[11px] font-mono text-gray-400 bg-gray-950/60 border border-white/5 rounded-lg p-4 overflow-x-auto whitespace-pre-wrap break-words max-h-96 leading-relaxed">{{ $log['stack'] }}</pre>
                                    </div>
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    @empty
                        @php
                            $hasFilters = (bool) ($search || $filterLevel || $dateFrom || $dateTo);
                            $emptyTitle = $hasFilters
                                ? 'Sin resultados para los filtros aplicados'
                                : 'Este archivo no contiene entradas de log';
                            $emptyDesc = $hasFilters
                                ? 'Ninguna entrada coincide con la búsqueda, nivel o rango de fechas seleccionado. Prueba a ampliar o quitar los filtros para ver más resultados.'
                                : 'El archivo está vacío o aún no se ha registrado ningún evento que coincida con los criterios actuales.';
                        @endphp
                        <tbody>
                        <tr>
                            <td colspan="4" class="px-4 py-14 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-800/70 border border-white/5 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="space-y-1.5">
                                        <p class="text-gray-300 font-medium text-sm">{{ $emptyTitle }}</p>
                                        <p class="text-gray-500 text-xs max-w-sm mx-auto leading-relaxed">{{ $emptyDesc }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        @if($hasFilters)
                                            <button wire:click="clearFilters"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 rounded-lg border border-emerald-500/20 transition-all duration-300">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Limpiar filtros
                                            </button>
                                        @endif
                                        <button wire:click="$refresh"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-gray-300 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/10 transition-all duration-300">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M4 9a8 8 0 0112.5-2.5M20 15a8 8 0 01-12.5 2.5"></path></svg>
                                            Recargar
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-wrapper :paginator="$logs" />
    </div>
    @else

    {{-- B5 — diff entre fechas --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-white/5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-violet-300">Comparación entre dos rangos de fechas</h3>
                <p class="text-xs text-gray-500 mt-0.5">Se comparan las líneas de <span class="font-mono text-gray-300">{{ $fileInfo ? ($fileInfo['name'] ?? $uploadedLogName) : ($uploadedLogName ?: 'log') }}</span> por contenido; las repeticiones exactas cuentan como comunes.</p>
            </div>
            <button wire:click="exitDiff"
                class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-gray-300 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/5 transition-all duration-300">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a la tabla
            </button>
        </div>

        <div class="px-5 py-4 border-b border-white/5 grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="space-y-2">
                <label class="block text-xs font-medium text-gray-400">Rango A — desde</label>
                <input type="date" wire:model.live="diffFromA" class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-violet-500/50 transition-colors [color-scheme:dark]">
                <label class="block text-xs font-medium text-gray-400">Rango A — hasta</label>
                <input type="date" wire:model.live="diffToA" class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-violet-500/50 transition-colors [color-scheme:dark]">
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-white/5 text-xs text-gray-300">Total: <strong class="text-violet-300">{{ $diffResult['rangeA']['total'] }}</strong></span>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-white/5 text-xs text-gray-300">Únicas: <strong class="text-violet-300">{{ $diffResult['rangeA']['distinct'] }}</strong></span>
                </div>
            </div>

            <div class="flex lg:flex-col items-center justify-center gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-white">{{ $diffResult['common'] }}</p>
                    <p class="text-xs text-gray-500">líneas comunes</p>
                </div>
                <button wire:click="swapDiffRanges"
                    class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-violet-300 hover:text-violet-200 bg-violet-500/10 hover:bg-violet-500/20 rounded-lg border border-violet-500/20 transition-all duration-300"
                    title="Intercambiar los dos rangos">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                    Intercambiar
                </button>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-medium text-gray-400">Rango B — desde</label>
                <input type="date" wire:model.live="diffFromB" class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-violet-500/50 transition-colors [color-scheme:dark]">
                <label class="block text-xs font-medium text-gray-400">Rango B — hasta</label>
                <input type="date" wire:model.live="diffToB" class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-violet-500/50 transition-colors [color-scheme:dark]">
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-white/5 text-xs text-gray-300">Total: <strong class="text-violet-300">{{ $diffResult['rangeB']['total'] }}</strong></span>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-white/5 text-xs text-gray-300">Únicas: <strong class="text-violet-300">{{ $diffResult['rangeB']['distinct'] }}</strong></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
            {{-- Solo en rango A (desaparecidas) --}}
            <div class="border-b lg:border-b-0 lg:border-r border-white/5">
                <div class="px-5 py-3 bg-amber-500/5 border-b border-white/5">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-amber-300">Solo en rango A <span class="ml-1 text-amber-400/70 normal-case font-medium">({{ count($diffResult['removed']) }})</span></h4>
                </div>
                <div class="p-4 max-h-[28rem] overflow-y-auto space-y-2">
                    @forelse($diffResult['removed'] as $entry)
                        @include('livewire.admin.logs._entry-row-diff', ['entry' => $entry, 'tone' => 'amber'])
                    @empty
                        <p class="text-sm text-gray-500 text-center py-8">Sin diferencias en este rango.</p>
                    @endforelse
                </div>
            </div>

            {{-- Solo en rango B (nuevas) --}}
            <div>
                <div class="px-5 py-3 bg-emerald-500/5 border-b border-white/5">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-emerald-300">Solo en rango B <span class="ml-1 text-emerald-400/70 normal-case font-medium">({{ count($diffResult['added']) }})</span></h4>
                </div>
                <div class="p-4 max-h-[28rem] overflow-y-auto space-y-2 ">
                    @forelse($diffResult['added'] as $entry)
                        @include('livewire.admin.logs._entry-row-diff', ['entry' => $entry, 'tone' => 'emerald'])
                    @empty
                        <p class="text-sm text-gray-500 text-center py-8">Sin diferencias en este rango.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Líneas comunes (presentes en ambos rangos) --}}
        <div class="border-t border-white/5">
            <div class="px-5 py-3 bg-white/[0.03] border-b border-white/5">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-400">Líneas comunes <span class="ml-1 text-gray-500 normal-case font-medium">({{ $diffResult['common'] }})</span></h4>
            </div>
            <div class="p-4 max-h-64 overflow-y-auto space-y-2">
                @forelse($diffResult['commonEntries'] ?? [] as $entry)
                    @include('livewire.admin.logs._entry-row-diff', ['entry' => $entry, 'tone' => 'neutral'])
                @empty
                    <p class="text-sm text-gray-500 text-center py-6">No hay líneas repetidas entre ambos rangos.</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    {{-- Confirmación: Limpiar --}}
    <div x-show="confirmAction === 'clean'" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        @click.self="confirmAction = null"
        role="dialog" aria-modal="true" aria-labelledby="log-clean-title">
        <div class="w-full max-w-md bg-gray-900/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-2xl shadow-black/50 p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-2 bg-amber-500/10 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <h3 id="log-clean-title" class="text-lg font-bold text-white mb-2">¿Limpiar el log?</h3>
            <p class="text-sm text-gray-400 mb-6">
                Se vaciará el contenido de <span class="font-mono text-gray-300">{{ $fileInfo['name'] ?? $selectedFile }}</span>. El archivo se conservará pero quedará sin entradas.
            </p>
            <div class="flex items-center justify-center gap-3">
                <button wire:click="cancelAction"
                    class="px-5 py-2.5 text-sm font-medium text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/5 transition-all duration-300">
                    Cancelar
                </button>
                <button wire:click="cleanLog"
                    class="px-5 py-2.5 text-sm font-bold uppercase tracking-widest text-white bg-amber-500/20 hover:bg-amber-500/30 rounded-lg border border-amber-500/30 transition-all duration-300">
                    <span wire:loading.remove wire:target="cleanLog">Limpiar</span>
                    <span wire:loading wire:target="cleanLog">Limpiando...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Confirmación: Eliminar --}}
    <div x-show="confirmAction === 'delete'" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        @click.self="confirmAction = null"
        role="dialog" aria-modal="true" aria-labelledby="log-delete-title">
        <div class="w-full max-w-md bg-gray-900/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-2xl shadow-black/50 p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-2 bg-red-500/10 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 id="log-delete-title" class="text-lg font-bold text-white mb-2">¿Eliminar el log?</h3>
            <p class="text-sm text-gray-400 mb-6">
                Se eliminará <span class="font-mono text-gray-300">{{ $fileInfo['name'] ?? $selectedFile }}</span>. Se guardará automáticamente una copia en <span class="font-mono text-gray-300">logs/backups/</span>.
            </p>
            <div class="flex items-center justify-center gap-3">
                <button wire:click="cancelAction"
                    class="px-5 py-2.5 text-sm font-medium text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/5 transition-all duration-300">
                    Cancelar
                </button>
                <button wire:click="deleteLog"
                    class="px-5 py-2.5 text-sm font-bold uppercase tracking-widest text-white bg-red-500/20 hover:bg-red-500/30 rounded-lg border border-red-500/30 transition-all duration-300">
                    <span wire:loading.remove wire:target="deleteLog">Eliminar</span>
                    <span wire:loading wire:target="deleteLog">Eliminando...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Confirmación: Eliminar todos los logs de la carpeta --}}
    <div x-show="confirmAction === 'deleteAll'" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        @click.self="confirmAction = null"
        role="dialog" aria-modal="true" aria-labelledby="log-deleteall-title">
        <div class="w-full max-w-md bg-gray-900/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-2xl shadow-black/50 p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-2 bg-red-500/10 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 id="log-deleteall-title" class="text-lg font-bold text-white mb-2">¿Eliminar todos los logs?</h3>
            <p class="text-sm text-gray-400 mb-6">
                Se eliminarán <span class="font-bold text-gray-200">{{ count($fileList) }}</span> archivo(s) de la carpeta
                <span class="font-mono text-gray-300">{{ $selectedFolder ?: '(raíz)' }}</span>.
                Se guardará una copia de cada uno en <span class="font-mono text-gray-300">logs/backups/</span>.
            </p>
            <div class="flex items-center justify-center gap-3">
                <button wire:click="cancelAction"
                    class="px-5 py-2.5 text-sm font-medium text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/5 transition-all duration-300">
                    Cancelar
                </button>
                <button wire:click="deleteAllLogs"
                    class="px-5 py-2.5 text-sm font-bold uppercase tracking-widest text-white bg-red-500/20 hover:bg-red-500/30 rounded-lg border border-red-500/30 transition-all duration-300">
                    <span wire:loading.remove wire:target="deleteAllLogs">Eliminar todos</span>
                    <span wire:loading wire:target="deleteAllLogs">Eliminando...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Confirmación: Limpiar logs antiguos --}}
    <div x-show="confirmAction === 'prune'" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        @click.self="confirmAction = null"
        role="dialog" aria-modal="true" aria-labelledby="log-prune-title">
        <div class="w-full max-w-md bg-gray-900/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-2xl shadow-black/50 p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-2 bg-indigo-500/10 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 id="log-prune-title" class="text-lg font-bold text-white mb-2">Limpiar logs antiguos</h3>
            <p class="text-sm text-gray-400 mb-4">
                Se eliminarán los archivos de la carpeta
                <span class="font-mono text-gray-300">{{ $selectedFolder ?: '(raíz)' }}</span>
                cuya fecha de modificación sea mayor a:
            </p>
            <div class="flex items-center justify-center gap-2 mb-6">
                <input type="number" wire:model.live="pruneDays" min="1" max="365"
                    class="w-24 text-center bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500/50">
                <span class="text-sm text-gray-400">días</span>
            </div>
            <div class="flex items-center justify-center gap-3">
                <button wire:click="cancelAction"
                    class="px-5 py-2.5 text-sm font-medium text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/5 transition-all duration-300">
                    Cancelar
                </button>
                <button wire:click="pruneOldLogs"
                    class="px-5 py-2.5 text-sm font-bold uppercase tracking-widest text-white bg-indigo-500/20 hover:bg-indigo-500/30 rounded-lg border border-indigo-500/30 transition-all duration-300">
                    <span wire:loading.remove wire:target="pruneOldLogs">Limpiar</span>
                    <span wire:loading wire:target="pruneOldLogs">Limpiando...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Drawer de detalle (desliza de derecha a izquierda) --}}
    @if(empty($diffResult) && !empty($selectedDetail))
    <div x-show="drawerOpen" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[300] bg-black/60 backdrop-blur-sm"
        @click="drawerOpen = false"
        role="presentation" aria-hidden="true"></div>
    <div x-show="drawerOpen" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-end="translate-x-full"
        class="fixed top-0 right-0 h-full w-full max-w-lg z-[310] bg-gray-900/95 backdrop-blur-xl border-l border-white/10 shadow-2xl shadow-black/50 flex flex-col"
        role="dialog" aria-modal="true" aria-labelledby="log-detail-title">

        <div class="flex items-center justify-between px-4 py-3 border-b border-white/5 bg-gray-800/30 shrink-0">
            <h3 id="log-detail-title" class="text-xs font-bold uppercase tracking-widest text-gray-400">Detalle</h3>
            <div class="flex items-center gap-2">
                @if($selectedDetail['stack'])
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border {{ $selectedDetail['level'] === 'ERROR' || $selectedDetail['level'] === 'CRITICAL' ? 'border-rose-500/20 text-rose-400' : 'border-emerald-500/20 text-emerald-400' }}">trace</span>
                @endif
                <button type="button" @click="drawerOpen = false"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition-all duration-300"
                    title="Cerrar panel (Esc)" aria-label="Cerrar panel de detalle">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-4 overflow-y-auto grow">
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border {{ $selectedDetail['level'] === 'ERROR' || $selectedDetail['level'] === 'CRITICAL' ? 'bg-rose-500/15 text-rose-400 border-rose-500/20' : ($selectedDetail['level'] === 'WARNING' ? 'bg-amber-500/15 text-amber-400 border-amber-500/20' : 'bg-emerald-500/15 text-emerald-400 border-emerald-500/20') }}">{{ $selectedDetail['level'] }}</span>
                <span class="text-xs font-mono text-gray-400">{{ $selectedDetail['date'] }}</span>
                @if($selectedDetail['env'])
                    <span class="text-[10px] text-gray-400">[{{ $selectedDetail['env'] }}]</span>
                @endif
            </div>

            <p class="text-sm text-gray-100 leading-relaxed break-words mb-4">{{ $selectedDetail['message'] }}</p>

            @if($selectedDetail['context'])
                <p class="text-[10px] font-bold uppercase tracking-widest text-cyan-400 mb-1.5">Contexto (JSON)</p>
                <pre class="text-[11px] text-cyan-300/80 bg-gray-950/60 border border-white/5 rounded-lg p-3 overflow-x-auto whitespace-pre-wrap break-words max-h-48 mb-4">{{ $selectedDetail['context'] }}</pre>
            @endif

            @if($selectedDetail['stack'])
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Stack trace</p>
                <pre class="text-[11px] font-mono text-gray-400 bg-gray-950/60 border border-white/5 rounded-lg p-4 overflow-x-auto whitespace-pre-wrap break-words max-h-[45vh] leading-relaxed">{{ $selectedDetail['stack'] }}</pre>
            @endif
        </div>
    </div>
    @endif
</div>
