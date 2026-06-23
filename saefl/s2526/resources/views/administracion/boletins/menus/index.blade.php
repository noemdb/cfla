@component('elements.buttons.default')
    @slot('title', 'Indicadores')
    @slot('class_bt', 'light')
    @slot('route', route('administracion.boletins.indicators'))
    @slot('icon', $icon_menus['chartbar'])
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
