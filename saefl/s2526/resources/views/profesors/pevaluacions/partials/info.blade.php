<div class="small text-muted">
    <ol class="ml-1 pl-1">
        <dt>Asignatura</dt>
        {{$pensum->asignatura->name ?? ''}}
    </ol>
    <ol class="ml-1 pl-1">
        <dt>Grado</dt>
        {{$grado->name ?? ''}}
        Sección {{$seccion->name ?? ''}}
    </ol>
    <ol class="ml-1 pl-1">
        <dt>Lapso</dt>
        {{$lapso->name ?? ''}}
    </ol>
</div>