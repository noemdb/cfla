@isset($selectopt)


    <div class="card p-2 m-2">

      <div class="card-body pt-1">

        <table class="table table-striped table-bordered {{-- table-hover --}}">
          <tbody>
            <tr>
                <th scope="col">View</th>
                <th scope="col">
                    <span class="text-selectopts-view-{{ $selectopt->id  ?? ''}}">
                        {{$selectopt->view ?? ''}}
                    </span>
                </th>
            </tr>

            <tr>
                <th scope="col">Table</th>
                <th scope="col">
                    <span class="text-selectopts-table-{{ $selectopt->id  ?? ''}}">
                        {{$selectopt->table ?? ''}}
                    </span>
                </th>
            </tr>

            <tr>
                <th scope="row">Nombre</th>
                <td id="text-selectopts-name-{{ $selectopt->id  ?? ''}}">
                    {{$selectopt->name ?? ''}}  
                </td>
            </tr>

            <tr>
                <th scope="row">Valor</th>
                <td id="text-selectopts-value-{{ $selectopt->id  ?? ''}}" class="text-uppercase">
                    {{$selectopt->value ?? ''}}  
                </td>
            </tr>

            <tr>
                <th scope="row">Creado</th>
                <td>
                    @if(isset($selectopt->created_at))
                        {{$selectopt->created_at->format('d-m-Y h:m:s')}}
                    @endif
                </td>
            </tr>

            <tr>
                <th scope="row">Actualizado</th>
                <td>
                    @if(isset($selectopt->updated_at))
                        {{$selectopt->updated_at->format('d-m-Y h:m:s')}}
                    @endif
                </td>
            </tr>

          </tbody>

        </table>


      </div>
    </div>



@endisset