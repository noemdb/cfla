{{-- resources/views/components/navbars/planning-items-mobile.blade.php --}}
{{-- Menú responsive para mobile (mismo set de enlaces que planning-items) --}}
@if(Auth::user()->is_planner)
    <div x-data="{ open: false }">
        <button @click="open = !open"
                :aria-expanded="open ? 'true' : 'false'"
                aria-controls="planning-submenu"
                class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-white/5 rounded-lg transition-colors focus-visible:ring-2 focus-visible:ring-amber-400/40">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Planificación
            </span>
            <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div x-show="open" id="planning-submenu" class="ml-4 mt-1 space-y-0.5 border-l border-gray-200 dark:border-white/10 pl-3">
            <a href="{{ route('app.planning.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.planning.index') ? 'text-emerald-400 bg-emerald-500/5' : 'text-gray-300 hover:text-emerald-300 hover:bg-white/5' }} transition-colors">Dashboard</a>
            <a href="{{ route('app.planning.pensums.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.planning.pensums.index') ? 'text-emerald-400 bg-emerald-500/5' : 'text-gray-300 hover:text-emerald-300 hover:bg-white/5' }} transition-colors">Pensums</a>
            <a href="{{ route('app.planning.activities.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.planning.activities.index') ? 'text-emerald-400 bg-emerald-500/5' : 'text-gray-300 hover:text-emerald-300 hover:bg-white/5' }} transition-colors">Actividades</a>
            <a href="{{ route('app.planning.lms.monitor') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.planning.lms.monitor') ? 'text-emerald-400 bg-emerald-500/5' : 'text-gray-300 hover:text-emerald-300 hover:bg-white/5' }} transition-colors">Contenido LMS</a>
            {{-- Contador de lecciones programadas (mobile) --}}
            <div class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300">
                <svg class="w-4 h-4 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Contenido LMS
                <livewire:planning.lms.lesson-pending-count />
            </div>
        </div>
    </div>
@endif