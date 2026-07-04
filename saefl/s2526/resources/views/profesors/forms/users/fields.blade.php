{{-- @include('profesors.elements.messeges.oper_ok') --}}

@include('profesors.elements.forms.errors')

<div class="form-label-group pb-1">
    <label for="username">Nombre de Usuario</label>
    {!! Form::text('username', old('username'), [
        'class' => 'form-control',
        'autofocus',
        'placeholder' => 'Nombre de Usuario',
        'id' => 'username',
    ]) !!}
</div>

<div class="form-label-group pb-1">
    <label for="password">Contraseña</label>
    {!! Form::password('password', ['class' => 'form-control', 'id' => 'password', 'placeholder' => 'Contraseña']) !!}
</div>

{{ Form::hidden('status_update', true) }}
