<div class="card pb-2">
    {{-- <div class="alert alert-primary pb-1 mb-1"><span class="font-weight-bold">{{ ($user) ? $user->fullname : null}}</span></div> --}}
    <div class="card-body py-1">
        <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
            <thead>
                <tr>
                    <th>N</th>
                    <th>NOMBRE</th>
                    <th>CEDULA</th>
                    <th>CARGO</th>
                    <th>FECHA</th>
                    <th class="text-center">ENTRADA / SALIDA (Turnos)</th>
                </tr>
            </thead>

            @forelse ($dates as $date)
                @php
                    $assit_attendances = $user->getAssitAttendances($date);
                    $current = Jenssegers\Date\Date::createFromFormat('Y-m-d h:i:s', $date);
                    $chunks = $assit_attendances->chunk(2);
                @endphp

                <tr>
                    <td class="{{ $class_N ?? null}}">{{$loop->iteration}}</td>
                    <td scope="row">{{$user->profile->fullname ?? null}} </td>
                    <td>{{$user->number_id ?? null}}</td>
                    <td>{{$user->cargo_name ?? null}}</td>
                    <td>{{($current) ? $current->format('d-m-Y') : null}} <span class="text-capitalize">{{($current) ? $current->format('l') : null}}</span></td>
                    <td class="text-center">
                        @if($assit_attendances->isNotEmpty())
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
                        @endif
                    </td>
                </tr>
            @empty
                @if (isset($finicial) && isset($ffinal))
                    <div class="alert alert-secondary">No se encontraron datos</div>
                @endif
            @endforelse
        </table>

    </div>
</div>

@include('administracion.datatables.exportBootstrap')
