<div class="fade-in space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-1">Debates · Revisión de Preguntas</h1>
            <p class="text-amber-600 dark:text-amber-400 font-medium text-sm">
                Revisa y da seguimiento a las preguntas de debate de las áreas de conocimiento que tienes asignadas.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                Seguimiento Jefe de Área
            </span>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400/60">Total (tu scope)</div>
            <div class="text-3xl font-black text-white">{{ $metrics['total'] }}</div>
        </div>
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400/60">En revisión</div>
            <div class="text-3xl font-black {{ $metrics['en_revision'] > 0 ? 'text-amber-400' : 'text-white' }}">{{ $metrics['en_revision'] }}</div>
        </div>
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-400/60">Activas</div>
            <div class="text-3xl font-black text-emerald-400">{{ $metrics['activas'] }}</div>
        </div>
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Inactivas</div>
            <div class="text-3xl font-black text-gray-400">{{ $metrics['inactivas'] }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-2 sm:p-5 rounded-lg">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Área de Conocimiento</label>
                <select wire:model.live="filterAreaId"
                    class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none">
                    <option value="">Todas mis áreas ({{ $areas->count() }})</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->name }} [{{ $area->pestudio->code ?? '?' }}]</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Texto, categoría, observación..."
                    class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none placeholder:text-gray-600">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Categoría</label>
                <select wire:model.live="filterCategory"
                    class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none">
                    <option value="">Todas</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Estado</label>
                <select wire:model.live="filterActive"
                    class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none">
                    <option value="">Todos</option>
                    <option value="1">Activas</option>
                    <option value="0">Inactivas</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Revisión</label>
                <select wire:model.live="filterUnderReview"
                    class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none">
                    <option value="">Todas</option>
                    <option value="1">En revisión</option>
                    <option value="0">Sin revisión</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Pregunta / Categoría</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Área / Pensum</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Debate</th>
                        <th class="text-center px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</th>
                        <th class="text-right px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Seguimiento</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($questions as $q)
                        <tr class="hover:bg-white/[0.02] transition-colors {{ $q->status_under_review ? 'bg-amber-500/[0.04]' : '' }}">
                            <td class="px-4 py-3">
                                <p class="text-white font-medium line-clamp-2 leading-snug" title="{{ $q->text }}">{{ \Illuminate\Support\Str::limit($q->text, 120) }}</p>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-white/5 text-gray-400 border border-white/10">{{ $q->category ?? '—' }}</span>
                                    <span class="text-[10px] text-gray-500">· {{ $q->time }}s · peso {{ $q->weighting }}</span>
                                    @if($q->status_answer)
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Respondida</span>
                                    @endif
                                </div>
                                @if($q->observation)
                                    <p class="text-[11px] text-amber-300/80 italic mt-1 line-clamp-2">“{{ $q->observation }}”</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-white font-medium">{{ $q->pensum?->asignatura?->name ?? '—' }}</span>
                                <span class="block text-[10px] text-gray-500">{{ $q->pensum?->grado?->name ?? $q->pensum_id ?? '—' }} · {{ $q->pensum?->pestudio?->code ?? '?' }}</span>
                                @php
                                    // resolver área por pensum_id
                                    $areaName = null;
                                    if ($q->pensum_id) {
                                        $campo = \App\Models\app\Academy\CampoConocimiento::where('pensum_id', $q->pensum_id)->with('area_conocimiento')->first();
                                        $areaName = $campo?->area_conocimiento?->name;
                                    }
                                @endphp
                                @if($areaName)
                                    <span class="inline-flex mt-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">{{ $areaName }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-xs">
                                {{ $q->debate?->name ?? '—' }}
                                <span class="block text-[10px] text-gray-500">{{ $q->debate?->competition?->name ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="toggleActive({{ $q->id }})"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold border transition-all {{ $q->status_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-gray-500/10 text-gray-400 border-white/10 hover:bg-white/10' }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $q->status_active ? 'bg-emerald-500' : 'bg-gray-500' }}"></span>
                                    {{ $q->status_active ? 'Activa' : 'Inactiva' }}
                                </button>
                                <div class="mt-1">
                                    <button wire:click="toggleUnderReview({{ $q->id }})"
                                        class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold border transition-all {{ $q->status_under_review ? 'bg-amber-500/15 text-amber-400 border-amber-500/30' : 'bg-white/5 text-gray-400 border-white/10 hover:bg-white/10' }}">
                                        {{ $q->status_under_review ? 'En revisión' : 'Sin revisión' }}
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <button wire:click="openObservation({{ $q->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Observar
                                    </button>
                                    <a href="{{ route('app.planning.educational.competition.index') }}" target="_blank"
                                        class="inline-flex items-center px-2 py-1.5 rounded-lg text-xs text-gray-400 hover:text-white hover:bg-white/5 border border-transparent transition-all" title="Ir a competiciones">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center">
                                <svg class="w-12 h-12 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-gray-500 font-medium">No hay preguntas en tu scope</p>
                                <p class="text-gray-600 text-sm mt-1">Verifica que tu usuario sea <code class="text-gray-400">AreaConocimiento.leader_id</code> y que existan <code class="text-gray-400">campo_conocimientos.pensum_id</code>.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($questions->hasPages())
            <x-pagination-wrapper :paginator="$questions" />
        @endif
    </div>

    {{-- Modal observación --}}
    @if($showObservationModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeObservation"></div>
            <div class="relative bg-gray-900 border border-white/10 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-white/5 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white">Observación de seguimiento</h3>
                    <button wire:click="closeObservation" class="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-5 space-y-3">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500">Observación (status_under_review / observation)</label>
                    <textarea wire:model="observation" rows="5" placeholder="Escribe indicaciones para el autor de la pregunta…"
                        class="w-full bg-white/5 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none resize-none"></textarea>
                    @error('observation') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
                    <p class="text-[11px] text-gray-500">Se guarda en <code class="text-gray-400">debate_questions.observation</code> y puedes alternar <code class="text-amber-400">status_under_review</code> con el botón de la tabla.</p>
                </div>
                <div class="px-5 py-3 bg-white/[0.02] border-t border-white/5 flex items-center justify-end gap-2">
                    <button wire:click="closeObservation" class="px-4 py-2 rounded-lg text-sm font-medium bg-white/5 hover:bg-white/10 text-gray-300 border border-white/10 transition-colors">Cancelar</button>
                    <button wire:click="saveObservation" class="px-4 py-2 rounded-lg text-sm font-bold bg-amber-500 hover:bg-amber-400 text-black transition-colors">Guardar</button>
                </div>
            </div>
        </div>
    @endif
</div>
