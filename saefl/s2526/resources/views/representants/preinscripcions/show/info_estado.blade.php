<!-- Button trigger modal -->
<button type="button" class="btn btn-light btn-sm" title="Estado" data-toggle="modal" data-target="#modal_preinscripsion_estado">
    <i class="{{$icon_menus['info'] ?? ''}}" aria-hidden="true"></i>
</button>

<!-- Modal -->
@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_preinscripsion_estado')
    @slot('title','Estado de la preinscripción')
    @slot('close',true)
    @slot('size','modal-md')
    @slot('body')
        {{-- <i class="fa fa-window-close-o" aria-hidden="true"></i> --}}
        <ul class="list-group font-weight-bold text-left">
            <li class="list-group-item  list-group-item-primary text-center">Preinscripción</li>
            <li class="list-group-item  text-dark">Registro en línea <span class=" float-right badge badge-success p-2"><i class="fa fa-check" aria-hidden="true"></i> </span></li>
            <li class="list-group-item  text-dark">Partida de Nacimineto del Estudiante <span class="float-right badge badge-danger p-2">X</span></li>
            <li class="list-group-item  text-dark">Foto tipo carnet del Estudiante <span class="float-right badge badge-danger p-2">X</span></li>
            <li class="list-group-item  text-dark">Cédula de Identidad del Estudiante <span class="float-right badge badge-danger p-2">X</span></li>
            <li class="list-group-item  text-dark">Planilla  de inscripción <span class="float-right badge badge-danger p-2">X</span></li>
            <li class="list-group-item  text-dark">Ficha del Estudiante <span class="float-right badge badge-danger p-2">X</span></li>
            <li class="list-group-item  text-dark">Cédula de Identidad del Representante <span class="float-right badge badge-danger p-2">X</span></li>
            <li class="list-group-item  text-dark">Foto tipo carnet del Representante <span class="float-right badge badge-danger p-2">X</span></li>
            <li class="list-group-item  text-dark">Ficha del Representante <span class="float-right badge badge-danger p-2">X</span></li>
            <li class="list-group-item  text-dark">Contrato de Servicio - Compromiso de Pago <span class="float-right badge badge-danger p-2">X</span></li>
            <li class="list-group-item  text-dark">Normas de Convivencia <span class="float-right badge badge-danger p-2">X</span></li>
        </ul>
    @endslot
@endcomponent

