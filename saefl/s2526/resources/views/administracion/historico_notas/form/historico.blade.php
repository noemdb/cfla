<h6 class=" ">
    @if (empty($estudiant->historico_nota->id))
        <span class="float-right badge badge-primary">Nuevo</span>
    @else
        <span class="float-right badge badge-warning">Edición</span>
    @endif
</h6>
<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active font-weight-bold" id="nav-datos_generales-tab" data-toggle="tab" href="#nav-datos_generales" role="tab" aria-controls="nav-datos_generales" aria-selected="true">
            I. Datos Generales
        </a>               
    </div>
</nav>
<div class="tab-content  border border-top-0" id="nav-tabContent">
    <div class="tab-pane fade show active px-2" id="nav-datos_generales" role="tabpanel" aria-labelledby="nav-datos_generales-tab">
        @include('administracion.historico_notas.form.fields.historico')
    </div>
</div>
