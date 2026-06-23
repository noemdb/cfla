@includeWhen($modeCreatorOption, 'livewire.profesor.debate.overlay.createOption')
<div class="text-muted font-weight-bold">Opciones registradas</div>
<ul class="list-group list-group-flush">
    @forelse ($options as $s3Item)
    <li class="list-group-item py-1">
        <div class="d-flex text-uppercase">
            <div class="flex-grow-1 {{ ($s3Item->status_option_correct) ? 'font-weight-bold text-success' : null}}">
                {{$loop->iteration}}. {{$s3Item->text}}
                @if ($s3Item->status_option_correct)
                    <span class="font-weight-bold small text-success">[Opción correcta]</span>
                @endif             
            </div>
            <div>
                <div class="btn-group">
                    <button title="Editar Opción" type="button" class="btn btn-success btn-sm"
                        wire:click="setEditOption({{$s3Item->id}})">
                        <i class="{{$icon_menus['edit'] ?? ''}}" style="cursor: pointer" aria-hidden="true"
                            role="button"></i>
                    </button>
                    @php $answers = $s3Item->answers; $disabled = ($answers->count() > 0) ? true : false @endphp
                    <button title="Eliminar Opción" type="button" {{ ($disabled) ? 'disabled' :null }}
                        class="btn btn-danger btn-sm" wire:click="deleteOption({{$s3Item->id}})">
                        <i class="{{$icon_menus['eliminar'] ?? ''}}" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </li>
    @empty
    <li class="list-group-item disabled">No hay opciones registradas</li>
    @endforelse
</ul>