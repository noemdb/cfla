<div class="form-label-group pb-1">
    {!! Form::text('username', old('username'), [
        'class' => 'form-control',
        'autofocus',
        'placeholder' => 'Nombre de Usuario',
        'id' => 'username',
        'required',
    ]) !!}
    <label for="username">Nombre de usuario</label>
</div>

<div class="form-label-group pb-1">
    {!! Form::password('password', [
        'class' => 'form-control',
        'id' => 'password',
        'placeholder' => 'Contraseña',
        'required',
    ]) !!}
    <label for="password">Nueva contraseña</label>
    <span class="small float-right">Mínimo 6 carácteres</span>
</div>
