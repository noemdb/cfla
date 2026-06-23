@component('elements.buttons.default')
    @slot('title', 'Listado de Promociones registradas')
    @slot('class_bt', 'light')
    @slot('route', route('administracion.registro_titulos.crud'))
    @slot('icon', $icon_menus['crud'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Gestionar Constacias/Títulos')
    @slot('class_bt', 'light')
    @slot('route', route('administracion.registro_titulos.index'))
    @slot('icon', $icon_menus['registro_titulos'])
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
