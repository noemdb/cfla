@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_details_edescriptivas_'.$estudiant->id)
    @slot('title','Detalles de las evaluaciones descriptivas')
    @slot('close',true)
    @slot('size','modal-xl')
    @slot('body')
        @php $edescriptivas = $estudiant->edescriptivas->sortBy('lapso_id'); @endphp
        @include('administracion.edescriptivas.partials.resume')
    @endslot
@endcomponent
