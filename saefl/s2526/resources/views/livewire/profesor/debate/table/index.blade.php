<table class="table table-sm small">
    <thead>
        <tr>
            <th>N°</th>
            <th>Nombre</th>
            <th>Fecha</th>
            <th>Token</th>
            <th>Descripción</th>
            <th class="text-right">Acción</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($competitions as $item)
            <tr>
                <th rowspan="3" class="text-center align-middle" style="border-right: 0.1rem solid #dee2e6">
                    {{ $item->id }}</th>
                <td>{{ $item->name }}</td>
                <td class="text-nowrap">{{ $item->date }}</td>
                <td>
                    <div>{{ $item->token }}</div>
                    <div><a href="{{ route('general.educations.competitions.interactive.index', $item->token) }}"
                            target="_BLANK">Accede</a></div>
                </td>
                <td>{{ $item->description }}</td>
                <td class="text-right">
                    @php $id = 'dropdownMenuButtonCompetition'.$item->id @endphp
                    <div class="btn-group dropleft">
                        <button class="btn btn-light dropdown-toggle border" type="button" id="{{ $id }}"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="{{ $id }}">
                            <a class="dropdown-item" href="#" wire:click="setEdit({{ $item->id }})"><i
                                    class="{{ $icon_menus['edit'] ?? '' }} p-1" aria-hidden="true"></i> Editar</a>
                            @php $disabled = (! $item->status_delete ) ? 'disabled' : null @endphp
                            <a class="dropdown-item {{ $disabled }}" href="#"
                                wire:click="delete({{ $item->id }})"><i
                                    class="{{ $icon_menus['eliminar'] ?? '' }} p-1" aria-hidden="true"></i>
                                Eliminar</a>
                            <a class="dropdown-item" href="#" wire:click="setCreateGroup({{ $item->id }})"><i
                                    class="{{ $icon_menus['nuevo'] ?? '' }} p-1" aria-hidden="true"></i> Registrar
                                Grupo</a>
                            <a class="dropdown-item" href="#"
                                wire:click="setCreateDebate({{ $item->id }})"><i
                                    class="{{ $icon_menus['nuevo'] ?? '' }} p-1" aria-hidden="true"></i> Registrar
                                Debate</a>
                            <a class="dropdown-item" href="#" wire:click="aiCreateDebate({{ $item->id }})"><i
                                    class="{{ $icon_menus['info'] ?? '' }} p-1" aria-hidden="true"></i> Debate con
                                IA</a>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    {{-- <div>Grupos</div> --}}
                    @php $groups = $item->groups; @endphp
                    <div class="d-flex justify-content-center align-items-center">
                        @forelse ($groups as $subItem)
                            @php $answers = $subItem->answers; @endphp
                            <div class="flex-fill py-1 text-nowrap px-2">
                                <div class="d-flex justify-content-center">
                                    <div class="pr-2 text-center">
                                        <span class="font-weight-bold">{{ $subItem->name }}</span>
                                        <span class="">
                                            @php $disabled = ($answers->count() > 0) ? true : false @endphp
                                            @if ($answers->count() > 0)
                                                <i class="{{ $icon_menus['eliminar'] ?? '' }} p-1 text-dark"
                                                    aria-hidden="true"></i>
                                            @else
                                                <i wire:click="deleteGroup({{ $subItem->id }})"
                                                    class="{{ $icon_menus['eliminar'] ?? '' }} p-2 text-danger"
                                                    aria-hidden="true" style="cursor: pointer"></i>
                                            @endif
                                        </span>
                                    </div>

                                </div>
                            </div>
                        @empty
                            <div class="disabled">No hay grupos</div>
                        @endforelse
                    </div>
                </td>
            </tr>
            <tr style="border-bottom: 0.4rem solid #c5c5c5">
                <td colspan="5">
                    <div class="p-2">
                        @php $debates = $item->debates; @endphp
                        @include('livewire.profesor.debate.table.partials.debates', [
                            'debates' => $debates,
                        ])
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">Seleccione competición</td>
            </tr>
        @endforelse
    </tbody>
</table>
