<div class="card bd-callout bd-callout-{{ Session::get('class_oper') ?? "" }}">
  <div class="card-header font-weight-bold">
    Formulario para la actualización del alert del usuario <strong>{{ $user->username ?? '' }}</strong>
  </div>
  <div class="card-body p-1">

      {{-- INI form --}}
      {!! Form::model($alert,['route' => ['alerts.update', $alert->id], 'method' => 'PUT', 'id'=>'form-update-task_'.$alert->id, 'taske'=>'form']) !!}

            {{-- partial con el formulario y campos --}}
            @include('admin.alerts.forms.fields')

            <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-alert-{{$alert->id}}">

                <i class="far fa-save"></i>
                Actualizar Alerta

            </button>

            {{-- INI Menu modelos realcionados --}}
            {{-- <div class="btn-group d-flex pt-2" style="width: 100%;" taske="group" aria-label="Basic example">

              <a class="btn btn-info w-100" href="{{ route('profiles.show',$profile->id) }}" taske="button">
                Mostrar
                <i class="{{ $icon_menus['profile'] ?? '' }}"></i>
              </a>

            </div> --}}
            {{-- FIN Menu modelos realcionados --}}

      {!! Form::close() !!}
      {{-- FIN form --}}

  </div>
</div>