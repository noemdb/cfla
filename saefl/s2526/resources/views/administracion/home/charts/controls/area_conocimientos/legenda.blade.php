<!-- Button trigger modal -->
<button type="button" class="btn btn-light btn-sm" title="Legenda" data-toggle="modal" data-target="#modal_area_conocimientos">
    <i class="{{$icon_menus['legenda'] ?? ''}}" aria-hidden="true"></i>
</button>

<!-- Modal -->
@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_area_conocimientos')
    @slot('title','AREAS DE CONOCIMIENTOS - Legenda')
    @slot('close',true)
    @slot('size','modal-lg')
    @slot('body')
        @include('administracion.configuraciones.area_conocimientos.partials.legenda.main')
    @endslot
@endcomponent
