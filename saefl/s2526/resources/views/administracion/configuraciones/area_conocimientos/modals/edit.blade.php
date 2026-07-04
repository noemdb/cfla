@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id',$id_modal)
    @slot('title','Actualizar Área de Conocimiento')
    @slot('close',true)
    @slot('size','modal-lg')
    @slot('body')
        @include('administracion.configuraciones.area_conocimientos.partials.edit')
    @endslot
@endcomponent
