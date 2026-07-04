<div class="card bd-callout bd-callout-{{ Session::get('class_oper') }}">
  <div class="card-header">
    Formulario para la actualización de Usuario.
  </div>
  <div class="card-body">

      {{-- INI form --}}
      {!! Form::model($user,['route' => ['users.update', $user->id], 'method' => 'PUT', 'id'=>'form-update-user_'.$user->id, 'role'=>'form']) !!}

            {{-- partial con el formulario y campos --}}
            @include('admin.users.forms.fields')

            <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-user-{{$user->id}}">

                <i class="far fa-save"></i>
                Actualizar Usuario

            </button>

            {{-- INI Menu modelos realcionados --}}
            <div class="btn-group d-flex pt-2" style="width: 100%;" role="group" aria-label="Basic example">

              <a class="btn btn-info w-100" href="{{ route('users.show',$user->id) }}" role="button">
                Mostrar
                <i class="fas fa-user"></i>
              </a>

              {{-- <a class="btn btn-secondary w-100" href="{{ isset($profile->id) ? route('profiles.edit',$profile->id) : route('profiles.create')}}" role="button"> --}}
                {{-- {{ isset($profile->id) ? 'Actualizar' : 'Crear'}} Perfíl --}}
                {{-- <i class="far fa-id-card"></i>  --}}
              {{-- </a> --}}

              {{--
              <a class="btn btn-secondary w-100" href="{{ ($rols->count() > 0) ? route('rols.edit',$user->id) : route('rols.create')}}" role="button">
                {{ ($rols->count() > 0) ? 'Actualizar' : 'Crear'}} Roles
                <i class="far fa-id-badge"></i>
              </a>
              --}}

            </div>
            {{-- FIN Menu modelos realcionados --}}

      {!! Form::close() !!}
      {{-- FIN form --}}

  </div>
</div>