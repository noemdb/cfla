<div class="fade-in space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Lecciones LMS</h1>
            <p class="text-amber-600 dark:text-amber-400 font-medium text-sm">Monitoreo de contenido publicado</p>
        </div>
        <div class="relative w-full md:w-72">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por tema..."
                class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none transition-all">
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-4">
        <div class="w-48">
            <select wire:model.live="lapso_id"
                class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none transition-all">
                <option value="">Todos los lapsos</option>
                @foreach($lapsos as $l)
                    <option value="{{ $l->id }}">{{ $l->name }}</option>
                @endforeach
            </select>
        </div>
        <label class="relative inline-flex items-center gap-2 cursor-pointer min-h-[44px] select-none group">
            <input type="checkbox" wire:model.live="filter_published" class="sr-only peer">
            <div class="relative w-10 h-6 rounded-full transition-all duration-500 peer-checked:bg-gradient-to-r peer-checked:from-amber-500 peer-checked:to-yellow-500 bg-gray-300 dark:bg-white/10 peer-checked:shadow-lg peer-checked:shadow-amber-500/30 after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:duration-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:shadow-md after:border after:border-gray-200 dark:after:border-white/10 peer-checked:after:shadow-amber-500/30 group-hover:after:scale-110 peer-checked:group-hover:after:scale-110"></div>
            <span class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 peer-checked:text-amber-600 dark:peer-checked:text-amber-400 transition-all duration-300 peer-checked:drop-shadow-[0_1px_2px_rgba(217,119,6,0.15)]">
                Solo publicadas
            </span>
        </label>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto rounded-xl border border-white/5">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-900/50 text-left">
                    <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Tema</th>
                    <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Asignatura</th>
                    <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Profesor</th>
                    <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Lapso</th>
                    <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</th>
                    <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($lessons as $lesson)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-4 py-3">
                            <span class="text-white font-medium">{{ $lesson->topic }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-400">
                            {{ $lesson->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-400">
                            {{ $lesson->pevaluacion?->profesor?->fullname ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-400">
                            {{ $lesson->pevaluacion?->lapso?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($lesson->lmsPublication && $lesson->lmsPublication->status === 'PUBLISHED')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/12 text-emerald-400 border border-emerald-500/20">
                                    Publicada
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-gray-500/12 text-gray-400 border border-gray-500/20">
                                    Borrador
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('app.planning.lms.preview', ['activity' => $lesson->id]) }}"
                                class="text-[10px] font-bold uppercase tracking-widest text-amber-400 hover:text-amber-300 transition-colors">
                                Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            No se encontraron lecciones en tus áreas asignadas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="flex items-center justify-between">
        <select wire:model.live="paginate"
            class="min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none transition-all w-24">
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
        {{ $lessons->links() }}
    </div>
</div>
