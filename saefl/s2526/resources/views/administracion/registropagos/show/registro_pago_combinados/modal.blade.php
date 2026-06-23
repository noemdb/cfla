@php $representant = (!empty($registro_pago_combinado)) ? $registro_pago_combinado->representant->name.' ['.$registro_pago_combinado->representant->ci_representant.'] ':null; @endphp

@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_resume_registro_pago_combinado')
    @slot('title','Detalles del Registro de Pago Combinado')
    @slot('subtitle',$representant)
    @slot('close',true)
    @slot('size','modal-xl')
    @slot('body')
        @include('administracion.registropagos.show.registro_pago_combinados.resume')
    @endslot
@endcomponent
