@component('administracion.elements.forms.input')
    @slot('name', 'address')
    @slot('value',  $institucion->address)
    @slot('label', 'Dirección')
@endcomponent
@component('administracion.elements.forms.input')
    @slot('name', 'town_hall')
    @slot('value',  $institucion->town_hall)
    @slot('label', 'Municipio')
@endcomponent
@component('administracion.elements.forms.input')
    @slot('name', 'city')
    @slot('value',  $institucion->city)
    @slot('label', 'Ciudad')
@endcomponent
@component('administracion.elements.forms.input')
    @slot('name', 'state')
    @slot('value',  $institucion->state)
    @slot('label', 'Estado')
@endcomponent
@component('administracion.elements.forms.input')
    @slot('name', 'state_code')
    @slot('value',  $institucion->state_code)
    @slot('label', 'Código del Estado / Entidad Federal')
@endcomponent
@component('administracion.elements.forms.input')
    @slot('name', 'country')
    @slot('value',  $institucion->country)
    @slot('label', 'País')
@endcomponent
