<div class="p-2">
    <div class="h4 font-weight-bold">Indicadores Principales</div>
    @includeIf('academicos.home.partials.labels.estudiantil')
</div>

<hr>

<div class=" border rounded p-2">
    <div class="h4 font-weight-bold m-0 mb-2 p-2 table-secondary rounded">Planificaciones - Plan de Actividades</div>
    <div class="px-2">
        @include('academicos.home.partials.planning')        
    </div>
</div>

<hr>

<div class=" border rounded p-2">
    <div class="h4 font-weight-bold pt-2">Rendimiento Estudiantíl</div>
    <div class="px-2">
        @include('academicos.performances.partials.index.pestudios')
    </div>
</div>

<hr>

<div class=" border rounded p-2">
    <div class="h4 font-weight-bold pt-2">Áreas de Conocimiento</div>
    <div class="px-2">
        @include('academicos.performances/partials/index/area_conocimientos')
    </div>
</div>
