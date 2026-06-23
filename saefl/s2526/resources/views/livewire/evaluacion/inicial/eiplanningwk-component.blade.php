<div>    
    
    <table width="100%" class="table table-sm small p-1 table-striped" id="table-data-default">
        <thead>
            <tr style="border-bottom: 0.2rem solid #c5c5c5">
                <th>N°</th>
                <th>Inscripción</th>
                <th>F.Inicial - F.Final</th>
                <th>T.Resumen</th>
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
            
                <tr class="{{ $loop->even ? 'table-active' : '' }}">
                    <th rowspan="2" class="{{ $loop->even ? 'bg-light' : '' }}">{{$loop->iteration}}</th>
                    <td colspan="8" class="{{ $loop->even ? 'bg-light' : '' }}">
                        <span class="font-weight-bold">Diagnóstico: </span>{{ $item->diagnostico}}
                        <div class="border-top mt-2">
                            <strong>Observación:</strong> 
                            @if (empty($item->observacion))
                                <span class="text-muted">No hay</span>
                            @else
                                <span>{{ucfirst(mb_strtolower($item->observacion, 'UTF-8'))}}</span>
                            @endif                            
                        </div>
                    </td>
                </tr>
                
                <tr style="border-bottom: 0.4rem solid #c5c5c5" class="{{ $loop->even ? 'table-active' : '' }}">                
                    <td class="{{ $loop->even ? 'bg-light' : '' }}">
                        <div class=" font-weight-bold">{{ $grado->name}} {{ $seccion->name}}</div>
                        
                        <div class="text-muted font-weight-bold">{{$profesor->fullname}}</div>                    

                    </td>

                    <td class="text-nowrap {{ $loop->even ? 'bg-light' : '' }}">{{ f_date($item->finicial)}} - {{ f_date($item->ffinal)}}</td>

                    <td class="{{ $loop->even ? 'bg-light' : '' }}">
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

                    <td class="{{ $loop->even ? 'bg-light' : '' }}">
                        <div class="btn-group-vertical">
                            <!-- Botón para ver estrategias -->
                            <button title="Ver Estrategias" class="btn btn-info btn-sm mr-1" 
                                    wire:click.prevent="viewStrategies({{ $item->id }})">
                                <i class="fas fa-table fa-1x"></i>
                            </button>

                            <a title="Formato Planificación Semanal" class="btn btn-dark btn-sm mr-1" 
                               href="{{ route('evaluacions.eiplanningwks.format.index', $item->id) }}" 
                               role="button" target="_BLANK">
                                <i class="{{ $icon_menus['pdf'] ?? 'fas fa-file-pdf' }} fa-1x"></i>
                            </a>

                            <a title="Gestionar Obs." class="btn btn-warning btn-sm mr-1" href="#" 
                               wire:click.prevent="showForm({{ $item->id }})" wire:preserve-scroll role="button">
                                <i class="{{ $icon_menus['registrar'] ?? 'fas fa-edit' }} fa-1x"></i>
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

    <!-- Modal para mostrar estrategias -->
    @if($showStrategiesModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            Estrategias del Docente - 
                            @if($selectedEiplanningwk)
                                {{ $selectedEiplanningwk->grado->name }} {{ $selectedEiplanningwk->seccion->name }} - 
                                {{ $selectedEiplanningwk->profesor->fullname }}
                            @endif
                        </h5>
                        <button type="button" class="close text-white" wire:click="closeStrategiesModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        @if($selectedEiplanningwk)
                            <!-- Tabla de Estrategias del Docente -->
                            <table class="table table-bordered table-sm" style="font-size:0.8rem;">
                                <thead>
                                    <tr>
                                        <th style="white-space: nowrap !important;">Momento de la Rutina Diaria</th>
                                        @foreach($selectedEiplanningwk->week_days as $day_key => $day_name)
                                            <th>{{ $day_name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedEiplanningwk->list_moment as $momento_key => $momento_name)
                                    <tr class="{{ $loop->even ? 'table-active' : '' }}">
                                        <td>{!! as_replace($momento_name) !!}</td>
                                        
                                        @foreach($selectedEiplanningwk->week_days as $day_key => $day_name)
                                            <td>
                                                @php
                                                    $estrategia = $selectedEiplanningwk->getStrategyByMomentAndDay($momento_key, $day_key);
                                                @endphp
                                                
                                                @if($estrategia)
                                                    {!! as_replace($estrategia->estrategia) !!}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeStrategiesModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

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