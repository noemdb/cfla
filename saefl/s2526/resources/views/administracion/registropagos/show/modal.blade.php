@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_registropago')
    {{-- @slot('id',$id_modal) --}}
    @slot('title','Detalles del Registro de Pago Combinado')
    @slot('close',true)
    @slot('size','modal-xl')
    @slot('body')
        @include('administracion.representants.table.list')
    @endslot
@endcomponent
