@component('elements.buttons.default')
    @slot('title', 'Ver indicadores')
    @slot('class_bt', 'light')
    @slot('route', route('bienestars.incidents.summaries'))
    @slot('icon', $icon_menus['chartarea'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Informes semanales')
    @slot('class_bt', 'success')
    @slot('route', route('bienestars.incidents.overviews'))
    @slot('icon', $icon_menus['overviews'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Descripciones Tabuladas')
    @slot('class_bt', 'warning')
    @slot('route', route('bienestars.incidents_descriptions.index'))
    @slot('icon', $icon_menus['description'])
@endcomponent

{{-- @component('elements.buttons.default')
    @slot('title', 'Imprimir en lotes')
    @slot('class_bt', 'dark disabled')
    @slot('route', route('bienestars.student_records.batch'))
    @slot('icon', 'fa fa-file-pdf')
@endcomponent
 --}}
{{-- @component('elements.buttons.default')
    @slot('title', 'Refrescar la página')
    @slot('class_bt', 'dark')
    @slot('route', route('bienestars.incidents.index'))
    @slot('icon', 'fas fa-redo')
@endcomponent --}}
