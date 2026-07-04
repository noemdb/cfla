@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_card')
    @slot('title','Resumen')
    {{-- @slot('subtitle',$estudiant->fullname.' ['.$estudiant->ci_estudiant.']') --}}
    @slot('close',true)
    @slot('size','modal-md')
    @slot('body')
        @include('administracion.estudiants.deck.card.modal.estudiant')
    @endslot
@endcomponent
