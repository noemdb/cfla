@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_abono_'.$prepago->id)
    {{-- @slot('id',$id_modal) --}}
    @slot('title','Procesando la notificación de pago - Registro de Abono')
    @slot('close',true)
    @slot('size','modal-xl')
    @slot('body')
        @include('administracion.prepagos.form.abono')
    @endslot
@endcomponent
