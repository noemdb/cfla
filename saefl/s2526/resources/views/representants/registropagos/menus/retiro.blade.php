@component('elements.buttons.default')
    @slot('title', 'Inscripciones')
    @slot('class_bt', 'primary')
    @slot('route', route('representants.inscripciones.index'))
    @slot('icon', $icon_menus['inscripciones'])
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
