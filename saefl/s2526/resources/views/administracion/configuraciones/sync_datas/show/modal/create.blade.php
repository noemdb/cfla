@php $id_modal = 'modal_descuento_create'; @endphp
<a title="Crear nuevo Descuento" class="btn btn-primary float-right" href="#" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}">
    <i class="fa fa-plus" aria-hidden="true"></i>
</a>
@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id',$id_modal)
    @slot('title','Crear nuevo Descuento')
    @slot('close',true)
    @slot('size','modal-lg')
    @slot('body')
        @include('administracion.configuraciones.descuentos.show.modal.partials.create')
    @endslot
@endcomponent
