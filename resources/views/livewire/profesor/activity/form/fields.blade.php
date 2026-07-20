{{-- AI Improvement Bar (idle) --}}
<div wire:loading.remove wire:target="improveActivity"
     class="mb-4 p-3 rounded-lg bg-indigo-500/5 border border-indigo-500/20">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Mejorar con IA</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Potencia los campos pedagógicos usando el referente normativo y actividades previas como contexto.</p>
            </div>
        </div>
        <button type="button"
            wire:click="improveActivity"
            wire:loading.attr="disabled"
            wire:target="improveActivity"
            @if(!$enable_edit) disabled @endif
            class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold transition-all duration-200
                {{ $enable_edit
                    ? 'bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500/20 border border-indigo-500/20'
                    : 'bg-gray-800/50 text-gray-600 cursor-not-allowed border border-gray-700/50' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Mejorar todo
        </button>
    </div>
</div>

{{-- AI Improvement Overlay (full-screen, same patrón como lesson-wizard) --}}
<div wire:loading.flex
     wire:target="improveActivity"
     class="fixed inset-0 z-[9999] items-center justify-center bg-slate-900/90 backdrop-blur-md">
    <div class="max-w-lg mx-auto px-6 py-8 space-y-6">
        {{-- Header with spinner --}}
        <div class="text-center space-y-4">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-indigo-500/20 to-emerald-500/20 border-2 border-indigo-500/30 mx-auto relative">
                <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <svg class="absolute inset-0 w-full h-full animate-spin text-indigo-400/40" viewBox="0 0 64 64" fill="none">
                    <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="3" stroke-dasharray="44 132" stroke-linecap="round" class="opacity-80"/>
                </svg>
            </div>
            <div>
                <p class="text-lg font-bold text-indigo-200">Mejorando contenido pedagógico</p>
                <p class="text-sm text-slate-400 mt-1">Inteligencia Artificial aplicando el CNB venezolano</p>
            </div>
        </div>

        {{-- Context cards (qué está pasando) --}}
        <div class="space-y-2.5">
            {{-- Contexto curricular --}}
            <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-800/40 border border-slate-700/40">
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-indigo-300">Contexto curricular</p>
                    <p class="text-[11px] text-slate-500">Competencias e indicadores del pensum</p>
                </div>
                <svg class="w-4 h-4 text-indigo-400/50 shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>

            {{-- Actividades previas --}}
            <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-800/40 border border-slate-700/40">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-emerald-300">Actividades previas</p>
                    <p class="text-[11px] text-slate-500">Coherencia pedagógica con otras actividades</p>
                </div>
                <svg class="w-4 h-4 text-emerald-400/50 shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>

            {{-- Cadena de servicios AI --}}
            <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-800/40 border border-slate-700/40">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-amber-300">Cadena de servicios</p>
                    <p class="text-[11px] text-slate-500">OpenRouter → Nvidia → Kimi (fallback)</p>
                </div>
                <svg class="w-4 h-4 text-amber-400/50 shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </div>

        {{-- Bouncing dots --}}
        <div class="flex items-center justify-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-400 animate-bounce" style="animation-delay:0s"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-400 animate-bounce" style="animation-delay:0.15s"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-400 animate-bounce" style="animation-delay:0.3s"></span>
        </div>
    </div>
</div>

{{-- Tabbed Form Container (Alpine.js, sin recarga Livewire) --}}
<div x-data="{ activeTab: 'general' }" class="space-y-4">

    {{-- ═══════════════ TAB NAVIGATION ═══════════════ --}}
    <div class="flex border-b border-white/10">
        {{-- Tab 1: Enseñanza y Evaluación --}}
        <button type="button" @click="activeTab = 'teaching'"
            :class="activeTab === 'teaching' ? 'border-indigo-400 text-indigo-300' : 'border-transparent text-gray-500 hover:text-gray-300 hover:border-gray-600'"
            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 text-[11px] font-bold uppercase tracking-widest border-b-2 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span class="hidden sm:inline">Enseñanza y Evaluación</span>
            <span class="inline sm:hidden">Enseñanza</span>
        </button>

        {{-- Tab 2: Datos Básicos --}}
        <button type="button" @click="activeTab = 'general'"
            :class="activeTab === 'general' ? 'border-indigo-400 text-indigo-300' : 'border-transparent text-gray-500 hover:text-gray-300 hover:border-gray-600'"
            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 text-[11px] font-bold uppercase tracking-widest border-b-2 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="hidden sm:inline">Datos Básicos</span>
            <span class="inline sm:hidden">Básicos</span>
        </button>

        {{-- Tab 3: Contenido Pedagógico --}}
        <button type="button" @click="activeTab = 'content'"
            :class="activeTab === 'content' ? 'border-indigo-400 text-indigo-300' : 'border-transparent text-gray-500 hover:text-gray-300 hover:border-gray-600'"
            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 text-[11px] font-bold uppercase tracking-widest border-b-2 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span class="hidden sm:inline">Contenido Pedagógico</span>
            <span class="inline sm:hidden">Contenido</span>
        </button>
    </div>

    {{-- ═══════════════ TAB 1: ENSEÑANZA Y EVALUACIÓN ═══════════════ --}}
    <div x-show="activeTab === 'teaching'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0">
        <div class="space-y-3">

            {{-- INICIO --}}
            <div>
                <label class="block text-[9px] font-bold uppercase tracking-widest text-cyan-400 mb-1.5">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        INICIO
                    </span>
                </label>
                <textarea wire:model="activityForm.teachingStart" rows="6"
                    class="w-full bg-gray-800/50 border border-cyan-500/20 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/20 transition-all duration-200 resize-y"
                    placeholder="Motivación, exploración de saberes previos, problematización..."></textarea>
                @error('activityForm.teachingStart') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- DESARROLLO --}}
            <div>
                <label class="block text-[9px] font-bold uppercase tracking-widest text-emerald-400 mb-1.5">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        DESARROLLO
                    </span>
                </label>
                <textarea wire:model="activityForm.teachingContent" rows="6"
                    class="w-full bg-gray-800/50 border border-emerald-500/20 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200 resize-y"
                    placeholder="Procesos didácticos, mediación, actividades de construcción del aprendizaje..."></textarea>
                @error('activityForm.teachingContent') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- CIERRE --}}
            <div>
                <label class="block text-[9px] font-bold uppercase tracking-widest text-amber-400 mb-1.5">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        CIERRE
                    </span>
                </label>
                <textarea wire:model="activityForm.teachingEnd" rows="6"
                    class="w-full bg-gray-800/50 border border-amber-500/20 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/20 transition-all duration-200 resize-y"
                    placeholder="Sistematización, conclusiones, metacognición, transferencia a la vida..."></textarea>
                @error('activityForm.teachingEnd') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Separador --}}
            <hr class="border-white/5 my-2">

            {{-- ODS / Sistematización --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['observations'] ?? 'ODS / Sistematización' }}</label>
                <textarea wire:model="activityForm.observations" rows="4"
                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200 resize-y"
                    placeholder="{{ $list_comment['observations'] ?? '' }}"></textarea>
                @error('activityForm.observations') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

        </div>
    </div>

    {{-- ═══════════════ TAB 2: DATOS BÁSICOS ═══════════════ --}}
    <div x-show="activeTab === 'general'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

            {{-- Fecha Inicial --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['finicial'] ?? 'Fecha Inicial' }}</label>
                <input type="date" wire:model="activityForm.finicial"
                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                @error('activityForm.finicial') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Fecha Final --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['ffinal'] ?? 'Fecha Final' }}</label>
                <input type="date" wire:model="activityForm.ffinal"
                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                @error('activityForm.ffinal') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Actividad Evaluativa --}}
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['description'] ?? 'Actividad Evaluativa' }}</label>
                <textarea wire:model="activityForm.description" rows="6"
                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200 resize-y"
                    placeholder="{{ $list_comment['description'] ?? '' }}"></textarea>
                @error('activityForm.description') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Aprendizaje --}}
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['learning'] ?? 'Aprendizaje' }}</label>
                <textarea wire:model="activityForm.learning" rows="6"
                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200 resize-y"
                    placeholder="{{ $list_comment['learning'] ?? '' }}"></textarea>
                @error('activityForm.learning') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

        </div>
    </div>

    {{-- ═══════════════ TAB 3: CONTENIDO PEDAGÓGICO ═══════════════ --}}
    <div x-show="activeTab === 'content'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

            {{-- Tema generador --}}
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['topic'] ?? 'Tema Generador y Énfasis' }}</label>
                <textarea wire:model="activityForm.topic" rows="6"
                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200 resize-y"
                    placeholder="{{ $list_comment['topic'] ?? '' }}"></textarea>
                @error('activityForm.topic') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Tejido temático --}}
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['thematic'] ?? 'Tejido Temático' }}</label>
                <textarea wire:model="activityForm.thematic" rows="6"
                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200 resize-y"
                    placeholder="{{ $list_comment['thematic'] ?? '' }}"></textarea>
                @error('activityForm.thematic') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Referentes --}}
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['references'] ?? 'Referentes Teórico-Prácticos y Éticos' }}</label>
                <textarea wire:model="activityForm.references" rows="6"
                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200 resize-y"
                    placeholder="{{ $list_comment['references'] ?? '' }}"></textarea>
                @error('activityForm.references') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

        </div>
    </div>

</div>
