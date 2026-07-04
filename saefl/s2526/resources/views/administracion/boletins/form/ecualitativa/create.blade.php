<div class="container">
    <div class="row">
        <div class="col-8">

            {!! Form::open(['route' => 'administracion.ecualitativas.store', 'method' => 'POST', 'id'=>'form-ecualitativa-create', 'class'=>'form-signin']) !!}

            @include('administracion.boletins.form.ecualitativa.fields')

            <button type="submit" class="btn-ecualitativa-create btn btn-primary btn-block" value="Registrar" data-id="create" id="btn-create-ecualitativa">
                <i class="far fa-save"></i>
                Registrar
            </button>

            {!! Form::close() !!}

        </div>
        <div class="col-4">
            @php $ecualitativas = $estudiant->ecualitativas; @endphp
            @include('administracion.boletins.form.ecualitativa.resume')
        </div>

    </div>
</div>

