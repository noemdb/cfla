{{--
    Modal de calendario para el seguimiento de planificaciones.
--}}
@if ($showCalendarModal)
    <div x-data x-trap="showCalendarModal" wire:key="calendar-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="relative w-[90vw] h-[90vh] rounded-xl bg-gray-900 border border-white/10 shadow-2xl"
            @click.outside="window.Livewire.dispatch('closeCalendar')">
            
            {{-- Encabezado del modal --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                <div>
                    <h3 class="text-lg font-semibold text-gray-100">Calendario de Planificaciones - Seguimiento</h3>
                    <p class="text-sm text-gray-400 mt-0.5">
                        Visualización de todas las planificaciones por fecha (vista de seguimiento)
                    </p>
                </div>
                <button wire:click="closeCalendar" type="button"
                    class="p-2 rounded-lg text-gray-500 hover:text-gray-200 hover:bg-white/5 transition-colors"
                    aria-label="Cerrar calendario">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Cuerpo del calendario --}}
            <div class="px-6 py-4 h-[calc(90vh-5rem)] overflow-y-auto">
                {{-- Filtros rápidos --}}
                <div class="mb-6 p-4 rounded-lg border border-white/10 bg-gray-800/50">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Rango de fechas</label>
                            <div class="text-sm text-gray-300">
                                @php
                                    $calendarPlans = $this->getCalendarPlans();
                                    $fechas = collect($calendarPlans)->pluck('date')->unique()->sort();
                                    $primeraFecha = $fechas->first() ? \Carbon\Carbon::parse($fechas->first())->format('d/m/Y') : 'Sin fechas';
                                    $ultimaFecha = $fechas->last() ? \Carbon\Carbon::parse($fechas->last())->format('d/m/Y') : 'Sin fechas';
                                @endphp
                                {{ $primeraFecha }} - {{ $ultimaFecha }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Total de planificaciones</label>
                            <div class="text-sm text-gray-300">
                                @php
                                    $planesUnicos = collect($calendarPlans)->pluck('plan.id')->unique()->count();
                                @endphp
                                {{ $planesUnicos }} planes
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Días cubiertos</label>
                            <div class="text-sm text-gray-300">
                                {{ $fechas->count() }} días
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Estado predominante</label>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 rounded-md border px-1.5 py-0.5 text-xs
                                    {{ collect($calendarPlans)->where('estado.clave', 'completo')->count() > collect($calendarPlans)->where('estado.clave', '!=', 'completo')->count() 
                                        ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-300'
                                        : 'border-amber-500/20 bg-amber-500/10 text-amber-300' }}">
                                    @if(collect($calendarPlans)->where('estado.clave', 'completo')->count() > 0)
                                        {{ round(collect($calendarPlans)->where('estado.clave', 'completo')->count() / max(1, count($calendarPlans)) * 100) }}% completos
                                    @else
                                        En progreso
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Vista mensual del calendario --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Calendario por meses --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-200 mb-4">Vista mensual</h4>
                        <div class="grid grid-cols-1 gap-6">
                            @php
                                $mesesAgrupados = collect($calendarPlans)
                                    ->groupBy(function($item) {
                                        return \Carbon\Carbon::parse($item['date'])->format('Y-m');
                                    })
                                    ->sortKeys();
                            @endphp
                            
                            @foreach($mesesAgrupados->take(3) as $mes => $planesDelMes)
                                @php
                                    $fechaMes = \Carbon\Carbon::parse($mes . '-01');
                                    $nombreMes = $fechaMes->translatedFormat('F Y');
                                    $diasDelMes = $fechaMes->daysInMonth;
                                @endphp
                                
                                <div class="border border-white/10 rounded-lg p-4 bg-gray-800/30">
                                    <div class="flex justify-between items-center mb-3">
                                        <h5 class="text-sm font-medium text-gray-200">{{ $nombreMes }}</h5>
                                        <span class="text-xs text-gray-400">
                                            {{ $planesDelMes->pluck('plan.id')->unique()->count() }} planes
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-7 gap-1">
                                        @foreach(['L', 'M', 'X', 'J', 'V', 'S', 'D'] as $dia)
                                            <div class="text-center text-xs text-gray-500 font-medium p-1">{{ $dia }}</div>
                                        @endforeach
                                        
                                        @php
                                            $primerDia = $fechaMes->copy()->startOfMonth();
                                            $diaSemana = $primerDia->dayOfWeekIso; // 1=Lunes, 7=Domingo
                                        @endphp
                                        
                                        {{-- Espacios vacíos al inicio --}}
                                        @for($i = 1; $i < $diaSemana; $i++)
                                            <div class="h-8"></div>
                                        @endfor
                                        
                                        {{-- Días del mes --}}
                                        @for($dia = 1; $dia <= $diasDelMes; $dia++)
                                            @php
                                                $fechaActual = $fechaMes->copy()->day($dia);
                                                $fechaStr = $fechaActual->format('Y-m-d');
                                                $planesDelDia = collect($planesDelMes)->where('date', $fechaStr);
                                                $tienePlanes = $planesDelDia->count() > 0;
                                            @endphp
                                            
                                            <div class="relative h-8 flex items-center justify-center">
                                                @if($tienePlanes)
                                                    @php
                                                        $estados = $planesDelDia->pluck('estado.clave');
                                                        $color = $estados->contains('completo') 
                                                            ? 'bg-emerald-500/30' 
                                                            : ($estados->contains('en_progreso') 
                                                                ? 'bg-amber-500/30' 
                                                                : 'bg-gray-500/30');
                                                    @endphp
                                                    <div class="absolute inset-1 {{ $color }} rounded-md" 
                                                         title="{{ $planesDelDia->count() }} plan(es) este día">
                                                    </div>
                                                @endif
                                                <span class="relative text-xs font-medium 
                                                    {{ $tienePlanes ? 'text-gray-100' : 'text-gray-500' }}
                                                    {{ $fechaActual->isToday() ? 'text-cyan-300 font-bold' : '' }}">
                                                    {{ $dia }}
                                                </span>
                                            </div>
                                        @endfor
                                    </div>
                                    
                                    @if($planesDelMes->count() > 0)
                                        <div class="mt-3 text-xs text-gray-400">
                                            {{ $planesDelMes->pluck('plan.id')->unique()->count() }} planes en {{ $planesDelMes->count() }} días
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Lista detallada de planes --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-200 mb-4">Detalle por plan</h4>
                        <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2">
                            @php
                                $planesUnicos = collect($calendarPlans)
                                    ->groupBy('plan.id')
                                    ->map(function($dias, $planId) {
                                        $plan = $dias->first()['plan'];
                                        $primerDia = $dias->where('esPrimerDia', true)->first();
                                        $ultimoDia = $dias->where('esUltimoDia', true)->first();
                                        $estado = $dias->first()['estado'];
                                        $color = $dias->first()['color'];
                                        
                                        return [
                                            'plan' => $plan,
                                            'dias' => $dias,
                                            'primerDia' => $primerDia,
                                            'ultimoDia' => $ultimoDia,
                                            'estado' => $estado,
                                            'color' => $color,
                                            'duracion' => $dias->count(),
                                        ];
                                    })
                                    ->sortByDesc('plan.created_at')
                                    ->take(10);
                            @endphp
                            
                            @foreach($planesUnicos as $planData)
                                <div class="border border-white/10 rounded-lg p-3 hover:border-white/20 transition-colors
                                    {{ $planData['color'] }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-gray-100">
                                                    {{ $planData['plan']->grado?->name ?? 'Nivel' }} · {{ $planData['plan']->seccion?->name ?? 'Sección' }}
                                                </span>
                                                <span class="text-xs px-1.5 py-0.5 rounded border {{ $planData['color'] }}">
                                                    {{ $planData['estado']['etiqueta'] }}
                                                </span>
                                            </div>
                                            
                                            <div class="mt-1 text-xs text-gray-300">
                                                {{ $planData['primerDia']['date'] ? \Carbon\Carbon::parse($planData['primerDia']['date'])->format('d/m/Y') : '' }}
                                                →
                                                {{ $planData['ultimoDia']['date'] ? \Carbon\Carbon::parse($planData['ultimoDia']['date'])->format('d/m/Y') : '' }}
                                                ({{ $planData['duracion'] }} días)
                                            </div>
                                            
                                            @if($planData['plan']->diagnostico)
                                                <p class="mt-2 text-xs text-gray-400 line-clamp-2">
                                                    {{ \Illuminate\Support\Str::limit($planData['plan']->diagnostico, 120) }}
                                                </p>
                                            @endif
                                            
                                            <div class="mt-2 flex gap-2 text-xs">
                                                <span class="text-cyan-300">
                                                    {{ $planData['plan']->eiplanningwstrategies_count ?? 0 }}/50 estrategias
                                                </span>
                                                <span class="text-purple-300">
                                                    {{ $planData['plan']->eiplanningwsummaries_count ?? 0 }} resumen(es)
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex gap-1 ml-2">
                                            <button wire:click="abrirDetalle({{ $planData['plan']->id }})" 
                                                wire:click.prevent="closeCalendar"
                                                class="p-1.5 rounded-md bg-white/5 hover:bg-white/10 text-gray-400 hover:text-gray-200 transition-colors"
                                                title="Ver detalles en seguimiento">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            @if($planesUnicos->isEmpty())
                                <div class="text-center py-8 text-gray-500 border border-dashed border-white/10 rounded-lg">
                                    <svg class="w-12 h-12 mx-auto text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm">No hay planificaciones en el calendario para los filtros seleccionados</p>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Leyenda de estados --}}
                        <div class="mt-6 pt-4 border-t border-white/10">
                            <h5 class="text-xs font-medium text-gray-400 mb-2">Leyenda de estados</h5>
                            <div class="flex flex-wrap gap-2">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-3 h-3 rounded bg-emerald-500/30 border border-emerald-500/50"></div>
                                    <span class="text-xs text-gray-300">Completo</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-3 h-3 rounded bg-amber-500/30 border border-amber-500/50"></div>
                                    <span class="text-xs text-gray-300">En progreso</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-3 h-3 rounded bg-gray-500/30 border border-gray-500/50"></div>
                                    <span class="text-xs text-gray-300">Sin iniciar</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-3 h-3 rounded border border-dashed border-cyan-500/50"></div>
                                    <span class="text-xs text-gray-300">Hoy</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif