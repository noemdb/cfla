<div class="bd-callout bd-callout-warning">

    {!! Form::model($historico_nota,['route' => ['administracion.historico_notas.update', $historico_nota->id], 'method' => 'PUT', 'id'=>'form-update-boletin_'.$historico_nota->id, 'role'=>'form']) !!}
        {{ Form::hidden('pestudio_id', $pestudio->id) }}
        {{ Form::hidden('estudiant_id', $estudiant->id) }}

        @include('administracion.historico_notas.form.historico')
        <hr>
        @include('administracion.historico_notas.form.pestudios')
        <hr>
        <button type="submit" class="btn-historico_nota-edit btn btn-primary btn-block" value="Actualizar" data-id="edit" id="btn-edit-historico_nota-{{$historico_nota->id ?? ''}}">
            <i class="far fa-save"></i>
            Actualizar
        </button>

    {!! Form::close() !!}

</div>
