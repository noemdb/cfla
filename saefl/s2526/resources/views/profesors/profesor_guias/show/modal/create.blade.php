@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_create_edescriptivas_'.$estudiant->id)
    @slot('title','Registrar/Actualizar Evaluación Descriptiva')
    @slot('close',true)
    @slot('size','modal-xl')
    @slot('body')
        @include('profesors.profesor_guias.partials.create')
    @endslot
@endcomponent
