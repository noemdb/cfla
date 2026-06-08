@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_details_edescriptivas_'.$estudiant->id)
    @slot('title','Detalles de las evaluaciones descriptivas')
    @slot('close',true)
    @slot('size','modal-xl')
    @slot('body')
        @php $profesor_guias = $estudiant->profesor_guias->sortBy('lapso_id'); @endphp
        @include('profesors.profesor_guias.partials.resume')
    @endslot
@endcomponent
