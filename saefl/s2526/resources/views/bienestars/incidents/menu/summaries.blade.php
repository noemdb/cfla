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

@component('elements.buttons.default')
    @slot('title', 'Incidentes de Estudiantes')
    @slot('class_bt', 'info')
    @slot('route', route('bienestars.incidents.index'))
    @slot('icon', $icon_menus['incidents'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Informes semanales')
    @slot('class_bt', 'success')
    @slot('route', route('bienestars.incidents.overviews'))
    @slot('icon', $icon_menus['overviews'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Descripciones Tabuladas')
    @slot('class_bt', 'warning')
    @slot('route', route('bienestars.incidents_descriptions.index'))
    @slot('icon', $icon_menus['description'])
@endcomponent


{{-- @component('elements.buttons.default')
    @slot('title', 'Refrescar la página')
    @slot('class_bt', 'dark')
    @slot('route', url()->current())
    @slot('icon', 'fas fa-redo')
@endcomponent --}}
