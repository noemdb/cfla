<tbody id="tdatos">
    @php
        $nota_final_count = 0;
        $nota_final_sum = 0;
        
    @endphp

    @foreach ($pensums as $pensum)

        @php
            $asignatura = $pensum->asignatura;
            $academic_index = $asignatura->enable_academic_index;
            $pestudio = $pensum->pestudio;
        @endphp

        <tr>

            <td style="white-space:nowrap;">
                {{$loop->iteration}}
            </td>

            <td  style="white-space:nowrap;">
                {{ ($academic_index=='false') ? '*' : ''}}
                {{$asignatura->fullname ?? ''}}
            </td>

            @php $promedio = null; @endphp
            @php $n_lapso = null;@endphp

            @foreach ($lapsos as $lapso)

                @php
                    $nota = $estudiant->getnota($lapso->id,$pensum->id,4);
                    $nota_sa = $estudiant->getnota($lapso->id,$pensum->id,4,false);
                    $nota_literal = ($nota) ? $baremo->getLiteral($pestudio->id,$nota) : null ;
                @endphp

                @php $pevaluacion = $pensum->pevaluacions->where('lapso_id',$lapso->id)->where('seccion_id',$seccion->id)->first(); @endphp
                @php $nota_round = (isset($nota)) ? round($nota,0):null; @endphp
                @php $nota_ajuste = (!empty($pevaluacion->id)) ? $estudiant->getAjuste($pevaluacion->id):null; @endphp

                <td style="white-space:nowrap; text-align:center">

                    <span class="text-italic" style="{{ ($nota_ajuste) ? ' text-decoration: underline':''}}; {{ ($nota==null) ? 'font-style: italic;':null }} ">

                        <span style="width: 33%">{{ ($lapso->id <= $lapso_id) ? $nota_round : null}}</span>
                        
                        @if ($lapso->id <= $lapso_id)
                            @admin
                                [<span style="width: 33%">{{number_format($nota, 4, ',', '.') }}</span>]
                                [<span style="width: 33%">{{number_format($nota_sa, 4, ',', '.') }}</span>]
                                [<span style="width: 33%">{{str_pad($nota_ajuste, 2, '0', STR_PAD_LEFT)}}</span>]
                            @endadmin
                        @endif

                    </span>

                </td>

                @if (isset($nota) && ($lapso->id <= $lapso_id))
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

            @php $getNotaFinal = $estudiant->getNotaFinal($pensum->id,0,true,$lapso_id); @endphp
            <th style="text-align:left;white-space:nowrap;">
                {{ $getNotaFinal ?? '' }}
            </th>

        </tr>

    @endforeach

    <tr style="background-color: #ccc; padding-top:0.8rem; margin-top:0.8rem;">
        <th colspan="2" class="" style="text-align:left; white-space:nowrap;  font-size:0.6rem; padding:0.2rem; margin:0.2rem;">Promedio</th>
        @foreach ($lapsos as $lapso)
            @php $promedio_1 = $estudiant->getPromedioLapsoRound($lapso->id,2) ;  @endphp
            @php $promedio_2 = $estudiant->getPromedioLapsoRoundForPensums($lapso->id,2); @endphp
            @php $promedio_3 = $estudiant->getNotaFinalLapso($lapso->id,4) @endphp
            @php $promedio_4 = $estudiant->getNotaFinalLapso($lapso->id,4,true,false) @endphp
            @php $ajuste_lapso = $estudiant->getAjusteLapso($lapso->id) @endphp
            <th style="text-align:center;white-space:nowrap;">
                {{ ($lapso->id <= $lapso_id) ? $promedio_2 : ''}}
                
                @if ($lapso->id <= $lapso_id)
                    @admin [{{number_format($promedio_3, 4, ',', '.') }}] [{{number_format($promedio_4, 4, ',', '.') }}] [{{ str_pad($ajuste_lapso, 2, '0', STR_PAD_LEFT)}}]  @endadmin
                @endif
            </th>
        @endforeach
        <th style="text-align:left;white-space:nowrap;">
            @php $final_promedio = $estudiant->getPromedioFinalLapsoId($lapso_id,2); @endphp
            {{ $final_promedio ?? ''}}
        </th>
    </tr>

    <tr>
        <td colspan="6" class="text-muted"> * No tomada en cuenta para el indice o promedio académico</td>
    </tr>

</tbody>
