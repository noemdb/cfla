@if(Auth::user()->isProfesor())
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" @click.outside="open = false"
            class="inline-flex items-center gap-1.5 text-sm font-medium rounded-lg px-3 py-1.5 transition-all duration-200 {{ request()->routeIs('app.profesors.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300 hover:bg-white/5' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Profesor
            <svg class="w-3 h-3 ml-0.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div x-show="open" x-cloak
            @click.outside="open = false"
            class="absolute left-0 mt-1 w-48 bg-gray-800/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-2xl shadow-black/50 py-1.5 z-50">
            <a href="{{ route('app.profesors.home') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 transition-colors {{ request()->routeIs('app.profesors.home') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('app.profesors.activities.index') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 transition-colors {{ request()->routeIs('app.profesors.activities.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                Actividades
            </a>
            <a href="{{ route('app.profesors.diagnostics.index') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 transition-colors {{ request()->routeIs('app.profesors.diagnostics.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                Diagnósticos
            </a>
            <a href="{{ route('app.profesors.competitions.index') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 transition-colors {{ request()->routeIs('app.profesors.competitions.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Competencias
            </a>

            {{-- LMS: Contenido de Lecciones --}}
            <a href="{{ route('app.profesors.lms.lesson.wizard') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 transition-colors {{ request()->routeIs('app.profesors.lms.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Contenido LMS
            </a>
        </div>
    </div>
@endif
