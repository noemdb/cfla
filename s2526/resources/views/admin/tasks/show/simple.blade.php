@isset($task)

    <div class="card bd-callout bd-callout-{{ $task->tipo ?? '' }} p-2 m-2">

      <div class="card-body pt-1">

        <table class="table table-striped table-bordered {{-- table-hover --}}">
          <tbody>
            <tr>

                <th scope="col">Usuario</th>

                <th scope="col">

                    <span class="text-users-username-{{ $task->user->id  ?? ''}}">
                        {{$task->user->username ?? ''}}
                    </span>

                </th>
            </tr>
            <tr>
                <th scope="row">Código</th>
                <td id="text-tasks-codigo-{{ $task->id  ?? ''}}">
                    {{$task->codigo ?? ''}}  
                </td>
            </tr>

            <tr>
                <th scope="row">Descripcion</th>
                <td id="text-tasks-descripcion-{{ $task->id  ?? ''}}">
                    {{$task->descripcion ?? ''}}  
                </td>
            </tr>

            {{-- 
            <tr>
                <th scope="row">Tipo</th>
                <td id="text-tasks-tipo-{{ $task->id  ?? ''}}">
                    {{$task->tipo ?? ''}}  
                </td>
            </tr> 
            --}}

            <tr>
                <th scope="row">Evento</th>
                <td id="text-tasks-evento-{{ $task->id  ?? ''}}">
                    {{$task->evento ?? ''}}  
                </td>
            </tr>

            <tr>
                <th scope="row">Estado</th>
                <td id="text-tasks-estado-{{ $task->id  ?? ''}}" class="text-uppercase text-{{$task->class  ?? 'secondary'}}">
                    {{$task->estado ?? ''}}  
                </td>
            </tr>

            <tr>
                <th scope="row">Fecha Inicial</th>
                <td id="text-tasks-finicial-{{ $task->id  ?? ''}}">
                    @if(isset($task->finicial))
                        {{ (isset($task->finicial)) ? Carbon\Carbon::parse($task->finicial)->format('d-m-Y') : '' }}
                    @endif
                </td>
            </tr>

            <tr>
                <th scope="row">Fecha Final</th>
                <td id="text-tasks-ffinal-{{ $task->id  ?? ''}}">
                    @if(isset($task->ffinal))
                        {{ (isset($task->ffinal)) ? Carbon\Carbon::parse($task->ffinal)->format('d-m-Y') : '' }}
                    @endif
                </td>
            </tr>

            <tr>
                <th scope="row">Creado</th>
                <td>
                    @if(isset($task->created_at))
                        {{$task->created_at->format('d-m-Y h:m:s')}}
                    @endif
                </td>
            </tr>

            <tr>
                <th scope="row">Actualizado</th>
                <td>
                    @if(isset($task->updated_at))
                        {{$task->updated_at->format('d-m-Y h:m:s')}}
                    @endif
                </td>
            </tr>

          </tbody>

        </table>


      </div>
    </div>



@endisset