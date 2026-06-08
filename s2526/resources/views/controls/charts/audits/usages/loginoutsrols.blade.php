<div class="pt-2 px-2">
    <div class=" alert alert-secondary p-1 text-center font-weight-bold">
        ¿Cuáles son los roles de los usuarios que más acceden al sistema?
    </div>
    <div class="px-2">
        @include('controls.charts.audits.usages.partials.loginoutsrols')
    </div>
    <div class="text-muted">
        En la gráfica se puede ver la cantidad de veces que los usuarios, agrupados por sus roles, han iniciado sesión correctamente
    </div>
</div>
