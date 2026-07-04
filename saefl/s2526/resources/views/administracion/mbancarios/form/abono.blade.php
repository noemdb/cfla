<div class="card">
    <div class="card-header pb-0 mb-0">
        <h4 class="card-title">
            <i class="{{ $icon_menus['abonos'] }} fa-1x text-success "></i>
            Datos para el nuevo abono
        </h4>
    </div>
    <div class="card-body">
        <div class="row">

            <div class="col-7">
                {!!Form::open(['route'=>'administracion.mbancarios.abono.store','method'=>'POST','id'=>'form-mbancarios-create-representant','class'=>'form-signin'])!!}
                    @include('administracion.mbancarios.form.fields.abono')
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="far fa-save"></i>
                        Registrar
                    </button>
                {!! Form::close() !!}
            </div>

            <div class="col-5">
                @include('administracion.mbancarios.show.representant.resume')
            </div>

        </div>

    </div>
</div>

