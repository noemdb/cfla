@component('elements.buttons.default')
    @slot('title', 'Crear nueva')
    @slot('class_bt', 'primary')
    @slot('route', route('administracion.enrollments.create'))
    @slot('icon', $icon_menus['nuevo'])
@endcomponent
@component('elements.buttons.default')
    @slot('title', 'Lista')
    @slot('class_bt', 'ligth')
    @slot('route', route('administracion.enrollments.crud'))
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
