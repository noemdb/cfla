@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id',$id_modal)
    @slot('title','Historico de Pago de Representante')
    @slot('close',true)
    @slot('size','modal-lg')
    @slot('body')
        @include('administracion.representants.table.modal.historico')
    @endslot
@endcomponent
