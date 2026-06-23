@component('elements.buttons.default')
    @slot('title', 'Registrar Abono')
    @slot('class_bt', 'primary')
    @slot('route', route('administracion.abonos.index'))
    @slot('icon', $icon_menus['nuevo'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Anterior')
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
