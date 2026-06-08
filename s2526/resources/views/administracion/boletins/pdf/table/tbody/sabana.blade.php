<tbody>
    @php $estudiants = (!empty($seccion))  ? $seccion->estudiants_in:null; @endphp
    @php $total_ar = null; @endphp

    @php $escala = $grado->pestudio->escala; @endphp
    @php $aprobacion = ($escala->aprobacion) ? : ''; @endphp
    @php $promedio_seccion = ($lapso) ? $seccion->getPromedioLapso($lapso->id) : $seccion->getPromedio() ; @endphp

    @foreach($estudiants as $estudiant)

        @php
            $decimal = 2;
            // $promedio = $estudiant->getNotaFinalLapso($lapso->id,$decimal) ;
            $promedio = ($lapso) ? $estudiant->getNotaFinalLapso($lapso->id,$decimal) : $estudiant->getPromedioFinalLapso(1) ;
        @endphp

        <tr style="font-size:0.62rem;">
            <td id="td-count">
                {{$loop->iteration}}
            </td>
            <td>
                {{ $estudiant->ci_estudiant ?? ''}}
            </td>
            <td style="{{ (strlen($estudiant->fullname) > 20) ? 'font-size:0.55rem;':null}}">
                {{$estudiant->fullname ?? null}}
            </td>
            @php $sum_nota = 0; @endphp
            @php $count_nota = 0; @endphp
            @php $count_ar = null; @endphp
            @foreach ($pensums as $pensum)

                @php $pensum_id = $pensum->id; @endphp

                @if ($lapso)
                    @php
                        $pevaluacion = $pensum->pevaluacions->where('seccion_id',$seccion->id)->where('lapso_id',$lapso->id)->first();
                        $nota = $estudiant->getNota($lapso->id,$pensum->id);
                        // $nota = $estudiant->getNotaPensumLapso($pensum->id,$lapso->id,0);
                        $ajuste = (!empty($estudiant->getAjuste($pevaluacion->id))) ? $estudiant->getAjuste($pevaluacion->id):null;
                    @endphp
                @else
                    @php
                        $nota = $estudiant->getNotaFinal($pensum_id,0);
                        $ajuste = $estudiant->getAllAjuste($pensum_id) ;
                    @endphp
                @endif

                <td style="text-align:right; padding-right:3px">
                    <span style="{{ ($ajuste) ? 'font-style: italic; text-decoration: underline;':null }}" >
                        {{ ($nota) ? $nota : '0' }}
                    </span>
                </td>
                <td style="white-space: nowrap; background:#ccc; border:1px solid #fff; text-align:left; padding-left:3px">
                    @if ($ajuste)
                        <b>{{ ($ajuste) ? $ajuste:''}}</b>
                    @endif
                </td>

                @if ($nota < $aprobacion && !empty($nota ))
                    @php
                        $count_ar++;
                        $total_ar++;
                    @endphp
                @endif

            @endforeach
            <th style="text-align:center;">{{ $count_ar }}</th>
            <th style="text-align:center;"> {{ $promedio ?? ''}} </th>
        </tr>
    @endforeach

    <tr style="background-color:#ccc;">
        <th colspan="3" style="text-align:right;font-size:1rem; padding-right:0.5rem;;font-size:0.8rem">Promedio</th>
        @foreach ($pensums as $pensum)
            <th colspan="2" style="text-align:center;font-size:0.7rem">
                {{ $pensum->getPorcAprobados($lapso_id) ?? '' }}
            </th>
        @endforeach
        <th style="text-align:center;font-size:0.8rem">{{ $total_ar }}</th>
        <th style="text-align:center;font-size:0.9rem">
            {{ $promedio_seccion ?? '' }}
        </th>
    </tr>

</tbody>
