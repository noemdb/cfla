<div class="bd-callout bd-callout-warning p-1 m-1">

    {!! Form::model($catchment_activity,['route' => ['administracion.matriculations.catchment_activities.update', $catchment_activity->id], 'method' => 'PUT', 'id'=>'form-update_'.$catchment_activity->id, 'role'=>'form']) !!}

        @includeif('administracion.matriculations.catchment_activities.form.fields')

        <button type="submit" class="btn-catchment_activity-edit btn btn-primary btn-block" value="Actualizar" data-id="edit" id="btn-edit-catchment_activity-{{$catchment_activity->id ?? ''}}">
            <i class="far fa-save"></i>
            Actualizar
        </button>

    {!! Form::close() !!}

</div>
