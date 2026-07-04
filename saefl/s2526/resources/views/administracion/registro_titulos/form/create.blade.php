<div class="bd-callout bd-callout-primary p-1 m-1">

    {!! Form::open(['route' => 'administracion.registro_titulos.store', 'method' => 'POST', 'id'=>'form-create', 'class'=>'form-signin']) !!}

        {{ Form::hidden('institucion_id', $institucion->id) }}

        <label for="institucion_id" class="font-weight-bold text-secondary m-0">{{$list_comment['institucion_id'] ?? ''}}</label>
        <div class="alert alert-secondary">
            {{$institucion->name ?? ''}}
        </div>

        @include('administracion.registro_titulos.form.fields')

        <button type="submit" class="btn-create btn btn-primary btn-block" value="Registrar">
            <i class="far fa-save"></i>
            Registrar
        </button>

    {!! Form::close() !!}

</div>
