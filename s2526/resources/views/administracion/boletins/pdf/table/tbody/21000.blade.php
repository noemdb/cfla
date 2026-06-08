<tbody id="tdatos">

    @php $sum_nota = 0; @endphp
    @php $count_nota = 0; @endphp
    @php $count_eva = 0; @endphp
    @php $rowCount = 0; @endphp
    @foreach ($pensums as $pensum)
        @php $rowCount++; @endphp
        @php $asignatura = (!empty($pensum->asignatura)) ? $pensum->asignatura:null ; @endphp
        @php $pevaluacion = $pensum->pevaluacions->where('seccion_id',$seccion->id)->where('lapso_id',$lapso->id)->first(); @endphp
        @php $evaluacions = ($pevaluacion) ? $pevaluacion->evaluacions : array() ; @endphp
        @php $profesor = ($pevaluacion) ? $pevaluacion->profesor : null  @endphp

        @if ($profesor)
            {{-- <thead style="display: table-header-group;"> --}}
            <tr style="background-color:#e0e0e0">

                <td style="font-size:0.8rem;text-align:left">
                    {{ $asignatura->name }} || <span style="font-size:0.8rem; margin-left: 0.6rem;">Prof:
                        {{ $profesor->fullname ?? 'fallo' }}</span>

                    {{-- || <span style="font-size:0.8rem;text-align:right">VAL.</span> --}}
                </td>
                {{-- <td>&nbsp;</td> --}}

                @php $nota = $pensum->GetNota($estudiant->id,$seccion->id,$lapso->id,10) ; @endphp
                @if ($nota)
                    @php $sum_nota = $sum_nota + $nota; @endphp
                    @php $count_nota = $count_nota + 1; @endphp
                @endif

                <td style="font-size:0.8rem;text-align:right">
                    @php
                        $baremo = !empty($nota) ? $pensum->GetValoracion($pestudio->id, $nota) : null;
                        $valoracion = $baremo ? $baremo->valoracion : null;
                    @endphp
                    {{-- <b>{{ $valoracion ?? ''}}</b> --}}
                    <span style="font-size:0.8rem;text-align:right">&nbsp;</span>
                </td>

            </tr>
            {{-- </thead> --}}
<tbody style="display: table-row-group;">
    <tr>
        <td colspan="2" data-ident="ident">

            @php $query = $pensum->pevaluacions->where('lapso_id',$lapso->id)->where('seccion_id',$seccion->id)->first(); @endphp
            @php $evaluacions = (!empty($query->evaluacions)) ? $query->evaluacions : array() ; @endphp

            <table class="table table-sm">

                <tbody id="tdatos">
                    @foreach ($evaluacions as $evaluacion)
                        @php $boletin = $evaluacion->boletins->where('estudiant_id',$estudiant->id)->first(); @endphp
                        @php $nota = ($boletin) ? $boletin->nota : null @endphp
                        @php $count_eva++; @endphp
                        <tr style="font-size:0.7rem">
                            <td style="">
                                <div style="margin-left: 0.6rem; font-size: 0.5rem !important">
                                    {{ $loop->iteration }}.-
                                    {{ Str::limit(strtoupper($evaluacion->description), 120, '...') }}
                                </div>
                            </td>
                            @php
                                // $baremo = (!empty($nota)) ? $boletin->getBaremo($pestudio->id) : null;
                                $baremo = !empty($nota)
                                    ? $pensum->GetValoracion($pestudio->id, $nota, $lapso->id)
                                    : null;
                                // $baremo = ($boletin) ? $boletin->getBaremo($pestudio->id) : null;
                                $valoracion = $baremo ? $baremo->valoracion : null;
                            @endphp
                            <td style="width:6%; text-align:right">{{ !empty($boletin->nota) ? $valoracion : null }}
                            </td>
                        </tr>

                        @if ($count_eva >= 30)
                            @php $count_eva = 0; @endphp
                            <tr>
                                <td>
                                    <div style="page-break-after:always;display:block;"></div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

        </td>
    </tr>
</tbody>
@endif

@if ($rowCount % 20 == 0)
    <tr>
        <td colspan="2">
            <div style="page-break-after: always;"></div>
        </td>
    </tr>
@endif
@endforeach

{{-- <tr style="font-size:1rem !important;background-color:#e0e0e0">
        @php $notal_total = (!empty($count_nota)) ? round(($sum_nota/$count_nota),2):null; @endphp
        <td style="text-align:right"><b>Varolación del Lapso</b></th>
        <td style="text-align:center">
            <b>{{ (!empty($notal_total)) ? $baremo->getValoracion($pestudio->id,$notal_total)->valoracion : null}}</b>
        </td>
    </tr> --}}

</tbody>
