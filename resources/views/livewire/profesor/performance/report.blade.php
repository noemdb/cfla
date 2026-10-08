{{-- Reporte de desempeño del profesor: métricas por rango de fechas (solo datos propios). --}}
<div id="perf-report" class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6 print:max-w-none print:px-0 print:py-0 print:space-y-4">
    <style>
        @media print {
            #perf-report * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            #perf-report details > ul { max-height: none !important; overflow: visible !important; }
            #perf-report details > summary { display: none !important; }
        }
    </style>
    <script>
        if (!window.__perfPrintHook) {
            window.__perfPrintHook = true;
            window.addEventListener('beforeprint', () => {
                document.querySelectorAll('#perf-report details').forEach((d) => {
                    d.dataset.wasOpen = d.open ? '1' : '';
                    d.open = true;
                });
            });
            window.addEventListener('afterprint', () => {
                document.querySelectorAll('#perf-report details').forEach((d) => {
                    if (!d.dataset.wasOpen) d.removeAttribute('open');
                    delete d.dataset.wasOpen;
                });
            });
        }
    </script>
    <x-loading-simple message="Actualizando métricas" />
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <p class="text-cyan-400 font-medium text-sm">Seguimiento docente</p>
            <h1 class="text-2xl font-bold text-white">Mi desempeño</h1>
            <p class="text-sm text-gray-400">
                {{ $profesor?->full_name ?? 'Sin perfil de profesor' }} ·
                Rango: {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
            </p>
        </div>
        <form class="flex flex-wrap items-end gap-2 print:hidden" onsubmit="return false;">
            <div>
                <label for="desde" class="block text-xs text-gray-400 mb-1">Fecha inicial</label>
                <input id="desde" type="date" wire:model.live="desde" max="{{ $hasta }}"
                    class="bg-gray-800 border border-white/10 rounded-lg px-3 py-1.5 text-sm text-gray-200" />
            </div>
            <div>
                <label for="hasta" class="block text-xs text-gray-400 mb-1">Fecha final</label>
                <input id="hasta" type="date" wire:model.live="hasta" min="{{ $desde }}"
                    class="bg-gray-800 border border-white/10 rounded-lg px-3 py-1.5 text-sm text-gray-200" />
            </div>
            <div class="inline-flex items-stretch rounded-lg border border-white/10 overflow-hidden divide-x divide-white/10" role="group" aria-label="Rango rápido">
                <button type="button" wire:click="setPreset('7d')" class="px-2.5 py-1.5 text-xs bg-white/5 text-gray-300 hover:text-cyan-300 hover:bg-white/10 transition-colors">7d</button>
                <button type="button" wire:click="setPreset('30d')" class="px-2.5 py-1.5 text-xs bg-white/5 text-gray-300 hover:text-cyan-300 hover:bg-white/10 transition-colors">30d</button>
                <button type="button" wire:click="setPreset('3m')" class="px-2.5 py-1.5 text-xs bg-white/5 text-gray-300 hover:text-cyan-300 hover:bg-white/10 transition-colors">3m</button>
                <button type="button" wire:click="setPreset('year')" class="px-2.5 py-1.5 text-xs bg-white/5 text-gray-300 hover:text-cyan-300 hover:bg-white/10 transition-colors">Año</button>
                <select wire:model.live="lapsoId" title="Rango del lapso (finicial → ffinal)" aria-label="Lapso"
                    class="bg-gray-800 border-0 px-2.5 py-1.5 text-xs text-gray-300 max-w-44 focus:outline-none cursor-pointer">
                    <option value="">Lapso…</option>
                    @foreach ($lapsos as $lapso)
                        <option value="{{ $lapso->id }}" title="{{ $lapso->finicial?->toDateString() }} → {{ $lapso->ffinal?->toDateString() }}">{{ $lapso->name }}</option>
                    @endforeach
                </select>
                <button type="button" x-data="{ estado: 'idle' }"
                    @click="navigator.clipboard.writeText(document.getElementById('perf-payload').textContent.trim()).then(() => { estado = 'copiado'; setTimeout(() => estado = 'idle', 2000); }).catch(() => { estado = 'error'; setTimeout(() => estado = 'idle', 2000); })"
                    title="Copiar todas las cifras en JSON para generar el informe con IA"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs bg-white/5 text-gray-300 hover:text-cyan-300 hover:bg-white/10 transition-colors">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span x-text="estado === 'copiado' ? '¡Copiado!' : (estado === 'error' ? 'Error' : 'JSON IA')"></span>
                </button>
            </div>
            <script type="application/json" id="perf-payload">@json($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
        </form>
    </div>

    @if (!$profesor)
        <div class="bg-amber-500/10 border border-amber-500/30 text-amber-300 rounded-lg px-4 py-3 text-sm">
            No tenés un perfil de docente asociado. Pedí a planificación que vincule tu usuario.
        </div>
    @else
        {{-- Bento de indicadores: una sola grilla de 4 columnas --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-gray-800 border border-white/10 rounded-xl p-4">
                <p class="text-xs text-gray-400">Carga académica</p>
                <p class="text-2xl font-bold text-white">{{ $metrics['carga'] }}</p>
                <p class="text-xs text-gray-500">cargas asignadas</p>
            </div>
            <div class="bg-gray-800 border border-white/10 rounded-xl p-4 col-span-2">
                <p class="text-xs text-gray-400">Actividades planificadas</p>
                <p class="text-2xl font-bold text-white">{{ $metrics['activities_total'] }}</p>
                <p class="text-xs text-gray-500">{{ $metrics['activities_aprobadas'] }} aprobadas · {{ $metrics['activities_revision'] }} en revisión</p>
                @php($tasaAprob = $metrics['activities_total'] > 0 ? (int) round($metrics['activities_aprobadas'] / $metrics['activities_total'] * 100) : 0)
                <div class="mt-2">
                    <div class="flex justify-between text-[11px]"><span class="text-gray-500">Índice de aprobación</span><span class="text-white font-semibold">{{ $tasaAprob }}%</span></div>
                    <div class="h-1.5 mt-1 rounded-full bg-white/10 overflow-hidden" role="progressbar" aria-valuenow="{{ $tasaAprob }}" aria-valuemin="0" aria-valuemax="100" title="{{ $metrics['activities_aprobadas'] }} de {{ $metrics['activities_total'] }} aprobadas">
                        <div class="h-full rounded-full bg-emerald-500/70 transition-all" style="width: {{ $tasaAprob }}%"></div>
                    </div>
                </div>
                <details class="mt-2 text-sm">
                    <summary class="cursor-pointer text-xs text-cyan-300 hover:text-cyan-200 select-none">¿Qué cuenta? ({{ count($metrics['actividades_detalle'] ?? []) }})</summary>
                    <p class="text-xs text-gray-500 mt-1">Tus actividades con fecha <code>planificada</code> (<code>finicial</code>) en el rango. Aprobada = <code>status 1</code>; en revisión = <code>0 o vacío</code>.</p>
                    @if (!empty($metrics['actividades_detalle']))
                        <ul class="mt-1.5 space-y-1.5 max-h-56 overflow-y-auto pr-1 print:max-h-none print:overflow-visible">
                            @foreach (array_slice($metrics['actividades_detalle'], 0, 15) as $det)
                                <li class="bg-white/5 rounded-lg px-2.5 py-1.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-gray-200 text-xs font-medium truncate">{{ $det['topic'] }}</span>
                                        <span class="shrink-0 text-[11px] px-1.5 py-0.5 rounded {{ $det['estado'] === 'Aprobada' ? 'bg-emerald-500/15 text-emerald-300' : 'bg-amber-500/15 text-amber-300' }}">{{ $det['estado'] }}</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-0.5">#{{ $det['id'] }} · {{ $det['finicial'] }} · {{ $det['asignatura'] }} · {{ $det['grado'] ?? '—' }} · Sec. {{ $det['seccion'] }}</p>
                                </li>
                            @endforeach
                        </ul>
                        @if (count($metrics['actividades_detalle']) > 15)
                            <p class="text-[11px] text-gray-500 mt-1">Mostrando las 15 más relevantes de {{ count($metrics['actividades_detalle']) }}.</p>
                        @endif
                    @endif
                </details>
            </div>
            <div class="bg-gray-800 border border-white/10 rounded-xl p-4">
                <p class="text-xs text-gray-400">Lecciones con contenido</p>
                <p class="text-2xl font-bold text-white">{{ $metrics['lms_con_contenido'] }}</p>
                <p class="text-xs text-gray-500">{{ $metrics['lms_publicadas'] }} publicadas · {{ $metrics['lms_programadas'] }} programadas</p>
            </div>
            <div class="bg-gray-800 border border-white/10 rounded-xl p-4">
                <p class="text-xs text-gray-400">Mi horario vigente</p>
                <p class="text-2xl font-bold text-white">{{ $metrics['slots_vigentes'] }}</p>
                <p class="text-xs text-gray-500">slots en calendarios activos</p>
            </div>

            <div class="bg-gray-800 border border-white/10 rounded-xl p-4">
                <p class="text-xs text-gray-400">Actividades registradas</p>
                <p class="text-2xl font-bold text-white">{{ $metrics['activities_creadas'] }}</p>
                <p class="text-xs text-gray-500">creadas en el rango {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}</p>
                <details class="mt-2 text-sm">
                    <summary class="cursor-pointer text-xs text-cyan-300 hover:text-cyan-200 select-none">Ver detalle ({{ count($metrics['actividades_creadas_detalle'] ?? []) }})</summary>
                    <p class="text-xs text-gray-500 mt-1">Por fecha de <code>creación</code>, no planificada: difiere del total de arriba si cargaste actividades de otras fechas.</p>
                    @if (empty($metrics['actividades_creadas_detalle']))
                        <p class="text-xs text-gray-500 mt-1">Sin actividades creadas en el rango elegido.</p>
                    @else
                        <ul class="mt-1.5 space-y-1.5 max-h-56 overflow-y-auto pr-1 print:max-h-none print:overflow-visible">
                            @foreach (array_slice($metrics['actividades_creadas_detalle'], 0, 15) as $det)
                                <li class="bg-white/5 rounded-lg px-2.5 py-1.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-gray-200 text-xs font-medium truncate">{{ $det['topic'] }}</span>
                                        <span class="shrink-0 text-[11px] px-1.5 py-0.5 rounded {{ $det['estado'] === 'Aprobada' ? 'bg-emerald-500/15 text-emerald-300' : 'bg-amber-500/15 text-amber-300' }}">{{ $det['estado'] }}</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-0.5">#{{ $det['id'] }} · creada {{ $det['creada'] }} · planificada {{ $det['finicial'] }} · {{ $det['asignatura'] }} · {{ $det['grado'] ?? '—' }} · Sec. {{ $det['seccion'] }}</p>
                                </li>
                            @endforeach
                        </ul>
                        @if (count($metrics['actividades_creadas_detalle']) > 15)
                            <p class="text-[11px] text-gray-500 mt-1">Mostrando las 15 más relevantes de {{ count($metrics['actividades_creadas_detalle']) }}.</p>
                        @endif
                    @endif
                </details>
            </div>
            <div class="bg-gray-800 border border-white/10 rounded-xl p-4 col-span-2">
                <p class="text-xs text-gray-400">Calidad de enseñanza</p>
                <p class="text-2xl font-bold text-white">{{ $metrics['activities_calidad'] }}<span class="text-sm font-normal text-gray-500"> / {{ $metrics['activities_total'] }}</span></p>
                <p class="text-xs text-gray-500">cumplen el criterio en el rango</p>
                <details class="mt-2 text-sm">
                    <summary class="cursor-pointer text-xs text-cyan-300 hover:text-cyan-200 select-none">Ver detalle ({{ count($metrics['calidad_detalle'] ?? []) }})</summary>
                    <p class="text-xs text-gray-500 mt-1">Criterio: <code>teachingWordsMayorCount(3) ≥ 10</code> — palabras de más de 3 letras en el campo <code>teaching</code> (<code>Activity.php:177</code>).</p>
                    @if (empty($metrics['calidad_detalle']))
                        <p class="text-xs text-gray-500 mt-1">Sin actividades planificadas en el rango elegido.</p>
                    @else
                        <ul class="mt-1.5 space-y-1.5 max-h-56 overflow-y-auto pr-1 print:max-h-none print:overflow-visible">
                            @foreach (array_slice($metrics['calidad_detalle'], 0, 15) as $det)
                                <li class="bg-white/5 rounded-lg px-2.5 py-1.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-gray-200 text-xs font-medium truncate">{{ $det['topic'] }}</span>
                                        <span class="shrink-0 text-[11px] px-1.5 py-0.5 rounded {{ $det['cumple'] ? 'bg-emerald-500/15 text-emerald-300' : 'bg-gray-500/15 text-gray-400' }}">{{ $det['palabras'] }} palabras</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-0.5">#{{ $det['id'] }} · {{ $det['cumple'] ? 'Cumple el criterio' : 'No cumple (menos de 10)' }}</p>
                                </li>
                            @endforeach
                        </ul>
                        @if (count($metrics['calidad_detalle']) > 15)
                            <p class="text-[11px] text-gray-500 mt-1">Mostrando las 15 más relevantes de {{ count($metrics['calidad_detalle']) }}.</p>
                        @endif
                    @endif
                </details>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            {{-- Actividades --}}
            <div class="bg-gray-800 border border-white/10 rounded-xl p-4 space-y-1.5">
                <h2 class="text-sm font-semibold text-emerald-300">Actividades</h2>
                <p class="text-xs text-gray-500">Cuenta tus actividades con fecha planificada (<code>finicial</code>) dentro del rango {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}. <code>status = 1</code> es aprobada; <code>0/vacío</code> es en revisión.</p>
                <dl class="text-sm space-y-1">
                    <div class="flex justify-between"><dt class="text-gray-400">Total</dt><dd class="text-white font-semibold">{{ $metrics['activities_total'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-400">Aprobadas</dt><dd class="text-white font-semibold">{{ $metrics['activities_aprobadas'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-400">En revisión</dt><dd class="text-white font-semibold">{{ $metrics['activities_revision'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-400">Con evaluativo</dt><dd class="text-white font-semibold">{{ $metrics['activities_con_eval'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-400">Calidad enseñanza (≥10 palabras)</dt><dd class="text-white font-semibold">{{ $metrics['activities_calidad'] }}</dd></div>
                </dl>
                <details class="mt-1 text-sm">
                    <summary class="cursor-pointer text-xs text-cyan-300 hover:text-cyan-200 select-none">Ver detalle ({{ count($metrics['actividades_detalle'] ?? []) }})</summary>
                    @if (empty($metrics['actividades_detalle']))
                        <p class="text-xs text-gray-500 mt-1.5">Sin actividades con <code>finicial</code> en el rango elegido.</p>
                    @else
                        <ul class="mt-1.5 space-y-1.5 max-h-56 overflow-y-auto pr-1 print:max-h-none print:overflow-visible">
                            @foreach (array_slice($metrics['actividades_detalle'], 0, 15) as $det)
                                <li class="bg-white/5 rounded-lg px-2.5 py-1.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-gray-200 text-xs font-medium truncate">{{ $det['topic'] }}</span>
                                        <span class="shrink-0 text-[11px] px-1.5 py-0.5 rounded {{ $det['estado'] === 'Aprobada' ? 'bg-emerald-500/15 text-emerald-300' : 'bg-amber-500/15 text-amber-300' }}">{{ $det['estado'] }}</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-0.5">#{{ $det['id'] }} · {{ $det['finicial'] }} · {{ $det['asignatura'] }} · {{ $det['grado'] ?? '—' }} · Sec. {{ $det['seccion'] }}</p>
                                </li>
                            @endforeach
                        </ul>
                        @if (count($metrics['actividades_detalle']) > 15)
                            <p class="text-[11px] text-gray-500 mt-1">Mostrando las 15 más relevantes de {{ count($metrics['actividades_detalle']) }}.</p>
                        @endif
                    @endif
                </details>
            </div>
            {{-- LMS --}}
            <div class="bg-gray-800 border border-white/10 rounded-xl p-4 space-y-1.5">
                <h2 class="text-sm font-semibold text-emerald-300">Lecciones LMS</h2>
                <dl class="text-sm space-y-1">
                    <div class="flex justify-between"><dt class="text-gray-400">Con contenido</dt><dd class="text-white font-semibold">{{ $metrics['lms_con_contenido'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-400">Publicadas</dt><dd class="text-white font-semibold">{{ $metrics['lms_publicadas'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-400">Programadas</dt><dd class="text-white font-semibold">{{ $metrics['lms_programadas'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-400">Borrador</dt><dd class="text-white font-semibold">{{ $metrics['lms_borrador'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-400">Secciones / recursos / enlaces</dt><dd class="text-white font-semibold">{{ $metrics['lms_secciones'] }} / {{ $metrics['lms_recursos'] }} / {{ $metrics['lms_enlaces'] }}</dd></div>
                </dl>
            </div>
            {{-- Horario / suplencias --}}
            <div class="bg-gray-800 border border-white/10 rounded-xl p-4 space-y-1.5">
                <h2 class="text-sm font-semibold text-emerald-300">Horario y suplencias</h2>
                <p class="text-xs text-gray-500">Slots en calendarios activos donde sos el docente de la carga (<code>pevaluacion.profesor_id</code>), solo secciones/grados activos. Misma regla que la grilla de <a href="{{ route('app.profesors.timetable') }}" class="text-cyan-300 hover:text-cyan-200">Mi horario</a> (suma todos los lapsos).</p>
                <dl class="text-sm space-y-1">
                    <div class="flex justify-between"><dt class="text-gray-400">Slots vigentes</dt><dd class="text-white font-semibold">{{ $metrics['slots_vigentes'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-400">Suplencias asignadas</dt><dd class="text-white font-semibold">{{ $metrics['suplencias_total'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-400">Pendientes / confirmadas / rechazadas</dt><dd class="text-white font-semibold">{{ $metrics['suplencias_pending'] }} / {{ $metrics['suplencias_confirmed'] }} / {{ $metrics['suplencias_declined'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-400">Ausencias (solapan rango)</dt><dd class="text-white font-semibold">{{ $metrics['ausencias'] }}</dd></div>
                </dl>
                <details class="mt-1 text-sm">
                    <summary class="cursor-pointer text-xs text-cyan-300 hover:text-cyan-200 select-none">Ver detalle ({{ count($metrics['slots_detalle'] ?? []) }})</summary>
                    @if (empty($metrics['slots_detalle']))
                        <p class="text-xs text-gray-500 mt-1.5">Sin slots vigentes como docente de la carga.</p>
                    @else
                        <ul class="mt-1.5 space-y-1.5 max-h-56 overflow-y-auto pr-1 print:max-h-none print:overflow-visible">
                            @foreach ($metrics['slots_detalle'] as $slot)
                                <li class="bg-white/5 rounded-lg px-2.5 py-1.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-gray-200 text-xs font-medium truncate">{{ $slot['asignatura'] }} · {{ $slot['grado'] }} · Sec. {{ $slot['seccion'] }}</span>
                                        <span class="shrink-0 text-[11px] text-gray-500">#{{ $slot['slot'] }}</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-0.5">{{ $slot['calendario'] }} · día {{ $slot['dia'] }} · {{ $slot['bloque'] }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </details>
                <a href="{{ route('app.profesors.timetable.substitutes') }}" class="inline-block text-xs text-cyan-300 hover:text-cyan-200 mt-1">Ir a mis suplencias →</a>
            </div>
            {{-- Bitácora --}}
            <div class="bg-gray-800 border border-white/10 rounded-xl p-4 space-y-1.5">
                <h2 class="text-sm font-semibold text-emerald-300">Mi actividad (bitácora)</h2>
                <dl class="text-sm space-y-1">
                    <div class="flex justify-between"><dt class="text-gray-400">Acciones en rango</dt><dd class="text-white font-semibold">{{ $metrics['binnacle_total'] }}</dd></div>
                    @php($catLabels = ['authentication' => 'Autenticación', 'user_action' => 'Acciones', 'system' => 'Sistema', 'security' => 'Seguridad', 'error' => 'Errores'])
                    @forelse ($metrics['binnacle_by_category'] as $cat => $total)
                        <div class="flex justify-between"><dt class="text-gray-400">{{ $catLabels[$cat] ?? $cat }}</dt><dd class="text-white font-semibold">{{ $total }}</dd></div>
                    @empty
                        <p class="text-xs text-gray-500">Sin actividad registrada en el rango.</p>
                    @endforelse
                </dl>
                <a href="{{ route('app.profesors.binnacle.mi-bitcora') }}" class="inline-block text-xs text-cyan-300 hover:text-cyan-200 mt-1">Ver mi bitácora →</a>
            </div>
            {{-- Notificaciones --}}
            <div class="bg-gray-800 border border-white/10 rounded-xl p-4 space-y-1.5 md:col-span-2">
                <h2 class="text-sm font-semibold text-emerald-300">Notificaciones</h2>
                <dl class="text-sm space-y-1">
                    <div class="flex justify-between"><dt class="text-gray-400">Recibidas en rango</dt><dd class="text-white font-semibold">{{ $metrics['notif_total'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-400">Sin leer</dt><dd class="text-white font-semibold">{{ $metrics['notif_no_leidas'] }}</dd></div>
                </dl>
                <a href="{{ route('app.notifications.index') }}" class="inline-block text-xs text-cyan-300 hover:text-cyan-200 mt-1">Abrir notificaciones →</a>
            </div>
        </div>

        {{-- Detalle por carga --}}
        <div class="bg-gray-800 border border-white/10 rounded-xl p-4">
            <h2 class="text-sm font-semibold text-emerald-300 mb-2">Detalle por área / sección (rango)</h2>
            @if (empty($metrics['por_pevaluacion']))
                <p class="text-sm text-gray-500">Sin actividades con fecha inicial en el rango seleccionado.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-gray-400 border-b border-white/10">
                                <th class="py-2 pr-3">Asignatura</th>
                                <th class="py-2 pr-3">Sección</th>
                                <th class="py-2 pr-3">Lapso</th>
                                <th class="py-2 pr-3 text-right">Actividades</th>
                                <th class="py-2 text-right">Publicadas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (array_slice($metrics['por_pevaluacion'], 0, 15) as $row)
                                <tr class="border-b border-white/5 text-gray-200">
                                    <td class="py-2 pr-3">{{ $row['asignatura'] }}</td>
                                    <td class="py-2 pr-3">{{ $row['seccion'] }}</td>
                                    <td class="py-2 pr-3">{{ $row['lapso'] }}</td>
                                    <td class="py-2 pr-3 text-right font-semibold">{{ $row['total'] }}</td>
                                    <td class="py-2 text-right">{{ $row['publicadas'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            @if (count($metrics['por_pevaluacion']) > 15)
                <p class="text-xs text-gray-500 mt-2">Mostrando las 15 más relevantes de {{ count($metrics['por_pevaluacion']) }} áreas/secciones.</p>
            @endif
            <p class="text-xs text-gray-500 mt-2">Base del rango: <code>activities.finicial</code> entre las fechas elegidas. Las suplencias sin aviso se incluyen siempre; ausencias y bitácora/notificaciones usan solape de fechas del rango.</p>
        </div>

        <footer class="border-t border-white/10 pt-3 pb-1 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-[11px] text-gray-500">
            <p>{{ $payload['estructura']['modulo'] ?? 'Mi desempeño' }} · {{ $profesor?->full_name ?? '' }}</p>
            <p>Rango {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}@isset($payload['generado_en']) · Generado {{ \Carbon\Carbon::parse($payload['generado_en'])->format('d/m/Y H:i') }}@endisset</p>
        </footer>
    @endif
</div>
