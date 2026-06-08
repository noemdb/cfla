<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th>N°</th>
            <th>{{$list_comment['profesor_id']}}</th>
            <th>Inscripción</th>
            <th>{{$list_comment['finicial']}}</th>
            <th>{{$list_comment['ffinal']}}</th>
            <th>{{$list_comment['tiempo_ejecucion']}}</th>
            <th>{{$list_comment['diagnostico']}}</th>
            <th>T.Resumen</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($eiplanningbwks as $item)
            <tr class="{{ ($item->id == $eiplanningbwk_id) ? 'table-secondary' : null}}">
                <td>
                    {{ $loop->iteration}}
                    
                    @php $show = ($modeEdit && $item->id == $eiplanningbwk_id) ? true : false @endphp
                    @includeWhen($show, 'livewire.inicial.overlay.eiplanningbwk.edit')

                    {{-- @php $show = ($modeReview && $item->id == $eiplanningbwk_id) ? true : false @endphp --}}
                    {{-- @includeWhen($show, 'livewire.inicial.overlay.eiprojectk.review') --}}

                    {{-- @php $show = ($modeSummary && $item->id == $eiplanningbwk_id) ? true : false @endphp --}}
                    {{-- @includeWhen($show, 'livewire.inicial.overlay.eiprojectk.summary') --}}

                    @php $show = ($modeEditSummary && $item->id == $eiplanningbwk_id) ? true : false @endphp
                    @includeWhen($show, 'livewire.inicial.overlay.eiplanningbwk.editSummary')

                    @php $show = ($modeModeSummary && $item->id == $eiplanningbwk_id) ? true : false @endphp
                    @includeWhen($show, 'livewire.inicial.overlay.eiplanningbwk.summary')


                </td>
                <td>{{ $item->profesor->fullname}}</td>
                <td>{{ $item->grado->name}} {{ $item->seccion->name}}</td>
                <td>{{ $item->finicial}}</td>
                <td>{{ $item->ffinal}}</td>
                <td>{{ $item->tiempo_ejecucion}}</td>
                <td>{{ $item->diagnostico}}</td>
                <td>
                    <ul class="list-group">
                        @php $eiplanningbwsummaries = $item->eiplanningbwsummaries; @endphp
                        @forelse ($eiplanningbwsummaries as $subItem)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div class="">{{$loop->iteration}}.- {{$subItem->objetivo}}</div>
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
                    <div class="btn-group-vertical">
                    {{-- <div class="d-flex justify-content-start"> --}}

                        <a title="Editar plan semanal" class="btn btn-warning btn-sm mr-1" href="#" wire:click.prevent="edit({{$item->id}})" role="button">
                            <i class="{{ $icon_menus['edit'] ?? '' }} fa-1x"></i>
                        </a>

                        <a title="Eliminar Plan de Semmanal" class="btn btn-danger btn-sm mr-1" href="#" wire:click.prevent="delete({{$item->id}})" role="button">
                            <i class="{{ $icon_menus['eliminar'] ?? '' }} fa-1x"></i>
                        </a>

                        <a title="Agregar Items a la tabla resumen" class="btn btn-info btn-sm mr-1" href="#" wire:click.prevent="setModeSummary({{$item->id}})" role="button">
                            <i class="{{ $icon_menus['nuevo'] ?? '' }} fa-1x"></i>
                        </a>
                        <a title="Formato Planificación semanal" class="btn btn-dark btn-sm mr-1" href="{{route('inicials.eiplanningbwks.format.index',$item->id)}}" role="button" target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>

                    {{-- </div> --}}
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