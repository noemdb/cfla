@component('elements.buttons.default')
    @slot('title', 'Listado de Lapsos')
    @slot('class_bt', 'light')
    @slot('route', route('administracion.configuraciones.lapsos.index'))
    @slot('icon', $icon_menus['crud'])
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
