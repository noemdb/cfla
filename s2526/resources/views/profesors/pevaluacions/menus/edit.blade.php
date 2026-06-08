@component('elements.buttons.default')
    @slot('title', 'Listado de Planes de Evalación')
    @slot('class_bt', 'light')
    @slot('route', route('profesors.pevaluacions.crud'))
    @slot('icon', $icon_menus['pevaluacion'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Listado de Evaluaciones')
    @slot('class_bt', 'light')
    {{-- @slot('route', route('profesors.pevaluacions.create',['grado_id'=>$grado->id,'seccion_id'=>$seccion->id,'pensum_id'=>$pensum->id,'lapso_id'=>$lapso->id])) --}}
    @slot('route', route('profesors.evaluacions.crud'))
    @slot('icon', $icon_menus['evaluacion'])
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
