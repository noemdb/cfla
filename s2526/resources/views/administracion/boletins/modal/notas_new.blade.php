<!-- Button trigger modal -->
<button type="button" class="btn btn-outline-success" title="Crear/Actualizar Evaluación descriptiva" data-toggle="modal" data-target="#modal_ecualitativa_create_update">
    <i class="{{ $icon_menus['notas'] ?? '' }} "></i>
</button>

<!-- Modal -->
@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_ecualitativa_create_update')
    @slot('title','Crear/Actualizar Evaluación descriptiva')
    @slot('close',true)
    @slot('size','modal-xl')
    @slot('body')
        {{-- @include('administracion.boletins.form.ecualitativa.create') --}}
        {{ $notas_new ?? '' }}
    @endslot
@endcomponent
