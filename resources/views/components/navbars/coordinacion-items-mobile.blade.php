@if(Auth::user()->is_admin || Auth::user()->isCoordinacion())
    <div x-data="{ open: false }">
        <button @click="open = !open"
                class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-white/5 rounded-lg transition-colors">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Coordinación
            </span>
            <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div x-show="open" class="ml-4 mt-1 space-y-0.5 border-l border-gray-200 dark:border-white/10 pl-3">
            <a href="{{ route('app.coordinacion.index') }}"
                class="flex items-center gap-2 px-3 py-2 text-sm text-gray-500 hover:text-emerald-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-emerald-300 dark:hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.coordinacion.index') ? 'text-emerald-600 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>
            <a href="{{ route('app.coordinacion.pensums') }}"
                class="flex items-center gap-2 px-3 py-2 text-sm text-gray-500 hover:text-emerald-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-emerald-300 dark:hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.coordinacion.pensums') ? 'text-emerald-600 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Pensums
            </a>
            <a href="{{ route('app.coordinacion.carga-academica') }}"
                class="flex items-center gap-2 px-3 py-2 text-sm text-gray-500 hover:text-emerald-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-emerald-300 dark:hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.coordinacion.carga-academica') ? 'text-emerald-600 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Carga Académica
            </a>
            <a href="{{ route('app.coordinacion.activities') }}"
                class="flex items-center gap-2 px-3 py-2 text-sm text-gray-500 hover:text-emerald-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-emerald-300 dark:hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.coordinacion.activities') || request()->routeIs('app.coordinacion.activities.*') ? 'text-emerald-600 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                Actividades
            </a>
            <a href="{{ route('app.coordinacion.lessons') }}"
                class="flex items-center gap-2 px-3 py-2 text-sm text-gray-500 hover:text-emerald-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-emerald-300 dark:hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.coordinacion.lessons') ? 'text-emerald-600 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Lecciones
            </a>
            <a href="{{ route('app.coordinacion.resources') }}"
                class="flex items-center gap-2 px-3 py-2 text-sm text-gray-500 hover:text-emerald-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-emerald-300 dark:hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.coordinacion.resources') ? 'text-emerald-600 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                Recursos
            </a>
            {{-- Contador de lecciones programadas (mobile) --}}
            <div class="flex items-center gap-2 px-3 py-2 text-sm text-gray-500">
                <svg class="w-4 h-4 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Contenido LMS
                <livewire:planning.lms.lesson-pending-count />
            </div>
        </div>
    </div>
@endif
