@component('administracion.elements.forms.input')
    @slot('name', 'name')
    @slot('value', $institucion->name)
    @slot('label', 'Nombre de la institución')
@endcomponent
@component('administracion.elements.forms.input')
    @slot('name', 'legalname')
    @slot('value', $institucion->legalname)
    @slot('label', 'Nombre jurídico de la institución')
@endcomponent

@component('administracion.elements.forms.input')
    @slot('name', 'code_oficial')
    @slot('value', $institucion->code_oficial)
    @slot('label', 'Código oficial público')
@endcomponent

@component('administracion.elements.forms.input')
    @slot('name', 'code_private')
    @slot('value', $institucion->code_private)
    @slot('label', 'Código oficial privado')
@endcomponent

@component('administracion.elements.forms.input')
    @slot('name', 'rif_institution')
    @slot('value', $institucion->rif_institution)
    @slot('label', 'Registro de Información Fiscal')
@endcomponent

@component('administracion.elements.forms.input')
    @slot('name', 'phone')
    @slot('value', $institucion->phone)
    @slot('label', 'Números de teléfonos')
@endcomponent

{{-- <label for="basic-url" class="p-0 m-0">Números de teléfonos</label>
<div class="input-group mb-3">
    {!! Form::text('phone', $institucion->phone, ['class' => 'form-control','placeholder'=>'Número de teléfono 1','id'=>'phone']); !!}
    {!! Form::text('phone2', $institucion->phone2, ['class' => 'form-control','placeholder'=>'Número de teléfono 2','id'=>'phone2']); !!}
    {!! Form::text('phone3', $institucion->phone3, ['class' => 'form-control','placeholder'=>'Número de teléfono 3','id'=>'phone3']); !!}
</div> --}}

@component('administracion.elements.forms.input')
    @slot('name', 'email_institution')
    @slot('value',  $institucion->email_institution)
    @slot('label', 'Correo electrónico administrativo')
@endcomponent

@component('administracion.elements.forms.input')
    @slot('name', 'password')
    @slot('label', 'Contraseña de correo electrónico')
@endcomponent


<div class="form-group pb-1">
    <label for="date_suspend" class="m-0">Fecha de suspención</label>
    {!! Form::date('date_suspend', old('date_suspend'),['class'=>'form-control','required']) !!}
</div>

