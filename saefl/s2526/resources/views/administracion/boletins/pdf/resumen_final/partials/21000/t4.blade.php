<table border="1" cellpadding="0" cellspacing="0" width="100%" class="table-list" class="text_small" style="width: 100% !important;">
    <thead  style="font-size: 0.7rem !important">
        <tr>
            <th colspan="14">IV. Resumen Final de Evaluación</th>
        </tr>
        <tr>
            <th rowspan="2" style="width: 1rem !important;">N°</th>
            <th rowspan="2">Cédula de Identidad o Cédula Escolar</th>
            <th rowspan="2">Lugar de Nacimiento</th>
            <th rowspan="2">EF</th>
            <th rowspan="2">Sexo</th>
            <th colspan="3">Fecha de Nacimiento</th>
            <th colspan="6">Resultados de la Evaluación</th>
        </tr>
        <tr>
            <th>Día</th>
            <th>Mes</th>
            <th>Año</th>
            @foreach ($baremos as $baremo) <th align="center">{{$baremo->literal ?? ''}}</th> @endforeach
            <th>P</th>
        </tr>
    </thead>
    <tbody style="font-size: 0.55rem !important">
        @php $estudiants_count = $estudiants->count(); @endphp
        @forelse ($estudiants as $estudiant)
            @php $totales[$estudiant->literal] = (array_key_exists($estudiant->literal, $totales)) ? ($totales[$estudiant->literal] + 1) : 1; @endphp
            <tr>
                <td style="width: 1rem !important;">{{$loop->iteration}}</td>
                <td>{{$estudiant->ci_estudiant ?? ''}}</td>
                <td>{{$estudiant->town_hall_birth ?? ''}}</td>
                <td align="center">{{$estudiant->entidad_federal ?? ''}}</td>
                <td  style="width:10px !important" align="center">{{$estudiant->gender_sm ?? ''}}</td>
                <td align="center">{{$estudiant->day_birth ?? ''}}</td>
                <td align="center">{{$estudiant->month_birth ?? ''}}</td>
                <td align="center">{{$estudiant->year_birth ?? ''}}</td>
                @foreach ($baremos as $baremo)
                    @php
                        $select = ($estudiant->literal == $baremo->literal) ? 'X':'*';
                    @endphp
                    <td align="center">
                        {{-- {{$estudiant->literal}} --}}
                        {{$select ?? ''}}
                    </td>
                @endforeach
                <td align="center">*</td>
            </tr>
        @empty
        <tr>
            <th colspan="14">No hay datos</th>
        </tr>
        @endforelse

        @if ($estudiants_count < $limit_page)
            @php
            $down = $estudiants_count + 1;
            $up = $limit_page;
            @endphp
            @for ($j = $down; $j <= $up; $j++)
                <tr>
                    <td style="width: 1rem !important;">{{$j}}</td>
                    @for ($i = 1; $i < 14; $i++) <td align="center"> &nbsp; *</td>  @endfor
                </tr>
            @endfor
        @endif

        <tr>
            <th colspan="8">TOTALES</th>
            @foreach ($baremos as $baremo)
                @php
                    $count = (array_key_exists($baremo->literal, $totales)) ? $totales[$baremo->literal] : '00';
                    $total = str_pad($count, 2, "0", STR_PAD_LEFT);
                @endphp
                <th align="center">{{$total ?? ''}}</th>
            @endforeach
            <th align="center">00 </th>
        </tr>

    </tbody>
</table>
