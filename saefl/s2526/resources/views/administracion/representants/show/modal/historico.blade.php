@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_historico')
    @slot('title','Histórico de pagos')
    @slot('subtitle',$representant->name.' ['.$representant->ci_representant.']')
    @slot('close',true)
    @slot('size','modal-xl')
    @slot('body')
        @include('administracion.representants.table.modal.historico')
    @endslot
@endcomponent
