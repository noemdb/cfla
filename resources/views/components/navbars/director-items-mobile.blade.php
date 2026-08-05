{{-- resources/views/components/navbars/director-items-mobile.blade.php --}}
{{-- Menú responsive para mobile (mismo set de enlaces que director-items) --}}
@if(Auth::user()->is_director)
    <div class="space-y-1">
        <div class="text-[10px] font-bold uppercase tracking-widest text-sky-400/60 px-3 py-1.5">Dirección · Supervisión</div>
        <a href="{{ route('app.director.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.director.index') ? 'text-sky-400 bg-sky-500/5' : 'text-gray-300 hover:text-sky-300 hover:bg-white/5' }} transition-colors">Dashboard</a>
        <a href="{{ route('app.director.pensums') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.director.pensums') ? 'text-sky-400 bg-sky-500/5' : 'text-gray-300 hover:text-sky-300 hover:bg-white/5' }} transition-colors">Pensums</a>
        <a href="{{ route('app.director.carga-academica') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.director.carga-academica') ? 'text-sky-400 bg-sky-500/5' : 'text-gray-300 hover:text-sky-300 hover:bg-white/5' }} transition-colors">Carga Académica</a>
        <a href="{{ route('app.director.activities') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.director.activities') || request()->routeIs('app.director.activities.*') ? 'text-sky-400 bg-sky-500/5' : 'text-gray-300 hover:text-sky-300 hover:bg-white/5' }} transition-colors">Actividades</a>
        <a href="{{ route('app.director.lessons') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.director.lessons') ? 'text-sky-400 bg-sky-500/5' : 'text-gray-300 hover:text-sky-300 hover:bg-white/5' }} transition-colors">Lecciones</a>
        <a href="{{ route('app.director.resources') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.director.resources') ? 'text-sky-400 bg-sky-500/5' : 'text-gray-300 hover:text-sky-300 hover:bg-white/5' }} transition-colors">Recursos</a>
        <a href="{{ route('app.director.profesores') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.director.profesores') ? 'text-sky-400 bg-sky-500/5' : 'text-gray-300 hover:text-sky-300 hover:bg-white/5' }} transition-colors">Profesores</a>
    </div>
@endif
