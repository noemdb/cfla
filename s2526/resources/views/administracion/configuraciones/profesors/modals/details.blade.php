<a title="Mostrar Plan de Estudio" class="btn btn-info" href="#" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}">
    <i class="{{ $icon_menus['show'] ?? ''}}" aria-hidden="true"></i>
</a>
@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id',$id_modal)
    @slot('title','Detalles del Profesor')
    @slot('close',true)
    @slot('size','modal-lg')
    @slot('body')
        @include('administracion.configuraciones.profesors.partials.details')
    @endslot
@endcomponent