<div class="bd-callout bd-callout-warning p-1 m-1">

    {!! Form::model($registro_titulo,['route' => ['administracion.registro_titulos.update', $registro_titulo->id], 'method' => 'PUT', 'id'=>'form-update_'.$registro_titulo->id, 'role'=>'form']) !!}

        {{ Form::hidden('institucion_id', $registro_titulo->institucion_id) }}
        {{ Form::hidden('pestudio_id', $registro_titulo->pestudio_id) }}

        <label for="institucion_id" class="font-weight-bold text-secondary m-0">{{$list_comment['institucion_id'] ?? ''}}</label>
        <div class="alert alert-secondary">
            {{$registro_titulo->institucion->name ?? ''}}
        </div>

        <label for="pestudio_id" class="font-weight-bold text-secondary m-0">{{$list_comment['pestudio_id'] ?? ''}}</label>
        <div class="alert alert-secondary">
            {{$registro_titulo->pestudio->name ?? ''}}
        </div>

        @include('administracion.registro_titulos.form.fields')

        <button type="submit" class="btn-registro_titulo-edit btn btn-primary btn-block" value="Actualizar" data-id="edit" id="btn-edit-registro_titulo-{{$registro_titulo->id ?? ''}}">
            <i class="far fa-save"></i>
            Actualizar
        </button>

    {!! Form::close() !!}

</div>
