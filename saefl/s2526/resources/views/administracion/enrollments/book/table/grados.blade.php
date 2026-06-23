<table class="table table-striped table-sm">
        <thead class="thead">
            <tr>
                <th>Descripción</th>
                {{-- <th>Pre-Inscritos</th> --}}
                <th>Inscritos</th>
                <th>Varones</th>
                <th>Hembras</th>
                <th>Retirados</th>
            </tr>
            </thead>
            <tbody>
                @php
                    $tot_inscritos = 0;
                    $tot_varones = 0;
                    $tot_hembras = 0;
                    $tot_retirados = 0;
                @endphp
                @foreach ($grados as $grado)
                    @php
                        $tot_inscritos = (!empty($grado->inscritos()->value)) ? ($grado->inscritos()->value + $tot_inscritos): $tot_inscritos ;
                        $tot_varones = (!empty($grado->varones()->value)) ? ($grado->varones()->value + $tot_varones): $tot_varones ;
                        $tot_hembras = (!empty($grado->hembras()->value)) ? ($grado->hembras()->value + $tot_hembras): $tot_hembras ;
                        $tot_retirados = (!empty($grado->retirados()->value)) ? ($grado->retirados()->value + $tot_retirados): $tot_retirados ;
                    @endphp
                    <tr>
                        <td scope="row">
                            {{$grado->name ?? ''}}<br>
                            <span class="text-muted">{{$grado->code ?? ''}}</span>
                        </td>
                        {{-- <td>{{$grado->preinscritos ?? ''}}</td> --}}
                        <td>{{$grado->inscritos()->value ?? ''}}</td>
                        <td>{{$grado->varones()->value ?? ''}}</td>
                        <td>{{$grado->hembras()->value ?? ''}}</td>
                        <td>{{$grado->retirados()->value ?? ''}}</td>
                    </tr>
                @endforeach
                <tr>
                    <th>TOTAL</th>
                    <th>{{$tot_inscritos ?? ''}}</th>
                    <th>{{$tot_varones ?? ''}}</th>
                    <th>{{$tot_hembras ?? ''}}</th>
                    <th>{{$tot_retirados ?? ''}}</th>
                </tr>
            </tbody>
    </table>

    <div class="col-md-3">
        <label for="user_id" class="form-label">Usuario</label>
        <div class="input-group">
            <input type="text"
                   class="form-control"
                   id="user_search"
                   placeholder="Buscar usuario..."
                   wire:model.debounce.300ms="userSearch">
            <select wire:model="user_id" class="form-select select2" id="user_id">
                <option value="">Todos los usuarios</option>
                @foreach($filteredUsers as $user)
                    <option value="{{ $user->id }}">{{ $user->username }}</option>
                @endforeach
            </select>
        </div>
    </div>
