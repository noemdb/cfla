@forelse ($dates as $date)

    @php
    // $users = $user->getWorkersAttendance($date,$area);
    $users = $user->getWorkersSchedule($date,$assit_schedule_id);
    $table_id = 'table-data-default'.$loop->iteration;
    // $table_id = 'table-data-default';
    @endphp

        <div class="card pb-2">
            <div class="alert alert-primary pb-1 mb-1"><span class="font-weight-bold">{{f_date($date)}}</span></div>
            <div class="card-body py-1">
                <table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}">
                    <thead>
                        <tr>
                            <th>NOMBRE</th>
                            <th>CEDULA</th>
                            <th>AREA</th>
                            <th class="text-center">ENTRADA</th>
                            <th class="text-center">SALIDA</th>
                            {{-- <th>OTROS</th> --}}
                        </tr>
                    </thead>
                    @if ($users->isNotEmpty())
                        <tbody>
                            @forelse ($users as $user)
                                @php
                                    $assit_attendances = $user->getAssitAttendances($date);
                                    $first = $assit_attendances->first();
                                    $last = $assit_attendances->where('timestamp', '<>',$first->timestamp)->last();
                                @endphp

                                <tr>
                                    <td scope="row">{{$user->profile->fullname ?? null}}</td>
                                    <td>{{$user->number_id?? null}}</td>
                                    <td>{{$user->area ?? null}}</td>
                                    <td class="table-success text-center">{{$first->time ?? ''}}</td>
                                    <td class="table-danger text-center">{{$last->time ?? ''}}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    @else
                        <tr><th colspan="5" class="text-center">No se encontraron datos</th></tr>
                    @endif
                </table>

                @section('scripts')
                    @parent

                    <script>

                        $(document).ready(function() {
                            var table = $('#{{$table_id}}').DataTable( {
                                // "pageLength": 10,
                                "bPaginate": true,
                                "lengthMenu": [[10, 25, 50, 100 , 500, -1], [10, 25, 50, 100 , 500, "Todos"]],
                                lengthChange: true,
                                buttons: [
                                    {
                                        extend: 'excelHtml5',
                                        text: 'Excel',
                                        exportOptions: {
                                            columns: ':visible'
                                        }
                                    },
                                    {
                                        extend: 'csvHtml5',
                                        pageSize: 'LEGAL',
                                        text: 'CSV',
                                        exportOptions: {
                                            columns: ':visible'
                                        }
                                    },
                                ]
                            } );

                            table.buttons().container()
                                .appendTo( '#{{$table_id}}_wrapper .col-md-6:eq(0)' );
                        } );

                    </script>
                @endsection

            </div>
        </div>

        <hr>
@empty
    @if (isset($finicial) && isset($ffinal))
        <div class="alert alert-secondary">No se encontraron datos</div>
    @endif
@endforelse

@include('administracion.datatables.exportBootstrapCustom')
