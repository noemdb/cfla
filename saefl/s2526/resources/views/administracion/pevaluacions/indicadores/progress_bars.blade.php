<span class="p-2">
    <i class="{{ $icon_menus['chartbar'] ?? ''}} fa-1x text-primary"></i>
    <b>PLANES DE EVALUACION ASIGNADOS - EJECUTADO / PLANIFICADO</b>
</span>
<div class="border border-top-0 rounded-bottom pb-2">
    <div class="row">
        <div class="col-sm-12">
            @includeif('administracion.pevaluacions.partials.progress_bars.pevaluacions')
        </div>
    </div>
</div>
