{{-- @component('elements.buttons.default')
    @slot('title', 'Registrar Grado')
    @slot('class_bt', 'primary')
    @slot('route', route('administracion.configuraciones.area_conocimientos.create'))
    @slot('icon', $icon_menus['nuevo'])
@endcomponent --}}

@php $id_modal = 'modal_edit_create'; @endphp
@php $title = 'Registrar Área de Conocimiento'; @endphp
<a title="{{$title ?? ''}}" class="btn btn-primary" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}" href="#"role="button">
    <i class="{{ $icon_menus['nuevo'] ?? ''}} fa-1x"></i>
</a>
@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id',$id_modal)
    @slot('title',$title)
    @slot('close',true)
    @slot('size','modal-lg')
    @slot('body')
        @include('administracion.configuraciones.area_conocimientos.partials.create')
    @endslot
@endcomponent

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
