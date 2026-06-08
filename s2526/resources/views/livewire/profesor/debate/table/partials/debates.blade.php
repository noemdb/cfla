@includeWhen($modeCreatorDebate, 'livewire.profesor.debate.overlay.createDebate')

<div class="font-weight-bold">Debates</div>
<ul class="nav nav-tabs nav-fill" id="myTab{{$item->id}}" role="tablist">
    @foreach ($debates as $subItem)
    @php $active = ($debate_id && $subItem->id == $debate_id) || (!$debate_id && $loop->first) ? 'active' : null; @endphp
    <li class="nav-item" title="{{$subItem->name}}">
        <a class="nav-link px-2 pb-3 font-weight-bold {{ $active }}" id="nav-tab-{{$subItem->id}}"
            data-toggle="tab" href="#contentTab{{$subItem->id}}" role="tab" aria-controls="nav-tab"
            aria-selected="true">
            {{$loop->iteration}}.- {{Str::limit($subItem->name,30,'...')}}
        </a>
    </li>
    @endforeach
</ul>
<div class="tab-content border border-top-0" id="myTabContent">

    @forelse ($debates as $subItem)
    @php $questions = $subItem->questions; @endphp
    @php $active = ($debate_id && $subItem->id == $debate_id) || (!$debate_id && $loop->first) ? 'show active' : null; @endphp

    <div class="tab-pane pt-2 px-2 fade table-light {{ $active }}" id="contentTab{{$subItem->id}}" role="tabpanel" aria-labelledby="nav-tab-{{$subItem->id}}">

        <div class="">
            <div class="px-2 font-weight-bold border-bottom" role="alert">
                <div class="d-flex pt-2">
                    <div class="flex-grow-1">
                        <div><span class="text-uppercase">{{$subItem->description}}</span> <span class="text-muted small">{{$subItem->token}}</span></div>
                        <div class="d-flex text-muted">
                            @php $inscription = ($subItem->grado) ? $subItem->grado->name .' '.$subItem->seccion->name: null ; @endphp
                            <div class="px-2">{{$inscription ?? null}}</div>
                            <div class="px-2">Cantidad máxima de preguntas por categoría: {{$subItem->question_max}}
                            </div>
                        </div>
                    </div>
                    <div class="text-right p-1">

                        @php $disabled = ($questions->count() > 0) ? true : false @endphp
                        @php $id = 'dropdownMenuButtonDebate'.$subItem->id @endphp
                        <div class="btn-group dropleft">
                            
                            <button class="btn btn-light dropdown-toggle border" type="button" id="{{$id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>                            
                            </button>
                            
                            <div class="dropdown-menu" aria-labelledby="{{$id}}">
                                <a class="dropdown-item" href="#" wire:click="setEditDebate({{$subItem->id}})"><i class="{{$icon_menus['edit'] ?? ''}} p-1" aria-hidden="true"></i> Editar</a>
                                <a class="dropdown-item {{ ($disabled) ? 'disabled' :null }}" href="#" wire:click="deleteDebate({{$subItem->id}})"><i class="{{$icon_menus['eliminar'] ?? ''}} p-1" aria-hidden="true"></i> Eliminar</a>
                                <a class="dropdown-item" href="#" wire:click="setCreateQuestion({{$subItem->id}})"><i class="{{$icon_menus['nuevo'] ?? ''}} p-1" aria-hidden="true"></i> Registrar Preguntas</a>
                            </div>
                            
                        </div>

                    </div>

                </div>
            </div>

            <div class="card-body p-2">

                @php $questions = $subItem->questions; @endphp
                @include('livewire.profesor.debate.table.partials.questions',['questions'=>$questions])

                {{-- @include('livewire.profesor.debate.table.partials.questions',['questions'=>$questions]) --}}

            </div>

        </div>

    </div>

    @empty

    <div>No hay debates registrados</div>

    @endforelse

</div>