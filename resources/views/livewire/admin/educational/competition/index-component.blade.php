<div class="fade-in">
    <!-- Header Section -->
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">Competiciones Académicas</h1>
            <p class="text-emerald-400 font-medium">Gestión y control de retos académicos y debates entre grados.</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- <x-button wire:click="$set('showCreateModal', true)" label="Nueva Competición" icon="plus" emerald rounded="xl"
                class="shadow-xl shadow-emerald-500/20 px-5 py-2.5 transition-all duration-300 hover:scale-105" /> --}}

            <a href="{{ route('app.planning.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 rounded-xl border border-cyan-500/20 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Planificación
            </a>
            <a href="{{ route('admin.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl border border-white/5 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Administración
            </a>
        </div>
    </div>

    <!-- Competitions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($competitions as $competition)
            <div
                class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-xl overflow-hidden transition-all duration-300 hover:border-emerald-500/30 group">
                <div class="flex items-start justify-between mb-6">
                    <div
                        class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <div class="flex flex-col items-end">
                        <x-toggle wire:click="toggleStatus({{ $competition->id }})" :checked="$competition->status_active" emerald />
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest mt-2 {{ $competition->status_active ? 'text-emerald-400' : 'text-gray-500' }}">
                            {{ $competition->status_active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                </div>

                <h3 class="text-lg font-bold text-white mb-2 group-hover:text-emerald-400 transition-colors">
                    {{ $competition->name }}</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-6 line-clamp-2">
                    {{ $competition->description ?? 'Sin descripción proporcionada.' }}
                </p>

                <div class="flex flex-col gap-3 mb-6">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ \Carbon\Carbon::parse($competition->date)->format('d/m/Y') }}
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Creado por: {{ $competition->user->name }}
                    </div>
                </div>

                <!-- Action Links -->
                <div class="grid grid-cols-4 gap-2">
                    <a href="{{ route('general.educational.competition.moderator', $competition->token) }}"
                        target="_blank"
                        class="flex flex-col items-center justify-center text-center gap-2 p-3 bg-white/5 hover:bg-emerald-500/10 rounded-xl border border-white/5 hover:border-emerald-500/20 transition-all duration-300 group/link">
                        <svg class="w-5 h-5 text-emerald-400 group-hover/link:scale-110 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        <span class="text-[9px] font-bold uppercase tracking-wider text-gray-400 font-black">Moderador</span>
                    </a>

                    <a href="{{ route('general.educational.competition.scoreboard', $competition->token) }}"
                        target="_blank"
                        class="flex flex-col items-center justify-center text-center gap-2 p-3 bg-white/5 hover:bg-purple-500/10 rounded-xl border border-white/5 hover:border-purple-500/20 transition-all duration-300 group/link">
                        <svg class="w-5 h-5 text-purple-400 group-hover/link:scale-110 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z">
                            </path>
                        </svg>
                        <span class="text-[9px] font-bold uppercase tracking-wider text-gray-400 font-black">Puntaje</span>
                    </a>

                    <a href="{{ route('admin.educational.competition.answers', $competition->token) }}"
                        target="_blank"
                        class="flex flex-col items-center justify-center text-center gap-2 p-3 bg-white/5 hover:bg-blue-500/10 rounded-xl border border-white/5 hover:border-blue-500/20 transition-all duration-300 group/link">
                        <svg class="w-5 h-5 text-blue-400 group-hover/link:scale-110 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <span class="text-[9px] font-bold uppercase tracking-wider text-gray-400 font-black">Audit</span>
                    </a>

                    <button type="button" wire:click="confirmReset({{ $competition->id }})"
                        class="flex flex-col items-center justify-center text-center gap-2 p-3 bg-white/5 hover:bg-amber-500/10 rounded-xl border border-white/5 hover:border-amber-500/20 transition-all duration-300 group/link">
                        <svg class="w-5 h-5 text-amber-400 group-hover/link:scale-110 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        <span class="text-[9px] font-bold uppercase tracking-wider text-gray-400 font-black">Reiniciar</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 bg-gray-900/20 border border-white/5 rounded-xl text-center">
                <svg class="w-16 h-16 text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500 font-medium">No se encontraron competiciones registradas.</p>
                <x-button wire:click="$set('showCreateModal', true)" label="Crear la primera" plain emerald
                    class="mt-4" />
            </div>
        @endforelse
    </div>

    <!-- Create Modal -->
    {{-- <x-modal-card title="Nueva Competición Académica" blur wire:model.defer="showCreateModal" max-width="lg">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="col-span-2">
                <x-input label="Nombre de la Competición" placeholder="Ej: Challenge de Matemáticas 2024" wire:model.defer="name" />
            </div>
            
            <div class="col-span-2">
                <x-date label="Fecha del Evento" placeholder="Seleccione fecha" wire:model.defer="date" without-time />
            </div>

            <div class="col-span-2">
                <x-textarea label="Descripción" placeholder="Breve resumen del evento..." wire:model.defer="description" />
            </div>

            <div class="col-span-2">
                <x-input label="Motivo o Propósito" placeholder="Opcional" wire:model.defer="motive" />
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button flat label="Cancelar" x-on:click="close" />
                <x-button emerald label="Crear Competición" wire:click="createCompetition" spinner="createCompetition" />
            </div>
        </x-slot>
    </x-modal-card> --}}
</div>
