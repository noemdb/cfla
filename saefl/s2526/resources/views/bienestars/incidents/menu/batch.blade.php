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
    @slot('title', 'Fichas de Estudiantes')
    @slot('class_bt', 'info')
    @slot('route', route('bienestars.student_records.index'))
    @slot('icon', 'fas fa-id-card')
@endcomponent

{{-- @component('elements.buttons.default')
    @slot('title', 'Refrescar la página')
    @slot('class_bt', 'dark')
    @slot('route', url()->current())
    @slot('icon', 'fas fa-redo')
@endcomponent --}}