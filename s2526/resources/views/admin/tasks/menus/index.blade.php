@component('elements.buttons.default')
    @slot('title', 'Crear nueva Tarea')
    @slot('class_bt', 'primary')
    @slot('route', route('tasks.create'))
    @slot('icon', $icon_menus['nuevo'])
@endcomponent

@component('elements.menus.dropdown')
    @slot('title', 'CRUD relacionados')
    @slot('class', 'info dropleft')
    @slot('icon', $icon_menus['crud'])
    @slot('dropdown')
        {{-- @component('elements.buttons.dropdown')
            @slot('title', 'CRUD Tareas')
            @slot('class_bt', 'info')
            @slot('route', route('tasks.index'))
            @slot('icon', $icon_menus['task'])
        @endcomponent --}}
        @component('elements.buttons.dropdown')
            @slot('title', 'CRUD Alertas')
            @slot('class_bt', 'info')
            @slot('route', route('alerts.index'))
            @slot('icon', $icon_menus['alert'])
        @endcomponent
        @component('elements.buttons.dropdown')
            @slot('title', 'CRUD Mensajes')
            @slot('class_bt', 'info')
            @slot('route', route('messeges.index'))
            @slot('icon', $icon_menus['messege'])
        @endcomponent
    @endslot
@endcomponent

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