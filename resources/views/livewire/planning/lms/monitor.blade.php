<div class="w-full mx-auto py-8 px-4 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-white">Monitor de Publicaciones LMS</h1>
            <p class="text-sm text-slate-400 mt-1">
                Supervisa qué actividades están publicadas, programadas o en borrador.
            </p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Estado</label>
            <select wire:model.live="filterStatus"
                    class="bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-3 py-1.5 text-sm
                           focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                <option value="">Todos</option>
                <option value="DRAFT">Borrador</option>
                <option value="SCHEDULED">Programado</option>
                <option value="PUBLISHED">Publicado</option>
                <option value="ARCHIVED">Archivado</option>
            </select>
        </div>
        <div class="flex-1 max-w-xs">
            <input wire:model.live="search" type="search" placeholder="Buscar por título de actividad…"
                   class="w-full bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-3 py-1.5 text-sm
                          placeholder-slate-500 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none"/>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-slate-800/30 border border-slate-700/50 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-700/30">
                <tr>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Actividad</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Asignatura</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Profesor</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Estado</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Publicado</th>
                    <th class="text-center px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Auditar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @forelse($publications as $pub)
                <tr class="hover:bg-slate-700/20">
                    <td class="px-4 py-2.5 text-slate-200">
                        {{ $pub->activity->topic ?? '—' }}
                    </td>
                    <td class="px-4 py-2.5 text-slate-400">
                        {{ $pub->activity->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                    </td>
                    <td class="px-4 py-2.5 text-slate-400">
                        {{ $pub->activity->pevaluacion?->profesor?->lastname ?? '—' }}
                    </td>
                    <td class="px-4 py-2.5">
                        <span @class([
                            'px-2 py-0.5 rounded text-xs font-medium',
                            'bg-emerald-500/10 text-emerald-400' => $pub->status === 'PUBLISHED',
                            'bg-amber-500/10 text-amber-400'     => $pub->status === 'SCHEDULED',
                            'bg-slate-500/10 text-slate-400'     => $pub->status === 'DRAFT',
                            'bg-red-500/10 text-red-400'         => $pub->status === 'ARCHIVED',
                        ])>
                            {{ match($pub->status) {
                                'PUBLISHED' => 'Publicado',
                                'SCHEDULED' => 'Programado',
                                'ARCHIVED'  => 'Archivado',
                                default     => 'Borrador',
                            } }}
                        </span>
                    </td>
                    <td class="px-4 py-2.5 text-slate-400 text-xs">
                        {{ $pub->published_at?->format('d/m/Y H:i') ?? '—' }}
                    </td>
                    <td class="px-4 py-2.5 text-center">
                        <a href="{{ route('planning.lms.activity.audit', $pub->activity) }}"
                           class="text-xs text-emerald-400 hover:text-emerald-300">
                            Ver logs
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                        No hay publicaciones registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($publications->hasPages())
        <div class="mt-4">
            {{ $publications->links('vendor.pagination.custom-tailwind') }}
        </div>
    @endif
</div>
