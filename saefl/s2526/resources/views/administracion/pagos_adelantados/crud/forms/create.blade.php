<div class="card bd-callout bd-callout-{{ $class_form_create_user ?? 'form' }}">
  <div class="card-header">
    Formulario para el Registro de Nuevo Usuario.
  </div>
  <div class="card-body">

      {{-- INI form --}}
      {!! Form::open(['route' => 'users.store', 'method' => 'POST', 'id'=>'form-user-create', 'class'=>'form-signin']) !!}
            
            {{-- partial con el formulario y campos --}}
            @include('admin.users.forms.fields')

            <button type="submit" class="btn-user-create btn btn-primary btn-block" value="create" data-id="create" id="create">

                <span class="glyphicon glyphicon-save" aria-hidden="true"></span>Registrar

            </button>

            <button type="reset" class="btn-user-reset btn btn-info btn-block" value="Reset">

                <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>Reset 

            </button>
      
      {!! Form::close() !!}    
      {{-- FIN form --}}

  </div>
</div>