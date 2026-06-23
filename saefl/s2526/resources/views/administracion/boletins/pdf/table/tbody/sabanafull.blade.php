<tbody id="tdatos">

    @php
        $total_ar = null;
        $escala = $grado->pestudio->escala;
        $aprobacion = ($escala->aprobacion) ? : '';
    @endphp

    @foreach($estudiants as $estudiant)

        @php
            $sum_nota = null;
            $count_nota = null;
            $count_ar = null;
            $aprobacion = ($escala->aprobacion) ? : '';
            $decimal = 1;
            $promedio_final = $estudiant->getPromedioFinal($decimal) ;
        @endphp

        <tr style="font-size:0.62rem;">

            <td style="width:0.5rem !important">
                {{$loop->iteration }}
            </td>

            <td style="white-space: nowrap; width:5rem !important">
                {{Str::limit($estudiant->fullname,45)}}
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

                    <td style="">
                    {{-- <td style="width:1.4rem !important"> --}}
                        {{ ($nota) ? round($nota,0):''}}
                    </td>

                    @php
                        $promedio = $promedio + $nota;
                        $n_lapso ++;
                    @endphp

                @endforeach

                {{-- @php $nota_final = $estudiant->getNotaFinal($pensum_id,0); @endphp --}}

                {{-- <td style="background-color: #ccc;">
                    @if (!is_null($promedio))
                        @php $nota_asignatura = round(($promedio/$n_lapso),0); @endphp
                        @if ($academic_index=='true')
                            <strong>{{ $nota_asignatura ?? '' }}</strong>
                        @else
                            @php $nota_literal = ($nota_asignatura) ? $baremo->getLiteral($pestudio->id,$nota_asignatura) : null ; @endphp
                            <strong>{{ $nota_literal ?? '' }}</strong>
                        @endif
                    @endif
                </td> --}}

                @php $getNotaFinal = $estudiant->getNotaFinal($pensum->id,0); @endphp
                <th style="text-align:left;white-space:nowrap;">
                    {{ $getNotaFinal ?? '' }}
                </th>

                @if ($nota < $aprobacion && !empty($nota )) @php $count_ar++; @endphp @endif

            @endforeach

            <td style=" color:#ff0000">
                {{ $count_ar ?? '' }}
            </td>
            <th style="text-align:center">
                <span >

                    {{ $promedio_final ?? ''}}

                </span>
            </th>
            @if ($pestudio->status_baremo == "true")
                <th class=" font-weight-bold {{ $class_estudiant ?? ''}} text-center">
                    <span class="pr-3">
                        {{ $estudiant->literal ?? '' }}
                        {{-- @admin {{ $estudiant->id ?? '' }} @endadmin --}}
                    </span>
                </th>
            @endif

        </tr>

        @endforeach

    </tbody>
