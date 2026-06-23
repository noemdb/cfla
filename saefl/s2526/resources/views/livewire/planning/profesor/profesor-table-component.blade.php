<div>
    @php
        $class_N = 'd-none d-sm-table-cell';
        $class_profesor = '';
        $class_ci = '';
        $class_gsuite = '';
        $class_phone = '';
        $class_username = '';
        $class_action = 'nosort';
    @endphp

    <div class="row mb-3">
        <div class="col-md-3">
            <input type="text" wire:model.debounce.300ms="search" class="form-control" placeholder="Buscar profesor...">
        </div>
        <div class="col-md-3">
            <select wire:model="peducativo_id" class="form-control">
                <option value="">Plan Educativo (Todos)</option>
                @foreach($all_peducativos as $peducativo)
                    <option value="{{ $peducativo->id }}">{{ $peducativo->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select wire:model="filter_pevaluacions" class="form-control">
                <option value="">Carga Académica (Todas)</option>
                <option value="SI">Con Carga</option>
                <option value="NO">Sin Carga</option>
            </select>
        </div>
        <div class="col-md-3">
            <select wire:model="filter_activities" class="form-control">
                <option value="">P. Actividades (Todos)</option>
                <option value="SI">Con Actividades</option>
                <option value="NO">Sin Actividades</option>
            </select>
        </div>
    </div>

    <table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-default">
        <thead>
            <tr class="table-secondary">
                <th class="{{ $class_N }}" style="cursor: pointer;" wire:click="sortBy('id')">
                    N
                    @if($sortField === 'id') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                </th>

                <th class="{{ $class_profesor }}" style="cursor: pointer;" wire:click="sortBy('name')">
                    Nombre
                    @if($sortField === 'name') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                </th>
                <th class="{{ $class_profesor }}" style="cursor: pointer;" wire:click="sortBy('peducativo')">
                    Plan Educativo
                    @if($sortField === 'peducativo') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                </th>
                <th class="{{ $class_ci }} text-center" style="cursor: pointer;" wire:click="sortBy('pevaluacions_count')">
                    <div class="text-center" title="Carga académica">C.Académica
                        @if($sortField === 'pevaluacions_count') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </div>
                </th>
                <th class="{{ $class_profesor }} text-center" style="cursor: pointer;" wire:click="sortBy('activities_count')">
                    <span title="Plan de Actividades">P.Actividades
                        @if($sortField === 'activities_count') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </span><br>
                </th>
                <th class="{{ $class_username }}" style="cursor: pointer;" wire:click="sortBy('username')">
                    N.Usuario
                    @if($sortField === 'username') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                </th>
                <th class="{{ $class_gsuite }}" style="cursor: pointer;" wire:click="sortBy('email')">
                    Email/Teléfono
                    @if($sortField === 'email') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                </th>
                <th class="{{ $class_gsuite }}" style="cursor: pointer;" wire:click="sortBy('gsemail')">
                    GSuite
                    @if($sortField === 'gsemail') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                </th>
                <th class="{{ $class_action }} text-center">Acción</th>

            </tr>
        </thead>

        <tbody id="tdatos">
            @foreach ($profesors as $profesor)
                @php
                    $pevaluacions = $profesor->pevaluacions;
                    $activities = $profesor->activities;
                    $pevaluacions_count = !empty($profesor->pevaluacions->count())
                        ? $profesor->pevaluacions->count()
                        : null;
                @endphp

                <tr data-id="{{ $profesor->id }}">

                    <td class="{{ $class_N }}">
                        {{ $loop->iteration }}
                    </td>

                    <td class="{{ $class_profesor ?? '' }}">
                        {{ $profesor->fullname }} <br> <span
                            class="text-muted font-weight-bold">{{ $profesor->ci_profesor ?? '' }}</span>
                    </td>

                    <td class="{{ $class_N }}">
                        @php $peducativos = $profesor->peducativos; @endphp
                        <ul class="list-group list-group-flush">
                            @foreach ($peducativos as $item)
                                <li class="list-group-item">
                                    <div class="d-flex">
                                        <div class="flex-fill">{{ $item->name ?? null }}</div>
                                        <div><small class="small">[{{ $item->count ?? null }}]</small></div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </td>

                    <td class="{{ $class_ci ?? '' }} text-center">
                        @php $n= 0; @endphp
                        @foreach ($lapsos as $lapso)
                            @php $count = ( $pevaluacions->isNotEmpty()) ?$pevaluacions->where('lapso_id',$lapso->id)->count() : null  @endphp
                            <span class="badge badge-{{ $lapso->class ?? secondary }} p-2" style="font-size: 0.9rem">
                                {{ str_pad($count, 2, '0', STR_PAD_LEFT) ?? '00' }}
                            </span>
                        @endforeach
                    </td>

                    <td class="{{ $class_ci ?? '' }} text-center">
                        @php $n= 0; @endphp
                        @foreach ($lapsos as $lapso)
                            @php
                                $activities = $profesor->getActivitiesForLapso($lapso->id);
                                $count = $activities->count();
                            @endphp
                            <span class="badge badge-{{ $lapso->class ?? secondary }} p-2" style="font-size: 0.9rem">
                                {{ str_pad($count, 2, '0', STR_PAD_LEFT) ?? '00' }}
                            </span>
                        @endforeach
                    </td>

                    <td class="{{ $class_username ?? '' }}">
                        {{ $profesor->user ? $profesor->user->username : null }}
                    </td>

                    <td class="{{ $class_gsuite ?? '' }}">
                        {{ mb_strtolower($profesor->email) ?? '' }}<br>{{ $profesor->phone ?? '' }}
                    </td>

                    <td class="{{ $class_gsuite ?? '' }}">
                        {{ $profesor->gsemail ?? '' }}
                    </td>

                    <td class="{{ $class_action ?? '' }} text-center">
                        <button class="btn btn-sm btn-outline-primary"
                            onclick="window.livewire.emit('editProfesor', {{ $profesor->id }})"
                            title="Editar Profesor">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>

    <div class="mt-3 d-flex justify-content-end">
        {{ $profesors->links() }}
    </div>

</div>
