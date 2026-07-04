@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_promedio="text-right";
    $class_position="text-right";
    $class_solvente="text-left";
    $class_action="nosort";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">

        <thead>

            <tr>

                <th class="{{ $class_N }}">Posición</th>
                <th class="{{ $class_estudiant }}">Identificador</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                {{-- <th class="{{ $class_grado }}">Grado/Sección</th> --}}
                {{-- <th class="{{ $class_estudiant }} text-right">% de carga</th> --}}
                <th class="{{ $class_promedio }} ">Promedio</th>
                {{-- <th class="{{ $class_position }} ">Posición</th> --}}

            </tr>

        </thead>

        <tbody id="tdatos">

            @foreach($estudiants as $estudiant)

            @php
                // $promedio = $estudiant->getNotaFinalLapso($lapso->id,2);
                //$posicion = $estudiant->getPosicionSeccionLapso($lapso->id);
                // $promedio_lapso = $estudiant->getNotaFinalLapso($lapso->id,4,true,false);
            @endphp


                <tr>

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td class="{{ $class_estudiant  ?? ''}}">
                        {{-- {{$estudiant->ci_estudiant}} --}}
                        {{$estudiant['ci_estudiant']}}
                    </td>

                    <td class="{{ $class_estudiant  ?? ''}}">
                        {{-- {{$estudiant->fullname}} --}}
                        {{$estudiant['fullname']}}
                    </td>

                    {{-- <td class="{{ $class_grado  ?? ''}}">
                        {{$estudiant->full_inscripcion ?? ''}}
                    </td>
                    <td class="{{ $class_estudiant  ?? ''}} text-right mr-2">
                        @php
                            $real = $estudiant->getRealEvaluacionsPensumLapso(null,$lapso_id);
                            $goal = $estudiant->getGoalEvaluacionsPensumLapso(null,$lapso_id);
                            $total = ($goal) ? round((100 * $real / $goal),2) : null ;
                        @endphp
                        {{$total ?? ''}}
                    </td> --}}
                    <td class="{{ $class_promedio  ?? ''}}">
                        {{-- {{$promedio ?? ''}} --}}
                        {{$estudiant['nota']}}
                    </td>
                    {{-- <td class="{{ $class_position  ?? ''}}">
                        {{$posicion ?? ''}}
                    </td> --}}

                </tr>

            @endforeach

        </tbody>

    </table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.simple_search')
