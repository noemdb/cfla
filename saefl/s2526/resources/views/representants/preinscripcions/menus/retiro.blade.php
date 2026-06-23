@component('elements.buttons.default')
    @slot('title', 'Preinscripciones')
    @slot('class_bt', 'primary')
    @slot('route', route('representants.preinscripcions.index'))
    @slot('icon', $icon_menus['preinscripcions'])
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
