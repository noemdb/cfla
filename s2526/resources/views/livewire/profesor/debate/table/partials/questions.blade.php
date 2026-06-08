@includeWhen($modeCreatorQuestion, 'livewire.profesor.debate.overlay.createQuestion')

<ul class="nav nav-tabs nav-fill" id="myTabQuestion{{$subItem->id}}" role="tablist">
    @foreach ($questions as $s2Item)
    @php $active = ($question_id && $s2Item->id == $question_id) || (!$question_id && $loop->first) ? 'active' : null; @endphp
    <li class="nav-item" title="{{$s2Item->text}}">
        <a class="nav-link px-2 pb-3 font-weight-bold {{ $active }}" id="nav-tab-{{$s2Item->id}}"
            data-toggle="tab" href="#contentQuestionTab{{$s2Item->id}}" role="tab" aria-controls="nav-tab" aria-selected="true">
            {{$loop->iteration}}. {{Str::limit($s2Item->text,15,'...')}}          
        </a>
    </li>
    @endforeach
</ul>

<div class="tab-content border border-top-0" id="myTabQuestionContent{{$subItem->id}}">

    @forelse ($questions as $s2Item) 

        @php $active = ($question_id && $s2Item->id == $question_id) || (!$question_id && $loop->first) ? 'show active' : null; @endphp

        <div class="tab-pane pt-2 px-2 fade table-light {{$active}}" id="contentQuestionTab{{$s2Item->id}}" role="tabpanel" aria-labelledby="nav-tab-{{$s2Item->id}}">
            <div class="d-flex mt-2 pt-2">
                <div class="flex-grow-1 text-uppercase">
                    {{$loop->iteration}}. {{$s2Item->text}}
                    <div class="pl-2 text-muted">{{$s2Item->category}}</div>
                </div>
    
                <div>
                    @php $options = $s2Item->options; @endphp
                    @php $disabled = ($options->count() > 0) ? true : false @endphp
                    @php $id = 'dropdownMenuButtonQuestion'.$s2Item->id @endphp
                    <div class="btn-group dropleft">
                        <button class="btn btn-light dropdown-toggle border" type="button" id="{{$id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>                            
                        </button>
                        <div class="dropdown-menu" aria-labelledby="{{$id}}">
                            <a class="dropdown-item" href="#" wire:click="setEditQuestion({{$s2Item->id}})"><i class="{{$icon_menus['edit'] ?? ''}} p-1" aria-hidden="true"></i> Editar</a>
                            <a class="dropdown-item {{ ($disabled) ? 'disabled' :null }}" href="#" wire:click="deleteQuestion({{$s2Item->id}})"><i class="{{$icon_menus['eliminar'] ?? ''}} p-1" aria-hidden="true"></i> Eliminar</a>
                            <a class="dropdown-item" href="#" wire:click="setCreateOption({{$s2Item->id}})"><i class="{{$icon_menus['nuevo'] ?? ''}} p-1" aria-hidden="true"></i> Registrar Opción</a>
                        </div>
                    </div>
                    
                </div>
            </div>
    
            <div class="p-2">
                @php $options = $s2Item->options; @endphp
                @include('livewire.profesor.debate.table.partials.options',['options'=>$options])            
            </div>

        </div>
    @empty
        
    @endforelse   

</div>
