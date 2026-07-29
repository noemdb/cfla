<div class="max-w-4xl mx-auto py-8 px-4 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white">Mis Actividades</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Contenido publicado por tus profesores
            </p>
        </div>
    </div>

    {{-- Search --}}
    <div class="relative">
        <input wire:model.live="search" type="search"
               placeholder="Buscar por asignatura, profesor o tema…"
               class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600
                      rounded-lg px-4 py-2.5 pl-10 text-sm text-gray-900 dark:text-gray-100
                      placeholder-gray-400 focus:ring-2 focus:ring-emerald-500/50
                      focus:border-emerald-500 outline-none transition-all"/>
        <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </div>

    {{-- Lista de unidades --}}
    @forelse($pevaluacions as $pe)
        <section wire:key="pe-{{ $pe->id }}"
                 class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">

            {{-- Header --}}
            <div class="px-4 sm:px-5 py-3 border-b border-gray-100 dark:border-gray-700/50">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 truncate">
                            {{ $pe->pensum?->asignatura?->name ?? 'Sin asignatura' }}
                        </p>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-0.5 sm:gap-2 mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                            <span class="truncate">{{ $pe->seccion?->grado?->name }} - Secci&oacute;n {{ $pe->seccion?->name }}</span>
                            <span class="hidden sm:inline text-gray-300 dark:text-gray-600">&middot;</span>
                            <span class="truncate">{{ $pe->profesor?->lastname }} {{ $pe->profesor?->name }}</span>
                            <span class="hidden sm:inline text-gray-300 dark:text-gray-600">&middot;</span>
                            <span class="shrink-0">{{ $pe->lapso?->name }}</span>
                        </div>
                    </div>
                    <span class="shrink-0 text-xs text-gray-400 mt-0.5">
                        {{ $pe->activities->count() }} activ.
                    </span>
                </div>
            </div>

            {{-- Activity list --}}
            @if($pe->activities->isNotEmpty())
            <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                @foreach($pe->activities as $activity)
                    @php $pub = $activity->lmsPublication; @endphp
                    <a href="{{ route('student.lms.activity', $activity) }}"
                       class="flex items-start gap-3 px-4 sm:px-5 py-3 hover:bg-gray-50
                              dark:hover:bg-gray-700/30 transition-colors group">
                        <span @class([
                            'w-2 h-2 rounded-full shrink-0 mt-1.5',
                            'bg-emerald-500' => $pub?->status === 'PUBLISHED',
                            'bg-amber-500'   => $pub?->status === 'SCHEDULED',
                            'bg-gray-400'    => !$pub || $pub->status === 'DRAFT',
                        ])></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200
                                      group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors line-clamp-2">
                                {{ $activity->topic ?? 'Actividad sin título' }}
                            </p>
                            @if($activity->finicial)
                            <p class="text-xs text-gray-400 mt-1">
                                {{ \Carbon\Carbon::parse($activity->finicial)->format('d/m/Y') }}
                                &ndash; {{ \Carbon\Carbon::parse($activity->ffinal)->format('d/m/Y') }}
                            </p>
                            @endif
                        </div>
                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-emerald-500 shrink-0 mt-1 transition-colors"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endforeach
            </div>
            @endif
        </section>
    @empty
        <div class="text-center py-16">
            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 font-medium">No hay actividades publicadas</p>
            <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">
                Cuando tus profesores publiquen contenido, aparecer&aacute; aqu&iacute;.
            </p>
        </div>
    @endforelse
</div>
