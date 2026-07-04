@component('elements.buttons.default')
    @slot('title', 'Historial Digital')
    @slot('class_bt', 'light')
    @slot('route', route('bienestars.estudiants.index'))
    @slot('icon', $icon_menus['crud'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Listado de Estudiantes')
    @slot('class_bt', 'success')
    @slot('route', route('bienestars.estudiants.crud'))
    @slot('icon', $icon_menus['estudiant'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Refrescar la página')
    @slot('class_bt', 'dark')
    @slot('route', url()->current())
    @slot('icon', 'fas fa-redo')
@endcomponent