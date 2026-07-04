@component('elements.buttons.default')
    @slot('title', 'Ver indicadores')
    @slot('class_bt', 'light')
    @slot('route', route('bienestars.student_records.summaries'))
    @slot('icon', $icon_menus['chartarea'])
@endcomponent

{{-- @component('elements.buttons.default')
    @slot('title', 'Imprimir en lotes')
    @slot('class_bt', 'secondary')
    @slot('route', route('bienestars.student_records.batch'))
    @slot('icon', 'fa fa-file-pdf')
@endcomponent --}}

{{-- @component('elements.buttons.default')
    @slot('title', 'Refrescar la página')
    @slot('class_bt', 'dark')
    @slot('route', url()->current())
    @slot('icon', 'fas fa-redo')
@endcomponent --}}



{{-- @component('elements.buttons.default')
    @slot('title', 'Refrescar la página')
    @slot('class_bt', 'dark')
    @slot('route', url()->current())
    @slot('icon', 'fas fa-redo')
@endcomponent --}}