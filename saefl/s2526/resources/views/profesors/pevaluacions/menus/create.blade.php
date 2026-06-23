@component('elements.buttons.default')
    @slot('title', 'Listado')
    @slot('class_bt', 'light')
    @slot('route', route('administracion.pevaluacions.crud'))
    @slot('icon', $icon_menus['crud'])
@endcomponent

@if (!empty($pevaluacion->id))
    @component('elements.buttons.default')
        @slot('title', 'Editar Plan de Evalaución')
        @slot('class_bt', 'warning')
        @slot('route', route('administracion.pevaluacions.edit',$pevaluacion->id))
        @slot('icon', $icon_menus['editar'])
    @endcomponent
@endif

@if (!empty($pevaluacion->id))
    @component('elements.buttons.default')
        @slot('title', 'Registro de notas para ésta asignatura')
        @slot('class_bt', 'primary')
        @slot('route', route('administracion.boletins.index',['grado_id'=>$grado_id,'seccion_id'=>$seccion_id,'lapso_id'=>$lapso_id,'pensum_id'=>$pensum_id]))
        @slot('icon', $icon_menus['notas'])
    @endcomponent
@endif

{{-- @component('elements.menus.dropdown')
    @slot('title', 'Listado relacionados')
    @slot('class', 'info')
    @slot('icon', $icon_menus['crud'])
    @slot('dropdown')
        @component('elements.buttons.dropdown')
            @slot('title', 'Listado Perfiles')
            @slot('class_bt', 'info')
            @slot('route', route('profiles.index'))
            @slot('icon', $icon_menus['profile'])
        @endcomponent
        @component('elements.buttons.dropdown')
            @slot('title', 'Listado Roles')
            @slot('class_bt', 'info')
            @slot('route', route('rols.index'))
        @endcomponent
    @endslot
@endcomponent --}}

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
