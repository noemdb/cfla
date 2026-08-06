<div class="fade-in" x-data="{ confirmAction: @entangle('confirmAction') }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-1">Log del Sistema</h1>
            <p class="text-emerald-400 font-medium text-sm">Inspecciona, filtra y administra los registros de la aplicación.</p>
        </div>

        @if($fileInfo)
            <div class="flex items-center gap-2">
                @if($fileInfo['size'] > 0)
                    <button wire:click="download"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/5 hover:bg-emerald-500/20 text-gray-300 hover:text-emerald-300 rounded-lg border border-white/5 transition-all duration-300 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Descargar
                    </button>
                    <button wire:click="confirmClean"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 rounded-lg border border-amber-500/20 transition-all duration-300 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Limpiar
                    </button>
                    <button wire:click="confirmDelete"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg border border-red-500/20 transition-all duration-300 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Eliminar
                    </button>
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
                <span class="font-mono text-emerald-400 font-semibold">{{ $fileInfo['name'] }}</span>
            </div>
            <span class="text-[11px] text-gray-500">Tamaño: <span class="font-mono text-gray-300">{{ number_format($fileInfo['size'] / 1024, 1) }} KB</span></span>
            <span class="text-[11px] text-gray-500">Modificado: <span class="font-mono text-gray-300">{{ \Carbon\Carbon::createFromTimestamp($fileInfo['modified'])->diffForHumans() }}</span></span>
        </div>
    @endif

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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
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
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Desde</label>
                <input type="date" wire:model.live="dateFrom" class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-colors [color-scheme:dark]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Hasta</label>
                <input type="date" wire:model.live="dateTo" class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-colors [color-scheme:dark]">
            </div>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mt-3">
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Buscar en mensajes</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar en el contenido del log..."
                        class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-10 pr-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-emerald-500/50 transition-colors">
                </div>
            </div>
            <div class="flex items-end gap-2">
                <button wire:click="clearFilters"
                    class="px-4 py-2.5 text-sm text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/5 transition-all duration-300">
                    Limpiar filtros
                </button>
            </div>
        </div>
    </div>

    @if(!$tooLarge && isset($stats['total']) && $stats['total'] > 0)
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

    {{-- Tabla de logs --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/5 bg-gray-800/30">
                        <th class="px-4 py-2 text-left w-28">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Nivel</span>
                        </th>
                        <th class="px-4 py-2 text-left w-40">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Fecha</span>
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
                        @php($levelStyle = [
                            'EMERGENCY' => 'bg-red-500/15 text-red-400 border-red-500/20',
                            'ALERT'     => 'bg-red-500/15 text-red-400 border-red-500/20',
                            'CRITICAL'  => 'bg-rose-500/15 text-rose-400 border-rose-500/20',
                            'ERROR'     => 'bg-rose-500/15 text-rose-400 border-rose-500/20',
                            'WARNING'   => 'bg-amber-500/15 text-amber-400 border-amber-500/20',
                            'NOTICE'    => 'bg-cyan-500/15 text-cyan-400 border-cyan-500/20',
                            'INFO'      => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/20',
                            'DEBUG'     => 'bg-gray-500/15 text-gray-400 border-gray-500/20',
                        ][$log['level']] ?? 'bg-gray-500/15 text-gray-400 border-gray-500/20')

                        <tr class="border-b border-white/5 hover:bg-white/[0.02] transition-colors" x-data="{ expanded: false, copied: false, copyLog: function(text){ var self = this; self.copied = false; (navigator.clipboard && navigator.clipboard.writeText) ? navigator.clipboard.writeText(text).then(function(){ self.copied = true; setTimeout(function(){ self.copied = false; }, 1500); }).catch(function(){ self.copyLegacy(text); }) : self.copyLegacy(text); }, copyLegacy: function(text){ var ta = document.createElement('textarea'); ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0'; document.body.appendChild(ta); ta.select(); try { document.execCommand('copy'); } catch(e) {} document.body.removeChild(ta); this.copied = true; setTimeout(function(){ this.copied = false; }.bind(this), 1500); } }">
                            <td class="px-4 py-2 align-top pt-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border {{ $levelStyle }}">{{ $log['level'] }}</span>
                            </td>
                            <td class="px-4 py-2 align-top pt-3">
                                <p class="text-xs font-mono text-gray-400">{{ $log['date'] }}</p>
                                @if($log['env'])
                                    <p class="text-[10px] text-gray-600 mt-0.5">[{{ $log['env'] }}]</p>
                                @endif
                            </td>
                            <td class="px-4 py-2 align-top pt-2">
                                <p class="text-sm text-gray-200 break-words leading-snug">
                                    {{ Str::limit($log['message'], 220) }}
                                </p>
                                @if($log['context'])
                                    <div class="mt-1">
                                        <button @click="expanded = true" x-show="!expanded"
                                            class="text-[10px] text-gray-500 hover:text-cyan-400 font-medium underline underline-offset-2">
                                            Ver contexto (JSON)
                                        </button>
                                        <pre x-show="expanded" x-transition
                                            class="mt-1 text-[11px] text-cyan-300/80 bg-gray-950/60 border border-white/5 rounded-lg p-3 overflow-x-auto whitespace-pre-wrap break-words max-h-48">{{ $log['context'] }}</pre>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center align-top pt-3">
                                <div class="flex items-center justify-center gap-1">
                                    @if($log['stack'])
                                        <button @click="expanded = !expanded"
                                            class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-medium text-gray-400 hover:text-emerald-300 bg-white/5 hover:bg-emerald-500/20 rounded-md border border-white/5 transition-all duration-200"
                                            title="Expandir stack trace">
                                            <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                            trace
                                        </button>
                                    @else
                                        <span class="text-[10px] text-gray-700">—</span>
                                    @endif

                                    <button @click="copyLog({{ json_encode(strip_tags($log['message'] . ($log['context'] ? '\n' . $log['context'] : '') . ($log['stack'] ? '\n\n' . $log['stack'] : '')), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }})"
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
                                </div>
                            </td>
                        </tr>
                        @if($log['stack'])
                            <tr x-show="expanded" x-cloak x-transition class="odd:bg-transparent hover:bg-white/[0.02]">
                                <td colspan="4" class="px-4 pb-3">
                                    <div class="ml-0 sm:ml-28">
                                        <pre class="text-[11px] font-mono text-gray-400 bg-gray-950/60 border border-white/5 rounded-lg p-4 overflow-x-auto whitespace-pre-wrap break-words max-h-96 leading-relaxed">{{ $log['stack'] }}</pre>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500 text-sm">No se encontraron entradas de log con los filtros aplicados</p>
                                @if($search || $filterLevel || $dateFrom || $dateTo)
                                    <button wire:click="clearFilters"
                                        class="mt-4 px-4 py-2 text-sm text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 rounded-lg border border-emerald-500/20 transition-all duration-300">
                                        Limpiar filtros
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-wrapper :paginator="$logs" />
    </div>

    {{-- Confirmación: Limpiar --}}
    <div x-show="confirmAction === 'clean'" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        @click.self="confirmAction = null">
        <div class="w-full max-w-md bg-gray-900/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-2xl shadow-black/50 p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-2 bg-amber-500/10 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">¿Limpiar el log?</h3>
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
        @click.self="confirmAction = null">
        <div class="w-full max-w-md bg-gray-900/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-2xl shadow-black/50 p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-2 bg-red-500/10 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">¿Eliminar el log?</h3>
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
</div>
