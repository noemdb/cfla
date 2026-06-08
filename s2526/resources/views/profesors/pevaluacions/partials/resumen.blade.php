<div class="small text-muted">
    @admin
    <ol class="ml-1 pl-1">
        <dt>ID</dt>
        {{$pevaluacion->id ?? ''}}
    </ol>
    @endadmin
    <ol class="ml-1 pl-1">
        <dt>Grado</dt>
        {{$pevaluacion->pensum->grado->name ?? ''}}
        Sección {{$pevaluacion->seccion->name ?? ''}}
    </ol>
    <ol class="ml-1 pl-1">
        <dt>Lapso</dt>
        {{$pevaluacion->lapso->name ?? ''}}
    </ol>
    <ol class="ml-1 pl-1">
        <dt>Asignatura</dt>
        {{$pevaluacion->pensum->asignatura->name ?? ''}}
    </ol>
    <ol class="ml-1 pl-1">
        <dt>Profesor</dt>
        {{$pevaluacion->profesor->fullname ?? ''}}
    </ol>
    <ol class="ml-1 pl-1">
        <dt>Tipo de nota final</dt>
        {{$pevaluacion->nota_type ?? ''}}
    </ol>
    <ol class="ml-1 pl-1">
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
    <ol class="ml-1 pl-1">
        <dt>Objetivo</dt>
        {{$pevaluacion->objetivo ?? ''}}
    </ol>
    <ol class="ml-1 pl-1">
        <dt>Descripción</dt>
        {{$pevaluacion->description ?? ''}}
    </ol>
    <ol class="ml-1 pl-1">
        <dt>Observación</dt>
        {{$pevaluacion->observations ?? ''}}
    </ol>
</div>

@php $disabled  = ($pevaluacion->evaluacions->isNotEmpty()) ? ' disabled ': null ; @endphp
<a title="Editar" class="btn {{$disabled ?? ''}}"  href="{{route('profesors.pevaluacions.edit',$pevaluacion->id)}}" role="button">
    <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x small"></i>
</a>
