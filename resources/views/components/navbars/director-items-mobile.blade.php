{{-- resources/views/components/navbars/director-items-mobile.blade.php --}}
{{-- Menú responsive para mobile (mismo set de enlaces que director-items) --}}
@if(Auth::user()->is_director)
    <div x-data="{ open: false }">
        <button @click="open = !open"
                :aria-expanded="open ? 'true' : 'false'"
                aria-controls="director-submenu"
                class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-white/5 rounded-lg transition-colors focus-visible:ring-2 focus-visible:ring-amber-400/40">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
                Dirección
            </span>
            <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div x-show="open" id="director-submenu" class="ml-4 mt-1 space-y-0.5 border-l border-gray-200 dark:border-white/10 pl-3">
            <a href="{{ route('app.director.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.director.index') ? 'text-sky-400 bg-sky-500/5' : 'text-gray-300 hover:text-sky-300 hover:bg-white/5' }} transition-colors">Dashboard</a>
            <a href="{{ route('app.director.pensums') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.director.pensums') ? 'text-sky-400 bg-sky-500/5' : 'text-gray-300 hover:text-sky-300 hover:bg-white/5' }} transition-colors">Pensums</a>
            <a href="{{ route('app.director.carga-academica') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.director.carga-academica') ? 'text-sky-400 bg-sky-500/5' : 'text-gray-300 hover:text-sky-300 hover:bg-white/5' }} transition-colors">Carga Académica</a>
            <a href="{{ route('app.director.activities') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.director.activities') ? 'text-sky-400 bg-sky-500/5' : 'text-gray-300 hover:text-sky-300 hover:bg-white/5' }} transition-colors">Actividades</a>
            <a href="{{ route('app.director.lessons') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.director.lessons') ? 'text-sky-400 bg-sky-500/5' : 'text-gray-300 hover:text-sky-300 hover:bg-white/5' }} transition-colors">Lecciones</a>
            <a href="{{ route('app.director.resources') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.director.resources') ? 'text-sky-400 bg-sky-500/5' : 'text-gray-300 hover:text-sky-300 hover:bg-white/5' }} transition-colors">Recursos</a>
            <a href="{{ route('app.director.profesores') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.director.profesores') ? 'text-sky-400 bg-sky-500/5' : 'text-gray-300 hover:text-sky-300 hover:bg-white/5' }} transition-colors">Profesores</a>
            {{-- Contador de lecciones programadas (mobile) --}}
            <div class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300">
                <svg class="w-4 h-4 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Contenido LMS
                <livewire:planning.lms.lesson-pending-count />
            </div>
        </div>
    </div>
@endif