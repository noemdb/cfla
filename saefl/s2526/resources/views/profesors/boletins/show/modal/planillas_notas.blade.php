@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_sabanafull_seccion_'.$seccion->id)
    @slot('title','Planilla Registro de Notas por lapsos y definitiva de: '.$seccion->grado->name.' '.$seccion->name)
    @slot('close',true)
    @slot('size','modal-xl')
    @slot('scrollable','modal-dialog-scrollable')
    @slot('body')
        @include('profesors.boletins.table.sabanafull')
    @endslot
@endcomponent
