<div class="bd-callout bd-callout-primary">

    {!! Form::open(['route' => 'administracion.historico_notas.store', 'method' => 'POST', 'id'=>'form-create-historico', 'class'=>'form-signin']) !!}

        {{ Form::hidden('pestudio_id', $pestudio->id) }}
        {{ Form::hidden('estudiant_id', $estudiant->id) }}

        @include('administracion.historico_notas.form.historico')
        <hr>
        @include('administracion.historico_notas.form.pestudios')
        <hr>

        <button type="submit" class="btn-create btn btn-primary btn-block" value="Registrar">
            <i class="far fa-save"></i>
            Registrar
        </button>

    {!! Form::close() !!}

</div>
