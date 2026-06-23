@php ($class_N="d-none d-sm-block")
@php ($class_user="")
@php ($class_name="")
@php ($class_state="")
@php ($class_action="nosort")

<table width="100%" class="table table-striped table-hover table-sm">
    <thead>
        <tr>
            {{-- <th class="{{ $class_N }}">N</th> --}}
            <th class="{{ $class_user }}">Usuario</th>
            <th class="{{ $class_name }}">Nombre</th>
            <th class="{{ $class_state }}">Estado</th>
            <th class="{{ $class_action }}">Aciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        <tr>

            <td class="{{ $class_user }}">
                <span class="text-users-username-{{ $user->id ?? '' }}">
                    {{$user->username ?? ''}}
                </span>
            </td>

            <td class="{{ $class_name }}">
                <span class="text-profiles-firstname-{{ $profile->id }}">{{$profile->firstname}}</span>
                <span class="text-profiles-lastname-{{ $profile->id }}">{{$profile->lastname}}</span>                
            </td>

            <td class="{{ $class_state }}">
                <span class="text-users-is_active-{{ $user->id ?? '' }} text-{{ $user->is_active ?? '' }}">
                    {{$user->is_active ?? ''}}
                </span>
            </td>

            <td class="{{ $class_action }}">
                
                <div class="btn-group btn-group-sm">

                    <a wire:click="editProfile({{$profile->id}})" title="Editar resgistro" class="btn btn-light btn-xs" href="#">
                        <i class="fas fa-pencil-alt"></i>
                    </a>

                </div>
                
            </td>
            
        </tr>
    
    </tbody>

</table>
