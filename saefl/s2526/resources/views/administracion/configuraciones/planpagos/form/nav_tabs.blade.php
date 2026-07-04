<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-datos-tab" data-toggle="tab" href="#nav-datos" role="tab" aria-controls="nav-datos" aria-selected="true">General</a>
        <a class="nav-item nav-link" id="nav-conceptos-tab" data-toggle="tab" href="#nav-conceptos" role="tab" aria-controls="nav-conceptos" aria-selected="false">Conceptos</a>
        <a class="nav-item nav-link" id="nav-cuentas-tab" data-toggle="tab" href="#nav-cuentas" role="tab" aria-controls="nav-cuentas" aria-selected="false">Cuentas</a>
    </div>
</nav>
<div class="tab-content border border-top-0" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-datos" role="tabpanel" aria-labelledby="nav-datos-tab">
        <div class="p-2">
            @include('administracion.configuraciones.planpagos.form.fields')
            <button type="submit" class="btn-planpago-create btn btn-primary btn-block" value="Actualizar" data-id="create" id="btn-create-planpago-{{$planpago->id ?? ''}}">
                <i class="far fa-save"></i>
                Actualizar
            </button>
        </div>
    </div>
    <div class="tab-pane fade" id="nav-conceptos" role="tabpanel" aria-labelledby="nav-conceptos-tab">
        <div class="p-2">
            @php $cuentaxpagars = $planpago->cuentaxpagars->where('type','GENERAL'); @endphp
            @include('administracion.configuraciones.planpagos.partials.cuentas')
        </div>
    </div>
    <div class="tab-pane fade" id="nav-cuentas" role="tabpanel" aria-labelledby="nav-cuentas-tab">
        <div class="p-2">
            @php $conceptopagos = $planpago->conceptopagos; @endphp
            @include('administracion.configuraciones.planpagos.partials.conceptos')
        </div>
    </div>
</div>
