@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_pago_'.$prepago->id)
    {{-- @slot('id',$id_modal) --}}
    @slot('title','Procesando la notificación de pago - Registro de Pago')
    @slot('close',true)
    @slot('size','modal-xl')
    @slot('scrollable','modal-dialog-scrollable')
    @slot('body')
        @include('representants.prepagos.form.pago')
    @endslot
@endcomponent
