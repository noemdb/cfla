<tbody id="tdatos">

    @php $sum_nota = 0; @endphp
    @php $count_nota = 0; @endphp
    @php $count_eva = 0; @endphp
    @foreach ($pensums as $pensum)
        @php $asignatura = (!empty($pensum->asignatura)) ? $pensum->asignatura:null ; @endphp
        @php $pevaluacion = $pensum->pevaluacions->where('seccion_id',$seccion->id)->where('lapso_id',$lapso->id)->first(); @endphp
        @php $evaluacions = ($pevaluacion) ? $pevaluacion->evaluacions : array() ; @endphp
        @php $profesor = ($pevaluacion) ? $pevaluacion->profesor : null  @endphp
        @php $count = null; @endphp
        @if ($profesor)
            {{-- <thead style="background-color:#e0e0e0"> --}}
            <tr style="background-color:#e0e0e0">
                <td style="font-size:0.8rem;text-align:left">
                    {{ $asignatura->name }} || <span style="font-size:0.8rem">Prof:
                        {{ $profesor->fullname ?? '' }}</span>
                    ||<span style="font-size:0.8rem;text-align:right">VAL.</span>
                </td>
                <td>&nbsp;</td>

                @php $nota = $pensum->GetNota($estudiant->id,$seccion->id,$lapso->id) ; @endphp
                @if ($nota)
                    @php $sum_nota = $sum_nota + $nota; @endphp
                    @php $count_nota = $count_nota + 1; @endphp
                @endif
                {{--
                            <td style="font-size:0.8 rem;text-align:right">
                                @php $valoracion = (!empty($nota)) ? $pensum->GetValoracion($pestudio->id,$nota)->valoracion : null; @endphp
                                <b>{{ $valoracion ?? ''}}</b>
                            </td>
                        --}}
            </tr>
            {{-- </thead> --}}

            <tr>
                <td colspan="2">

                    <table class="table table-sm small">

                        <tbody id="tdatos">
                            @foreach ($evaluacions as $evaluacion)
                                @php $boletin = $evaluacion->boletins->where('estudiant_id',$estudiant->id)->first(); @endphp
                                @php $nota = ($boletin) ? $boletin->nota : null @endphp
                                {{--
                                    @if ($boletin)
                                        <tr style="font-size:0.7rem">
                                            <td>
                                                <div class="text_wrap" style="font-size: 0.5rem !important;margin-left: 0.6rem;">
                                                    {{$loop->iteration}}.- {{ strtoupper($evaluacion->description) }}
                                                </div>
                                            </td>
                                            <td style="text-align:right">{{ (!empty($boletin->nota)) ? $boletin->getBaremo( $pestudio->id )->valoracion : null }}</td>
                                        </tr>
                                    @endif
                                --}}
                                @if ($boletin)
                                    @php $count++; @endphp
                                    @php $count_eva++; @endphp

                                    <tr style="font-size:0.7rem">
                                        <td>
                                            <div style="font-size: 0.5rem !important; margin-left: 0.6rem;">
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
                                        <td style="width:6%; text-align:right">
                                            <b style="font-size:0.7rem;text-align:right">
                                                {{ !empty($boletin->nota) ? $valoracion : null }}</b>
                                        </td>
                                    </tr>


                                    @if ($count_eva >= 35)
                                        @php $count_eva = 0; @endphp
                                        <tr>
                                            <td>
                                                <div style="page-break-after:always;display:block;"></div>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach

                            @if ($count_nota == null)
                                <tr style="font-size:0.5rem">
                                    <td colspan="2" style="text-align: right; color: #6c757d">SIN EVALUACIONES
                                        REGISTRADAS</td>
                                </tr>
                            @endif

                        </tbody>
                    </table>

                </td>
            </tr>
        @endif
    @endforeach

</tbody>
