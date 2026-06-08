<div>
    {{-- Loading indicator --}}
    <div wire:loading class="mb-4">
        <div class="flex items-center gap-2 text-sm text-emerald-400 font-medium">
            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Procesando...
        </div>
    </div>

    {{-- Main card --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-2xl overflow-hidden">

        {{-- Alert: Observación Coord.Eval. --}}
        <div class="border-b border-white/5 px-6 py-4 bg-amber-500/5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 bg-amber-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Observación [Coord.Eval.]</span>
                    </div>
                    @if($pevaluacion->observations)
                        <p class="text-sm text-gray-300 leading-relaxed">{{ $pevaluacion->observations }}</p>
                    @else
                        <p class="text-sm text-gray-500">No hay observaciones registradas.</p>
                    @endif
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    {{-- PDF Resume --}}
                    <a href="{{ route('app.profesors.activities.resume', $pevaluacion->id) }}"
                        title="Resumen del Plan de Actividades"
                        target="_blank"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </a>
                    {{-- PDF Format --}}
                    <a href="{{ route('app.profesors.activities.format', $pevaluacion->id) }}"
                        title="Plan de Actividades Completo"
                        target="_blank"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 border border-purple-500/20 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </a>
                    {{-- Empty All Activities --}}
                    @php $disabled = (!empty($pevaluacion->observations) || !$enable_edit) ? true : false; @endphp
                    <button wire:click="emptyActivities"
                            title="Eliminar todas las actividades"
                            {{ $disabled ? 'disabled' : '' }}
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg {{ $disabled ? 'bg-gray-800/50 text-gray-600 cursor-not-allowed' : 'bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20' }} transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Create Overlay (solo para nueva actividad) --}}
        @if($modeCreator)
            @include('livewire.profesor.activity.partials.create')
        @endif

        {{-- Activities List --}}
        <div class="p-6">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/5">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                    Actividades registradas
                    <span class="text-gray-500 font-normal normal-case">({{ $activities->count() }})</span>
                </h3>
                <div class="flex items-center gap-2">
                    {{-- Clone selector (only if activities empty) --}}
                    @if($activities->isEmpty())
                        <select wire:model="seccion_id"
                            class="bg-gray-800/50 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-gray-300 focus:border-emerald-500/50">
                            <option value="">Seleccione sección</option>
                            @foreach($list_seccions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <button wire:click="clone"
                            {{ $enable_edit ? '' : 'disabled' }}
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200 {{ $enable_edit ? 'bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 border border-cyan-500/20' : 'bg-gray-800/50 text-gray-600 cursor-not-allowed' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Clonar
                        </button>
                    @endif

                    {{-- New Activity --}}
                    <button wire:click="setCreate"
                        {{ $enable_edit ? '' : 'disabled' }}
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl text-xs font-bold transition-all duration-200 {{ $enable_edit ? 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20' : 'bg-gray-800/50 text-gray-600 cursor-not-allowed' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        + Nuevo
                    </button>
                </div>
            </div>

            {{-- Activities --}}
            <div class="space-y-4">
                @forelse($activities as $item)
                    @php $achievements = $item->achievements; @endphp
                    <div class="bg-gray-800/30 border border-white/5 rounded-xl p-4 transition-all hover:border-white/10 {{ $item->id == $activity_id ? 'ring-1 ring-emerald-500/20' : '' }}">

                        {{-- Activity Header --}}
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                {{-- Status badge --}}
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-400">
                                        {{ $loop->iteration }}
                                    </span>
                                    @if($item->status_resume)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            Act. Evaluación
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            Sin actividad de evaluación
                                        </span>
                                    @endif
                                    <span class="text-[11px] text-gray-500 font-mono">
                                        {{ \Carbon\Carbon::parse($item->finicial)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($item->ffinal)->format('d/m/Y') }}
                                    </span>
                                </div>

                                {{-- Content --}}
                                <div class="space-y-1 text-sm text-gray-300">
                                    @if($item->description)
                                        <div><span class="font-medium text-emerald-400">Act.Eval:</span> {{ $item->description }}</div>
                                    @endif
                                    <div><span class="font-medium text-gray-500">Tema:</span> {{ $item->topic }}</div>
                                    <div><span class="font-medium text-gray-500">T.Temático:</span> {{ $item->thematic }}</div>
                                    <div><span class="font-medium text-gray-500">Enseñanza:</span> {{ Str::limit($item->teaching, 120) }}</div>
                                    <div><span class="font-medium text-gray-500">Aprendizaje:</span> {{ Str::limit($item->learning, 120) }}</div>
                                </div>

                                {{-- Comment from J.Área --}}
                                @if($item->comments)
                                    <div class="mt-2 p-2 bg-cyan-500/5 border border-cyan-500/10 rounded-lg">
                                        <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider">Comentario [J.Área]</span>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $item->comments }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Action buttons --}}
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button wire:click="viewDetail({{ $item->id }})"
                                    title="Ver todos los detalles"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button wire:click="setEditActivity({{ $item->id }})"
                                    title="Editar actividad"
                                    {{ $enable_edit ? '' : 'disabled' }}
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold {{ $enable_edit ? 'bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20' : 'bg-gray-800/50 text-gray-600 cursor-not-allowed' }} transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                @php $disabled = ($achievements->count() > 0 || !$enable_edit); @endphp
                                <button wire:click="delActivity({{ $item->id }})"
                                    title="Eliminar actividad"
                                    {{ $disabled ? 'disabled' : '' }}
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold {{ $disabled ? 'bg-gray-800/50 text-gray-600 cursor-not-allowed' : 'bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20' }} transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Achievements Section --}}
                        <div class="mt-3 ml-8 pl-4 border-l border-white/5">
                            {{-- Achievement Create Overlay --}}
                            @if($modeCreatorAchievement && $item->id == $activity->id)
                                <div class="mb-3 p-4 bg-gray-800/50 border border-white/10 rounded-xl">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-xs font-bold text-gray-300 uppercase tracking-wider">Agregar o Editar Indicadores</span>
                                        <button wire:click="close" class="text-gray-500 hover:text-gray-300 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 gap-3">
                                        {{-- Name --}}
                                        <div>
                                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['name'] ?? 'Indicador' }}</label>
                                            <input type="text" wire:model="achievement.name"
                                                class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200"
                                                placeholder="{{ $list_comment['name'] ?? '' }}">
                                            @error('achievement.name') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Quantitative toggle --}}
                                        <div class="flex items-center gap-3">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" wire:model="achievement.status_quantitative_weighting"
                                                    class="rounded border-white/10 bg-gray-800/50 text-emerald-500 focus:ring-emerald-500/20">
                                                <span class="text-[11px] text-gray-400">{{ $list_comment['status_quantitative_weighting'] ?? 'Indicador Cuantitativo' }}</span>
                                            </label>
                                        </div>

                                        {{-- Weighting --}}
                                        @if($achievement->status_quantitative_weighting)
                                        <div>
                                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $list_comment['weighting'] ?? 'Ponderación' }}</label>
                                            <select wire:model="achievement.weighting"
                                                class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50">
                                                <option value="">Seleccione</option>
                                                @for($i = 1; $i <= 20; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                            @error('achievement.weighting') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        @endif
                                    </div>

                                    <div class="mt-4">
                                        <button wire:click="saveAchievement"
                                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 hover:text-emerald-300 rounded-xl border border-emerald-500/20 transition-all duration-200 text-xs font-bold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Guardar
                                        </button>
                                    </div>
                                </div>
                            @endif

                            {{-- Achievement list header --}}
                            <div class="flex items-center justify-between py-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-600">Indicadores / Aprendizajes Esperados</span>
                                <button wire:click="addAchievement({{ $item->id }})"
                                    {{ $enable_edit ? '' : 'disabled' }}
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $enable_edit ? 'bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20' : 'bg-gray-800/50 text-gray-600 cursor-not-allowed' }} transition-all duration-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Agregar
                                </button>
                            </div>

                            {{-- Achievement items --}}
                            <div class="space-y-1">
                                @forelse($achievements as $ach)
                                    <div class="flex items-center justify-between py-1.5 px-3 rounded-lg {{ $achievement_id == $ach->id ? 'bg-gray-700/30 ring-1 ring-emerald-500/20' : 'hover:bg-white/[0.02]' }} transition-all">
                                        <span class="text-xs text-gray-400">
                                            - {{ $ach->name }}
                                            @if($ach->status_quantitative_weighting)
                                                <span class="text-emerald-400 font-mono font-medium">[{{ $ach->weighting }}]</span>
                                            @endif
                                        </span>
                                        <div class="flex items-center gap-1">
                                            @if($enable_edit)
                                                <button wire:click="setEditAchievement({{ $ach->id }})"
                                                    class="p-1 text-gray-500 hover:text-amber-400 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button wire:click="deleteAchievement({{ $ach->id }})"
                                                    class="p-1 text-gray-500 hover:text-red-400 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-[11px] text-gray-600 italic py-1">No hay indicadores</p>
                                @endforelse

                                {{-- Total weight --}}
                                @php $totalWeight = $achievements->sum(fn($a) => (int)($a->weighting ?? 0)); @endphp
                                @if($totalWeight > 0 && $achievements->count() > 0)
                                    <div class="flex items-center justify-between py-2 px-3 mt-2 bg-gray-800/30 border border-white/5 rounded-lg">
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Ponderaciones</span>
                                        <span class="text-sm font-bold text-emerald-400 font-mono">{{ $totalWeight }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-sm font-medium text-gray-400">No hay actividades registradas</p>
                        <p class="text-xs text-gray-600 mt-1">Presione <span class="text-emerald-400 font-medium">"+ Nuevo"</span> para comenzar.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- DETAIL MODAL --}}
    @if($showDetailModal && $detailActivity)
        <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="detail-modal-{{ $detailActivity->id }}">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeDetailModal"></div>

            {{-- Modal panel --}}
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative w-full max-w-6xl bg-gray-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-white/5 bg-gray-800/50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Detalles de la Actividad</h3>
                                <p class="text-xs text-gray-500">
                                    {{ $pevaluacion->pensum?->asignatura?->name }} —
                                    {{ $pevaluacion->pensum?->grado?->name }} {{ $pevaluacion->seccion?->name }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="closeDetailModal"
                            class="p-1.5 text-gray-500 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-5 space-y-5 max-h-[70vh] overflow-y-auto">

                        {{-- Status + Fechas --}}
                        <div class="flex flex-wrap items-center gap-3 pb-4 border-b border-white/5">
                            @if($detailActivity->status_resume)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                    Act. Evaluación
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    Sin actividad de evaluación
                                </span>
                            @endif
                            <span class="text-xs text-gray-500 font-mono">
                                {{ \Carbon\Carbon::parse($detailActivity->finicial)->format('d/m/Y') }}
                            </span>
                            <span class="text-gray-600">→</span>
                            <span class="text-xs text-gray-500 font-mono">
                                {{ \Carbon\Carbon::parse($detailActivity->ffinal)->format('d/m/Y') }}
                            </span>
                        </div>

                        {{-- 2-column grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- Col 1 --}}
                            <div class="space-y-4">
                                {{-- Actividad Evaluativa --}}
                                @if($detailActivity->description)
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Actividad Evaluativa</label>
                                    <p class="text-sm text-gray-200 leading-relaxed">{{ $detailActivity->description }}</p>
                                </div>
                                @endif

                                {{-- Tema generador --}}
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Tema Generador y Énfasis</label>
                                    <p class="text-sm text-gray-200 leading-relaxed">{{ $detailActivity->topic }}</p>
                                </div>

                                {{-- Tejido temático --}}
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Tejido Temático</label>
                                    <p class="text-sm text-gray-200 leading-relaxed">{{ $detailActivity->thematic }}</p>
                                </div>

                                {{-- Referentes --}}
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Referentes Teórico-Prácticos</label>
                                    <p class="text-sm text-gray-200 leading-relaxed">{{ $detailActivity->references }}</p>
                                </div>
                            </div>

                            {{-- Col 2 --}}
                            <div class="space-y-4" x-data="{ showTeaching: false }">
                                {{-- Enseñanza with structure toggle --}}
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500">Enseñanza / Actividad Globalizada</label>
                                        @if($detailActivity->hasTeachingStructure())
                                            <button @click="showTeaching = !showTeaching"
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10 transition-all duration-200"
                                                x-text="showTeaching ? 'Ver completo' : 'Ver estructura'">
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Teaching full text (default visible) --}}
                                    <p class="text-sm text-gray-200 leading-relaxed" x-show="!showTeaching">{{ $detailActivity->teaching }}</p>

                                    {{-- Teaching structured view (toggle) --}}
                                    @if($detailActivity->hasTeachingStructure())
                                        @php $sections = $detailActivity->getTeachingSections(); @endphp
                                        <div x-show="showTeaching" x-cloak x-transition:enter.duration.200ms class="space-y-3">
                                            <div class="p-3 bg-cyan-500/5 border border-cyan-500/10 rounded-xl">
                                                <div class="text-[10px] font-bold uppercase tracking-widest text-cyan-400 mb-1">INICIO</div>
                                                <p class="text-sm text-gray-200">{{ $sections['INICIO'] ?? '' }}</p>
                                            </div>
                                            <div class="p-3 bg-emerald-500/5 border border-emerald-500/10 rounded-xl">
                                                <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-400 mb-1">DESARROLLO</div>
                                                <p class="text-sm text-gray-200">{{ $sections['DESARROLLO'] ?? '' }}</p>
                                            </div>
                                            <div class="p-3 bg-amber-500/5 border border-amber-500/10 rounded-xl">
                                                <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400 mb-1">CIERRE</div>
                                                <p class="text-sm text-gray-200">{{ $sections['CIERRE'] ?? '' }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Aprendizaje (full) --}}
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Aprendizaje</label>
                                    <p class="text-sm text-gray-200 leading-relaxed">{{ $detailActivity->learning }}</p>
                                </div>

                                {{-- Observaciones / ODS --}}
                                @if($detailActivity->observations)
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">ODS / Sistematización</label>
                                    <p class="text-sm text-gray-200 leading-relaxed">{{ $detailActivity->observations }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Comentario J.Área --}}
                        @if($detailActivity->comments)
                        <div class="p-4 bg-cyan-500/5 border border-cyan-500/10 rounded-xl">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider">Comentario [J.Área]</span>
                            </div>
                            <p class="text-sm text-gray-300 leading-relaxed">{{ $detailActivity->comments }}</p>
                        </div>
                        @endif

                        {{-- Achievements --}}
                        <div class="border-t border-white/5 pt-4">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Indicadores / Aprendizajes Esperados</span>
                            </div>
                            @php $detailAchievements = $detailActivity->achievements; @endphp
                            @if($detailAchievements->count() > 0)
                                <div class="space-y-1.5">
                                    @foreach($detailAchievements as $ach)
                                        <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-gray-800/30 border border-white/5">
                                            <span class="text-sm text-gray-300">
                                                <span class="text-gray-500 mr-1.5">—</span>
                                                {{ $ach->name }}
                                            </span>
                                            @if($ach->status_quantitative_weighting)
                                                <span class="text-xs font-bold font-mono text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md border border-emerald-500/20">
                                                    {{ $ach->weighting }} pts
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                    {{-- Total weight --}}
                                    @php $total = $detailAchievements->sum(fn($a) => (int)($a->weighting ?? 0)); @endphp
                                    @if($total > 0)
                                        <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-gray-800/50 border border-white/10">
                                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Ponderaciones</span>
                                            <span class="text-sm font-bold text-emerald-400 font-mono">{{ $total }}</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-gray-600 italic py-2">No hay indicadores registrados para esta actividad.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-3 border-t border-white/5 bg-gray-800/30 flex justify-end">
                        <button wire:click="closeDetailModal"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- EDIT MODAL --}}
    @if($modeEdit && $activity_id)
        <div class="fixed inset-0 z-[9998] overflow-y-auto" wire:key="edit-modal-{{ $activity_id }}">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="close"></div>

            {{-- Modal panel --}}
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative w-full max-w-6xl bg-gray-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-white/5 bg-gray-800/50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Editar Actividad</h3>
                                <p class="text-xs text-gray-500">
                                    {{ $pevaluacion->pensum?->asignatura?->name }} —
                                    {{ $pevaluacion->pensum?->grado?->name }} {{ $pevaluacion->seccion?->name }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="close"
                            class="p-1.5 text-gray-500 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Body: form fields --}}
                    <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                        @include('livewire.profesor.activity.form.fields')
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-3 border-t border-white/5 bg-gray-800/30 flex items-center justify-between">
                        <button wire:click="close"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancelar
                        </button>
                        <button wire:click="save"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-xs font-bold bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 hover:text-emerald-300 border border-emerald-500/20 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
