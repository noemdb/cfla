<div class="space-y-4 text-xs text-gray-400">
    {{-- Grado --}}
    <div>
        <span class="block text-[9px] font-bold uppercase tracking-widest text-gray-600 mb-1">Grado</span>
        <span class="text-gray-300 font-medium">{{ $pevaluacion->pensum?->grado?->name ?? '—' }}
            Sección {{ $pevaluacion->seccion?->name ?? '—' }}</span>
    </div>

    {{-- Lapso --}}
    <div>
        <span class="block text-[9px] font-bold uppercase tracking-widest text-gray-600 mb-1">Lapso</span>
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
            {{ $pevaluacion->lapso?->name ?? '—' }}
        </span>
    </div>

    {{-- Área de Formación --}}
    <div>
        <span class="block text-[9px] font-bold uppercase tracking-widest text-gray-600 mb-1">Área de Formación</span>
        <span class="text-gray-300 font-medium">{{ $pevaluacion->pensum?->asignatura?->name ?? '—' }}</span>
        @php $grupoEstable = $pevaluacion->grupoEstable; @endphp
        @if($grupoEstable)
            <span class="block text-[10px] text-gray-600 mt-0.5">Comp. Formación: {{ $grupoEstable->name }}</span>
        @endif
    </div>

    {{-- Profesor --}}
    <div>
        <span class="block text-[9px] font-bold uppercase tracking-widest text-gray-600 mb-1">Profesor</span>
        <span class="text-gray-300">{{ $pevaluacion->profesor?->full_name ?? '—' }}</span>
    </div>

    {{-- Tipo de nota final --}}
    <div>
        <span class="block text-[9px] font-bold uppercase tracking-widest text-gray-600 mb-1">Tipo Nota Final</span>
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
            {{ $pevaluacion->nota_type ?? '—' }}
        </span>
    </div>

    {{-- Escala --}}
    <div>
        <span class="block text-[9px] font-bold uppercase tracking-widest text-gray-600 mb-1">Nota - Escala</span>
        @switch($pevaluacion->nota_type)
            @case('ACUMULATIVA')
                <span class="text-gray-300">Nota Final: {{ $pevaluacion->escala?->maximo ?? '—' }}</span>
                @break
            @case('PROMEDIADA')
                <span class="text-gray-300">Escala: [{{ $pevaluacion->escala?->name ?? '—' }}]</span>
                @break
            @default
                <span class="text-gray-500">—</span>
        @endswitch
    </div>

    {{-- Objetivo --}}
    <div>
        <span class="block text-[9px] font-bold uppercase tracking-widest text-gray-600 mb-1">Objetivo</span>
        <p class="text-gray-300 leading-relaxed">{{ $pevaluacion->objetivo ?? '—' }}</p>
    </div>

    {{-- Descripción --}}
    @if($pevaluacion->description)
    <div>
        <span class="block text-[9px] font-bold uppercase tracking-widest text-gray-600 mb-1">Descripción</span>
        <p class="text-gray-300 leading-relaxed">{{ $pevaluacion->description }}</p>
    </div>
    @endif

    {{-- Observación Coord.Eval. --}}
    <div class="pt-2 border-t border-white/5">
        <span class="block text-[9px] font-bold uppercase tracking-widest text-gray-600 mb-1">
            Observación <span class="text-cyan-400">[Coord.Eval.]</span>
        </span>
        <p class="text-gray-400 leading-relaxed">{{ $pevaluacion->observations ?? 'No hay observaciones registradas.' }}</p>
    </div>

    {{-- Fecha de Precierre --}}
    <div class="pt-2 border-t border-white/5">
        <span class="block text-[9px] font-bold uppercase tracking-widest text-gray-600 mb-1">
            Fecha Precierre <span class="text-amber-400">[Control de Estudio]</span>
        </span>
        @php $lapso = $pevaluacion->lapso; @endphp
        <span class="text-gray-300">
            {{ $lapso && $lapso->date_preclosing ? $lapso->date_preclosing->format('d-m-Y') . ' ' . ($lapso->time_preclosing ?? '') : '—' }}
        </span>
    </div>
</div>
