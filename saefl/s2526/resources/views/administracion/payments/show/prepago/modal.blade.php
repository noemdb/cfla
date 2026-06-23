@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_create_'.$mbancario->id)
    {{-- @slot('id',$id_modal) --}}
    @slot('title','Registrando Notificación de Pago')
    @slot('close',true)
    @slot('size','modal-lg')
    @slot('scrollable','modal-dialog-scrollable')
    @slot('body')
        @include('administracion.prepagos.form.create')
    @endslot
@endcomponent
