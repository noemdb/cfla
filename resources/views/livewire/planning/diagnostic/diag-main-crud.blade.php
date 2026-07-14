<div class="mb-8" wire:key="diag-main-crud">
    {{-- Section header with create button --}}
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-lg font-bold text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            Registrados
        </h2>
        <button wire:click="create"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-200 text-xs font-bold uppercase tracking-widest">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nuevo Diagnóstico
        </button>
    </div>

    {{-- Single-column card list (full width) --}}
    @if($diagMains->isNotEmpty())
        <div class="space-y-3">
            @foreach($diagMains as $diag)
                <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg transition-all duration-300 hover:border-emerald-500/20"
                     wire:key="diag-{{ $diag->id }}">
                    <div class="p-4 flex flex-col md:flex-row md:items-center gap-3">
                        {{-- Left: status + info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-2 h-2 rounded-full {{ $diag->active ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-gray-600' }}"></span>
                                <span class="text-[10px] font-bold uppercase tracking-widest {{ $diag->active ? 'text-emerald-400' : 'text-gray-500' }}">
                                    {{ $diag->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                            <h3 class="text-base font-bold text-white truncate">{{ $diag->name }}</h3>
                            @if($diag->description)
                                <p class="text-[11px] text-gray-400 leading-relaxed mt-0.5 line-clamp-1">{{ $diag->description }}</p>
                            @endif
                            {{-- Meta row --}}
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-[10px] text-gray-500">
                                @if($diag->lapso)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $diag->lapso->name }}
                                    </span>
                                @endif
                                @if($diag->pestudio)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        {{ $diag->pestudio->code }}
                                    </span>
                                @endif
                                @if($diag->referent)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        {{ $diag->referent->code }}
                                    </span>
                                @endif
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $diag->questions_count ?? 0 }} preg.
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    {{ $diag->sessions_count ?? 0 }} ses.
                                </span>
                            </div>
                        </div>

                        {{-- Right: action buttons --}}
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button wire:click="detail({{ $diag->id }})"
                                class="p-2 bg-white/5 hover:bg-cyan-500/20 rounded-lg text-gray-400 hover:text-cyan-400 transition-all"
                                title="Ver detalles">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            <button wire:click="edit({{ $diag->id }})"
                                class="p-2 bg-white/5 hover:bg-cyan-500/20 rounded-lg text-gray-400 hover:text-cyan-400 transition-all"
                                title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button wire:click="toggleActive({{ $diag->id }})"
                                class="p-2 bg-white/5 hover:bg-emerald-500/20 rounded-lg text-gray-400 hover:text-emerald-400 transition-all"
                                title="{{ $diag->active ? 'Desactivar' : 'Activar' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                            <button wire:click="confirmDelete({{ $diag->id }})"
                                class="p-2 bg-white/5 hover:bg-red-500/20 rounded-lg text-gray-400 hover:text-red-400 transition-all"
                                title="Eliminar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty state --}}
        <div class="bg-gray-900/30 backdrop-blur-md border border-dashed border-white/5 rounded-lg p-10 text-center">
            <svg class="w-14 h-14 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <p class="text-gray-500 font-medium mb-1">Sin diagnósticos</p>
            <p class="text-gray-600 text-sm mb-2">Crea un nuevo diagnóstico para comenzar a configurar el diagnóstico académico.</p>
            <button wire:click="create"
                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-200 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Crear Primer Diagnóstico
            </button>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════
         FULL-SCREEN DIALOG: Create / Edit DiagMain
         ═══════════════════════════════════════════════════════════════════ --}}
    <div wire:key="diag-dialog">
        @if($showDialog)
            <div class="fixed inset-0 z-[100] flex items-start justify-center pt-10 pb-10 overflow-y-auto"
                 wire:key="diag-dialog-overlay-{{ $editingId ?? 'new' }}">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm"
                     wire:click="closeDialog"></div>

                {{-- Dialog panel --}}
                <div class="relative w-full max-w-3xl mx-4 bg-gray-900 border border-white/10 rounded-lg shadow-2xl overflow-hidden"
                     wire:key="diag-dialog-panel"
                     @click.away="$wire.closeDialog()">

                    {{-- Close button --}}
                    <button type="button" wire:click="closeDialog"
                        class="absolute top-4 right-4 p-1.5 bg-white/10 hover:bg-red-500/20 rounded-lg text-gray-400 hover:text-red-400 transition-all duration-200 z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    {{-- Dialog header --}}
                    <div class="px-6 py-5 border-b border-white/5">
                        <h3 class="text-lg font-bold text-white">
                            {{ $editingId ? 'Editar Diagnóstico' : 'Nuevo Diagnóstico' }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $editingId ? 'Modifica la información del diagnóstico.' : 'Registra un nuevo diagnóstico académico.' }}
                        </p>
                    </div>

                    {{-- Dialog body --}}
                    <div class="px-6 py-5 space-y-5">
                        {{-- Name --}}
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Nombre del Diagnóstico</label>
                            <input type="text" wire:model="name"
                                class="w-full bg-gray-800 text-gray-200 text-sm rounded-lg border border-white/5 px-3 py-2 focus:border-emerald-500/30 focus:ring-1 focus:ring-emerald-500/20 outline-none transition-all"
                                placeholder="Ej: Diagnóstico de Ingreso 2025-2026">
                            @error('name') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Descripción</label>
                            <textarea wire:model="description" rows="3"
                                class="w-full bg-gray-800 text-gray-200 text-sm rounded-lg border border-white/5 px-3 py-2 focus:border-emerald-500/30 focus:ring-1 focus:ring-emerald-500/20 outline-none transition-all resize-none"
                                placeholder="Descripción opcional del diagnóstico..."></textarea>
                            @error('description') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- 3-column row --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            {{-- Lapso --}}
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Lapso Académico</label>
                                <select wire:model="lapso_id"
                                    class="w-full bg-gray-800 text-gray-200 text-sm rounded-lg border border-white/5 px-3 py-2 focus:border-emerald-500/30 focus:ring-1 focus:ring-emerald-500/20 outline-none">
                                    <option value="">Seleccionar...</option>
                                    @foreach($lapsos as $lapso)
                                        <option value="{{ $lapso->id }}">{{ $lapso->name }}</option>
                                    @endforeach
                                </select>
                                @error('lapso_id') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Pestudio --}}
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Plan de Estudio</label>
                                <select wire:model="pestudio_id"
                                    class="w-full bg-gray-800 text-gray-200 text-sm rounded-lg border border-white/5 px-3 py-2 focus:border-emerald-500/30 focus:ring-1 focus:ring-emerald-500/20 outline-none">
                                    <option value="">Seleccionar...</option>
                                    @foreach($pestudios as $pest)
                                        <option value="{{ $pest->id }}">{{ $pest->code }} - {{ $pest->name }}</option>
                                    @endforeach
                                </select>
                                @error('pestudio_id') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Referent --}}
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Referente Curricular</label>
                                <select wire:model="referent_id"
                                    class="w-full bg-gray-800 text-gray-200 text-sm rounded-lg border border-white/5 px-3 py-2 focus:border-emerald-500/30 focus:ring-1 focus:ring-emerald-500/20 outline-none">
                                    <option value="">Seleccionar...</option>
                                    @foreach($referents as $ref)
                                        <option value="{{ $ref->id }}">{{ $ref->code }} - {{ $ref->name }}</option>
                                    @endforeach
                                </select>
                                @error('referent_id') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Token + Active --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Token de Acceso</label>
                                <input type="text" wire:model="token"
                                    class="w-full bg-gray-800 text-gray-200 text-sm rounded-lg border border-white/5 px-3 py-2 focus:border-emerald-500/30 focus:ring-1 focus:ring-emerald-500/20 outline-none transition-all"
                                    placeholder="Token opcional para acceso externo">
                                @error('token') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex items-end pb-2">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative">
                                        <input type="checkbox" wire:model="active" class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-700 rounded-full peer-checked:bg-emerald-500 transition-colors duration-200"></div>
                                        <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform duration-200"></div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-300 group-hover:text-white transition-colors">
                                        Diagnóstico Activo
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Dialog footer --}}
                    <div class="px-6 py-3 border-t border-white/5 flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeDialog"
                            class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/5 transition-all duration-200 text-xs font-bold uppercase tracking-widest">
                            Cancelar
                        </button>
                        <button type="button" wire:click="save" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-200 text-xs font-bold uppercase tracking-widest inline-flex items-center gap-2">
                            <svg wire:loading wire:loading.class="animate-spin" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            {{ $editingId ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         FULL-SCREEN DIALOG: Detail / View DiagMain
         ═══════════════════════════════════════════════════════════════════ --}}
    <div wire:key="diag-detail-dialog">
        @if($showDetail && $detailItem)
            <div class="fixed inset-0 z-[100] flex items-start justify-center pt-10 pb-10 overflow-y-auto"
                 wire:key="diag-detail-overlay-{{ $detailItem->id }}">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm"
                     wire:click="closeDetail"></div>

                {{-- Detail panel --}}
                <div class="relative w-full max-w-3xl mx-4 bg-gray-900 border border-white/10 rounded-lg shadow-2xl overflow-hidden">

                    {{-- Close button --}}
                    <button type="button" wire:click="closeDetail"
                        class="absolute top-4 right-4 p-1.5 bg-white/10 hover:bg-red-500/20 rounded-lg text-gray-400 hover:text-red-400 transition-all duration-200 z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    {{-- Header --}}
                    <div class="px-6 py-5 border-b border-white/5">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $detailItem->active ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-gray-600' }}"></span>
                            <span class="text-xs font-bold uppercase tracking-widest {{ $detailItem->active ? 'text-emerald-400' : 'text-gray-500' }}">
                                {{ $detailItem->active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-white">{{ $detailItem->name }}</h3>
                        @if($detailItem->description)
                            <p class="text-sm text-gray-400 mt-1">{{ $detailItem->description }}</p>
                        @endif
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-5 space-y-6">

                        {{-- Key info grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            {{-- Lapso --}}
                            <div class="bg-gray-800/40 rounded-lg p-4 border border-white/5">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Lapso Académico</p>
                                <p class="text-sm text-white font-medium">
                                    {{ $detailItem->lapso?->name ?? '—' }}
                                </p>
                            </div>

                            {{-- Plan de Estudio --}}
                            <div class="bg-gray-800/40 rounded-lg p-4 border border-white/5">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Plan de Estudio</p>
                                <p class="text-sm text-white font-medium">
                                    {{ $detailItem->pestudio ? "{$detailItem->pestudio->code} — {$detailItem->pestudio->name}" : '—' }}
                                </p>
                            </div>

                            {{-- Referente --}}
                            <div class="bg-gray-800/40 rounded-lg p-4 border border-white/5">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Referente Curricular</p>
                                <p class="text-sm text-white font-medium">
                                    {{ $detailItem->referent ? "{$detailItem->referent->code} — {$detailItem->referent->name}" : '—' }}
                                </p>
                            </div>
                        </div>

                        {{-- Token --}}
                        @if($detailItem->token)
                            <div class="bg-gray-800/40 rounded-lg p-4 border border-white/5">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Token de Acceso</p>
                                <code class="text-sm text-emerald-400 font-mono bg-gray-900/60 px-2 py-1 rounded">{{ $detailItem->token }}</code>
                            </div>
                        @endif

                        {{-- Stats section --}}
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Estadísticas</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                {{-- Total questions --}}
                                <div class="bg-gray-800/40 rounded-lg p-4 border border-white/5 text-center">
                                    <p class="text-lg font-extrabold text-cyan-400">{{ $detailItem->questions_count ?? 0 }}</p>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Preguntas</p>
                                </div>

                                {{-- Total sessions --}}
                                <div class="bg-gray-800/40 rounded-lg p-4 border border-white/5 text-center">
                                    <p class="text-lg font-extrabold text-emerald-400">{{ $detailItem->sessions_count ?? 0 }}</p>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Sesiones</p>
                                </div>

                                {{-- Completed sessions --}}
                                @php
                                    $completedSessions = $detailItem->sessions_count
                                        ? \App\Models\app\Instrument\DiagSession::where('diag_main_id', $detailItem->id)
                                            ->whereNotNull('completado_at')->count()
                                        : 0;
                                @endphp
                                <div class="bg-gray-800/40 rounded-lg p-4 border border-white/5 text-center">
                                    <p class="text-lg font-extrabold text-amber-400">{{ $completedSessions }}</p>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Completadas</p>
                                </div>

                                {{-- Students evaluated --}}
                                @php
                                    $studentsEvaluated = $detailItem->sessions_count
                                        ? \App\Models\app\Instrument\DiagSession::where('diag_main_id', $detailItem->id)
                                            ->distinct('estudiant_id')->count('estudiant_id')
                                        : 0;
                                @endphp
                                <div class="bg-gray-800/40 rounded-lg p-4 border border-white/5 text-center">
                                    <p class="text-lg font-extrabold text-purple-400">{{ $studentsEvaluated }}</p>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Estudiantes</p>
                                </div>
                            </div>
                        </div>

                        {{-- Related items metadata --}}
                        <div class="text-[10px] text-gray-600 border-t border-white/5 pt-4">
                            <p>ID: <span class="text-gray-500">#{{ $detailItem->id }}</span>
                                @if($detailItem->created_at)
                                    &middot; Creado: <span class="text-gray-500">{{ $detailItem->created_at->format('d/m/Y h:i A') }}</span>
                                @endif
                                @if($detailItem->updated_at)
                                    &middot; Actualizado: <span class="text-gray-500">{{ $detailItem->updated_at->format('d/m/Y h:i A') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-3 border-t border-white/5 flex items-center justify-end gap-3">
                        <button type="button" wire:click="edit({{ $detailItem->id }})"
                            class="px-4 py-2 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 rounded-lg border border-cyan-500/20 transition-all duration-200 text-xs font-bold uppercase tracking-widest inline-flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Editar Diagnóstico
                        </button>
                        <button type="button" wire:click="closeDetail"
                            class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/5 transition-all duration-200 text-xs font-bold uppercase tracking-widest">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
