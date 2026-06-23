<div>
    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th>Profesor</th>
                <th>Inscripción</th>
                <th>F.Inicial</th>
                <th>F.Final</th>
                <th>Tiempo</th>
                <th>Justificación</th>
                <th>Observación</th>
                <th>T.Actividades</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($eispecialks as $item)
                <tr class="">
                    <td>{{ $item->profesor->fullname}}</td>
                    <td>{{ $item->grado->name}} {{ $item->seccion->name}}</td>
                    <td class=" text-nowrap">{{ f_date($item->finicial)}}</td>
                    <td class=" text-nowrap">{{ f_date($item->ffinal)}}</td>
                    <td>{{ $item->tiempo_ejecucion}} (Sem.)</td>
                    <td>{{ $item->justificacion}}</td>
                    <td>{{ $item->observacion}}</td>

                    <td>
                        <ul class="list-group">
                            @php $activities = $item->activities; @endphp
                            @forelse ($activities as $subItem)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div class="text-nowrap">{{$loop->iteration}}.- {{$subItem->aprendizaje_esperado}} <div>{{$subItem->indicadores}}</div></div>
                                    </div>
                                </li>
                            @empty
                            <li class="list-group-item disabled">No hay datos</li>
                            @endforelse
                        </ul>
                    </td>

                    <td>
                        <div class="btn-group-vertical">

                            <a title="Formato Plan Especial" class="btn btn-dark btn-sm mr-1" href="{{route('evaluacions.eispecialks.format.index',$item->id)}}" role="button" target="_BLANK">
                                <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                            </a>

                            <a title="Gestionar Obs." class="btn btn-warning btn-sm mr-1" href="#" wire:click.prevent="showForm({{$item->id}})" wire:preserve-scroll role="button">
                                <i class="{{ $icon_menus['registrar'] ?? '' }} fa-1x"></i>
                            </a>

                        </div>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No hay datos</td>
                </tr>
            @endforelse

        </tbody>
    </table>

    @if($selectedEispecialkId)
        <!-- Fondo semitransparente -->
        <div class="position-fixed w-100 h-100"
            style="top: 0; left: 0; background: rgba(0,0,0,0.5); z-index: 1050;">
        </div>

        <!-- Modal centrado -->
        <div class="position-fixed d-flex justify-content-center align-items-center w-100 h-100"
            style="top:0; left:0; z-index: 1060; padding: 1rem;">
            <div class="bg-white rounded shadow" style="max-width: 500px; width: 100%;">
                <!-- Header estilo barra -->
                <div class="bg-primary text-white px-3 py-2 rounded-top d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestionar Observación <small class="text-white-50">[Coord. de Evaluación]</small></h5>
                    <button type="button" class="close text-white" aria-label="Close" wire:click="cancelForm" style="opacity: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Cuerpo del modal -->
                <div class="p-4">
                    <div class="form-group">
                        <label for="observacionInput">Observación</label>
                        <textarea id="observacionInput" wire:model.defer="observacion"
                                class="form-control" rows="4" autofocus></textarea>
                        @error('observacion') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary mr-2" wire:click="cancelForm">Cancelar</button>
                        <button type="button" class="btn btn-primary" wire:click="saveObservacion">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
