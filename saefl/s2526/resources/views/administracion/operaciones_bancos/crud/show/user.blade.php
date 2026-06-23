@isset($user)

    <table class="table table-striped table-bordered {{-- table-sm  table-hover --}}">
      <tbody>
        <tr>

            <th scope="col">Usuario</th>

            <th scope="col">

                <span class="text-users-username-{{ $user->id  ?? ''}}">
                    {{$user->username}}
                </span>

            </th>
        </tr>
        <tr>
            <th scope="row">Email</th>
            <td>

                <span class="text-users-email-{{ $user->id  ?? ''}}">
                    {{$user->email}}
                </span>

            </td>
        </tr>
        <tr>
          <th scope="row">Estado</th>
          <td>

            <span class="text-users-is_active-{{ $user->id  ?? ''}} text-{{ $user->is_active }}">
                {{$user->is_active}}
            </span>

          </td>
        </tr>
        {{-- @if(Auth::user()->isAdmin()) --}}
            <tr>
                <th scope="row">Último Ingreso</th>
                <td>
                    @if(!empty($user->last_login_at) && $user->last_login_at !='')
                        {{ date_format(new DateTime($user->last_login_at), 'd-m-Y  h:i:s') }}
                    @endif
                </td>
            </tr>
            <tr>
                <th scope="row">Última Salída</th>
                <td>
                    @if(!empty($user->last_loginout_at) && $user->last_loginout_at !='')
                        {{ date_format(new DateTime($user->last_loginout_at), 'd-m-Y  h:i:s') }}
                    @endif
                </td>
            </tr>
            <tr>
                <th scope="row">Última IP</th>
                <td>{{$user->last_login_ip ?? ''}}</td>
            </tr>
        {{-- @endif --}}
        <tr>
            <th scope="row">Creado</th>
            <td>
                @if(isset($user->created_at))
                    {{$user->created_at->format('d-m-Y h:m:s')}}
                @endif
            </td>
        </tr>
        <tr>
            <th scope="row">Actualizado</th>
            <td>
                @if(isset($user->updated_at))
                    {{$user->updated_at->format('d-m-Y h:m:s')}}
                @endif
            </td>
        </tr>
      </tbody>
    </table>
@endisset