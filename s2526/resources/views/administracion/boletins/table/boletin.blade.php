@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_solvente="text-left";
    $class_action="nosort";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">

        <thead>

            <tr>

                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Identificador</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_solvente }}">SOLVENTE</th>
                {{-- <th class="{{ $class_estudiant }} text-right">PROMEDIO</th> --}}
                <th class="{{ $class_action }} text-right">Boletines</th>

            </tr>

        </thead>

        <tbody id="tdatos">

            @foreach($estudiants as $estudiant)

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
                        {{ $estudiant->promedio ?? '' }}
                        </span>
                    </td>
                    --}}

                    <td class="{{ $class_solvente  ?? ''}}">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                            @foreach ($lapsos as $lapso)
                                @php $disabled = $estudiant->getStatusBillDate($lapso->ffinal); $exchange_ammount_expire_bill = $estudiant->exchange_ammount_expire_bill; @endphp
                                <a target="_blank" class="btn btn-light" title="{{$lapso->name ?? ''}}">
                                    <i class=" {{ ($disabled) ? 'fas fa-times text-danger' : 'fa fa-check-circle text-success' }}" aria-hidden="true"></i>
                                </a>
                            @endforeach
                        </div>
                    </td>

                    <td class="{{ $class_action ?? '' }} text-right" id="btn-action-{{ $estudiant->id }}">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">

                            @foreach ($lapsos as $lapso)
                                <a target="_blank" class="btn btn-{{$lapso->class ?? ''}}" title="{{$lapso->name ?? ''}}"
                                    href="{{ $route=route('administracion.boletins.boletin.pdf',[$estudiant->id,$lapso->id])}}" role="button">
                                    {!! $lapso->id ?? '' !!}
                                </a>
                            @endforeach

                        </div>
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.simple_search')
