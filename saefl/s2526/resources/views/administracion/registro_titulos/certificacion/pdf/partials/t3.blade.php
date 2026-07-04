
<div class="tr_strong" align="center" style="padding-top: 0.1rem; font-size:0.7rem">{{ $grado->name ?? ''}}</div>
{{-- <table width="100%" cellpadding="0" cellspacing="0" border="1" style="padding:1px 0px 1px 0px;margin:1px 0px 1px 0px"> --}}
<table width="100%" cellpadding="0" cellspacing="0" border="1" id="table_normal">
    {{-- <thead>
      <tr>
          <th align="center" colspan="7" style="border: 0px">
            {{ $grado->name ?? ''}}
          </th>
      </tr>
    </thead> --}}
    <tr>
      <td class="tr_strong valign" rowspan="2" align="center">ÁREAS DE FORMACIÓN</td>
      <td class="tr_strong" colspan="2" align="center">CALIFICACIÓN</td>
      <td class="tr_strong no_wrap valign" rowspan="2" align="center">T-E</td>
      <td class="tr_strong" colspan="2" align="center">FECHA</td>
      <td class="tr_strong valign"  rowspan="2" align="center">
        <div class="v_text" style="font-size:0.5rem;">
          PLANTEL
        </div>
      </td>
    </tr>
    <tr>
      <td class="tr_strong" align="center">N°</td>
      <td class="tr_strong" align="center">LETRAS</td>
      <td class="tr_strong" align="center"><small>MES</small></td>
      <td class="tr_strong" align="center"><small>AÑO</small></td>
    </tr>

    @php $notas = $estudiant->GetHNotas($grado->id,'true') @endphp
    @foreach ($notas as $nota)
      @php
        $class_asignatura_name = (strlen($nota->pensum->asignatura->name) > 25) ? 'td_font_sm_1' : null;

        $valor = (!empty($nota->valor)) ? $nota->valor : '*' ;
        $valor = (!empty($valor) && is_numeric($valor) && $valor < 10) ? $valor = str_pad($valor, 2, "0", STR_PAD_LEFT) : $valor ;
      @endphp
      <tr>
        {{-- <td align="left" class="">{{ $nota->pensum->asignatura->name ?? ''}}</td> --}}
        <td align="left" class="{{ $class_asignatura_name ?? ''}} no_wrap">{{ $nota->pensum->asignatura->name ?? ''}}</td>
        <td align="center" class="">{{ (!empty($valor)) ? $valor : '*'}}</td>
        <td align="left" class="">{{ (!empty($nota->valor)) ? num_to_string($nota->valor): '*'}}</td>
        <td align="center" class="">{{ (!empty($nota->valor)) ? $nota->tevaluacion->code : '*'}}</td>
        <td align="center" class="">{{ (!empty($nota->valor)) ? substr($nota->fecha,0,2) : '*'}}</td>
        <td align="center" class="">{{ (!empty($nota->valor)) ? substr($nota->fecha,-4) : '*'}}</td>
        <td align="center" class="">{{ (!empty($nota->valor)) ? $arr_institucion[$nota->institucion_id] : '*'}}</td>
      </tr>
    @endforeach

    @php $offset = 10 - $notas->count() @endphp
    @for ($i = 0; $i < $offset; $i++)
        <tr class="">
            <td align="left">*</td>
            <td align="center">*</td>
            <td align="left">*</td>
            <td align="center">*</td>
            <td align="center">*</td>
            <td align="center">*</td>
            <td align="center">*</td>
        </tr>
    @endfor

  </table>
