@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp

@php $estudiants = (!empty($seccion))  ? $seccion->estudiants_in:null; @endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Identificador</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                @if (!empty($pevaluacion))
                    @if (!empty($pevaluacion->evaluacions->first()))
                        @foreach ($pevaluacion->evaluacions as $evaluacion)
                            <th class="{{ $class_pensum }} text-center" title="{{$evaluacion->description ?? ''}}">
                                {{$loop->iteration}}
                            </th>
                        @endforeach
                    @else
                        <th class="alert alert-danger text-center">NO HAY EVALUACIONES REGISTRADAS</th>
                    @endif
                @else
                <th class="alert alert-danger text-center">NO HAY PLAN DE EVALUACIÓN REGISTRADO</th>
                @endif

                <th class="{{ $class_action ?? '' }}">Acción</th>
                <th class="{{ $class_estudiant ?? '' }}">Promedio</th>
            </tr>
        </thead>

        <tbody id="tdatos">
            @foreach($estudiants as $estudiant)

                <tr data-id="{{$estudiant->id}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td class="{{ $class_estudiant }}">
                        {{ $estudiant->ci_estudiant ?? ''}}
                    </td>

                    <td class="{{ $class_estudiant }}">
                        {{$estudiant->fullname}}
                    </td>

                    @if ($pevaluacion)
                        @php $studiant_current = $estudiant; @endphp

                        @if (!empty($pevaluacion->evaluacions->first()))
                            @php $acum_nota = 0; @endphp
                            @php $count_eva = 0; @endphp
                            @foreach ($pevaluacion->evaluacions as $evaluacion)
                                <td class="{{ $class_pensum }} text-center">
                                    @php
                                        $name = 'nota['.$estudiant->id.']['.$evaluacion->id.']';
                                        $minimo = 0;
                                        // $minimo = $evaluacion->escala->minimo;
                                        $maximo = $evaluacion->escala->maximo;
                                        $nota = (!empty($evaluacion->boletins->where('estudiant_id',$estudiant->id)->first()->id)) ? $evaluacion->boletins->where('estudiant_id',$estudiant->id)->first()->nota : null ;
                                    @endphp
                                </td>
                                @if ($nota)
                                    @php $count_eva = $count_eva + 1; @endphp
                                    @php $acum_nota = $acum_nota + $nota; @endphp
                                @endif
                            @endforeach
                        @else
                            <td></td>
                        @endif
                    @else
                        <td></td>
                    @endif

                    <td>
                        <span id="promedio_{{$estudiant->id ?? ''}}">
                            {{ (!empty($count_eva)) ? round(($acum_nota/$count_eva),2) : '' }}
                        </span>
                    </td>

                </tr>

            @endforeach
        </tbody>

    </table>

@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/datatable/simple.js") }}"></script>
@endsection
