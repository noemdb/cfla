<div class="card bd-callout bd-callout-{{ Session::get('class_oper') }}">
  <div class="card-header">
    Formulario para la actualización del rol del usuario <strong>{{ $user->username ?? '' }}</strong>
  </div>
  <div class="card-body p-1">

      {{-- INI form --}}
      {!! Form::model($rol,['route' => ['rols.update', $rol->id], 'method' => 'PUT', 'id'=>'form-update-rol_'.$rol->id, 'role'=>'form']) !!}

            {{-- partial con el formulario y campos --}}
            @include('admin.rols.forms.fields')

            <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-rol-{{$rol->id}}">

                <i class="far fa-save"></i>
                Actualizar Rol

            </button>

            {{-- INI Menu modelos realcionados --}}
            <div class="btn-group d-flex pt-2" style="width: 100%;" role="group" aria-label="Basic example">

              <a class="btn btn-info w-100" href="{{ route('users.show',$user->id) }}" role="button">
                Mostrar
                <i class="{{ $icon_menus['user'] ?? '' }}"></i>
              </a>

              <a class="btn btn-info w-100" href="{{ route('profiles.show',$profile->id) }}" role="button">
                Mostrar
                <i class="{{ $icon_menus['profile'] ?? '' }}"></i>
              </a>

            </div>
            {{-- FIN Menu modelos realcionados --}}

      {!! Form::close() !!}
      {{-- FIN form --}}

  </div>
</div>