@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id',$id_modal)
    @slot('title','Detalles del Registro de pago')
    @slot('close',true)
    @slot('size','modal-lg')
    @slot('body')
        @include('administracion.registropagos.table.list')
    @endslot
@endcomponent
