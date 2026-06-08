<div>

    <table border="1" cellpadding="0" cellspacing="0" width="100%" class="table-list" class="text_small" style="width: 100% !important;">
        <thead>
        <tr>
            <th colspan="12">III. Identificación del Estudiante</th>
            @php $colspan = $pensums->count() + 1; @endphp
            <th colspan="{{$colspan ?? ''}}">IV. Resumen Final del Rendimiento</th>
        </tr>
        </thead>
        <tbody style="font-size: 0.55rem !important">
        <tr align="center" style="font-weight: bold">
            <td style="width: 16px !important;" rowspan="4">N°</td>
            <td colspan="2" rowspan="4">Cédula<br>de<br>Identidad</td>
            <td rowspan="4" style="min-width: 4rem !important">Apellidos</td>
            <td colspan="2" rowspan="4">Nombres</td>
            <td rowspan="4">Lugar de<br>Nacimiento</td>
            <td rowspan="4">EF</td>
            <td rowspan="4" style="width:0.2rem !important"><div class="orientation">Sexo</div></td>
            <td colspan="3" rowspan="2">Fecha de<br>Nacimiento</td>
            @php $colspan = $pensums->count() - 1; @endphp
            <td colspan="{{ $colspan ?? ''}}">ÁREAS DE FORMACIÓN</td>
            <td colspan="1" class="td-less-border">&nbsp;</td>
            <td colspan="1" class="text_small text-strong td-less-border" align="center" rowspan="2">PART. EN GRUPOS DE CREACIÓN, RECREACIÓN Y PRODUCCIÓN</td>
        </tr>
        <tr align="center" style="font-weight: bold">
            <td colspan="{{ $colspan ?? ''}}" align="center">ÁREA COMÚN</td>
            <td colspan="1" class="td-less-border">&nbsp;</td>
        </tr>
        <tr align="center" style="font-weight: bold">
            <td rowspan="2"><div class="orientation">Día</div></td>
            <td rowspan="2"><div class="orientation">Mes</div></td>
            <td rowspan="2"><div class="orientation">Año</div></td>
            @foreach ($pensums as $pensum)
                <td class="" >{{$loop->iteration ?? ''}}</td>
            @endforeach
            <td rowspan="2">GRUPO</td>
        </tr>
        <tr>
            @foreach ($pensums as $pensum)
                <td class="td-nowrap small-90"  align="center">{{$pensum->asignatura->code_sm ?? ''}}</td>
            @endforeach
        </tr>

        @php
            $escala = $pestudio->escala;
            $aprobacion = ($escala->aprobacion) ? : '';
            $count_arr_in = array();
            $count_arr_ia = array();
            $count_arr_ap = array();
            $count_arr_ar = array();
            $count_arr_nc = array();
        @endphp

        @foreach ($estudiants as $estudiant)
            @php
                $count_item++;
                $class_name_studiant = ((strlen($estudiant->name) + strlen($estudiant->lastname)) > 10) ? 'text_small':null ;
                $inscripcion = $estudiant->getInscripcion();
            @endphp
            <tr style="line-height: 0.9rem !important">
                <td style="width: 16px !important;">{{ str_pad($count_item, 2, "0", STR_PAD_LEFT) ?? ''}}</td>
                {{-- <td colspan="2" class="td-nowrap small">{{$estudiant->getCiFull() ?? ''}}</td> --}}
                <td colspan="2" class="td-nowrap small">{{$estudiant->getCiFullF2() ?? ''}}</td>
                <td class="td-nowrap {{$class_name_studiant ?? ''}}">{{$estudiant->lastname ?? ''}}</td>
                <td colspan="2" class="td-nowrap {{$class_name_studiant ?? ''}}">{{$estudiant->name ?? ''}}</td>
                <td class="td-nowrap text_small"> &nbsp; {{$estudiant->town_hall_birth ?? ''}} &nbsp; </td>
                <td align="center">{{$estudiant->entidad_federal ?? ''}}</td>
                <td  style="width:10px !important" align="center">{{$estudiant->gender_sm ?? ''}}</td>
                <td align="center">{{$estudiant->day_birth ?? ''}}</td>
                <td align="center">{{$estudiant->month_birth ?? ''}}</td>
                <td align="center">{{$estudiant->year_birth ?? ''}}</td>
                @foreach ($pensums as $pensum)
                    @php
                        $nota_final = $estudiant->getNotaFinal($pensum->id,0) ;
                        $escolaridad = $inscripcion->escolaridad_id;
                        $status_ar = false;

                        if (!array_key_exists($pensum->id,$count_arr_in)) { $count_arr_in[$pensum->id] = 0; }

                        if ( !empty($nota_final) ) {
                            $count_arr_in[$pensum->id]++;
                        }
                        else {
                            if ($escolaridad==1) {
                                    $count_arr_in[$pensum->id]++;
                            }
                        }

                        if (!array_key_exists($pensum->id,$count_arr_ia)) { $count_arr_ia[$pensum->id] = 0; }
                        if ($nota_final == 'IN') { $count_arr_ia[$pensum->id]++; }

                        if (!array_key_exists($pensum->id,$count_arr_ap)) { $count_arr_ap[$pensum->id] = 0; }
                        if ($nota_final >= $aprobacion && !empty($nota_final) && $nota_final <> 'IN') { $count_arr_ap[$pensum->id]++; }

                        if (!array_key_exists($pensum->id,$count_arr_ar)) { $count_arr_ar[$pensum->id] = 0; }
                        if ($nota_final < $aprobacion && ($escolaridad == 1 )) { $count_arr_ar[$pensum->id]++; $status_ar = true;}

                        if (!array_key_exists($pensum->id,$count_arr_nc)) { $count_arr_nc[$pensum->id] = 0; }
                        if (empty($nota_final) && $escolaridad == 2) { $count_arr_nc[$pensum->id]++; }
                        $nota_final = (is_numeric($nota_final)) ? str_pad($nota_final, 2, "0", STR_PAD_LEFT) : $nota_final ;

                    @endphp
                    <td class="td-nowrap" align="center" style="width:24px !important; {{($status_ar) ? ' color: red ' : null}}">
                        @if ($nota_final==null)
                            @php $td_nota = ($escolaridad == 2) ? 'NC':'IN' ; @endphp
                        @else
                            @php $td_nota = $nota_final @endphp
                        @endif
                        {{ $td_nota ?? '' }}
                    </td>
                @endforeach
                @php
                    $name = ($estudiant->grupo_estable) ? $estudiant->grupo_estable->name : null;
                    $len = strlen($name);
                @endphp
                <td class="text_small" align="center" style="white-space: nowrap !important;{{ ($len>40) ? 'font-size:0.3rem !important' : null }}">
                    {{$estudiant->grupo_estable->name ?? null}}
                </td>
            </tr>
        @endforeach
        @if ($estudiants->count() < $limit_page)

            @php $count_item++; @endphp

            @for ($i = $count_item; $i <= $limit_page; $i++)

            @php $col_leng = $pensums->count() + 10 @endphp

                <tr style="line-height: 0.9rem !important">
                    <td>{{ str_pad($i, 2, "0", STR_PAD_LEFT) ?? ''}}</td>
                    @for ($n = 0; $n < $col_leng; $n++)
                        <td style="width:24px !important;" {{ ($n==0 || $n==2) ? " colspan=2 ":null }} align="center" class="td-nowrap small">*</td>
                    @endfor
                </tr>

            @endfor

        @endif

        @include('administracion.boletins.pdf.resumen_final.partials.31059.t31')
        @include('administracion.boletins.pdf.resumen_final.partials.31059.t32')

        </tbody>
    </table>

</div>
