@include('admin.elements.forms.errors')

@include('admin.elements.messeges.oper_ok')

<div class="form-label-group pb-1">
    {!! Form::text('username', old('username'), [
        'class' => 'form-control',
        'autofocus',
        'placeholder' => 'Nombre de Usuario',
        'id' => 'username',
    ]) !!}
    <label for="username">Nombre de Usuario</label>

</div>

<div class="form-label-group pb-1">

    {!! Form::password('password', ['class' => 'form-control', 'id' => 'password', 'placeholder' => 'Contraseña']) !!}
    <label for="password">Contraseña</label>

</div>

<div class="form-label-group pb-1">
    {!! Form::text('email', old('email'), ['class' => 'form-control', 'id' => 'email', 'placeholder' => 'Email']) !!}
    <label for="email">Email del Usuario</label>
</div>

<div class="form-label-group pb-1">
    {!! Form::text('ident', old('ident'), ['class' => 'form-control', 'id' => 'ident', 'placeholder' => 'ident']) !!}
    <label for="ident">Ident. Trabajador BIO</label>
</div>

<div class="form-label-group pb-1">
    {!! Form::text('work_id', old('work_id'), [
        'class' => 'form-control',
        'id' => 'work_id',
        'placeholder' => 'work_id',
    ]) !!}
    <label for="work_id">Ident. Trabajador</label>
</div>
<div class="form-label-group pb-1">
    {!! Form::text('number_id', old('number_id'), [
        'class' => 'form-control',
        'id' => 'number_id',
        'placeholder' => 'number_id',
    ]) !!}
    <label for="number_id">Cèdula de Identidad</label>
</div>

<label class="mb-0 pb-0 mt-1 pt-1 font-weight-bold text-muted" for="worker_order">Orden en la nómina</label>
<div class="form-label-group pb-1">
    {!! Form::selectRange('worker_order', 1, 100, old('worker_order'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

<label class="mb-0 pb-0 mt-1 pt-1 font-weight-bold text-muted" for="worker_order">Activo/Desactivo</label>
<div class="form-label-group pb-1">
    {!! Form::select('is_active', $is_active_list, old('is_active'), [
        'class' => 'form-control',
        'placeholder' => 'Estado',
    ]) !!}
</div>

<label class="mb-0 pb-0 mt-1 pt-1 font-weight-bold text-muted" for="worker_order">Renovación de Contraseña</label>
<div class="form-label-group pb-1">
    {!! Form::select('status_update', [false => 'SI', true => 'NO'], old('status_update'), [
        'class' => 'form-control',
        'placeholder' => 'Solicitar Cambio de contraseña',
    ]) !!}
</div>

<div class="form-label-group pb-1">
    {!! Form::text('mail_username', old('mail_username'), [
        'class' => 'form-control',
        'id' => 'mail_username',
        'placeholder' => 'mail_username',
    ]) !!}
    <label for="mail_username">Dirección de correo ECA</label>
</div>

<div class="form-label-group pb-1">
    {!! Form::text('mail_password', old('mail_password'), [
        'class' => 'form-control',
        'id' => 'mail_password',
        'placeholder' => 'mail_password',
    ]) !!}
    <label for="mail_password">Contraseña de correo ECA</label>
</div>

<div class="form-label-group pb-1">
    {!! Form::text('mail_cc_address', old('mail_cc_address'), [
        'class' => 'form-control',
        'id' => 'mail_cc_address',
        'placeholder' => 'mail_cc_address',
    ]) !!}
    <label for="mail_cc_address">Dirección de Correo CC ECA</label>
</div>

@section('stylesheet')
    @parent
    <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet">
@endsection



{{-- ,'mail_username','mail_password','mail_cc_address' --}}
