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
            $promedio = $estudiant->getPromedioFinal($decimal) ;
        @endphp

        <tr style="font-size:0.62rem;">

            <td style="width:0.5rem !important">
                {{$loop->iteration }}
            </td>

            <td style="white-space: nowrap; width:5rem !important">
                {{Str::limit($estudiant->fullname,45)}}
            </td>

            @foreach ($pensums as $pensum)

                @php $pensum_id = $pensum->id; @endphp

                @foreach ($lapsos as $lapso)
                    @php
                        $nota = $estudiant->getNota($lapso->id,$pensum->id);
                    @endphp

                    <td style="">
                    {{-- <td style="width:1.4rem !important"> --}}
                        {{ ($nota) ? round($nota,0):''}}
                    </td>

                @endforeach

                @php $nota_final = $estudiant->getNotaFinal($pensum_id,0); @endphp

                <th style="background-color: #ccc;">
                {{-- <th style="background-color: #ccc;width:1.4rem !important"> --}}

                    {{ ($nota_final) ? round($nota_final,0):''}}

                </th>

                @if ($nota < $aprobacion && !empty($nota )) @php $count_ar++; @endphp @endif

            @endforeach

            <td style=" color:#ff0000">
                {{ $count_ar ?? '' }}
            </td>
            <th style="text-align:center">
                <span >
                    {{ $promedio ?? ''}}
                </span>
            </th>

        </tr>

        @endforeach

    </tbody>
