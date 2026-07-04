@component('elements.buttons.default')
    @slot('title', 'Listado de las Preinscripcines registradas')
    @slot('class_bt', 'light')
    @slot('route', route('representants.preinscripcions.crud'))
    @slot('icon', $icon_menus['crud'])
@endcomponent

{{-- @component('elements.menus.dropdown')
    @slot('title', 'Listado relacionados')
    @slot('class', 'info dropleft')
    @slot('icon', $icon_menus['crud'])
    @slot('dropdown')
        @component('elements.buttons.dropdown')
            @slot('title', 'Listado Tareas')
            @slot('class_bt', 'info')
            @slot('route', route('tasks.index'))
            @slot('icon', $icon_menus['task'])
        @endcomponent
        @component('elements.buttons.dropdown')
            @slot('title', 'Listado Mensajes')
            @slot('class_bt', 'info')
            @slot('route', route('messeges.index'))
            @slot('icon', $icon_menus['messege'])
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

