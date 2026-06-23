<div class="card bd-callout bd-callout-{{ $class_form_create_messege ?? 'form' }}">
  <div class="card-header font-weight-bold">
    Formulario para el Registro de un nuevo Mensaje.
  </div>
  <div class="card-body">

      {{-- <form> --}}
      {!! Form::open(['route' => 'messeges.store', 'method' => 'POST', 'id'=>'form-alert-create-'. (isset($user->id)? $user->id : 'create')]) !!}    

          {{-- partial con el formulario y campos --}}
          @include('admin.messeges.forms.fields')

          <button type="submit" class="btn-alert-create btn btn-primary btn-block" value="create" data-user="{{$user->id ?? 'create'}}">
              <span class="glyphicon glyphicon-save" aria-hidden="true"></span>
              Registrar
          </button>
          <button type="reset" class="btn-alert-reset btn btn-info btn-block" value="Reset">
              <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
              Reset
          </button>

      {!! Form::close() !!}
      {{-- </form> --}}

  </div>
</div>