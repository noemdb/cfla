@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_email="d-none d-xl-table-cell";
    $class_saldo="";
    $class_action="nosort";
@endphp
    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">

        <thead>

            <tr>

                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Identificador</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                {{-- <th class="{{ $class_estudiant }} text-right">Literal</th> --}}
                {{-- <th class="{{ $class_estudiant }} text-right">Promédio</th> --}}
                <th class="{{ $class_email }}" title="Correo Electrónico Classroom">Email CR</th>
                <th class="{{ $class_saldo }}">Saldo</th>
                <th class="{{ $class_action }} text-right">I. Corte</th>
                {{-- <th class="{{ $class_action }} text-center" title="Evaluación Descriptiva">E. Descriptivas</th> --}}

            </tr>

        </thead>

        <tbody id="tdatos">

            @foreach($estudiants as $estudiant)

                @php
                    $exchange_ammount_expire_bill = round($estudiant->exchange_ammount_expire_bill,2);
                    $pestudio = $estudiant->pestudio;
                    $status_baremo = ($pestudio) ? $pestudio->status_baremo : false ;
                    $status_a_cualitative = ($pestudio) ? $pestudio->status_a_cualitative : false ;
                @endphp

                <tr data-id="{{$estudiant->id}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td class="{{ $class_user  ?? ''}}">
                        {{$estudiant->ci_estudiant}}
                    </td>

                    <td class="{{ $class_user  ?? ''}}">
                        {{$estudiant->fullname}}
                    </td>

                    <td class="{{ $class_user  ?? ''}}">
                        {{$estudiant->full_inscripcion ?? ''}}
                    </td>
{{--
                    <td class=" font-weight-bold {{ $class_estudiant ?? ''}} text-right">
                        <span class="pr-3">
                        {{ ($status_baremo) ? $estudiant->literal : null }}
                        </span>
                    </td>
                    <td class=" font-weight-bold {{ $class_estudiant ?? ''}} text-right">
                        <span class="pr-3">
                        {{ $estudiant->promedio ?? '' }}
                        </span>
                    </td> --}}

                    <td class="{{ $class_email ?? '' }}">
                        {{ $estudiant->gsemail ?? ''}}
                    </td>

                    <td class="{{ $class_saldo ?? '' }}">
                        @if ($exchange_ammount_expire_bill > 0)
                            <span class="badge badge-danger">$ {{ f_float($exchange_ammount_expire_bill) }}</span>
                        @else
                            <span class="badge badge-success">SOLVENTE</span>
                        @endif
                    </td>

                    <td class="{{ $class_action ?? '' }} text-right">

                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">

                            {{-- @php $date_validate = Carbon\Carbon::now()->subMonth()->format('Y-m-d'); @endphp --}}
                            {{-- @php $enabled_bill = !($estudiant->getStatusBillDate($date_validate)); @endphp --}}

                            @php $date = Carbon\Carbon::now()->format('Y-m-d'); @endphp
                            @php $enabled_bill = ($exchange_ammount_expire_bill<=0) ? true : false @endphp
                            @foreach ($lapsos as $lapso)
                                {{-- @php $enabled_date = ($date >= $lapso->finicial && $date <= $lapso->ffinal) ? true : false; @endphp --}}
                                @php $enabled_date = ($date >= $lapso->ffinal) ? true : false; @endphp 
                                <a target="_blank" class="btn btn-{{ ( $lapso->getStatusEnableCorte($estudiant->id)) ? $lapso->class : 'secondary disabled' }}" title="{{$lapso->name_public ?? ''}}"
                                    href="{{ $route=route('representants.boletins.corte.pdf',[$estudiant->id,$lapso->id])}}" role="button">
                                    {!! $lapso->id ?? '' !!}
                                </a>
                            @endforeach

                        </div>

                    </td>
                    {{-- <td class="{{ $class_action ?? '' }} text-center">

                        @if ($status_a_cualitative)
                            <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">

                                @php $disabled = ($estudiant->edescriptivas->count()) ? null:' disabled ' ; @endphp
                                <a title="Evaluación Descriptiva" target="_blank" class="btn btn-info {{ $disabled ?? '' }}" title="{{$lapso->name ?? ''}}"
                                    href="{{ $route=route('representants.boletins.edescriptiva.pdf',[$estudiant->id])}}" role="button">
                                    ED
                                </a>

                            </div>
                        @endif

                    </td> --}}

                </tr>

            @endforeach

        </tbody>

    </table>

{{-- partials contentivo de los scripts datatables --}}
@include('representants.datatables.simple')
