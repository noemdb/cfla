<tbody id="tdatos">

    @php $sum_nota = 0; @endphp
    @php $count_nota = 0; @endphp
    @foreach ($pensums as $pensum)
        @php $asignatura = (!empty($pensum->asignatura)) ? $pensum->asignatura:null ; @endphp
        <thead style="background-color:#e0e0e0">
            <tr style="background-color:#e0e0e0">
                <td style="font-size:0.8rem;text-align:left">
                    @php $profesor = $pensum->pevaluacions->where('seccion_id',$seccion->id)->where('lapso_id',$lapso->id)->first()->profesor; @endphp
                    {{$asignatura->name}} || <span style="font-size:0.8rem">Prof:  {{$profesor->fullname ?? 'fallo'}}</span>
                </td>

                    @php $nota = $pensum->GetNota($estudiant->id,$seccion->id,$lapso->id) ; @endphp
                    @if ($nota)
                        @php $sum_nota = $sum_nota + $nota; @endphp
                        @php $count_nota = $count_nota + 1; @endphp
                    @endif

                <td style="font-size:0.8rem;text-align:right">
                    @php
                        $baremo = (!empty($nota)) ? $pensum->GetValoracion($pestudio->id,$nota) : null;
                        $valoracion = ($baremo) ? $baremo->valoracion : null;
                    @endphp
                    <b>{{ $valoracion ?? ''}}</b>
                </td>
            </tr>
        </thead>
        <tr>
            <td colspan="2">

                @php $query = $pensum->pevaluacions->where('lapso_id',$lapso->id)->where('seccion_id',$seccion->id)->first(); @endphp
                @php $evaluacions = (!empty($query->evaluacions)) ? $query->evaluacions : array() ; @endphp

                <table class="table table-sm small">

                    <tbody id="tdatos">
                        @foreach ($evaluacions as $evaluacion)
                            @php $boletin = $evaluacion->boletins->where('estudiant_id',$estudiant->id)->first(); @endphp
                            <tr style="font-size:0.7rem">
                                <td>
                                    <div class="text_wrap">
                                        {{$loop->iteration}}.- {{ strtoupper($evaluacion->description) }}
                                    </div>
                                </td>
                                @php
                                    $baremo = (!empty($nota)) ? $boletin->getBaremo($pestudio->id) : null;
                                    $valoracion = ($baremo) ? $baremo->valoracion : null;
                                @endphp
                                <td style="text-align:right">{{ (!empty($boletin->nota)) ? $valoracion : null }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </td>
        </tr>

    @endforeach

    <tr style="font-size:1rem !important;background-color:#e0e0e0">
        @php $notal_total = (!empty($count_nota)) ? round(($sum_nota/$count_nota),2):null; @endphp
        <td style="text-align:right"><b>Varolación del Lapso</b></th>
        <td style="text-align:center">
            <b>{{ (!empty($notal_total)) ? $baremo->getValoracion($pestudio->id,$notal_total,$lapso->id)->valoracion : null}}</b>
        </td>
    </tr>

</tbody>
