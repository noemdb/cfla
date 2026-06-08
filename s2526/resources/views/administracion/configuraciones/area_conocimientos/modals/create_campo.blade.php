@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id',$id_modal)
    @slot('title','Agregar asignatura al Área de Conocimiento')
    @slot('close',true)
    @slot('size','modal-xl')
    @slot('body')
        @include('administracion.configuraciones.area_conocimientos.partials.create_campo')
    @endslot
@endcomponent
