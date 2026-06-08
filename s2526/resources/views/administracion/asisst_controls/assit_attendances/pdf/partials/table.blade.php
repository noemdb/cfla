@forelse ($dates as $date)

    @php
    // $users = $user->getWorkersAttendance($date,$area);
    // $users = $user->getWorkersSchedule($date,$assit_schedule_id);
    $users = $user->getWorkersCargoSchedule($date,$cargo_id,$assit_schedule_id);
    $table_id = 'table-data-default'.$loop->iteration;
    // $table_id = 'table-data-default';
    @endphp

        <div class="card pb-2">
            <div class="alert alert-primary pb-1 mb-1" style="font-size:0.7rem;background-color: #515151;color: #fff;font-weight:bold;"><span class="font-weight-bold">{{f_date($date)}}</span></div>
            <div class="card-body py-1">
                <table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}" style="font-size:0.6rem;margin-bottom:0rem; padding-bottom:0rem;">
                    <thead>
                        <tr align="left" style="background-color: #f1f1f1;color: #000">
                            <th style="width: 15rem !important;">NOMBRE</th>
                            <th style="width: 10rem !important;">CEDULA</th>
                            <th>CARGO</th>
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
                                    <td scope="row" style="width: 15rem !important;">{{$user->profile->fullname ?? null}}</td>
                                    <td style="width: 10rem !important;">{{$user->number_id?? null}}</td>
                                    <td>{{$user->cargo_name ?? null}}</td>
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

            </div>
        </div>

        <hr>
@empty
    @if (isset($finicial) && isset($ffinal))
        <div class="alert alert-secondary">No se encontraron datos</div>
    @endif
@endforelse
