<div class="card bd-callout bd-callout-{{ $class_form_create_task ?? 'form' }}">
  <div class="card-header font-weight-bold">
    Formulario para el Registro de un nuevo logdbs.
  </div>
  <div class="card-body p-1">

      {{-- <form> --}}
      {!! Form::open(['route' => 'logdbs.store', 'method' => 'POST', 'id'=>'form-logdb-create-'. (isset($user->id)? $user->id : 'create')]) !!}    

          {{-- partial con el formulario y campos --}}
          @include('admin.logdbs.forms.fields')

          <button type="submit" class="btn-logdb-create btn btn-primary btn-block" value="create" data-user="{{$user->id ?? 'create'}}">
              <span class="glyphicon glyphicon-save" aria-hidden="true"></span>
              Registrar
          </button>
          <button type="reset" class="btn-logdb-reset btn btn-info btn-block" value="Reset">
              <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
              Reset
          </button>

      {!! Form::close() !!}
      {{-- </form> --}}

  </div>
</div>