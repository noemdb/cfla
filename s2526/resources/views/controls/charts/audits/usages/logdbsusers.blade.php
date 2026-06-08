<div class="pt-2 px-4 mx-4">
    <div class=" alert alert-secondary p-1 text-center font-weight-bold">
        ¿Cuáles son los usuarios que realizan más consultas a la BD?
    </div>
    <div class="px-2">
        @include('controls.charts.audits.usages.partials.logdbsusers')
    </div>
    <div class="text-muted px-4">
        En la gráfica se puede ver los usuarios que más veces han consultado la base de datos
    </div>
</div>
