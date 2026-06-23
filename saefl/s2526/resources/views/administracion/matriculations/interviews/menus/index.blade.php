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

@component('elements.buttons.default')
    @slot('title', 'Manifestaciones de Interés')
    @slot('class_bt', 'primary')
    @slot('route', route('administracion.matriculations.catchments.index'))
    @slot('icon', 'fas fa-list')
@endcomponent
