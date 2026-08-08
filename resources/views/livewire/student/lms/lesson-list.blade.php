<div class="max-w-4xl mx-auto py-8 px-4 space-y-6">
    @php
    // D2 · Color por materia. Misma paleta/clave que home/activity/académica
    // (Tailwind JIT necesita las clases literales aquí).
    $__sc = [
        'sky' => ['dot' => 'bg-sky-400', 'chip' => 'bg-sky-500/10 text-sky-400'],
        'emerald' => ['dot' => 'bg-emerald-400', 'chip' => 'bg-emerald-500/10 text-emerald-400'],
        'amber' => ['dot' => 'bg-amber-400', 'chip' => 'bg-amber-500/10 text-amber-400'],
        'indigo' => ['dot' => 'bg-indigo-400', 'chip' => 'bg-indigo-500/10 text-indigo-400'],
        'purple' => ['dot' => 'bg-purple-400', 'chip' => 'bg-purple-500/10 text-purple-400'],
        'orange' => ['dot' => 'bg-orange-400', 'chip' => 'bg-orange-500/10 text-orange-400'],
        'rose' => ['dot' => 'bg-rose-400', 'chip' => 'bg-rose-500/10 text-rose-400'],
        'teal' => ['dot' => 'bg-teal-400', 'chip' => 'bg-teal-500/10 text-teal-400'],
        'slate' => ['dot' => 'bg-slate-400', 'chip' => 'bg-slate-500/10 text-slate-400'],
    ];
    $__scResolve = static fn (?string $name): array => $__sc[\App\Models\app\Academy\Asignatura::colorKey($name)] ?? $__sc['slate'];
    @endphp

    {{-- Header --}}
    <div class="flex items-center gap-4">
        @if($showMascot)
            <x-lms.mascot :variant="'greet'" :size="'sm'" :emphasis="$mascotEmphasis" />
        @else
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        @endif
        <div>
            <h1 class="text-lg font-display font-bold text-gray-900 dark:text-white">Lecciones</h1>
            <p class="text-xs text-gray-600 dark:text-gray-400">Actividades publicadas por tus profesores</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <input type="text" wire:model.live.debounce.300ms="search"
               placeholder="Buscar lección…"
               class="w-full min-h-[44px] bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-colors"/>
        <select wire:model.live="lapsoId"
                class="w-full min-h-[44px] bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-colors">
            <option value="">Todos los lapsos</option>
            @foreach($lapsos as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
        <select wire:model.live="asignaturaId"
                class="w-full min-h-[44px] bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-colors">
            <option value="">Todas las asignaturas</option>
            @foreach($asignaturas as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>

    {{-- ═══ Bento grid: TODAS las tarjetas SIEMPRE con la misma altura ═══
         Un solo CSS grid (grid-cols-2 → lg:grid-cols-4), gap uniforme y
         grid-auto-rows-[168px] fija la altura de cada pista. Ningún tile usa
         row-span, así que Toda celda ocupa exactamente 1 fila = 168px,
         sea col-span-1 o col-span-2 (solo varía el ancho). Ninguna tarjeta
         se estira ni se encoge por el contenido. --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 grid-auto-rows-[168px] [grid-auto-flow:dense]">
        @forelse($activities as $activity)
            @php
                $span = match ($loop->index % 6) {
                    0 => 'col-span-2',
                    1 => 'col-span-2',
                    2 => 'col-span-2',
                    3 => 'col-span-1',
                    4 => 'col-span-1',
                    5 => 'col-span-2',
                };
                $sc = $__scResolve($activity->pevaluacion?->pensum?->asignatura?->name);
            @endphp
            <a href="{{ route('student.lms.activity', $activity) }}"
               class="group flex flex-col {{ $span }} bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 overflow-hidden transition-all duration-200 ease-out hover:-translate-y-0.5 hover:shadow-md hover:ring-2 hover:ring-emerald-500/30 motion-reduce:transform-none motion-reduce:transition-none focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg {{ $sc['chip'] }} flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start gap-2">
                            <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-300 transition-colors truncate">
                                {{ $activity->topic }}
                            </p>
                            @if($activity->lmsPublication?->isPreviewToStudents())
                                <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-500/10 border border-amber-300 dark:border-amber-500/30">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Vista previa
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 flex items-center gap-1.5 truncate">
                            <span class="w-2 h-2 {{ $sc['dot'] }} rounded-full shrink-0" aria-hidden="true"></span>
                            {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                        </p>
                    </div>
                </div>
                @if($activity->description)
                    <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2">
                        {{ Str::limit(strip_tags($activity->description), 120) }}
                    </p>
                @endif
                <div class="mt-auto flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-[11px] text-gray-500 dark:text-gray-400 tabular-nums">
                        {{ $activity->pevaluacion?->lapso?->name ?? '' }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 min-h-[28px] rounded text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10">
                        Ver lección
                    </span>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-16">
                @if($showMascot)
                    <x-lms.mascot :variant="'idle'" :size="'sm'" :emphasis="$mascotEmphasis" />
                @endif
                <p class="text-gray-600 font-medium mt-2">No hay lecciones disponibles</p>
                <p class="text-xs text-gray-500 mt-1">Las lecciones aparecerán cuando los profesores las publiquen.</p>
            </div>
        @endforelse
    </div>

    @if($activities->hasPages())
        <div class="pt-4">{{ $activities->links('vendor.livewire.custom-tailwind') }}</div>
    @endif
</div>