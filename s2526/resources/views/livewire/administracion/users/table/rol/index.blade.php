@php
    $class_N="d-none d-md-block";
    $class_area="d-none d-lg-block";
    $class_name="";
    $class_finicial="";
    $class_ffinal="";
    $class_action="nosort";
@endphp

<table width="100%" class="table table-striped table-hover table-sm">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th>Usuario</th>
            <th class="{{$class_area}}">Área</th>
            <th>Rol</th>
            <th>Horario</th>
            <th class="hidden-xs hidden-sm">F.Inicial</th>
            <th class="hidden-xs hidden-sm">F.Final</th>
            <th align="right" class="{{$class_action}}"><strong>Aciones</strong></th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach($rols as $rol)

            @php $profile = $rol->profile ; @endphp

            <tr>

                <td class="{{$class_N}}">
                    {{ ($loop->iteration) }}
                </td>

                <td>
                    <span class="text-users-username-{{ $user->id ?? '' }} text-{{ $user->is_active ?? '' }}">
                        {{$user->username ?? ''}}
                    </span>
                </td>

                <td class="{{$class_area}}">
                    <span class="text-rols-area-{{$rol->id}} rol-{{ $rol->area ?? '' }}">
                        {{$rol->area}}
                    </span>
                </td>

                <td title="{{ $rol->descripcion ?? ''}} ">
                    <span class="text-rols-rol-{{$rol->id}} rol-{{ $rol->rol ?? '' }}">
                        {{$rol->rol}}
                    </span>
                </td>

                <td class="{{$class_area}}">
                    <span class="text-rols-area-{{$rol->id}} rol-{{ $rol->area ?? '' }}">
                        {{$rol->assit_schedule->name ?? ''}}
                    </span>
                </td>

                <td class="{{$class_finicial}}">
                    <span class="text-rols-finicial-{{$rol->id}}">
                        {{ (isset($rol->finicial)) ? Carbon\Carbon::parse($rol->finicial)->format('d-m-Y') : '' }}
                    </span>
                </td>

                <td class="{{$class_ffinal}}">
                    <span class="text-rols-ffinal-{{$rol->id}}">
                        {{ (isset($rol->ffinal)) ? Carbon\Carbon::parse($rol->ffinal)->format('d-m-Y') : '' }}
                    </span>
                </td>

                <td style="padding: 2px; vertical-align: middle;">

                    <div class="btn-group btn-group-sm">

                        <a wire:click="editRol({{$rol->id}})" title="Editar rol" class="btn btn-light btn-xs" href="#">
                            <i class="fas fa-pencil-alt"></i>
                        </a>

                    </div>

                </td>


            </tr>
        @endforeach
    </tbody>
</table>
