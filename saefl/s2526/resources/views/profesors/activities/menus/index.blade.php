@component('elements.buttons.default')
    @slot('title', 'Listado de Planes de Actividades')
    @slot('class_bt', 'light')
    @slot('route', route('profesors.activities.index'))
    @slot('icon', $icon_menus['activities'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Refrescar la página')
    @slot('class_bt', 'dark')
    @slot('route', url()->current())
    @slot('icon', 'fas fa-redo')
@endcomponent
