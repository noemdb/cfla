<div class="card bd-callout bd-callout-primary">
    <div class="card-header">Formulario para datos del Usuario.</div>
    <div class="card-body">

        {{-- INI form --}}
        {!! Form::model($user,['route' => ['directors.users.update', $user->id], 'method' => 'PUT', 'id'=>'form-update-user_'.$user->id, 'role'=>'form']) !!}

            {{-- partial con el formulario y campos --}}
            @includeif('directors.users.form.fields')

            <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-user-{{$user->id}}">
                <i class="far fa-save"></i>
                Actualizar datos
            </button>

        {!! Form::close() !!}
        {{-- FIN form --}}

    </div>
</div>
