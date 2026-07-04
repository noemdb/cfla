<table cellpadding="4" cellspacing="4" width="100%">
  <tbody>
    <tr>
      <td colspan="7" rowspan="3">
        <img alt="{{$inscripcion->logo ?? ''}}" height="30px" src="{{ asset('images/brand/gob/logo.png') }}">
      </td>
      <th colspan="16" align="center" class="uline">CERTIFICACI&Oacute;N DE CALIFICACIONES EMG</th>
    </tr>
    <tr>
      <th width="111" align="left" class="no_wrap">I. Plan de Estudio:</th>
      <td colspan="13" class="td_uline">{{ $pestudio->name ?? '' }}</td>
      <td width="51">C&oacute;digo: </td>
      <td width="40" class="td_uline">{{ $pestudio->code_oficial ?? '' }}</td>
    </tr>
    <tr>
      <td  class="no_wrap">Lugar y Fecha de Expedici&oacute;n:</td>
      <td colspan="16" class="td_uline" style=" text-transform:uppercase">SAN FELIPE {{ $fecha_remision->format('j') . ' de ' . $fecha_remision->format('F').' de '.$fecha_remision->format('Y') }}</td>
    </tr>
    <tr>
      <th colspan="23" align="left">II. DATOS DEL PLANTEL O ZONA EDUCATIVA QUE EMITE LA CERTIFICACIÓN:</th>
    </tr>
    <tr>
      <td width="40">C&oacute;digo:</td>
      <td colspan="5" class="td_uline">{{ $institucion->code_oficial ?? '' }}</td>
      <td colspan="17" class="td_uline" style="padding: 0 !important; margin: 0 !important;">
          <div style="display: inline;border-bottom-style: solid;border-bottom-width: 1px; border-color: #fff;">Denominación y Epónimo:</div>
          <div style="display: inline; width: 100% !important">
            {{ $institucion->name ?? '' }}
          </div>
      </td>
      {{-- <td colspan="16" class="td_uline">{{ $institucion->name ?? '' }}</td> --}}
    </tr>
    <tr>
      <td>Direcci&oacute;n:  </td>
      <td colspan="15" class="td_uline">{{ $institucion->address ?? '' }}</td>
      <td colspan="7" class="td_uline" style="padding: 0 !important; margin: 0 !important;">
        <div style="display: inline;border-bottom-style: solid;border-bottom-width: 1px; border-color: #fff;">Tel&eacute;fono:</div>
        <div style="display: inline; width: 100% !important">
            {{ $institucion->phone ?? '' }} / {{ $institucion->phone2 ?? '' }}
        </div>
      </td>
      {{-- <td align="right">Tel&eacute;fono:</td> --}}
      {{-- <td colspan="6" class="td_uline">{{ $institucion->phone ?? '' }} / {{ $institucion->phone2 ?? '' }}</td> --}}
    </tr>
    <tr>
      <td>Municipio:</td>
      <td colspan="6" class="td_uline">{{ $institucion->town_hall ?? '' }}</td>
      <td colspan="9" class="td_uline" style="padding: 0 !important; margin: 0 !important;">
        <div style="display: inline;border-bottom-style: solid;border-bottom-width: 1px; border-color: #fff;">Entidad Federal: &nbsp;</div>
        <div style="display: inline; width: 100% !important">
            {{ $institucion->state ?? '' }}
        </div>
      </td>
      {{-- <td align="right">Entidad Federal: </td> --}}
      {{-- <td colspan="8" class="td_uline">{{ $institucion->state ?? '' }}</td> --}}
      <td colspan="7" class="td_uline" style="padding: 0 !important; margin: 0 !important;">
        <div style="display: inline;border-bottom-style: solid;border-bottom-width: 1px; border-color: #fff;">CDCEE: &nbsp;</div>
        <div style="display: inline; width: 100% !important">
            YARACUY
        </div>
      </td>
      {{-- <td colspan="2" align="right">Zona Educativa: </td> --}}
      {{-- <td colspan="6" class="td_uline">YARACUY</td> --}}
    </tr>
    <tr>
      <th colspan="23" align="left">III. Datos de Identificaci&oacute;n del Estudiante</th>
    </tr>
    <tr>
      <td colspan="7" class="td_uline" style="padding: 0 !important; margin: 0 !important;">
        <div style="display: inline;border-bottom-style: solid;border-bottom-width: 1px; border-color: #fff;"> C&eacute;dula de Identidad: &nbsp;</div>
        <div style="display: inline; width: 100% !important">
            {{-- {{ ($estudiant->nacionalidad ?? '') }}-{{ $estudiant->ci_estudiant ?? '' }} --}}
            {{-- {{ $estudiant->ci_estudiant ?? '' }} --}}
            {{-- {{ $estudiant->formatted_ci ?? '' }} --}}
            {{ $estudiant->getCiFullF2('-') ?? '' }}
        </div>
      </td>
      {{-- <td class="no_wrap" colspan="2">C&eacute;dula de Identidad:</td> --}}
      {{-- <td colspan="5" class="td_uline"> --}}
        {{-- {{ ($estudiant->nacionalidad ?? '') }}-{{ $estudiant->ci_estudiant ?? '' }} --}}
      {{-- </td> --}}

      <td colspan="16" class="td_uline" style="padding: 0 !important; margin: 0 !important;">
        <div style="display: inline;border-bottom-style: solid;border-bottom-width: 1px; border-color: #fff;"> Fecha de Nacimiento: &nbsp;</div>
        <div style="display: inline; width: 100% !important">
            {{ f_date($estudiant->date_birth,'/') ?? '' }}
        </div>
      </td>
      {{-- <td class="no_wrap" align="right">Fecha de Nacimiento: </td> --}}
      {{-- <td colspan="15" class="td_uline">{{ f_date($estudiant->date_birth,'/') ?? '' }}</td> --}}

      {{-- @php $date_birth = (!empty($estudiant->date_birth)) ? Jenssegers\Date\Date::createFromDate($estudiant->date_birth): null; @endphp       --}}
      {{-- <td colspan="15" class="td_uline">{{ $date_birth->format('l j F Y') ?? '' }}</td> --}}
    </tr>
    <tr>
      <td>Apellidos:   </td>
      <td colspan="7" class="td_uline">{{ $estudiant->lastname ?? '' }}</td>
      <td><span class="p0 ft2">Nombres:</span></td>
      <td colspan="14" class="td_uline">{{ $estudiant->name ?? '' }}</td>
    </tr>
    <tr>
      <td colspan="8" class="td_uline" style="padding: 0 !important; margin: 0 !important;">
        <div style="display: inline;border-bottom-style: solid;border-bottom-width: 1px; border-color: #fff;"> Lugar de Nacimiento: Pa&iacute;s: &nbsp;</div>
        <div style="display: inline; width: 100% !important">
            {{ $estudiant->country_birth ?? '' }}
        </div>
      </td>
      {{-- <td colspan="2" class="no_wrap">Lugar de Nacimiento: Pa&iacute;s:</td> --}}
      {{-- <td colspan="6" class="td_uline">{{ $estudiant->country_birth ?? '' }}</td> --}}

      <td>Estado:</td>
      <td colspan="8" class="td_uline">{{ $estudiant->state_birth ?? '' }}</td>
      <td align="right">Municipio:</td>
      <td colspan="5" class="td_uline">{{ $estudiant->town_hall_birth ?? '' }}</td>
    </tr>
  </tbody>
</table>

