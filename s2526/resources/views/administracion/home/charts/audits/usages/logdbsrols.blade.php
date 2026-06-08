<div class="pt-2 px-2">
    <div class=" alert alert-secondary p-1 text-center font-weight-bold">
        ¿Cuál es la cantidad de consultas realizadas a la BD por roles?
    </div>
    <div class="px-2">
        @include('directors.charts.audits.usages.partials.logdbsrols')
    </div>
    <div class="text-muted">
        En la gráfica se puede ver la cantidad de veces que los usuarios, agrupados por sus roles, han consultado la BD
    </div>
</div>
