<div class="fade-in">
    <div class="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">{{ $subjectLabel }}</h1>
            <p class="text-emerald-600 dark:text-emerald-400 font-medium text-sm">{{ $calendar->name }} · Lunes a Viernes</p>
        </div>
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
                                    @php $slot = $row->get($day); @endphp
                                    @if ($slot)
                                        <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                            {{ $slot->lesson?->pevaluacion?->pensum?->asignatura?->name ?? '?' }}
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            {{ $slot->lesson?->pevaluacion?->profesor?->lastname ?? '' }}{{ $slot->room_id ? ' · Aula' : '' }}
                                        </div>
                                    @else
                                        <span class="text-gray-200 dark:text-white/5">—</span>
                                    @endif
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