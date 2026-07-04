<span class="tr_strong" style="padding-top:0;padding-left:0.5rem; font-size:0.8rem">
    V. Cantidad de Estudiantes a los cuales se le otorgó el Título
</span>
<table border="1" class="table-seccion grid">
    <thead style="font-size: 0.8rem !important;">
        <tr>
          <th rowspan="2" style="height: 3rem !important">N°</th>
          <th rowspan="2">Serial del Título</th>
          <th rowspan="2" align="center">Cédula de Identidad</th>
          <th rowspan="2">Nombres y Apellidos</th>
          <th colspan="2" style="font-size: 0.55rem !important">Lugar de Nacimiento</th>
          <th colspan="3" align="center" style="font-size: 0.55rem !important">Fecha de Nacimiento</th>
          <th rowspan="2">Observaciones</th>
        </tr>
        <tr>
          <th>EF</th>
          <th>Municipio</th>
          <th>Día</th>
          <th>Mes</th>
          <th>Año</th>
        </tr>
      </thead>

    <tbody>

    @foreach ($titulos_chunk as $titulo)
        @php
            $count_item++;
            $estudiant = $titulo->estudiant
        @endphp
        <tr >
            <td>{{$count_item ?? ''}}</td>
            <td class="small">{{$titulo->serie ?? ''}}</td>
            <td class="td-nowrap small">{{$estudiant->ci_estudiant ?? ''}}</td>
            <td class="td-nowrap small">{{$estudiant->fullname ?? ''}}</td>
            <td class="small">{{$estudiant->entidad_federal ?? ''}}</td>
            <td class="td-nowrap small">{{$estudiant->town_hall_birth ?? ''}}</td>
            <td class="small">{{$estudiant->day_birth ?? ''}}</td>
            <td class="small">{{$estudiant->month_birth ?? ''}}</td>
            <td class="small">{{$estudiant->year_birth ?? ''}}</td>
            <td {{(!isset($titulo->observations)) ? 'align=center':null}} >{{(isset($titulo->observations)) ? $titulo->observations : null}}</td>
        </tr>
    @endforeach

    @if ($titulos_chunk->count() < 25)

        @php $count_item++; @endphp

        @for ($i = $count_item; $i <= 25; $i++)

            <tr>
                <td>{{$i}}</td>
                @for ($n = 0; $n < 9; $n++)
                    <td align="center"> * </td>
                @endfor
            </tr>

        @endfor

    @endif

    </tbody>
</table>
