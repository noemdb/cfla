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
        
            <tr>
                <th rowspan="2" class="">

                    {{$loop->iteration}}
                    
                    @php $show = ($modeEdit && $item->id == $eiplanningwk_id) ? true : false @endphp
                    @includeWhen($show, 'livewire.inicial.overlay.eiplanningwk.edit')

                    @php $show = ($modeModeSummary && $item->id == $eiplanningwk_id) ? true : false @endphp
                    @includeWhen($show, 'livewire.inicial.overlay.eiplanningwk.summary')
                    
                    @php $show = ($modeEditSummary && $item->id == $eiplanningwk_id) ? true : false @endphp
                    @includeWhen($show, 'livewire.inicial.overlay.eiplanningwk.editSummary')
                    
                    @php $show = ($modeModeStrategy && $item->id == $eiplanningwk_id) ? true : false @endphp
                    @includeWhen($show, 'livewire.inicial.overlay.eiplanningwk.strategy')

                </th>
                <td colspan="8" class="">
                    <span class="font-weight-bold">Diagnóstico: </span>{{ $item->diagnostico}}
                    <div>
                        <strong>Observación</strong>
                        <small>[Coord. Evaluación]</small>: {{$item->observacion ?? null}}
                    </div>
                </td>
            </tr>
            
            <tr style="border-bottom: 0.4rem solid #c5c5c5">                
                <td>{{ $item->grado->name}} {{ $item->seccion->name}}</td>
                <td class="text-nowrap">{{ f_date($item->finicial)}} - {{ f_date($item->ffinal)}}</td>
                <td>
                    <ul class="list-group">
                        @php $eiplanningwsummaries = $item->getOrderedSummaries(); @endphp
                        @forelse ($eiplanningwsummaries as $subItem)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div class="" title="{{$subItem->objetivo}}">{{$subItem->order}}.- {{Str::limit($subItem->objetivo,30)}}</div>
                                    <div class="ml-2 text-nowrap">
                                        <i wire:click.prevent="setEditSummary({{$subItem->id}})" class="{{$icon_menus['edit'] ?? ''}} p-1 btn-light text-dark" style="cursor: pointer" aria-hidden="true"></i>
                                        <i wire:click.prevent="deleteSummary({{$subItem->id}})" class="{{$icon_menus['eliminar'] ?? ''}} p-1 btn-light text-danger" style="cursor: pointer" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </li>
                        @empty
                        <li class="list-group-item disabled">No hay datos</li>
                        @endforelse
                    </ul>
                </td>

                <td>
                    <ul class="list-group">
                        @php $eiplanningwstrategies = $item->getOrderedStrategies(); @endphp
                        @forelse ($eiplanningwstrategies as $subItem)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div class="text-nowrap">{{$subItem->order}}.- {{ $subItem->momento_rutina_diaria}}</div>
                                    <div class="ml-2 text-nowrap">
                                        <i wire:click.prevent="setEditStrategy({{$subItem->id}})" class="{{$icon_menus['edit'] ?? ''}} p-1 btn-light text-dark" style="cursor: pointer" aria-hidden="true"></i>
                                        <i wire:click.prevent="deleteStrategy({{$subItem->id}})" class="{{$icon_menus['eliminar'] ?? ''}} p-1 btn-light text-danger" style="cursor: pointer" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </li>
                        @empty
                        <li class="list-group-item disabled">No hay datos</li>
                        @endforelse
                    </ul>
                </td>

                <td>

                    <div class="btn-group-vertical">
                        <a title="Editar plan semanal" class="btn btn-warning btn-sm mr-1" href="#" wire:click.prevent="edit({{$item->id}})" wire:preserve-scroll role="button">
                            <i class="{{ $icon_menus['edit'] ?? '' }} fa-1x"></i>
                        </a>

                        <a title="Eliminar Plan de Semmanal" class="btn btn-danger btn-sm mr-1" href="#" wire:click.prevent="delete({{$item->id}})" role="button">
                            <i class="{{ $icon_menus['eliminar'] ?? '' }} fa-1x"></i>
                        </a>

                        <a title="Agregar Items a la tabla resumen" class="btn btn-info btn-sm mr-1" href="#" wire:click.prevent="setModeSummary({{$item->id}})" role="button">
                            <i class="{{ $icon_menus['nuevo'] ?? '' }} fa-1x"></i>
                        </a>
                        <a title="Agregar estrategias del docente" class="btn btn-success btn-sm mr-1" href="#" wire:click.prevent="setModeStrategy({{$item->id}})" role="button">
                            <i class="{{ $icon_menus['nuevo'] ?? '' }} fa-1x"></i>
                        </a>
                        <a title="Formato Planificación semanal" class="btn btn-dark btn-sm mr-1" href="{{route('inicials.eiplanningwks.format.index',$item->id)}}" role="button" target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
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