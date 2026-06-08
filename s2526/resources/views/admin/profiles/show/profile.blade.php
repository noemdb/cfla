@isset($profile)

    <table class="table table-striped table-bordered {{-- table-sm table-hover --}}">
      {{-- <thead> --}}
      {{-- </thead> --}}
      <tbody>
        <tr>

            <th scope="col">Usuario</th>

            <th scope="col">

                <span class="text-user-username-{{ $profile->user_id  ?? ''}}">
                    {{ $profile->user->username ?? '' }}
                </span>

            </th>
        </tr>
        <tr>
            <th scope="row">Nombre</th>
            <td>

                <span class="text-profile-email-{{ $profile->id  ?? ''}}">
                    {{$profile->fullname ?? ''}}
                </span>

            </td>
        </tr>
        <tr>
            <th scope="row">Creado</th>
            <td>
                @if(isset($profile->created_at))
                    {{$profile->created_at->format('d-m-Y h:m:s')}}
                @endif
            </td>
        </tr>
        <tr>
            <th scope="row">Actualizado</th>
            <td>
                @if(isset($profile->updated_at))
                    {{$profile->updated_at->format('d-m-Y h:m:s')}}
                @endif
            </td>
        </tr>
      </tbody>
    </table>
@endisset