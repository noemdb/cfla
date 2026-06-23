@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_registropagos')
    {{-- @slot('id',$id_modal) --}}
    @slot('title','Listado de Pagos Registrados al representante: '.$representant->name)
    @slot('close',true)
    @slot('size','modal-xl')
    @slot('body')
    {{-- /home/user/code/saefl/resources/views/administracion/registropagos/show/modal/partials/lists.blade.php --}}
        @include('administracion.registropagos.show.modal.partials.lists')
    @endslot
@endcomponent
