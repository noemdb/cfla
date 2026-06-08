@php
    $pestudio_id = (!empty($pestudio->id)) ? $pestudio->id:null;
    $grado_id = (!empty($grado->id)) ? $grado->id:null;
    $id_modal = (!empty($id_modal)) ? $id_modal:'id_modal';
    $title = (!empty($title)) ? $title:'Agregar Asiginatuta al Pensum';
@endphp
<a title="{{$id_modal}}"
    href="{{ route('administracion.configuraciones.pensums.create',['pestudio_id'=>$pestudio_id,'grado_id'=>$grado_id]) }}"
    class="btn btn-outline-primary btn-sm" 
    href="#" data-toggle="modal" data-target="#{{$id_modal}}"
    role="button">
    <i class="fa fa-plus" aria-hidden="true"></i>
</a>

@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id',$id_modal)
    @slot('title',$title)
    @slot('close',true)
    @slot('size','modal-md')
    @slot('body')
        @includeif('administracion.configuraciones.pensums.partials.create')
    @endslot
@endcomponent