
<div class="tr_strong" align="center" style="padding-top: 0.1rem; font-size:0.7rem; text-transform: uppercase;">{{ $grado->name ?? ''}}</div>
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
      <td class="tr_strong valign" rowspan="2" align="center">
        {{-- <div class="v_text" style="font-size:0.5rem;width:2rem;vertical-align: text-bottom;"> --}}
        <div class="rotate-text orientation" style="font-size:0.5rem;width:0.8rem">
          INST. EDUC.
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
        $tevaluacion = ($nota->tevaluacion) ? $nota->tevaluacion : null ;
        $status_exonerate = ($tevaluacion) ? $tevaluacion->status_exonerate : null ;

        $tevaluacion_id = ($tevaluacion) ? $tevaluacion->id : null ;
        $status_exonerate = ($tevaluacion_id == 15 || $tevaluacion_id == 16) ? true : false ;

        $valor = (!empty($nota->valor)) ? $nota->valor : '*' ;
        $valor = (!empty($nota->valor)) ? $nota->valor : '*' ;
        $str_valor = (!empty($nota->valor)) ? num_to_string($nota->valor): '*' ;
        $code = (!empty($nota->valor)) ?$nota->tevaluacion->code: '*' ;
        $mes = (!empty($nota->valor)) ? substr($nota->fecha,0,2) : '*' ;
        $ano = (!empty($nota->valor)) ? substr($nota->fecha,-4) : '*' ;
        $institucion = (!empty($nota->valor)) ? $arr_institucion[$nota->institucion_id] : '*' ;

        $valor = (!empty($valor) && is_numeric($valor) && $valor < 10) ? $valor = str_pad($valor, 2, "0", STR_PAD_LEFT) : $valor ;

        $valor = ($valor == -1) ? 'I' : $valor ;

      @endphp
      <tr>
        <td align="left" class="{{ $class_asignatura_name ?? ''}} no_wrap">{{ $nota->pensum->asignatura->name ?? ''}}</td>
        {{-- <td align="center" class="">{{ (!empty($nota->valor)) ? $nota->valor : '*'}}</td> --}}
        <td align="center" class="" style="">{{ ($status_exonerate) ? 'EX' : $valor }}</td>
        <td align="left" style="font-size:0.45rem" class="">{{($status_exonerate) ? 'EXONERADA' : $str_valor}}</td>
        <td align="center" class="">{{ ($status_exonerate) ? '*' : $code }} </td>
        <td align="center" class="">{{ ($status_exonerate) ? '*' : $mes }} </td>
        <td align="center" class="">{{ ($status_exonerate) ? '*' : $ano }} </td>
        <td align="center" class="">{{ $institucion }} </td>
      </tr>
    @endforeach

    @php $offset = 10 - $notas->count() @endphp
    @for ($i = 0; $i < $offset; $i++)
        <tr class="">
            <td align="left" style="">*</td>
            <td align="center" style="">*</td>
            <td align="left" style="">*</td>
            <td align="center" style="">*</td>
            <td align="center" style="">*</td>
            <td align="center" style="">*</td>
            <td align="center" style="">*</td>
        </tr>
    @endfor

  </table>
