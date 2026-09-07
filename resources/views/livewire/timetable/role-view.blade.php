<div class="fade-in">
    <div class="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">{{ $subjectLabel }}</h1>
            <p class="text-emerald-600 dark:text-emerald-400 font-medium text-sm">{{ $calendar->name }} · Lunes a Viernes</p>
        </div>
        @if ($shareUrl)
            <a href="{{ $shareUrl }}" target="_blank" rel="noopener"
                class="inline-flex items-center gap-2 px-3 py-2 min-h-[44px] bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold transition-colors duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 005.656 5.656l1.5-1.5m-1.5-8.828a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.5 1.5"></path></svg>
                <span>Compartir</span>
            </a>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-white/5 border-b border-gray-200 dark:border-white/5">
                        <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Hora</th>
                        @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dayLabel)
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase tracking-widest text-gray-400">{{ $dayLabel }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($grid as $order => $row)
                        <tr class="border-b border-gray-100 dark:border-white/5 last:border-0">
                            <td class="px-3 py-2 font-bold text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $order }}º</td>
                            @foreach (range(1, 5) as $day)
                                <td class="px-2 py-2 align-top text-center">
                                    @forelse ($row->get($day) as $slot)
                                        <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                            {{ $slot->lesson?->pevaluacion?->pensum?->asignatura?->name ?? '?' }}
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            {{ $slot->lesson?->pevaluacion?->profesor?->lastname ?? '' }}{{ $slot->grupo_estable_id ? ' · '.($slot->lesson?->pevaluacion?->grupoEstable?->name ?? 'G'.$slot->grupo_estable_id) : '' }}{{ $slot->room_id ? ' · Aula' : '' }}
                                        </div>
                                    @empty
                                        <span class="text-gray-200 dark:text-white/5">—</span>
                                    @endforelse
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-400">Sin bloques asignados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>