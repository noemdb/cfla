<div>    
    
    <table width="100%" class="table table-sm small p-1" id="table-data-default">
        <thead>
            <tr style="border-bottom: 0.2rem solid #c5c5c5">
                <th>N°</th>
                <th>Inscripción</th>
                <th>F.Inicial - F.Final</th>
                <th>T.Resumen</th>
                <th>Estrategias</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($eiplanningwks as $item)

                @php
                    $profesor = $item->profesor;
                    $grado = $item->grado;
                    $seccion = $item->seccion;
                @endphp
            
                <tr>
                    <th rowspan="2" class="">{{$loop->iteration}}</th>
                    <td colspan="8" class="">
                        <span class="font-weight-bold">Diagnóstico: </span>{{ $item->diagnostico}}
                        <div>
                            <strong>Observación:</strong> {{$item->observacion}}
                        </div>
                    </td>
                </tr>
                
                <tr style="border-bottom: 0.4rem solid #c5c5c5">                
                    <td>
                        <div class=" font-weight-bold">{{ $grado->name}} {{ $seccion->name}}</div>
                        
                        <div class="text-muted font-weight-bold">{{$profesor->fullname}}</div>                    

                    </td>

                    <td class="text-nowrap">{{ f_date($item->finicial)}} - {{ f_date($item->ffinal)}}</td>

                    <td>
                        <ul class="list-group">
                            @php $eiplanningwsummaries = $item->eiplanningwsummaries; @endphp
                            @forelse ($eiplanningwsummaries as $subItem)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div class="" title="{{$subItem->objetivo}}">{{$loop->iteration}}.- {{$subItem->objetivo}}</div>
                                    </div>
                                </li>
                            @empty
                            <li class="list-group-item disabled">No hay datos</li>
                            @endforelse
                        </ul>
                    </td>

                    <td>
                        <ul class="list-group">
                            @php $eiplanningwstrategies = $item->eiplanningwstrategies; @endphp
                            @forelse ($eiplanningwstrategies as $subItem)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div class="text-nowrap">{{$loop->iteration}}.- {{ $subItem->momento_rutina_diaria}}</div>
                                    </div>
                                </li>
                            @empty
                            <li class="list-group-item disabled">No hay datos</li>
                            @endforelse
                        </ul>
                    </td>

                    <td>

                        <div class="btn-group-vertical">
                            <a title="Formato Planificación Semanal" class="btn btn-dark btn-sm mr-1" href="{{route('evaluacions.eiplanningwks.format.index',$item->id)}}" role="button" target="_BLANK">
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

    @if($selectedEiplanningwkId)
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
