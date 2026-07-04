@include('admin.elements.forms.errors')

@include('admin.elements.messeges.oper_ok')

<div class="form-label-group pb-1">
    {!! Form::text('firstname', old('firstname'), [
        'class' => 'form-control',
        'placeholder' => '1er Nombre de Usuario',
        'id' => 'firstname',
        'required',
        'autofocus',
    ]) !!}
    {{-- <input type="text" id="username" name="username" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" placeholder="Nombre de Usuario" value="{{ old('username') }}"> --}}
    <label for="firstname">1er Nombre de Usuario</label>

</div>

<div class="form-label-group pb-1">
    {!! Form::text('lastname', old('lastname'), [
        'class' => 'form-control',
        'placeholder' => '2dp Nombre de Usuario',
        'id' => 'lastname',
        'required',
        'autofocus',
    ]) !!}
    {{-- <input type="text" id="username" name="username" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" placeholder="Nombre de Usuario" value="{{ old('username') }}"> --}}
    <label for="lastname">2do Nombre de Usuario</label>

</div>

@section('stylesheet')
    @parent

    <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet">
@endsection
