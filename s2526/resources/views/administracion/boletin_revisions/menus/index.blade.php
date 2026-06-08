@component('elements.buttons.default')
    @slot('title', 'Listado de Revisiones')
    @slot('class_bt', 'light')
    @slot('route', route('administracion.boletin_revisions.crud'))
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
