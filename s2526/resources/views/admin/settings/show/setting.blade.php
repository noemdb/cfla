@isset($setting)


    <div class="card bd-callout bd-callout-{{ $setting->tipo ?? '' }} p-2 m-2">

      <div class="card-body pt-1">

        <table class="table table-striped table-bordered {{-- table-hover --}}">
          <tbody>
            <tr>

                <th scope="col">Usuario</th>

                <th scope="col">

                    <span class="text-users-username-{{ $setting->user->id  ?? ''}}">
                        {{$setting->user->username ?? ''}}
                    </span>

                </th>
            </tr>

            <tr>
                <th scope="row">Nombre</th>
                <td id="text-settings-name-{{ $setting->id  ?? ''}}">
                    {{$setting->name ?? ''}}  
                </td>
            </tr>

            <tr>
                <th scope="row">Valor</th>
                <td id="text-settings-value-{{ $setting->id  ?? ''}}" class="text-uppercase">
                    {{$setting->value ?? ''}}  
                </td>
            </tr>

            <tr>
                <th scope="row">Creado</th>
                <td>
                    @if(isset($setting->created_at))
                        {{$setting->created_at->format('d-m-Y h:m:s')}}
                    @endif
                </td>
            </tr>

            <tr>
                <th scope="row">Actualizado</th>
                <td>
                    @if(isset($setting->updated_at))
                        {{$setting->updated_at->format('d-m-Y h:m:s')}}
                    @endif
                </td>
            </tr>

          </tbody>

        </table>


      </div>
    </div>



@endisset