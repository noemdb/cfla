@isset($messege)

    <div class="card bd-callout bd-callout-{{ $messege->tipo ?? '' }} p-2 m-2">

      <div class="card-body pt-1">

        <table class="table table-striped table-bordered {{-- table-hover --}}">
          <tbody>
            <tr>

                <th scope="col">
                    Usuario
                    {{-- @include('admin.users.show.usercard') --}}
                    
                    @component('admin.users.show.usercard')
                        @slot('user', $messege->user)                                   
                    @endcomponent
                </th>

                <th scope="col">
                    Destino
                    {{-- @include('admin.users.show.usercard') --}}
                    @php ($duser = $user->getUserId($messege->destino_user_id))
                    @component('admin.users.show.usercard')
                        @slot('user', $duser)                                   
                    @endcomponent
                </th>
            </tr>
            {{-- <tr>
                <th scope="row">Destino</th>
                <td id="text-alerts-destino_user_id-{{ $messege->id  ?? ''}}">
                    @php ($dusername = $user->getUsernameId($messege->destino_user_id))
                    {{$dusername ?? ''}}  
                </td>
            </tr> --}}

            <tr>
                <th scope="row">Mensaje</th>
                <td id="text-alerts-mensaje-{{ $messege->id  ?? ''}}">
                    {{$messege->mensaje ?? ''}}  
                </td>
            </tr>

            <tr>
                <th scope="row">Tipo</th>
                <td id="text-alerts-tipo-{{ $messege->id  ?? ''}}" class="text-uppercase text-{{ $messege->TipClass ?? '' }}">
                    {{$messege->tipo ?? ''}}  
                </td>
            </tr>

            <tr>
                <th scope="row">Estado</th>
                <td id="text-alerts-estado-{{ $messege->id  ?? ''}}" class="text-uppercase text-{{$messege->class ?? 'secondary'}}">
                    {{$messege->estado ?? ''}}  
                </td>
            </tr>

            <tr>
                <th scope="row">Creado</th>
                <td>
                    @if(isset($messege->created_at))
                        {{$messege->created_at->format('d-m-Y h:m:s')}}
                    @endif
                </td>
            </tr>

            <tr>
                <th scope="row">Actualizado</th>
                <td>
                    @if(isset($messege->updated_at))
                        {{$messege->updated_at->format('d-m-Y h:m:s')}}
                    @endif
                </td>
            </tr>

          </tbody>

        </table>

      </div>

    </div>

@endisset