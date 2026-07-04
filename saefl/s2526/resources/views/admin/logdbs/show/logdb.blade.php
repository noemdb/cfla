@isset($logdb)

    <div class="card p-2 m-2 bd-callout bd-callout-{{ $logdb->tipo ?? '' }}">

      <div class="card-body pt-1">

        <table class="table table-striped table-bordered {{-- table-hover --}}">
          <tbody>
            <tr>
                <th scope="col">Username</th>
                <th scope="col">
                    <span class="text-logdbs-user_id-{{ $logdb->id  ?? ''}}">
                        {{$logdb->user->username ?? ''}}
                    </span>
                </th>
            </tr>

            <tr>
                <th scope="col">Action</th>
                <th scope="col">
                    <span class="text-logdbs-action-{{ $logdb->id  ?? ''}}">
                        {{$logdb->action ?? ''}}
                    </span>
                </th>
            </tr>

            <tr>
                <th scope="col">Type</th>
                <th scope="col">
                    <span class="text-logdbs-tipo-{{ $logdb->id  ?? ''}} text text-{{$logdb->tipo ?? ''}} text-uppercase">
                        {{$logdb->tipo ?? ''}}
                    </span>
                </th>
            </tr>

            <tr>
                <th scope="row">Model Class</th>
                <td id="text-logdbs-model_class-{{ $logdb->id  ?? ''}}">
                    {{$logdb->model_class ?? ''}}  
                </td>
            </tr>

            <tr>
                <th scope="row">IP</th>
                <td id="text-logdbs-ip-{{ $logdb->id  ?? ''}}" class="text-uppercase">
                    {{$logdb->ip ?? ''}}  
                </td>
            </tr>

            <tr>
                <th scope="row">Data</th>
                <td id="text-logdbs-data-{{ $logdb->id  ?? ''}}">
                    {{$logdb->data ?? ''}}  
                </td>
            </tr>

            <tr>
                <th scope="row">PathInfo</th>
                <td id="text-logdbs-pathInfo-{{ $logdb->id  ?? ''}}">
                    {{$logdb->pathInfo ?? ''}}  
                </td>
            </tr>

            <tr>
                <th scope="row">URL</th>
                <td id="text-logdbs-url-{{ $logdb->id  ?? ''}}">
                    {{$logdb->url ?? ''}}  
                </td>
            </tr>

            <tr>
                <th scope="row">Creado</th>
                <td>
                    @if(isset($logdb->created_at))
                        {{$logdb->created_at->format('d-m-Y h:m:s')}}
                    @endif
                </td>
            </tr>

            {{--
            <tr>
                <th scope="row">Actualizado</th>
                <td>
                    @if(isset($logdb->updated_at))
                        {{$logdb->updated_at->format('d-m-Y h:m:s')}}
                    @endif
                </td>
            </tr> 
            --}}

          </tbody>

        </table>


      </div>
    </div>



@endisset