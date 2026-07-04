@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_update_'.$inscripcion->id)
    {{-- @slot('id',$id_modal) --}}
    @slot('title','Asignar/Actilizar Grupo estable')
    @slot('close',true)
    @slot('size','modal-md')
    @slot('body')
        @include('representants.inscripciones.form.grupo_estable')
    @endslot
@endcomponent
