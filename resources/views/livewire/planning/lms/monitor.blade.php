<div class="w-full mx-auto py-8 px-4 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-white">Seguimiento y Control para el Contenido LMS</h1>
            <p class="text-sm text-slate-400 mt-1">
                Supervisa, controla y da seguimiento al contenido digital publicado por los docentes.
            </p>
        </div>
    </div>

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
        <div class="bg-slate-800/40 border border-slate-700/50 rounded-xl p-3">
            <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
            <p class="text-xs text-slate-400">Total lecciones</p>
        </div>
        <div class="bg-emerald-500/5 border border-emerald-500/20 rounded-xl p-3">
            <p class="text-2xl font-bold text-emerald-400">{{ $stats['published'] }}</p>
            <p class="text-xs text-emerald-400/70">Publicadas</p>
        </div>
        <div class="bg-amber-500/5 border border-amber-500/20 rounded-xl p-3">
            <p class="text-2xl font-bold text-amber-400">{{ $stats['scheduled'] }}</p>
            <p class="text-xs text-amber-400/70">Programadas</p>
        </div>
        <div class="bg-slate-500/5 border border-slate-500/20 rounded-xl p-3">
            <p class="text-2xl font-bold text-slate-400">{{ $stats['draft'] }}</p>
            <p class="text-xs text-slate-400/70">Borradores</p>
        </div>
        <div class="bg-red-500/5 border border-red-500/20 rounded-xl p-3">
            <p class="text-2xl font-bold text-red-400">{{ $stats['archived'] }}</p>
            <p class="text-xs text-red-400/70">Archivadas</p>
        </div>
        <div class="bg-blue-500/5 border border-blue-500/20 rounded-xl p-3">
            <p class="text-2xl font-bold text-blue-400">{{ $stats['withContent'] }}</p>
            <p class="text-xs text-blue-400/70">Con contenido</p>
        </div>
        <div class="bg-purple-500/5 border border-purple-500/20 rounded-xl p-3">
            <p class="text-2xl font-bold text-purple-400">{{ $stats['totalActivities'] }}</p>
            <p class="text-xs text-purple-400/70">Total actividades</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-slate-800/30 border border-slate-700/50 rounded-xl p-4 space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-7 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Estado</label>
                <select wire:model.live="filterStatus"
                        class="w-full bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                    <option value="">Todos</option>
                    <option value="DRAFT">Borrador</option>
                    <option value="SCHEDULED">Programado</option>
                    <option value="PUBLISHED">Publicado</option>
                    <option value="ARCHIVED">Archivado</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Profesor</label>
                <select wire:model.live="filterProfesor"
                        class="w-full bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                    <option value="">Todos</option>
                    @foreach($profesores as $prof)
                        <option value="{{ $prof->id }}">{{ $prof->user?->name ?? $prof->lastname ?? 'Profesor #'.$prof->id }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Grado</label>
                <select wire:model.live="filterGrado"
                        class="w-full bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                    <option value="">Todos</option>
                    @foreach($grados as $g)
                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Sección</label>
                <select wire:model.live="filterSeccion"
                        class="w-full bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                    <option value="">Todas</option>
                    @foreach($secciones as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Asignatura</label>
                <select wire:model.live="filterAsignatura"
                        class="w-full bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                    <option value="">Todas</option>
                    @foreach($asignaturas as $a)
                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">P.Estudio</label>
                <select wire:model.live="filterPestudio"
                        class="w-full bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                    <option value="">Todos</option>
                    @foreach($pestudios as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Buscar</label>
                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Título actividad…"
                       class="w-full bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-3 py-1.5 text-sm placeholder-slate-500 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none"/>
            </div>
        </div>
        @php $hasFilters = $search || $filterStatus || $filterProfesor || $filterGrado || $filterSeccion || $filterAsignatura || $filterPestudio; @endphp
        @if($hasFilters)
            <div class="flex justify-end pt-1">
                <button wire:click="clearFilters"
                        class="text-xs text-slate-500 hover:text-slate-300 transition-colors inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Limpiar filtros
                </button>
            </div>
        @endif
    </div>

    {{-- Bulk action bar --}}
    @if(count($selectedIds) > 0)
        <div class="flex items-center gap-3 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
            <span class="text-sm text-emerald-300 font-medium">{{ count($selectedIds) }} seleccionado(s)</span>
            <div class="flex items-center gap-2 ml-auto">
                <button wire:click="bulkPublish"
                        wire:confirm="¿Publicar {{ count($selectedIds) }} contenido(s)?"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500/30 border border-emerald-500/30 transition-colors">
                    Publicar
                </button>
                <button wire:click="bulkUnpublish"
                        wire:confirm="¿Archivar {{ count($selectedIds) }} contenido(s)?"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-500/20 text-red-300 hover:bg-red-500/30 border border-red-500/30 transition-colors">
                    Archivar
                </button>
                <button wire:click="bulkDelete"
                        wire:confirm="¿Eliminar permanentemente {{ count($selectedIds) }} publicación(es)? Esta acción no se puede deshacer."
                        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20 transition-colors">
                    Eliminar
                </button>
                <button wire:click="clearSelection"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-700/50 text-slate-400 hover:text-white border border-slate-600/50 transition-colors">
                    Cancelar
                </button>
            </div>
        </div>
    @endif

    {{-- Tabla de publicaciones --}}
    <div class="bg-slate-800/30 border border-slate-700/50 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-700/30">
                <tr>
                    <th class="text-center px-2 py-2.5 w-10">
                        <input type="checkbox" wire:model.live="selectAll"
                               class="rounded border-slate-600 bg-slate-700 text-emerald-500 focus:ring-emerald-500/50">
                    </th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Actividad</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Asignatura</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Grado/Sec.</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Profesor</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Estado</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Publicado</th>
                    <th class="text-center px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @forelse($publications as $pub)
                    @php
                        $profesor = $pub->activity?->pevaluacion?->profesor;
                        $grado = $pub->activity?->pevaluacion?->pensum?->grado?->name ?? '—';
                        $seccion = $pub->activity?->pevaluacion?->seccion?->name ?? '—';
                        $isSelected = in_array($pub->activity_id, $selectedIds);
                    @endphp
                    <tr class="hover:bg-slate-700/20 {{ $isSelected ? 'bg-emerald-500/5' : '' }}">
                        <td class="text-center px-2 py-2.5">
                            <input type="checkbox" value="{{ $pub->activity_id }}"
                                   wire:change="toggleSelect({{ $pub->activity_id }})"
                                   {{ $isSelected ? 'checked' : '' }}
                                   class="rounded border-slate-600 bg-slate-700 text-emerald-500 focus:ring-emerald-500/50">
                        </td>
                        <td class="px-4 py-2.5 text-slate-200 max-w-[200px] truncate" title="{{ $pub->activity->topic ?? '' }}">
                            {{ $pub->activity->topic ?? '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-400">
                            {{ $pub->activity->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-400 text-xs">
                            {{ $grado }} {{ $seccion }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-400">
                            {{ $profesor?->user?->name ?? $profesor?->lastname ?? '—' }}
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
                            @if($pub->status === 'SCHEDULED' && $pub->publish_at)
                                <span class="block text-[10px] text-amber-500/70 mt-0.5">
                                    {{ $pub->publish_at->format('d/m/Y H:i') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-slate-500 text-xs">
                            @if($pub->published_at)
                                {{ $pub->published_at->format('d/m/Y H:i') }}
                            @elseif($pub->status === 'SCHEDULED' && $pub->publish_at)
                                <span class="text-amber-500/60">Pendiente</span>
                            @elseif($pub->status === 'DRAFT')
                                —
                            @else
                                <span class="text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5">
                            <div class="flex items-center justify-center gap-1">
                                {{-- Ver contenido en dialog --}}
                                <button wire:click="openPreview({{ $pub->activity_id }})"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-white bg-slate-700/30 hover:bg-slate-600/50 border border-slate-600/30 hover:border-slate-500/50 transition-all"
                                        title="Vista previa de lección">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>

                                {{-- Auditar --}}
                                <a href="{{ route('planning.lms.activity.audit', $pub->activity) }}"
                                   class="p-1.5 rounded-lg text-slate-400 hover:text-cyan-300 bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/20 hover:border-cyan-400/40 transition-all"
                                   title="Auditar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </a>

                                {{-- Configuración --}}
                                @if($pub->status !== 'DRAFT')
                                    <button wire:click="openSettings({{ $pub->activity_id }})"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-blue-300 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/20 hover:border-blue-400/40 transition-all"
                                            title="Configurar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </button>
                                @endif

                                {{-- Programar / Publicar --}}
                                @if($pub->status === 'DRAFT' || $pub->status === 'ARCHIVED')
                                    <button wire:click="publish({{ $pub->activity_id }})"
                                            wire:confirm="¿Publicar esta lección? Será visible para los estudiantes."
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 hover:border-emerald-400/40 transition-all"
                                            title="Publicar ahora">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                        </svg>
                                    </button>
                                    <button wire:click="openSchedule({{ $pub->activity_id }})"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-amber-300 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20 hover:border-amber-400/40 transition-all"
                                            title="Programar publicación">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                @endif
                                @if($pub->status === 'PUBLISHED' || $pub->status === 'SCHEDULED')
                                    <button wire:click="unpublish({{ $pub->activity_id }})"
                                            wire:confirm="¿Archivar esta lección? Dejará de ser visible para los estudiantes."
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-red-300 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 hover:border-red-400/40 transition-all"
                                            title="Archivar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                        </svg>
                                    </button>
                                @endif
                                @if($pub->status === 'PUBLISHED' || $pub->status === 'SCHEDULED')
                                    <button wire:click="setDraft({{ $pub->activity_id }})"
                                            wire:confirm="¿Revertir a borrador? La lección dejará de estar {{ $pub->status === 'SCHEDULED' ? 'programada' : 'publicada' }}."
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-orange-300 bg-orange-500/10 hover:bg-orange-500/20 border border-orange-500/20 hover:border-orange-400/40 transition-all"
                                            title="Revertir a borrador">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-slate-500">
                            No hay publicaciones que coincidan con los filtros.
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

    {{-- ============================================================ --}}
    {{-- MODAL: Programar publicación                                 --}}
    {{-- ============================================================ --}}
    @if($showScheduleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-slate-700/50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Programar publicación
                    </h3>
                    <button wire:click="$set('showScheduleModal', false)" class="text-slate-500 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                {{-- Body --}}
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Fecha de publicación *</label>
                        <input type="datetime-local" wire:model="schedulePublishAt"
                               class="w-full bg-slate-900/50 border border-slate-600 text-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                        @error('schedulePublishAt') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Despublicar automáticamente (opcional)</label>
                        <input type="datetime-local" wire:model="scheduleUnpublishAt"
                               class="w-full bg-slate-900/50 border border-slate-600 text-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                        @error('scheduleUnpublishAt') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm text-slate-300">
                            <input type="checkbox" wire:model="scheduleAllowComments"
                                   class="rounded border-slate-600 bg-slate-700 text-emerald-500 focus:ring-emerald-500/50">
                            Permitir comentarios
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-300">
                            <input type="checkbox" wire:model="scheduleAllowDownloads"
                                   class="rounded border-slate-600 bg-slate-700 text-emerald-500 focus:ring-emerald-500/50">
                            Permitir descargas
                        </label>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Notas internas (opcional)</label>
                        <textarea wire:model="scheduleNotes" rows="2"
                                  class="w-full bg-slate-900/50 border border-slate-600 text-slate-200 rounded-lg px-3 py-2 text-sm placeholder-slate-500 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none"
                                  placeholder="Notas para el coordinador…"></textarea>
                        @error('scheduleNotes') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-slate-700/50 flex justify-end gap-3">
                    <button wire:click="$set('showScheduleModal', false)"
                            class="px-4 py-2 text-sm font-medium text-slate-400 hover:text-white bg-slate-700/50 hover:bg-slate-700 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="saveSchedule"
                            class="px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-500 rounded-lg transition-colors">
                        Programar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Configurar publicación                                --}}
    {{-- ============================================================ --}}
    @if($showSettingsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-slate-700/50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Configurar publicación
                    </h3>
                    <button wire:click="$set('showSettingsModal', false)" class="text-slate-500 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                {{-- Body --}}
                <div class="px-6 py-5 space-y-4">
                    <div class="flex items-center gap-2 text-sm text-slate-400">
                        <span>Estado actual:</span>
                        <span @class([
                            'px-2 py-0.5 rounded text-xs font-medium',
                            'bg-emerald-500/10 text-emerald-400' => $settingsStatus === 'PUBLISHED',
                            'bg-amber-500/10 text-amber-400'     => $settingsStatus === 'SCHEDULED',
                            'bg-red-500/10 text-red-400'         => $settingsStatus === 'ARCHIVED',
                            'bg-slate-500/10 text-slate-400'     => true,
                        ])>
                            {{ match($settingsStatus) {
                                'PUBLISHED' => 'Publicado',
                                'SCHEDULED' => 'Programado',
                                'ARCHIVED'  => 'Archivado',
                                default     => 'Borrador',
                            } }}
                        </span>
                    </div>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm text-slate-300">
                            <input type="checkbox" wire:model="settingsAllowComments"
                                   class="rounded border-slate-600 bg-slate-700 text-emerald-500 focus:ring-emerald-500/50">
                            Permitir comentarios
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-300">
                            <input type="checkbox" wire:model="settingsAllowDownloads"
                                   class="rounded border-slate-600 bg-slate-700 text-emerald-500 focus:ring-emerald-500/50">
                            Permitir descargas
                        </label>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Notas internas</label>
                        <textarea wire:model="settingsNotes" rows="2"
                                  class="w-full bg-slate-900/50 border border-slate-600 text-slate-200 rounded-lg px-3 py-2 text-sm placeholder-slate-500 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none"
                                  placeholder="Notas para el coordinador…"></textarea>
                        @error('settingsNotes') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-slate-700/50 flex justify-end gap-3">
                    <button wire:click="$set('showSettingsModal', false)"
                            class="px-4 py-2 text-sm font-medium text-slate-400 hover:text-white bg-slate-700/50 hover:bg-slate-700 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="saveSettings"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-500 rounded-lg transition-colors">
                        Guardar cambios
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Vista previa de lección (w-full dialog)              --}}
    {{-- ============================================================ --}}
    @if($showPreviewModal && $previewActivityId)
        <div class="fixed inset-0 z-50 bg-black/70 backdrop-blur-md flex items-start justify-center py-6 px-4" wire:click.self="closePreview">
            <div class="w-full max-w-6xl h-full flex flex-col bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-700/50" wire:click.stop>
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 shrink-0">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Vista previa de lección
                    </h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('planning.lms.preview', $previewActivityId) }}"
                           target="_blank"
                           class="px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-700/50 text-slate-400 hover:text-white border border-slate-600/50 transition-colors inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Abrir en pestaña
                        </a>
                        <button wire:click="closePreview"
                                class="p-1.5 rounded-lg text-slate-500 hover:text-white hover:bg-white/5 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                {{-- Iframe — ocupa todo el espacio restante --}}
                <div class="flex-1 bg-white">
                    <iframe src="{{ route('planning.lms.preview', $previewActivityId) }}"
                            class="w-full h-full border-0"
                            title="Vista previa de lección"></iframe>
                </div>
            </div>
        </div>
    @endif
</div>
