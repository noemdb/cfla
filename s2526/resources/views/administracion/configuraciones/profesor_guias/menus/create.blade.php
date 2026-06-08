@component('elements.buttons.default')
    @slot('title', 'Pofesores Guía')
    @slot('class_bt', 'light')
    @slot('route', route('administracion.configuraciones.profesor_guias.index'))
    @slot('icon', $icon_menus['profesor_guia'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Listado Profesor Guía')
    @slot('class_bt', 'light')
    @slot('route', route('administracion.configuraciones.profesor_guias.crud'))
    @slot('icon', $icon_menus['crud'])
@endcomponent


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
    @slot('title', 'Ir atrás')
    @slot('class_bt', 'dark')
    @slot('route', url()->previous())
    @slot('icon', 'fas fa-chevron-left')
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Refrescar la página')
    @slot('class_bt', 'dark')
    @slot('route', url()->current())
    @slot('icon', 'fas fa-redo')
@endcomponent
