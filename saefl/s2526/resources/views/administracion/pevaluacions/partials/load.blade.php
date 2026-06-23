<div class="col-sm-3">
    <h5 class="card-title">Resumen</h5>
    <div class="dropdown-divider mb-0"></div>
    @include('administracion.pevaluacions.partials.resumen')
</div>

<div class="col-sm-6">
    <div class="card shadow p-1 mb-5 bg-white rounded">
        <div class="card-body p-1 m-1">
            <h6 class="card-title"> <b> Asignar Evaluaciones/Logros/Indicadores al Plan</b></h6>
            <div class="dropdown-divider mb-0"></div>
            {!! Form::open([
                'route' => 'administracion.evaluacions.store_pevaluacions',
                'method' => 'POST',
                'id' => 'form-evaluacions-create',
                'class' => 'form-signin',
            ]) !!}
            @include('administracion.pevaluacions.evaluacions.form.fields_pevaluacions')
            <button type="submit" class="btn-pevaluacions-create btn btn-primary btn-block"
                value="Registrar" data-id="create" id="btn-create-pevaluacions">
                <i class="far fa-save"></i> Registrar
            </button>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@if (!empty($pevaluacion->evaluacions->first()))
    <div class="col-sm-3 h-100 alert-{{ $pevaluacion->status_eva_complete ? 'success' : 'warning' }}">
        <h5 class="card-title">Lista de Evaluaciones registradas</h5>
        <div class="dropdown-divider mb-0"></div>
        @php $evaluacions = (!empty($pevaluacion->evaluacions)) ? $pevaluacion->evaluacions:null; @endphp
        @includewhen($evaluacions, 'administracion.pevaluacions.partials.evaluacion')
    </div>
@endif