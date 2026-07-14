<div class="w-full mx-auto py-8 px-4 space-y-6">

    {{-- Header --}}
    <div>
        <a href="{{ route('app.planning.lms.monitor') }}"
           class="text-xs text-emerald-400 hover:text-emerald-300 mb-2 inline-block">
            ← Volver al monitor
        </a>
        <h1 class="text-xl font-bold text-white">Auditoría de Actividad</h1>
        <p class="text-sm text-slate-400 mt-1">
            {{ $activity->topic ?? 'Actividad sin título' }}
            · {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? '' }}
        </p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach(['VIEW' => 'Visitas', 'RESOURCE_DOWNLOAD' => 'Descargas', 'PUBLISH' => 'Publicaciones', 'EDIT' => 'Ediciones'] as $event => $label)
            <div class="bg-slate-800/40 border border-slate-700/50 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-white">{{ $eventCounts[$event] ?? 0 }}</p>
                <p class="text-xs text-slate-400">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filtros --}}
    <div class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Evento</label>
            <select wire:model.live="filterEvent"
                    class="bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-3 py-1.5 text-sm
                           focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                <option value="">Todos</option>
                @foreach(['VIEW','CONTENT_VIEW','RESOURCE_DOWNLOAD','PUBLISH','UNPUBLISH','EDIT','SECTION_ADD','RESOURCE_ADD','RESOURCE_DELETE'] as $ev)
                    <option value="{{ $ev }}">{{ $ev }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Desde</label>
            <input wire:model.live="dateFrom" type="date"
                   class="bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-3 py-1.5 text-sm
                          focus:ring-emerald-500/50 focus:border-emerald-500 outline-none"/>
        </div>
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Hasta</label>
            <input wire:model.live="dateTo" type="date"
                   class="bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-3 py-1.5 text-sm
                          focus:ring-emerald-500/50 focus:border-emerald-500 outline-none"/>
        </div>
    </div>

    {{-- Tabla de logs --}}
    <div class="bg-slate-800/30 border border-slate-700/50 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-700/30">
                <tr>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Fecha</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Usuario</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Evento</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Contexto</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-700/20">
                        <td class="px-4 py-2.5 text-slate-300 whitespace-nowrap">
                            {{ $log->created_at?->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-300">{{ $log->user?->name ?? '—' }}</td>
                        <td class="px-4 py-2.5">
                            <span @class([
                                'px-2 py-0.5 rounded text-xs font-medium',
                                'bg-emerald-500/10 text-emerald-400' => in_array($log->event, ['PUBLISH','VIEW']),
                                'bg-blue-500/10 text-blue-400'       => str_contains($log->event, 'RESOURCE'),
                                'bg-amber-500/10 text-amber-400'     => $log->event === 'EDIT',
                                'bg-red-500/10 text-red-400'         => $log->event === 'UNPUBLISH',
                                'bg-slate-500/10 text-slate-400'     => true,
                            ])>
                                {{ $log->event }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-slate-400 text-xs">
                            @if($log->context_type)
                                {{ class_basename($log->context_type) }} #{{ $log->context_id }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-slate-500 text-xs font-mono">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                            No hay registros de auditoría para esta actividad.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($logs->hasPages())
        <div class="mt-4">
            {{ $logs->links('vendor.pagination.custom-tailwind') }}
        </div>
    @endif
</div>
