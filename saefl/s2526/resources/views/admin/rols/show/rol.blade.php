@isset($rol)

    <table class="table table-striped table-bordered {{-- table-sm table-hover --}}">
      <tbody>
        <tr>

            <th scope="col">Usuario</th>

            <th scope="col">

                <span class="text-users-username-{{ $user->id  ?? ''}}">
                    {{$user->username ?? ''}}
                </span>

            </th>
        </tr>
        <tr>
            <th scope="row">Rol</th>
            <td>
                <span class="text-rols-rol-{{ $rol->id  ?? ''}}">
                    {{$rol->rol}}
                </span>
            </td>
        </tr>
        <tr>
          <th scope="row">Área</th>
          <td>

            <span class="text-rols-area-{{ $rol->id  ?? ''}}">
                {{$rol->area}}
            </span>

          </td>
        </tr>
        <tr>
          <th scope="row">Descripción</th>
          <td>

            <span class="text-rols-descripcion-{{ $rol->id  ?? ''}}">
                {{$rol->descripcion}}
            </span>

          </td>
        </tr>
        <tr>
            <th scope="row">Fecha Inicial</th>
            <td>
                <span class="text-rols-finicial-{{ $rol->id  ?? ''}}">
                    @if(isset($rol->finicial))
                        {{ (isset($rol->finicial)) ? Carbon\Carbon::parse($rol->finicial)->format('d-m-Y') : '' }}
                        {{-- {{$rol->finicial->format('d-m-Y')}} --}}
                    @endif
                </span>
            </td>
        </tr>
        <tr>
            <th scope="row">Fecha Final</th>
            <td>
                <span class="text-rols-ffinal-{{ $rol->id  ?? ''}}">
                    @if(isset($rol->ffinal))
                        {{ (isset($rol->ffinal)) ? Carbon\Carbon::parse($rol->ffinal)->format('d-m-Y') : '' }}
                        {{-- {{$rol->ffinal->format('d-m-Y')}} --}}
                    @endif
                </span>
            </td>
        </tr>
        <tr>
            <th scope="row">Creado</th>
            <td>
                @if(isset($rol->created_at))
                    {{$rol->created_at->format('d-m-Y h:m:s')}}
                @endif
            </td>
        </tr>
        <tr>
            <th scope="row">Actualizado</th>
            <td>
                @if(isset($rol->updated_at))
                    {{$rol->updated_at->format('d-m-Y h:m:s')}}
                @endif
            </td>
        </tr>
{{--         <tr>
            <th scope="row" colspan="2">
                <a class="btn btn-dark w-100" href="{{ route('rols.edit',$rol->id)}}" role="button">
                    Actualizar
                    <i class="far fa-id-badge"></i>
                </a>
            </th>
        </tr> --}}
      </tbody>
    </table>

@endisset
