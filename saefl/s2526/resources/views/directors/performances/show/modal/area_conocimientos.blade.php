@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_area_conocimientos')
    @slot('title','Histórico de pagos')
    {{-- @slot('subtitle',$representant->name.' ['.$representant->ci_representant.']') --}}
    @slot('close',true)
    @slot('size','modal-lg')
    @slot('body')
    123
        {{-- @include('administracion.representants.table.modal.historico') --}}
    @endslot
@endcomponent
