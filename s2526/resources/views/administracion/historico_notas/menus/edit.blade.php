@component('elements.buttons.default')
    @slot('title', 'Crear nuevo Histórico de Nota')
    @slot('class_bt', 'primary')
    @slot('route', route('administracion.historico_notas.index'))
    @slot('icon', $icon_menus['nuevo'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Formato Certificación de Notas')
    @slot('class_bt', 'light')
    @slot('route', route('administracion.historico_notas.crud'))
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
