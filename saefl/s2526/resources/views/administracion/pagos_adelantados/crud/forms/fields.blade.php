@include('admin.elements.forms.errors')

@include('admin.elements.messeges.oper_ok')

<div class="form-label-group pb-1">
    {!! Form::text('username', old('username'), [
        'class' => 'form-control',
        'autofocus',
        'placeholder' => 'Nombre de Usuario',
        'id' => 'username',
    ]) !!}
    {{-- <input type="text" id="username" name="username" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" placeholder="Nombre de Usuario" value="{{ old('username') }}"> --}}
    <label for="username">Nombre de Usuario</label>

</div>

<div class="form-label-group pb-1">

    {!! Form::password('password', ['class' => 'form-control', 'id' => 'password', 'placeholder' => 'Contraseña']) !!}
    {{-- <input type="password" id="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="Contraseña"> --}}
    <label for="password">Contraseña</label>

</div>

<div class="form-label-group pb-1">

    {!! Form::text('email', old('email'), ['class' => 'form-control', 'id' => 'email', 'placeholder' => 'Email']) !!}
    {{-- <input type="text" id="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid ' : '' }}" placeholder="Email" value="{{ old('email') }}"> --}}
    <label for="email">Email del Usuario</label>

</div>

<div class="form-label-group pb-1">
    {!! Form::select('is_active', $is_active_list, old('is_active'), [
        'class' => 'form-control',
        'placeholder' => 'Estado',
    ]) !!}
    {{-- <label for="is_active">{{ trans('validation.attributes.is_active') }}</label> --}}

</div>


@section('stylesheet')
    @parent

    <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet">
@endsection
