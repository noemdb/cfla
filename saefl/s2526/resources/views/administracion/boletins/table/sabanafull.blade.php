@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp

@php $pestudio = $grado->pestudio; @endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}"></th>
            <th class="{{ $class_estudiant }}"></th>

            @foreach ($pensums as $pensum)
                <th colspan="4" class="{{ $class_estudiant }} text-center table-secondary border-right" title="{{$pensum->asignatura->name ?? ''}}">
                    {{$pensum->asignatura->code ?? ''}}
                </th>
            @endforeach

            <th class="{{ $class_grado }} text-center" title="Asignaturas Aplazadas">&nbsp;</th>
            <th class="{{ $class_estudiant }}"></th>

        </tr>

            <tr>
                <td class="{{ $class_N ?? '' }}">N</td>
                <td class="{{ $class_estudiant ?? '' }}">Estudiante</td>
                @foreach ($pensums as $pensum)
                    @foreach ($lapsos as $lapso)
                        <td class="{{ $class_estudiant ?? '' }} p-1 m-1">{{ $lapso->id ?? '' }}</td>
                    @endforeach
                    <th class="{{ $class_estudiant ?? '' }} p-1 m-1 table-secondary border-right">D</th>
                @endforeach
                <td class="{{ $class_grado ?? '' }} text-right">AR</td>
                <th class="{{ $class_estudiant ?? '' }} text-right">PROMEDIO</th>
                @if ($pestudio->status_baremo == "true")
                    <th class="{{ $class_estudiant ?? '' }} text-right">LITERAL</th>
                @endif
            </tr>

    </thead>

    <tbody id="tdatos">

        @foreach($estudiants as $estudiant)
            @php
                $sum_nota = null;
                $count_nota = null;
                $count_ar = null;
                $aprobacion = ($escala->aprobacion) ? : '';
                $decimal = 1;
                $promedio_final = $estudiant->getPromedioFinal($decimal) ;
                // $promedio = $estudiant->promedio ;
            @endphp

            <tr data-id="{{$estudiant->id}}" class=" table-{{ ($promedio_final > $escala->maximo) ? 'danger':'default'}}">

                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration }}
                </td>

                <td class="{{ $class_user  ?? ''}}">
                    {{$estudiant->fullname }}
                </td>

                @foreach ($pensums as $pensum)

                    @php
                        $asignatura = $pensum->asignatura;
                        $academic_index = $asignatura->enable_academic_index;
                        $pestudio = $pensum->pestudio;
                        $promedio = null;
                        $n_lapso = null;
                    @endphp

                    @foreach ($lapsos as $lapso)
                        @php
                            $nota = $estudiant->getNota($lapso->id,$pensum->id);
                        @endphp

                        <td class="{{ $class_estudiant ?? ''}} table-{{($nota) ? 'default':'danger'}} p-1 m-1">
                            {{ ($nota) ? round($nota,0):''}}
                        </td>

                        @php
                            $promedio = $promedio + $nota;
                            $n_lapso ++;
                        @endphp

                    @endforeach

                    {{-- <th class="{{ $class_estudiant ?? ''}} table-secondary p-1 m-1 border-right">

                        @if (!is_null($promedio))
                            @php $nota_asignatura = round(($promedio/$n_lapso),0); @endphp
                            @if ($academic_index=='true')
                                <strong>{{ $nota_asignatura ?? '' }}</strong>
                            @else
                                @php $nota_literal = ($nota_asignatura) ? $baremo->getLiteral($pestudio->id,$nota_asignatura) : null ; @endphp
                                <strong>{{ $nota_literal ?? '' }}</strong>
                            @endif
                        @endif

                    </th> --}}

                    @php $getNotaFinal = $estudiant->getNotaFinal($pensum->id,0); @endphp
                    <th style="text-align:left;white-space:nowrap;">
                        {{ $getNotaFinal ?? '' }}
                    </th>

                    @if ($nota < $aprobacion && !empty($nota )) @php $count_ar++; @endphp @endif

                @endforeach

                <td class=" font-weight-bold {{ $class_estudiant ?? ''}} text-center text-danger">
                    {{ $count_ar ?? '' }}
                </td>

                <th class=" font-weight-bold {{ $class_estudiant ?? ''}} text-center">
                    <span class="pr-3">
                        {{ $promedio_final ?? ''}}
                    </span>
                </th>

                @if ($pestudio->status_baremo == "true")
                    <th class=" font-weight-bold {{ $class_estudiant ?? ''}} text-center">
                        <span class="pr-3">
                            {{ $estudiant->literal ?? 'N/A' }}
                            {{-- {{ (!empty($promedio)) ? $baremo->getLiteral($grado->pestudio->id,$promedio) : null }} --}}
                        </span>
                    </th>
                @endif
            </tr>

        @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.simple')
