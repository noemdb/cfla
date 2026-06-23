@forelse ($dates as $date)

    @php
    $users = $user->getWorkersCargoSchedule($date,$cargo_id,$assit_schedule_id);
    $table_id = 'table-data-default'.$loop->iteration;
    @endphp

        <div class="card pb-2">
            <div class="alert alert-primary pb-1 mb-1"><span class="font-weight-bold">{{ ($date) ? $date->format('d-m-Y') : null}}</span></div>
            <div class="card-body py-1">
                <table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}">
                    <thead>
                        <tr>
                            <th>N</th>
                            <th>UNIDAD OPERATIVA</th>
                            <th>NOMBRE</th>
                            <th>CEDULA</th>
                            <th>CARGO</th>
                            <th class="text-center">ENTRADA / SALIDA (Turnos)</th>
                        </tr>
                    </thead>
                    @if ($users->isNotEmpty())
                        <tbody>
                            @forelse ($users as $user)
                                @php
                                    $assit_attendances = $user->getAssitAttendances($date);
                                    // Group by pairs if possible, or just list them
                                    // Assuming strict pairs for now as per requirement "siempre en pares"
                                    $chunks = $assit_attendances->chunk(2);
                                @endphp

                                <tr>
                                    <td class="{{ $class_N ?? null}}">{{$loop->iteration}}</td>
                                    <td>{{$user->area ?? 'N/A'}}</td>
                                    <td scope="row">{{$user->profile->fullname ?? null}} </td>
                                    <td>{{$user->number_id ?? null}}</td>
                                    <td>{{$user->cargo_name ?? null}}</td>
                                    <td class="text-center">
                                        @foreach($chunks as $chunk)
                                            @php
                                                $in = $chunk->first();
                                                $out = $chunk->count() > 1 ? $chunk->last() : null;
                                            @endphp
                                            <span class="badge badge-light border">
                                                <span class="text-success">{{ $in->time ?? '--:--' }}</span>
                                                -
                                                <span class="text-danger">{{ $out->time ?? '--:--' }}</span>
                                            </span>
                                        @endforeach
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    @else
                        <tr><th colspan="6" class="text-center">No se encontraron datos</th></tr>
                    @endif
                </table>

                @section('scripts')
                    @parent

                    <script>

                        $(document).ready(function() {
                            var table = $('#{{$table_id}}').DataTable( {
                                "pageLength": 25,
                                "bPaginate": false,
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
