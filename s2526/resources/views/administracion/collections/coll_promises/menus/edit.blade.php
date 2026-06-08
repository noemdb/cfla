@component('elements.buttons.default')
    @slot('title', 'Página inicial')
    @slot('class_bt', 'ligth')
    @slot('route', route('administracion.collections.coll_politicals.index'))
    @slot('icon', $icon_menus['coll_politicals'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Listado')
    @slot('class_bt', 'light')
    @slot('route', route('administracion.collections.coll_promises.crud'))
    @slot('icon', $icon_menus['crud'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Registrar nuevas actividades de cobranza')
    @slot('class_bt', 'primary')
    @slot('route', route('administracion.collections.coll_promises.create'))
    @slot('icon', $icon_menus['nuevo'])
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
