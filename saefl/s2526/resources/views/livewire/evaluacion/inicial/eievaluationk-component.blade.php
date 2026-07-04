<div>

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th>Profesor</th>
                <th>Inscripción</th>
                <th>Momento</th>
                <th>F.Inicial</th>
                <th>F.Final</th>
                <th>observaciones</th>
                <th>Justificación</th>
                <th>Actividaes</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($eievaluationks as $item)
                @php
                    $grado = $item->grado;
                    $seccion = $item->seccion;
                    $lapso = $item->lapso;
                @endphp
                <tr class="">
                    <td>{{ $item->profesor->fullname}}</td>
                    <td>{{ $grado->name ?? null}} {{ $seccion->name ?? null}}</td>
                    <td>{{ $lapso->name}}</td>
                    <td class="text-nowrap">{{ $item->finicial->format('d-m-Y')}}</td>
                    <td class="text-nowrap">{{ $item->ffinal->format('d-m-Y')}}</td>
                    <td>{{ $item->observaciones}}</td>
                    <td>{{ $item->justificacion}}</td>
                    <td>
                        <ul class="list-group">
                            @php $eievaluationps = $item->eievaluationps; @endphp
                            @forelse ($eievaluationps as $subItem)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div class="text-wrap">{{$loop->iteration}}.- {{$subItem->nombre_ninos}} <div>{{$subItem->aprendizaje_alcanzado}}</div></div>
                                    </div>
                                </li>
                            @empty
                            <li class="list-group-item disabled">No hay datos</li>
                            @endforelse
                        </ul>
                    </td>             

                    <td>
                        <div class="btn-group-vertical">

                            <a title="Formato Plan de Evaluación" class="btn btn-dark btn-sm mr-1" href="{{route('evaluacions.eievaluationks.format.index',$item->id)}}" role="button" target="_BLANK">
                                <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                            </a>

                            <a title="Gestionar Recomendaciones." class="btn btn-warning btn-sm mr-1" href="#" wire:click.prevent="showForm({{$item->id}})" wire:preserve-scroll role="button">
                                <i class="{{ $icon_menus['registrar'] ?? '' }} fa-1x"></i>
                            </a>

                        </div>

                    </td>
                </tr>

                @if ($item->recomendacion)
                    <tr>
                        <td colspan="9"> <strong>Recomendaciones:</strong>  {{$item->recomendacion}}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="9">Sin recomendación</td>
                    </tr>
                @endif

            @empty
                <tr>
                    <td colspan="9">No hay datos</td>
                </tr>
            @endforelse           
            
        </tbody>
    </table>

    @if($selectedEievaluationkId)
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
                    <h5 class="mb-0">Gestionar Recomendación. <small class="text-white-50">[Coord. de Evaluación]</small></h5>
                    <button type="button" class="close text-white" aria-label="Close" wire:click="cancelForm" style="opacity: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Cuerpo del modal -->
                <div class="p-4">
                    <div class="form-group">
                        <label for="recomendacionInput">Recomendación</label>
                        <textarea id="recomendacionInput" wire:model.defer="recomendacion"
                                class="form-control" rows="4" autofocus></textarea>
                        @error('recomendacion') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary mr-2" wire:click="cancelForm">Cancelar</button>
                        <button type="button" class="btn btn-primary" wire:click="saveRecomendacion">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
