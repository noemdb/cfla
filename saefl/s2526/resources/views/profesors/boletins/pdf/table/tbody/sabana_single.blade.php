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
        @endphp

        <tr style="font-size:0.62rem;">

            <td style="width:0.5rem !important">
                {{$loop->iteration }}
            </td>

            <td style="white-space: nowrap; width:5rem !important; padding-right: 1rem;">
                {{Str::limit($estudiant->fullname,45)}}
            </td>

            @foreach ($pensums as $pensum)

                @php
                    $pensum_id = $pensum->id;
                    $enable_academic_index = $pensum->asignatura->enable_academic_index;
                @endphp

                @foreach ($lapsos as $lapso)
                    @php
                        $nota = $estudiant->getNota($lapso->id,$pensum->id);
                    @endphp

                    <td style="text-align:center">
                        {{ ($nota) ? round($nota,0):''}}
                    </td>

                @endforeach

                @php $nota_final = $estudiant->getNotaFinal($pensum_id,0); @endphp
                @php $promedio_final = $estudiant->getPromedioFinalPensum($pensum_id,1); @endphp

                <th style="background-color: #ccc; text-align:center">

                    {{ $nota_final ?? ''}}

                </th>

                @if ($enable_academic_index == "false")
                    <th style="background-color: #eee; border-right-color:#aaa; text-align:right">
                        {{ $promedio_final ?? '' }}
                    </th>
                @endif

                @if ($pestudio->status_baremo == "true")
                    <th style=" text-align:center">
                        {{ (!empty($nota_final)) ? $baremo->getLiteral($grado->pestudio->id,$nota_final) : null }}
                    </th>
                @endif

                @if ($nota < $aprobacion && !empty($nota )) @php $count_ar++; @endphp @endif

            @endforeach

            <td style="color:#ff0000;text-align:center">
                {{ $count_ar ?? '' }}
            </td>
        </tr>

        @endforeach

    </tbody>
