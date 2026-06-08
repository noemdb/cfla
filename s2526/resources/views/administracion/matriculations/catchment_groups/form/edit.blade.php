<div class="bd-callout bd-callout-warning p-1 m-1">

    {!! Form::model($catchment_group,['route' => ['administracion.matriculations.catchment_groups.update', $catchment_group->id], 'method' => 'PUT', 'id'=>'form-update_'.$catchment_group->id, 'role'=>'form']) !!}

        @includeif('administracion.matriculations.catchment_groups.form.fields')

        <button type="submit" class="btn-catchment_group-edit btn btn-primary btn-block" value="Actualizar" data-id="edit" id="btn-edit-catchment_group-{{$catchment_group->id ?? ''}}">
            <i class="far fa-save"></i>
            Actualizar
        </button>

    {!! Form::close() !!}

</div>
