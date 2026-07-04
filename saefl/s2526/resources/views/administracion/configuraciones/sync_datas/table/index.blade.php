@php
    $class_N="d-none d-sm-table-cell";
    $class_fecha="";
    $class_hora="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_fecha }}">Fecha</th>
                <th class="{{ $class_hora }}">Hora</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($sync_datas as $sync_data)

            <tr data-id="{{$sync_data->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_code_sm  ?? ''}}">
                    {{$sync_data->created_at->format('d-m-Y')}}
                </td>
                <td class="{{ $class_user  ?? ''}}">
                    {{$sync_data->created_at->format('hh-mm')}}
                </td>
            </tr>
        @endforeach

        </tbody>

    </table>

{{-- </div> --}}

@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/datatable/default.js") }}"></script>
@endsection
