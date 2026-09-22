<div class="fade-in space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-1">Diagnóstico · Revisión de Preguntas</h1>
            <p class="text-amber-600 dark:text-amber-400 font-medium text-sm">
                Revisa y da seguimiento a las preguntas de diagnóstico de las áreas de conocimiento que tienes asignadas.
            </p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
            Seguimiento y revisión
        </span>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2"><div class="text-[10px] font-bold uppercase tracking-widest text-amber-400/60">Total (tu scope)</div><div class="text-3xl font-black text-white">{{ $metrics['total'] }}</div></div>
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2"><div class="text-[10px] font-bold uppercase tracking-widest text-emerald-400/60">Activas</div><div class="text-3xl font-black text-emerald-400">{{ $metrics['activas'] }}</div></div>
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2"><div class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Inactivas</div><div class="text-3xl font-black text-gray-400">{{ $metrics['inactivas'] }}</div></div>
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2"><div class="text-[10px] font-bold uppercase tracking-widest text-amber-400/60">Múltiple opción</div><div class="text-3xl font-black text-white">{{ $metrics['multiples'] }}</div></div>
    </div>

    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-2 sm:p-5 rounded-lg space-y-3">
        {{-- Diagnóstico (instrumento) — encima de la grilla de filtros --}}
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Diagnóstico</label>
            <select wire:model.live="filterDiagMain" class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 outline-none"><option value="">Todos</option>@foreach($diagMains as $dm)<option value="{{ $dm->id }}">{{ $dm->name }}</option>@endforeach</select>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div><label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Área</label>
                <select wire:model.live="filterAreaId" class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 outline-none"><option value="">Todas mis áreas ({{ $areas->count() }})</option>@foreach($areas as $a)<option value="{{ $a->id }}">{{ $a->name }} [{{ $a->pestudio->code ?? '?' }}]</option>@endforeach</select></div>
            <div><label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Pensum</label>
                <select wire:model.live="filterPensumId" @if($filterAreaId === '') disabled @endif class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 outline-none disabled:opacity-40 disabled:cursor-not-allowed"><option value="">Todos los pensums</option>@foreach($pensumsOptions as $p)<option value="{{ $p->id }}">{{ $p->asignatura?->name ?? '?' }} — {{ $p->grado?->name ?? '?' }}</option>@endforeach</select></div>
            <div><label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Pregunta o tipo..." class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 outline-none placeholder:text-gray-600"></div>
            <div><label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Tipo</label>
                <select wire:model.live="filterTipo" class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 outline-none"><option value="">Todos</option>@foreach($tipos as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach</select></div>
            <div><label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Activa</label>
                <select wire:model.live="filterActive" class="w-full min-h-[44px] bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 outline-none"><option value="">Todas</option><option value="1">Activas</option><option value="0">Inactivas</option></select></div>
        </div>
    </div>

    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-white/5">
                    <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Pregunta</th>
                    <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Pensum / Área</th>
                    <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Competencia</th>
                    <th class="text-center px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</th>
                    <th class="text-right px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Revisión</th>
                </tr></thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($questions as $q)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-white font-medium line-clamp-2 leading-snug" title="{{ $q->pregunta }}">{{ \Illuminate\Support\Str::limit($q->pregunta, 130) }}</p>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $q->tipo_pregunta === 'multiple' ? 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20' }}">{{ $q->tipo_pregunta }}</span>
                                    <span class="text-[10px] text-gray-500">orden {{ $q->orden ?? '—' }} · {{ $q->difficulty ?? '—' }} · peso {{ $q->weighing ?? '—' }}</span>
                                    <span class="text-[10px] text-gray-500">· {{ $q->options->count() }} opc.</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-white font-medium">{{ $q->pensum?->asignatura?->name ?? '—' }}</span>
                                <span class="block text-[10px] text-gray-500">{{ $q->pensum?->grado?->name ?? $q->pensum_id }} · {{ $q->pensum?->pestudio?->code ?? '?' }}</span>
                                @php $campo = $q->pensum_id ? \App\Models\app\Academy\CampoConocimiento::where('pensum_id', $q->pensum_id)->with('area_conocimiento')->first() : null; @endphp
                                @if($campo?->area_conocimiento)<span class="inline-flex mt-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">{{ $campo->area_conocimiento->name }}</span>@endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400">
                                {{ $q->competency?->name ?? '—' }}
                                <span class="block text-[10px] text-gray-500">{{ $q->indicator?->name ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="toggleActivo({{ $q->id }})" class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold border transition-all {{ $q->activo ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-gray-500/10 text-gray-400 border-white/10' }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $q->activo ? 'bg-emerald-500' : 'bg-gray-500' }}"></span>{{ $q->activo ? 'Activa' : 'Inactiva' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <button wire:click="openDetail({{ $q->id }})" title="Ver detalle" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver
                                    </button>
                                    <button wire:click="openQuestionModal({{ $q->id }})" title="Editar pregunta" class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] w-7 h-7 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-16 text-center"><p class="text-gray-500 font-medium">No hay preguntas en tu scope</p><p class="text-gray-600 text-sm mt-1">Verifica <code class="text-gray-400">AreaConocimiento.leader_id</code> y <code class="text-gray-400">campo_conocimientos.pensum_id</code> (<code class="text-amber-400">php8.2 artisan campo:poblar-pensum --force</code>).</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($questions->hasPages())
            <x-pagination-wrapper :paginator="$questions" />
        @endif
    </div>

    @if($showDetail && $selected)
        @php
            $optCorrect = fn ($o) => ((int) ($o->valor ?? 0)) === 1;
            $tipoLabel = match ($selected->tipo_pregunta) {
                'multiple' => 'Múltiple opción',
                'open' => 'Abierta',
                'scale' => 'Escala',
                default => $selected->tipo_pregunta,
            };
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeDetail"></div>
            <div class="relative bg-gray-900 border border-white/10 rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden max-h-[88vh] flex flex-col">
                <div class="px-5 py-4 border-b border-white/5 flex items-start justify-between gap-3 shrink-0">
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-white truncate">Pregunta #{{ $selected->id }} · {{ $selected->pensum?->asignatura?->name ?? '—' }}</h3>
                        <p class="text-[11px] text-gray-500 mt-0.5">
                            {{ $selected->pensum?->pestudio?->code }} · {{ $selected->pensum?->grado?->name }} · orden {{ $selected->orden ?? '—' }}
                        </p>
                    </div>
                    <button wire:click="closeDetail" class="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto">
                    {{-- Pregunta --}}
                    <div class="rounded-xl border border-white/5 bg-white/[0.02] p-4">
                        <p class="text-sm text-white leading-relaxed">{{ $selected->pregunta }}</p>
                        <div class="flex flex-wrap items-center gap-1.5 mt-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-cyan-500/10 text-cyan-400 border-cyan-500/20">{{ $tipoLabel }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $selected->activo ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-gray-500/10 text-gray-400 border-white/10' }}">{{ $selected->activo ? 'Activa' : 'Inactiva' }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-white/5 text-gray-400 border-white/10">Dificultad: {{ $selected->difficulty ?? '—' }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-white/5 text-gray-400 border-white/10">Peso: {{ $selected->weighing ?? '—' }}</span>
                            @if($selected->diagMain)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-amber-500/10 text-amber-400 border-amber-500/20">{{ $selected->diagMain->name }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Clasificación curricular --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-xl border border-white/5 bg-white/[0.02] p-3">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Área de formación</p>
                            <p class="text-xs text-white font-medium">{{ $selected->pensum?->asignatura?->name ?? '—' }}</p>
                            <p class="text-[11px] text-gray-500">{{ $selected->pensum?->pestudio?->code }} · {{ $selected->pensum?->pestudio?->name }}</p>
                            <p class="text-[11px] text-gray-500">{{ $selected->pensum?->grado?->name }}</p>
                        </div>
                        <div class="rounded-xl border border-white/5 bg-white/[0.02] p-3">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Competencia</p>
                            <p class="text-xs text-white font-medium">{{ $selected->competency?->name ?? '—' }}</p>
                            @if($selected->competency?->description)<p class="text-[11px] text-gray-500 mt-0.5">{{ $selected->competency->description }}</p>@endif
                        </div>
                        <div class="rounded-xl border border-white/5 bg-white/[0.02] p-3 sm:col-span-2">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Indicador de logro</p>
                            @if($selected->indicator)
                                <p class="text-xs text-white font-medium">
                                    @if($selected->indicator->code)<span class="font-mono text-amber-400">{{ $selected->indicator->code }}</span> · @endif
                                    {{ $selected->indicator->description ?? '—' }}
                                </p>
                                @if($selected->indicator->expected_level)<p class="text-[11px] text-gray-500 mt-0.5">Nivel esperado: {{ $selected->indicator->expected_level }}</p>@endif
                            @else
                                <p class="text-xs text-gray-500 italic">Sin indicador asociado.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Opciones --}}
                    <div>
                        <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Opciones de respuesta ({{ $selected->options->count() }})</h4>
                        @forelse($selected->options->sortBy('orden') as $opt)
                            <div class="flex items-start gap-2 py-2 border-b border-white/5 last:border-0 {{ $optCorrect($opt) ? 'bg-emerald-500/[0.04] rounded-lg px-2' : '' }}">
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5 {{ $optCorrect($opt) ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-white/5 text-gray-500 border-white/10' }}">{{ chr(65 + $loop->index) }}</span>
                                <span class="text-xs text-gray-200 flex-1">{{ $opt->opcion }}</span>
                                @if($optCorrect($opt))<span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">Correcta</span>@endif
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 italic">
                                @if($selected->tipo_pregunta === 'open')
                                    Pregunta abierta: respuesta libre.
                                @elseif($selected->tipo_pregunta === 'scale')
                                    Pregunta de escala: valoración numérica.
                                @else
                                    Sin opciones cargadas.
                                @endif
                            </p>
                        @endforelse
                    </div>
                </div>

                <div class="px-5 py-3 bg-white/[0.02] border-t border-white/5 flex items-center justify-between gap-2 shrink-0">
                    <button wire:click="toggleActivo({{ $selected->id }})" class="px-3 py-1.5 rounded-lg text-xs font-bold border transition-all {{ $selected->activo ? 'bg-amber-500/10 text-amber-400 border-amber-500/20 hover:bg-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 hover:bg-emerald-500/20' }}">{{ $selected->activo ? 'Desactivar' : 'Activar' }}</button>
                    <button wire:click="closeDetail" class="px-4 py-2 rounded-lg text-sm font-medium bg-white/5 hover:bg-white/10 text-gray-300 border border-white/10">Cerrar</button>
                </div>
            </div>
        </div>
    @endif

    @include('livewire.leadership.partials.question-wizard')
</div>
