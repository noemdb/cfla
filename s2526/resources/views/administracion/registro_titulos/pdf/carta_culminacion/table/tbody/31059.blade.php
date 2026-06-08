<tbody id="tdatos">
    @php
        $nota_final_count = 0;
        $nota_final_sum = 0;
    @endphp

    @foreach ($pensums as $pensum)

        @php
            $asignatura = $pensum->asignatura;
            $academic_index = $asignatura->enable_academic_index;
        @endphp

        <tr>

            <td style="white-space:nowrap;">
                {{$loop->iteration}}
            </td>

            <td  style="white-space:nowrap;">
                {{ ($academic_index=='false') ? '*' : ''}}
                {{$asignatura->fullname ?? ''}}
                {{-- {{ Str::limit($asignatura->fullname,20) }} --}}
            </td>

            @php $promedio = null; @endphp
            @php $n_lapso = null; @endphp
            @foreach ($lapsos as $lapso)

                @php $nota = $estudiant->getnota($lapso->id,$pensum->id); @endphp
                @php $pevaluacion = $pensum->pevaluacions->where('lapso_id',$lapso->id)->where('seccion_id',$seccion->id)->first(); @endphp
                @php $nota_round = (isset($nota)) ? round($nota,0):null; @endphp
                @php $nota_ajuste = (!empty($pevaluacion->id)) ? $estudiant->getAjuste($pevaluacion->id):null; @endphp

                <td style="white-space:nowrap; text-align:center">
                    <span style="{{ ($nota_ajuste) ? ' text-decoration: underline ':''}}">
                        {{ ($lapso->id <= $lapso->id) ? $nota_round : ''}}
                    </span>
                </td>

                @if (isset($nota) && ($lapso->id <= $lapso->id))
                    @php
                        $promedio = $promedio + $nota_round;
                        $n_lapso ++;
                    @endphp
                    @if ($academic_index!='false')
                        @php
                            $arr_notas[$lapso->id][$asignatura->id] = $nota_round;
                            $nota_final_sum = $nota_final_sum + $nota_round;
                            $nota_final_count = $nota_final_count + 1;
                        @endphp
                    @endif
                @endif

            @endforeach

            <td style="text-align:left;white-space:nowrap;">
                @php $nota_asignatura = '[vacio]'; @endphp
                @if (!is_null($promedio))
                    @php $nota_asignatura = round(($promedio/$n_lapso),2); @endphp
                @endif
                <strong>{{ $nota_asignatura ?? '' }}</strong>
            </td>
        </tr>

    @endforeach

    <tr>
        <td colspan="2" class="text-muted"> * No tomada en cuenta para el indice o promedio académico</td>
        @foreach ($lapsos as $lapso)
            @php $media = (isset($arr_notas[$lapso->id])) ? round((array_sum($arr_notas[$lapso->id]) / count($arr_notas[$lapso->id])),2) : ''; @endphp
            <th style="text-align:center;white-space:nowrap;">{{ $media ?? '' }}</th>
        @endforeach
        <th style="text-align:left;white-space:nowrap;">
            @php
                $final_promedio = (isset($nota_final_count)) ? round(($nota_final_sum/$nota_final_count),2):null;
            @endphp
            {{ $final_promedio ?? ''}}
        </th>
    </tr>

</tbody>
