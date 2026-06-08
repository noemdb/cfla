{!! Form::model($selected,['route' => ['administracion.creditoafavors.set.omit', $selected->id], 'method' => 'PUT', 'id'=>'form-update-selected_'.$selected->id, 'role'=>'form']) !!}

    @include('administracion.creditoafavors.form.omit.fields')

    <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-selected-{{$selected->id}}">
        <i class="far fa-save"></i>
        Actualizar
    </button>

{!! Form::close() !!}
