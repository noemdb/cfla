@component('elements.buttons.default')
    @slot('title', 'Crear Promociones')
    @slot('class_bt', 'primary')
    @slot('route', route('administracion.registro_titulos.create'))
    @slot('icon', $icon_menus['nuevo'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Listado de Promociones registradas')
    @slot('class_bt', 'light')
    @slot('route', route('administracion.registro_titulos.crud'))
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
    {{-- @slot('route', url()->full()) --}}
    @slot('icon', 'fas fa-redo')
@endcomponent
