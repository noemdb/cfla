@component('elements.buttons.default')
    @slot('title', 'Listado')
    @slot('class_bt', 'light')
    @slot('route', route('administracion.ingresos.crud'))
    @slot('icon', $icon_menus['crud'])
@endcomponent

{{-- @component('elements.buttons.default')
    @slot('title', 'Crear nuevo boletin')
    @slot('class_bt', 'primary')
    @slot('route', route('administracion.boletins.create'))
    @slot('icon', $icon_menus['nuevo'])
@endcomponent --}}

{{-- @component('elements.menus.dropdown')
    @slot('title', 'Listado relacionados')
    @slot('class', 'info')
    @slot('icon', $icon_menus['crud'])
    @slot('dropdown')
        @component('elements.buttons.dropdown')
            @slot('title', 'Listado Perfiles')
            @slot('class_bt', 'info')
            @slot('route', route('profiles.index'))
            @slot('icon', $icon_menus['profile'])
        @endcomponent
        @component('elements.buttons.dropdown')
            @slot('title', 'Listado Roles')
            @slot('class_bt', 'info')
            @slot('route', route('rols.index'))
        @endcomponent
    @endslot
@endcomponent --}}

@php $previus_id = (($ingreso->id-1)>0) ? $ingreso->id-1 : 1 ; @endphp

@component('elements.buttons.default')
    @slot('title', 'Ir atrás')
    @slot('class_bt', 'danger')
    @slot('route', route('administracion.ingresos.edit',['id'=> $previus_id]))
    @slot('icon', 'fas fa-chevron-left')
@endcomponent

@php $next_id = $ingreso->id + 1 ; @endphp

@component('elements.buttons.default')
    @slot('title', 'Siguiente')
    @slot('class_bt', 'success')
    @slot('route', route('administracion.ingresos.edit',['id'=> $next_id]))
    @slot('icon', 'fas fa-chevron-right')
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Refrescar la página')
    @slot('class_bt', 'dark')
    @slot('route', url()->current())
    @slot('icon', 'fas fa-redo')
@endcomponent