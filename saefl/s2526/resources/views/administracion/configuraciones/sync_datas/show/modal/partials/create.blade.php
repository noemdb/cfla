<div class="row">
    <div class="col-8">
        <div class="bd-callout bd-callout-primary">
            <div class="p-2">
                {!! Form::open(['route' => 'administracion.configuraciones.descuentos.store', 'method' => 'POST', 'id'=>'form-descuentos-create', 'class'=>'form-signin']) !!}
                    {{ Form::hidden('estudiant_id', $estudiant->id) }}
                    {{ Form::hidden('view', 'administracion.configuraciones.plan_beneficos.create') }}
                    @include('administracion.configuraciones.descuentos.form.fields')
                    <button type="submit" class="btn-descuento-create btn btn-primary btn-block" value="Registrar" data-id="create" id="btn-create-descuentos-{{$descuento->id ?? ''}}">
                        <i class="far fa-save"></i>
                        Registrar
                    </button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <div class="col-4">
        @include('administracion.configuraciones.descuentos.partials.list')
    </div>
</div>
