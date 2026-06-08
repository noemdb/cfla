@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_creditoafavors_'.$creditoafavor->id)
    @slot('title','Detalles del crédito a favor')
    @slot('close',true)
    @slot('size','modal-lg')
    @slot('body')
        @include('administracion.creditoafavors.partial.detaill')
    @endslot
@endcomponent
