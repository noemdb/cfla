<div class="card bd-callout bd-callout-{{ Session::get('class_oper') }}">
  <div class="card-header">
    Formulario para la actualización del perfíl del usuario <strong>{{ $user->username ?? '' }}</strong>
  </div>
  <div class="card-body p-1">

      {{-- INI form --}}
      {!! Form::model($profile,['route' => ['profiles.update', $profile->id], 'method' => 'PUT', 'id'=>'form-update-profile_'.$profile->id, 'role'=>'form']) !!}

            {{-- partial con el formulario y campos --}}
            @include('admin.profiles.forms.fields')

            <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-profile-{{$profile->id}}">

                <i class="far fa-save"></i>
                Actualizar Perfíl

            </button>

            {{-- INI Menu modelos realcionados --}}
            <div class="btn-group d-flex pt-2" style="width: 100%;" role="group" aria-label="Basic example">

              <a class="btn btn-info w-100" href="{{ route('users.show',$profile->user_id) }}" role="button">
                Mostrar
                <i class="fas fa-user"></i>
              </a>

              <a class="btn btn-warning w-100" href="{{ isset($profile->user_id) ? route('users.edit',$profile->user_id) : route('users.create')}}" role="button">
                Actualizar
                <i class="fas fa-user"></i>
              </a>

              {{--
              <a class="btn btn-secondary w-100" href="{{ route('rols.edit',$user->id)}}" role="button">
                Mostrar
                <i class="fas fa-id-badge"></i>
              </a>
              --}}

            </div>
            {{-- FIN Menu modelos realcionados --}}

      {!! Form::close() !!}
      {{-- FIN form --}}

  </div>
</div>