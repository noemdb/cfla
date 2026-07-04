@component('elements.buttons.default')
    @slot('title', 'Listado de las Entrevistas registradas')
    {{-- @slot('target', '_BLANK') --}}
    @slot('class_bt', 'light')
    @slot('route', route('bienestars.matriculations.catchments.index'))
    @slot('icon', $icon_menus['catchments'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Registro de Entrevistas Censo Escolar')
    @slot('target', '_BLANK')
    @slot('class_bt', 'info')
    @slot('route', route('catchments.interview'))
    @slot('icon', 'fas fa-id-card')
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Registro de Entrevistas Censo Escolar')
    @slot('target', '_BLANK')
    @slot('class_bt', 'primary')
    @slot('route', route('catchments.matriculations.censo'))
    @slot('icon', 'fas fa-sticky-note')
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Listado de las Entrevistas registradas')
    {{-- @slot('target', '_BLANK') --}}
    @slot('class_bt', 'success')
    @slot('route', route('bienestars.matriculations.interviews.index'))
    @slot('icon', 'fas fa-address-book')
@endcomponent