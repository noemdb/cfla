@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id',$id_modal)
    @slot('title','Editar adscripción')
    @slot('close',true)
    @slot('size','modal-lg')
    @slot('body')
        @include('administracion.configuraciones.area_conocimientos.partials.edit_campo')
    @endslot
@endcomponent
