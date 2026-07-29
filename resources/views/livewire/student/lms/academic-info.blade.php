<div class="max-w-4xl mx-auto py-8 px-4 space-y-6">
    <h1 class="text-lg font-bold text-gray-900 dark:text-white">Información Académica</h1>

    @if($inscripcionData && $inscripcionData['estudiant'])
        {{-- Resumen de inscripción --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Grado</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inscripcionData['grado']?->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Sección</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inscripcionData['seccion']?->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Plan de Estudio</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inscripcionData['pestudio']?->name ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Pensum (Asignaturas) --}}
        @if($pensums && $pensums->isNotEmpty())
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Áreas de Formación</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($pensums as $pensum)
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/10 flex items-center justify-center shrink-0">
                            <span class="text-xs font-bold text-emerald-400">{{ strtoupper(substr($pensum->asignatura?->name ?? '?', 0, 2)) }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $pensum->asignatura?->name ?? 'Sin asignatura' }}</p>
                            <p class="text-[10px] text-gray-400">{{ $pensum->asignatura?->code ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Planificación (Pevaluacions) --}}
        @if($pevaluacions && $pevaluacions->isNotEmpty())
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white">Planificación Académica</h2>
                @if($currentLapsoId)
                    <span class="text-[10px] font-bold text-gray-400">Lapso actual</span>
                @endif
            </div>
            <div class="space-y-2">
                @foreach($pevaluacions as $pev)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $pev->pensum?->asignatura?->name ?? '—' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $pev->lapso?->name ?? '' }}
                                @if($pev->profesor)
                                    · {{ $pev->profesor?->user?->profile?->firstname ?? '' }} {{ $pev->profesor?->user?->profile?->lastname ?? '' }}
                                @endif
                            </p>
                        </div>
                        <span class="text-xs text-gray-400 ml-3">{{ $pev->objetivo ?? '' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-sm text-gray-500">No hay planificación disponible para este lapso.</p>
        </div>
        @endif
    @else
        <div class="text-center py-12">
            <p class="text-gray-500">No se encontraron datos académicos.</p>
            <p class="text-xs text-gray-400 mt-1">Contacta al departamento de control de estudio.</p>
        </div>
    @endif
</div>
