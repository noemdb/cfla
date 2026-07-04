@component('elements.buttons.default')
    @slot('title', 'Crear nueva')
    @slot('class_bt', 'primary')
    @slot('route', route('administracion.enrollments.create'))
    @slot('icon', $icon_menus['nuevo'])
@endcomponent
@component('elements.buttons.default')
    @slot('title', 'Imprimir formatos')
    @slot('class_bt', 'light')
    @slot('route', route('administracion.enrollments.crud'))
    @slot('icon', $icon_menus['enrollments'])
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
