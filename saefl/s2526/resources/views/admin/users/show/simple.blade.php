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