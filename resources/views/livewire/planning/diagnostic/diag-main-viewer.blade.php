<div class="mb-8 block w-full" wire:key="diag-main-viewer">
    {{-- Dropdown w-full + botón Nuevo justo a su derecha --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4">
        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Instrumento diagnóstico</label>
        <div class="flex items-center gap-2">
            <select wire:model.live="selectedId"
                class="flex-1 min-w-0 bg-gray-800 text-gray-200 text-sm rounded-lg border border-white/5 px-3 py-2.5 min-h-[44px] focus:border-emerald-500/30 focus:ring-1 focus:ring-emerald-500/20 outline-none">
                <option value="">— Seleccionar diagnóstico —</option>
                @foreach($diagMains as $diag)
                    <option value="{{ $diag->id }}">
                        {{ $diag->name }}{{ $diag->active ? ' · Activo' : ' · Inactivo' }}{{ $diag->pestudio ? ' — '.$diag->pestudio->code : '' }}{{ $diag->lapso ? ' · '.$diag->lapso->name : '' }}
                    </option>
                @endforeach
            </select>
            <button wire:click="triggerCreate" class="inline-flex items-center gap-1.5 px-3 py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-200 text-xs font-bold uppercase tracking-widest shrink-0 min-h-[44px]">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nuevo Diagnóstico
            </button>
        </div>
        <p class="text-[11px] text-gray-500 mt-1.5">
            @if($diagMains->isEmpty())
                No hay instrumentos registrados.
            @else
                {{ $diagMains->count() }} instrumento(s) registrado(s). Selecciona uno para ver su información.
            @endif
        </p>
    </div>

    {{-- Info asociada — solo cuando hay selección --}}
    @if($selected)
        <div class="mt-4 bg-gray-900/40 backdrop-blur-md border border-emerald-500/20 rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-white/5">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full {{ $selected->active ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-gray-600' }}"></span>
                    <span class="text-[10px] font-bold uppercase tracking-widest {{ $selected->active ? 'text-emerald-400' : 'text-gray-500' }}">{{ $selected->active ? 'Activo' : 'Inactivo' }}</span>
                </div>
                <h3 class="text-base font-bold text-white">{{ $selected->name }}</h3>
                @if($selected->description)
                    <p class="text-xs text-gray-400 mt-1 leading-relaxed">{{ $selected->description }}</p>
                @endif
            </div>

            <div class="px-5 py-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Lapso</p>
                        <p class="text-sm text-white font-medium">{{ $displayLapso?->name ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Plan de Estudio</p>
                        <p class="text-sm text-white font-medium">
                            @if($displayPestudio)
                                {{ $displayPestudio->code }} — {{ $displayPestudio->name }}
                            @elseif($selected)
                                <span class="text-gray-400">Múltiples planes</span>
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Referente</p>
                        <p class="text-sm text-white font-medium">{{ $displayReferent ? $displayReferent->code.' — '.$displayReferent->name : '—' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                        <p class="text-lg font-extrabold text-cyan-400">{{ $questionsCount ?? $selected->questions_count ?? 0 }}</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Preguntas</p>
                    </div>
                    <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                        <p class="text-lg font-extrabold text-sky-400">{{ $pensumsWithAnswersCount ?? 0 }}</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">A.Formación c/ resp.</p>
                    </div>
                    <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                        <p class="text-lg font-extrabold text-indigo-400">{{ $questionsWithAnswersCount ?? 0 }}</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Preg. c/ resp.</p>
                    </div>
                    <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                        <p class="text-lg font-extrabold text-amber-400">{{ $totalAnswersCount ?? 0 }}</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Respuestas</p>
                    </div>
                    <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                        <p class="text-lg font-extrabold text-purple-400">{{ $studentsEvaluated ?? 0 }}</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Estudiantes</p>
                    </div>
                    <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                        @if($completionRate !== null)
                            <p class="text-lg font-extrabold {{ $completionRate >= 80 ? 'text-emerald-400' : ($completionRate >= 50 ? 'text-amber-400' : 'text-red-400') }}">{{ $completionRate }}%</p>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">% Completitud</p>
                            <p class="text-[10px] text-gray-500">{{ $completedSessions ?? 0 }} / {{ $sessionsCount ?? 0 }}</p>
                        @else
                            <p class="text-lg font-extrabold text-gray-500">—</p>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">% Completitud</p>
                            <p class="text-[10px] text-gray-600">Sin datos</p>
                        @endif
                    </div>
                    <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                        @if($abandonRate !== null)
                            <p class="text-lg font-extrabold {{ $abandonRate <= 20 ? 'text-emerald-400' : ($abandonRate <= 40 ? 'text-amber-400' : 'text-red-400') }}">{{ $abandonRate }}%</p>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Tasa Abandono</p>
                            <p class="text-[10px] text-gray-500">{{ ($sessionsCount ?? 0) - ($completedSessions ?? 0) }} / {{ $sessionsCount ?? 0 }}</p>
                        @else
                            <p class="text-lg font-extrabold text-gray-500">—</p>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Tasa Abandono</p>
                            <p class="text-[10px] text-gray-600">Sin datos</p>
                        @endif
                    </div>
                    <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
                        @if($precision !== null)
                            <p class="text-lg font-extrabold {{ $precision >= 80 ? 'text-emerald-400' : ($precision >= 60 ? 'text-amber-400' : 'text-red-400') }}">{{ $precision }}%</p>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Precisión</p>
                            <p class="text-[10px] text-gray-500">{{ $precisionCorrect }} / {{ $precisionTotal }}</p>
                        @else
                            <p class="text-lg font-extrabold text-gray-500">—</p>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Precisión</p>
                            <p class="text-[10px] text-gray-600">Sin datos</p>
                        @endif
                    </div>
                </div>

                {{-- Enriquecimiento s2526: sesiones recientes + distribución por tipo/dificultad --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                    {{-- Sesiones recientes --}}
                    <div class="lg:col-span-2 bg-gray-800/30 border border-white/5 rounded-lg overflow-hidden">
                        <div class="px-3 py-2 border-b border-white/5 flex items-center justify-between">
                            <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Sesiones recientes</h4>
                            <span class="text-[10px] text-gray-500">{{ $recentSessions->count() }} última(s)</span>
                        </div>
                        @if($recentSessions->isEmpty())
                            <p class="text-xs text-gray-500 text-center py-6">Sin sesiones registradas.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs">
                                    <thead class="bg-white/[0.02] border-b border-white/5">
                                        <tr class="text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                            <th class="text-left px-3 py-1.5">Estudiante</th>
                                            <th class="text-left px-3 py-1.5">Área</th>
                                            <th class="text-left px-3 py-1.5">Progreso</th>
                                            <th class="text-left px-3 py-1.5">Iniciado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        @foreach($recentSessions as $sess)
                                            <tr class="hover:bg-white/[0.02]">
                                                <td class="px-3 py-1.5 text-white">{{ $sess->estudiant?->full_name ?? '—' }}</td>
                                                <td class="px-3 py-1.5 text-gray-400">{{ $sess->pensum?->full_name ?? '—' }}</td>
                                                <td class="px-3 py-1.5">
                                                    <div class="flex items-center gap-1.5">
                                                        <div class="w-12 h-1.5 bg-white/10 rounded-full overflow-hidden"><div class="h-full bg-cyan-500 rounded-full" style="width: {{ $sess->progreso ?? 0 }}%"></div></div>
                                                        <span class="text-[10px] text-gray-400">{{ $sess->progreso ?? 0 }}%</span>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-1.5 text-gray-500 text-[11px]">{{ $sess->iniciado_at ? \Carbon\Carbon::parse($sess->iniciado_at)->format('d/m/Y H:i') : '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- Distribución por tipo y dificultad --}}
                    <div class="space-y-3">
                        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-3">
                            <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Por tipo</h4>
                            @forelse($questionsByType as $t)
                                @php $max = $questionsByType->max('count') ?: 1; $pct = $max ? round(100 * $t->count / $max) : 0; @endphp
                                <div class="mb-2">
                                    <div class="flex justify-between text-[11px] text-gray-400 mb-1"><span class="font-medium text-white">{{ $t->type }}</span><span class="px-1.5 py-0.5 rounded-full bg-white/5 border border-white/5 text-[10px]">{{ $t->count }}</span></div>
                                    <div class="w-full h-1.5 bg-white/10 rounded-full overflow-hidden"><div class="h-full bg-cyan-500 rounded-full" style="width: {{ $pct }}%"></div></div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-500">Sin datos.</p>
                            @endforelse
                        </div>
                        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-3">
                            <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Por dificultad</h4>
                            @forelse($questionsByDifficulty as $d)
                                @php $maxD = $questionsByDifficulty->max('count') ?: 1; $pctD = $maxD ? round(100 * $d->count / $maxD) : 0; $col = match(strtolower($d->difficulty ?? '')) { 'easy' => 'bg-emerald-500', 'medium' => 'bg-amber-500', 'hard' => 'bg-red-500', default => 'bg-gray-500' }; @endphp
                                <div class="mb-2">
                                    <div class="flex justify-between text-[11px] text-gray-400 mb-1"><span class="font-medium text-white capitalize">{{ $d->difficulty ?? '—' }}</span><span class="px-1.5 py-0.5 rounded-full bg-white/5 border border-white/5 text-[10px]">{{ $d->count }}</span></div>
                                    <div class="w-full h-1.5 bg-white/10 rounded-full overflow-hidden"><div class="h-full {{ $col }} rounded-full" style="width: {{ $pctD }}%"></div></div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-500">Sin datos.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Resumen por área de formación — con filtros y paginación como s2526 --}}
                @php $hasProgress = $pensumProgress instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pensumProgress->total() > 0 : $pensumProgress->isNotEmpty(); @endphp
                @if($hasProgress)
                    <div class="bg-gray-800/30 border border-white/5 rounded-lg overflow-hidden">
                        <div class="px-3 py-2 border-b border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Resumen por área de formación</h4>
                            <span class="text-[10px] text-gray-500">{{ $pensumProgress instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pensumProgress->total() : $pensumProgress->count() }} área(s)</span>
                        </div>
                        <div class="px-3 py-2 border-b border-white/5 grid grid-cols-1 sm:grid-cols-4 gap-2 bg-white/[0.02]">
                            <input type="text" wire:model.live.debounce.300ms="progressSearch" placeholder="Buscar área..." class="bg-gray-900/50 border border-white/10 rounded-lg px-2.5 py-1.5 text-xs text-gray-200 placeholder:text-gray-600 focus:ring-1 focus:ring-cyan-500/30 outline-none" />
                            <select wire:model.live="progressPestudioId" class="bg-gray-900/50 border border-white/10 rounded-lg px-2.5 py-1.5 text-xs text-gray-200 focus:ring-1 focus:ring-cyan-500/30 outline-none">
                                <option value="">Pestudio: Todos</option>
                                @foreach($progressPestudios as $pe)
                                    <option value="{{ $pe->id }}">{{ $pe->code }} — {{ $pe->name }}</option>
                                @endforeach
                            </select>
                            <select wire:model.live="progressGradoId" @if(!$progressPestudioId) disabled @endif class="bg-gray-900/50 border border-white/10 rounded-lg px-2.5 py-1.5 text-xs text-gray-200 focus:ring-1 focus:ring-cyan-500/30 outline-none disabled:opacity-40 disabled:cursor-not-allowed">
                                <option value="">Grado: Todos</option>
                                @foreach($progressGrados as $g)
                                    <option value="{{ $g->id }}">{{ $g->code ?? $g->name }} — {{ $g->name }}</option>
                                @endforeach
                            </select>
                            <select wire:model.live="progressPensumId" @if(!$progressGradoId) disabled @endif class="bg-gray-900/50 border border-white/10 rounded-lg px-2.5 py-1.5 text-xs text-gray-200 focus:ring-1 focus:ring-cyan-500/30 outline-none disabled:opacity-40 disabled:cursor-not-allowed">
                                <option value="">Pensum: Todos</option>
                                @foreach($progressPensums as $p)
                                    <option value="{{ $p->id }}">{{ $p->grado?->name ?? '?' }} — {{ $p->asignatura?->name ?? '?' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead class="bg-white/[0.02] border-b border-white/5">
                                    <tr class="text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                        <th class="text-left px-3 py-1.5">Área</th>
                                        <th class="text-center px-3 py-1.5">Preg.</th>
                                        <th class="text-center px-3 py-1.5">Ses.</th>
                                        <th class="text-center px-3 py-1.5">Compl.</th>
                                        <th class="text-center px-3 py-1.5">% Finalización</th>
                                        <th class="text-center px-3 py-1.5">Precisión</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @foreach($pensumProgress as $pp)
                                        <tr class="hover:bg-white/[0.02]">
                                            <td class="px-3 py-1.5 text-white font-medium">{{ $pp->fullname }}</td>
                                            <td class="px-3 py-1.5 text-center"><span class="px-1.5 py-0.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px]">{{ $pp->total_questions }}</span></td>
                                            <td class="px-3 py-1.5 text-center"><span class="px-1.5 py-0.5 rounded-full bg-white/5 border border-white/5 text-gray-300 text-[10px]">{{ $pp->total_sessions }}</span></td>
                                            <td class="px-3 py-1.5 text-center"><span class="px-1.5 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px]">{{ $pp->completed_sessions }}</span></td>
                                            <td class="px-3 py-1.5">
                                                <div class="flex items-center gap-1.5 justify-center">
                                                    <div class="w-12 h-1.5 bg-white/10 rounded-full overflow-hidden"><div class="h-full rounded-full {{ $pp->completion_percentage >= 80 ? 'bg-emerald-500' : ($pp->completion_percentage >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $pp->completion_percentage }}%"></div></div>
                                                    <span class="text-[10px] text-gray-400">{{ number_format($pp->completion_percentage, 1) }}%</span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-1.5 text-center">
                                                @if($pp->precision !== null)
                                                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold border {{ $pp->precision >= 80 ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : ($pp->precision >= 60 ? 'bg-amber-500/10 border-amber-500/20 text-amber-400' : 'bg-red-500/10 border-red-500/20 text-red-400') }}">{{ $pp->precision }}%</span>
                                                @else
                                                    <span class="text-[10px] text-gray-600">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($pensumProgress instanceof \Illuminate\Pagination\LengthAwarePaginator && $pensumProgress->hasPages())
                            <x-pagination-wrapper :paginator="$pensumProgress" />
                        @elseif($pensumProgress instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="px-3 py-2 border-t border-white/5 flex items-center justify-between">
                                <span class="text-[11px] text-gray-500">Mostrando {{ $pensumProgress->firstItem() ?? 0 }} a {{ $pensumProgress->lastItem() ?? 0 }} de {{ $pensumProgress->total() }}</span>
                                <span class="text-[11px] text-gray-600">{{ $pensumProgress->total() }} área(s) en total</span>
                            </div>
                        @endif
                    </div>
                @endif

                <p class="text-[10px] text-gray-600">ID #{{ $selected->id }} @if($selected->created_at) · Creado {{ $selected->created_at->format('d/m/Y') }} @endif</p>
            </div>
        </div>
    @else
        <div class="mt-3 text-center py-4">
            <p class="text-xs text-gray-500">Selecciona un instrumento para ver su información asociada.</p>
        </div>
    @endif

    {{-- State loading — más visible pero sutil: barra superior + píldora con anillo --}}
    <div wire:loading.delay class="fixed top-0 inset-x-0 z-40 h-0.5 bg-gradient-to-r from-emerald-500 via-cyan-500 to-emerald-500 animate-pulse pointer-events-none"></div>
    <div wire:loading.delay class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-full bg-gray-900/90 backdrop-blur-xl pl-3 pr-5 py-3 text-xs font-bold tracking-widest uppercase text-white shadow-2xl shadow-emerald-500/10 border border-emerald-500/20 ring-1 ring-white/5">
        <span class="relative flex h-7 w-7 items-center justify-center rounded-full bg-emerald-500/15 border border-emerald-500/20">
            <svg class="h-4 w-4 animate-spin text-emerald-400" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="absolute -top-0.5 -right-0.5 h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
            <span class="absolute -top-0.5 -right-0.5 h-2 w-2 rounded-full bg-emerald-500"></span>
        </span>
        <span>Cargando</span>
        <span class="h-3 w-px bg-white/10"></span>
        <span class="text-[10px] font-normal normal-case tracking-normal text-white/60">Sincronizando…</span>
    </div>
</div>
