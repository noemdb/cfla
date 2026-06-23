{{-- @forelse ($dates as $date) --}}

    @php
    // $users = $user->getWorkersAttendance($date,$area);
    // $users = $user->getWorkersSchedule($date,$assit_schedule_id);
    $users = $user->getWorkersCargoScheduleRange($dates,$cargo_id,$assit_schedule_id);
    $usersAusentes = $user->getWorkersCargoScheduleRangeAsuntes($dates,$cargo_id,$assit_schedule_id);
    //$users = $user->getWorkersCargoSchedule($date,$cargo_id,$assit_schedule_id);
    $table_id = 'table-data-default';
    $index = 0;
    $count = count((array)$dates);
    $colspan = ($count) ? ($count + 7) : null ;
    @endphp

        <div class="card pb-2">
            {{-- <div class="alert alert-primary pb-1 mb-1" style="font-size:0.7rem;background-color: #515151;color: #fff;font-weight:bold;"><span class="font-weight-bold">{{f_date($date)}}</span></div> --}}
            <div class="card-body py-1">
                <table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id ?? null}}" style="font-size:0.6rem;margin-bottom:0rem; padding-bottom:0rem;">
                    <thead>
                        <tr align="left">
                            <th colspan="2">&nbsp;</th>
                            <th colspan="10" align="center">DIAS</th>
                        </tr>
                        <tr align="left" style="background-color: #e1e1e1;color: #000">
                            <td style="background-color: #fff">&nbsp;</td>
                            <th style="background-color: #fff">&nbsp;</th>
                            @foreach ($dates as $date)
                                <th colspan="2" align="center">{{$date->format('l') ?? null}} <br><small>[{{ ($date) ? $date->format('d-m-Y') : null}}]</small></th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @if ($users->isNotEmpty())

                            <tr>
                                <th align="left">Nº</th>
                                <th align="left">APELLIDO Y NOMBRE</th>
                                @foreach ($dates as $date)
                                <th align="center" style="border-left-style: solid;border-left-width: 1px;border-left-color: #ccc">H.ENT</th>
                                <th align="center" style="border-right-style: solid;border-right-width: 1px;border-right-color: #ccc">H.SAL</th>
                                @endforeach
                            </tr>
                            @forelse ($users as $user)
                                @php $index++; @endphp
                                <tr>
                                    <td class="{{ $class_N ?? null}}">{{$index }}</td>
                                    <td scope="row" style="width: 15rem !important;">{{$user->profile->fullnameinv ?? null}}</td>

                                    @foreach ($dates as $date)
                                        @php
                                            $current = $date->format('Y-m-d');
                                            $assit_attendances = $user->getAssitAttendances($current);
                                            $first = ($assit_attendances->isNotEmpty()) ? $assit_attendances->first() : null;
                                            $last = ($assit_attendances->isNotEmpty() && $first) ? $assit_attendances->where('timestamp', '<>', $first->timestamp)->last() : null;
                                        @endphp
                                        <td align="center" style="border-left-style: solid;border-left-width: 1px;border-left-color: #ccc">{{$first->time ?? ''}}</td>
                                        <td align="center" style="border-right-style: solid;border-right-width: 1px;border-right-color: #ccc">{{$last->time ?? ''}}</td>
                                    @endforeach
                                </tr>
                            @empty
                            @endforelse

                        @else
                            <tr><th colspan="{{$colspan ?? null}}" class="text-center">No se encontraron datos</th></tr>
                        @endif

                        @if ($usersAusentes->isNotEmpty())
                            @php

                            @endphp

                            <tr><th colspan="{{$colspan ?? null}}" class="text-center" style="padding-top:1rem">Ausentes</th></tr>
                            @forelse ($usersAusentes as $user)
                                @php $index++; @endphp
                                <tr>
                                    <td class="{{ $class_N ?? null}}">{{$index }}</td>
                                    <td scope="row" style="width: 15rem !important;">{{ ($user->profile) ? $user->profile->fullnameinv : null}}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @empty
                            @endforelse

                        @endif

                    </tbody>
                </table>

            </div>
        </div>

        <hr>
