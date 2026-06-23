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
                {{-- <th class="{{ $class_estudiant }} text-right">PROMEDIO</th> --}}
                <th class="{{ $class_estudiant }} text-right">% de carga</th>
                {{-- <th class="{{ $class_solvente }}">SOLVENTE</th> --}}
                <th class="{{ $class_action }} text-right">I.Corte Notas</th>

            </tr>

        </thead>

        <tbody id="tdatos">

            @foreach($estudiants as $estudiant)

                <tr data-id="{{$estudiant->id}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td class="{{ $class_estudiant  ?? ''}}">
                        {{$estudiant->ci_estudiant}}
                    </td>

                    <td class="{{ $class_estudiant  ?? ''}}">
                        {{$estudiant->fullname}}
                    </td>

                    <td class="{{ $class_grado  ?? ''}}">
                        {{$estudiant->full_inscripcion ?? ''}}
                    </td>
                    <td class="{{ $class_estudiant  ?? ''}} text-right mr-2">
                        @php
                            $real = $estudiant->getRealEvaluacionsPensumLapso(null,$lapso_id);
                            $goal = $estudiant->getGoalEvaluacionsPensumLapso(null,$lapso_id);
                            $total = ($goal) ? round((100 * $real / $goal),2) : null ;
                        @endphp
                        {{$total ?? ''}}
                    </td>

                    {{-- <td class="{{ $class_solvente  ?? ''}}">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                            @php $date = Carbon\Carbon::now()->subMonth()->format('Y-m-d'); @endphp
                            @php $disabled = $estudiant->getStatusBillDate($date); @endphp
                            <a target="_blank" class="btn btn-light" title="{{$lapso->name ?? ''}}">
                                <i class=" {{ ($disabled) ? 'fas fa-times text-danger' : 'fa fa-check-circle text-success' }}" aria-hidden="true"></i>
                            </a>
                        </div>
                    </td> --}}

                    <td class="{{ $class_action ?? '' }} text-right" id="btn-action-{{ $estudiant->id }}">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">

                            {{-- @foreach ($lapsos as $lapso) --}}
                                <a target="_blank" class="btn btn-{{$lapso->class ?? ''}}" title="{{$lapso->name ?? ''}}"
                                    href="{{ $route=route('administracion.boletins.corte.pdf',[$estudiant->id,$lapso->id])}}" role="button">
                                    {!! $lapso->code_sm ?? '' !!}
                                </a>
                            {{-- @endforeach --}}
                            {{-- @foreach ($lapsos as $lapso)
                                <a target="_blank" class="btn btn-{{$lapso->class ?? ''}}" title="{{$lapso->name ?? ''}}"
                                    href="{{ $route=route('administracion.boletins.corte.pdf',[$estudiant->id,$lapso->id])}}" role="button">
                                    {!! $lapso->id ?? '' !!}
                                </a>
                            @endforeach --}}

                        </div>
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.simple_search')
