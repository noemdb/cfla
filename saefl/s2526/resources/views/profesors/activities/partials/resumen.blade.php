<div class="small text-muted">
    @admin
    <ol class="ml-1 pl-1 mb-1">
        <dt>ID</dt>
        {{$pevaluacion->id ?? ''}}
    </ol>
    @endadmin
    <ol class="ml-1 pl-1 mb-1">
        <dt>Grado</dt>
        {{$pevaluacion->pensum->grado->name ?? ''}}
        Sección {{$pevaluacion->seccion->name ?? ''}}
    </ol>
    <ol class="ml-1 pl-1 mb-1">
        <dt>Lapso</dt>
        {{$pevaluacion->lapso->name ?? ''}}
    </ol>
    <ol class="ml-1 pl-1 mb-1">
        @php $grupo_estable = $pevaluacion->grupo_estable; $asignatura = $pevaluacion->pensum->asignatura;@endphp
        <dt>Área de Formación</dt>
        {{ ($asignatura) ? $asignatura->name : null }}
        @if ($grupo_estable) <div class=" text-muted small">Comp. de Formación: {{$grupo_estable->name ?? null}}</div> @endif
    </ol>
    <ol class="ml-1 pl-1 mb-1">
        <dt>Profesor</dt>
        {{$pevaluacion->profesor->fullname ?? ''}}
    </ol>
    <ol class="ml-1 pl-1 mb-1">
        <dt>Tipo de nota final</dt>
        {{$pevaluacion->nota_type ?? ''}}
    </ol>
    <ol class="ml-1 pl-1 mb-1">
        <dt>Nota - Escala</dt>
        @switch($pevaluacion->nota_type)
            @case('ACUMULATIVA')
                Nota Final: {{ $pevaluacion->escala->maximo ?? ''}}
                @break
            @case('PROMEDIADA')
                Escala: [{{ $pevaluacion->escala->name ?? ''}}]
                @break
            @default
        @endswitch
    </ol>
    <ol class="ml-1 pl-1 mb-1">
        <dt>Objetivo</dt>
        {{$pevaluacion->objetivo ?? ''}}
    </ol>
    <ol class="ml-1 pl-1 mb-1">
        <dt>Descripción</dt>
        {{$pevaluacion->description ?? ''}}
    </ol>
    <ol class="ml-1 pl-1 mb-1">
        <dt>Observación <strong>[Coord.Eval.]</strong></dt>
        {{$pevaluacion->observations ?? ''}}
    </ol>
    <ol class="ml-1 pl-1 mb-1 border-top" title="Fecha de Precierre">
        <dt>Fecha de Precierre <br><strong>[Coord.Registro y Control de Estudio.]</strong></dt>
        @php $lapso = $pevaluacion->lapso @endphp
        {{$lapso->full_date_preclosing->format('d-m-Y h:i A') ?? ''}}
    </ol>
</div>
