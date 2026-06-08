<div class="card bd-callout bd-callout-{{ Session::get('class_oper') }}">
    {{-- <div class="card-header">
        Formulario para la actualización de Usuario.
    </div> --}}
    <div class="card-body">

        {{-- INI form --}}
        {!! Form::model($user,['route' => ['profesors.users.update', $user->id], 'method' => 'PUT', 'id'=>'form-update-user_'.$user->id, 'role'=>'form']) !!}

            {{-- partial con el formulario y campos --}}
            @include('profesors.forms.users.fields')

            <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-user-{{$user->id}}">

                <i class="far fa-save"></i>
                Actualizar Usuario

            </button>


        {!! Form::close() !!}
        {{-- FIN form --}}

    </div>
</div>
