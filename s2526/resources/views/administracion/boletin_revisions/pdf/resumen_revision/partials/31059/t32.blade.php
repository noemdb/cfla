<tr>
    <th colspan="5" style="font-size: 0.7rem !important">V. Profesores por Áreas</th>
    <td colspan="4" rowspan="2" align="center" style="font-weight: bold">Apellidos y Nombres</td>
    <td colspan="3" rowspan="2" align="center" style="font-weight: bold">Cédula de Identidad</td>
    @php
        $pensums_count = $pensums->count() - 3;
        $pensums_par = ( $pensums_count % 2 == 0 ) ? true:false;
        $colspan_firma = ( $pensums_par ) ? 4 : 5 ;
        $colspan_vi = ( $pensums_par ) ? $pensums_count : $pensums_count - 1 ;
    @endphp
    <td colspan="{{ $colspan_firma }}" rowspan="2" align="center" style="font-weight: bold">Firma</td>
    <th colspan="{{ $colspan_vi }}" align="center" style="font-size: 0.7rem !important">VI. Identificaci&oacute;n del Curso</th>

  </tr>
  <tr>
    <td align="center" style="font-weight: bold;">N°</td>
    <td colspan="4" align="center" style="font-weight: bold">Áreas de Formación</td>
    <td colspan="{{ $colspan_vi }}" align="center">PLAN DE ESTUDIO</td>
  </tr>
  @foreach ($pensums as $pensum)
    @php
        $asignatura = $pensum->asignatura;
        $profesor = $pensum->pevaluacions->last()->profesor;
        $class_name_profesor = (strlen($profesor->fullname)  > 15) ? 'small':null ;
        $class_name_asignatura = (strlen($asignatura->name)  > 25) ? 'text_small':null ;
        $exist = ($pensums_revision->where('id',$pensum->id)->first()) ? true:false ;
    @endphp
    <tr>
        <td >{{$loop->iteration }}</td>
        <td style="font-weight: bold; width:18px !important;">{{$pensum->asignatura->code_sm ?? ''}}</td>
        <td colspan="3" class="{{ $class_name_asignatura ?? '' }}">{{$asignatura->name ?? ''}}</td>
        <td colspan="4" class="td-nowrap {{$class_name_profesor ?? 'small-90'}}" {{ (!$exist) ? ' align=center ' : null}}>{{ ($exist) ? $profesor->fullname : '*'}}</td>
        <td colspan="3" {{ (!$exist) ? ' align=center ' : null}}>{{ ($exist) ? $profesor->getCiFull('-') : '*'}}</td>
        <td colspan="{{ $colspan_firma }}" {{ (!$exist) ? ' align=center ' : null}}>{{ ($exist) ? null : ' * '}}</td>

        @switch($loop->iteration)
            @case(1) <td colspan="{{ $colspan_vi ?? ''}}" align="center" style="font-weight: bold">{{ $pestudio->name ?? '' }}</td> @break
            @case(2) <td colspan="{{ $colspan_vi ?? ''}}" align="center" style="font-weight: normal">CÓDIGO</td> @break
            @case(3) <td colspan="{{ $colspan_vi ?? ''}}" align="center" style="font-weight: bold">{{ $pestudio->code ?? '' }}</td> @break
            @case(4) <td colspan="{{ $colspan_vi ?? ''}}" align="center" style="font-weight: normal">AÑO CURSADO</td> @break
            @case(5) <td colspan="{{ $colspan_vi ?? ''}}" align="center" style="font-weight: bold">{{ $grado->short_name ?? '' }}</td> @break
            @case(6) <td colspan="{{ $colspan_vi ?? ''}}" align="center" style="font-weight: normal">SECCIÓN</td> @break
            @case(7) <td colspan="{{ $colspan_vi ?? ''}}" align="center" style="font-weight: bold">{{ $seccion->name ?? '' }}</td> @break
            @case(8)
                <td colspan="{{ floor($colspan_vi/2) + 1 }}" align="center" style="font-weight: normal" class="text_small">No DE ESTUDIANTES<br>POR SECCI&Oacute;N</td>
                <td colspan="{{ ceil($colspan_vi/2) - 1 }}" align="center" style="font-weight: normal; min-height: 10rem !important;" class="text_small">No DE ESTUDIANTES<br> EN ESTA P&Aacute;GINA</td>
            @break
            @case(9)
                @php $rowspan = $pensums->count() - 8  @endphp
                <th rowspan="{{$rowspan}}" colspan="{{ floor($colspan_vi/2) + 1 }}" align="center" style="font-size: 0.7rem !important" >{{ zfill ($estudiants->count(),2) ?? '' }}</th>
                <th rowspan="{{$rowspan}}" colspan="{{ ceil($colspan_vi/2) - 1 }}" align="center" style="font-size: 0.7rem !important" >{{ zfill ($estudiants->count(),2) ?? '' }}</th>
            @break

        @endswitch

    </tr>
  @endforeach

{{-- vertical-align: top !important; --}}
