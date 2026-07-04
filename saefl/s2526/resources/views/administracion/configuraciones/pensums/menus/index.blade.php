@component('elements.buttons.default')
    @slot('title', 'Listado de Pensums Registrados')
    @slot('class_bt', 'light')
    @slot('route', route('administracion.configuraciones.pensums.crud'))
    @slot('icon', $icon_menus['crud'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Imprimir listado de los Pemsuns registrados')
    @slot('class_bt', 'danger btn-print')
    @slot('target', '_blank')
    @slot('data_url', route('administracion.configuraciones.pensums.pdf'))
    @slot('route', '#')
    @slot('icon', $icon_menus['pdf'])
@endcomponent

{{--
<button class="btn btn-light my-2 my-sm-0 btn-sm btn-toprint" id="btn_toprint" type="button">
    <i class="fa fa-print" aria-hidden="true"></i>
</button>
--}}

{{--
@component('elements.menus.dropdown')
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
@endcomponent
--}}

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
