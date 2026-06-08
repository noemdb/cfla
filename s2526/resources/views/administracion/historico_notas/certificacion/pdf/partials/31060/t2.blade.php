<table width="100%" border="0" cellspacing="1" cellpadding="1"  style="padding-top: 0.2rem;">
    <tr>
      <td valign="top" width="50%">
          <TABLE width="100%" border="1" cellpadding=0 cellspacing=0>
          {{-- <TABLE border="1" cellpadding=0 cellspacing=0 width="100%" id="table_normal"> --}}
              <TR class="tr_strong">
                <th align="left" colspan="4" class="th_2_sb" style="padding-top:0;padding-bottom:0;">
                    IV. Instituciones Educativas donde Cursó Estudios
                </th>
              </TR>
              <TR class="tr_strong">
                <TD>N°</TD>
                <TD>Denominación y Epónimo de la Institución Educativa</TD>
                <TD>Localidad</TD>
                <TD>E.F.</TD>
              </TR>

              @php
                $iteration_inst = 0;
                $start = 0;
                $end = 1;
                $take = 2;
              @endphp
              @foreach ($oinstitucions->slice($start,$take) as $institucion)
                <TR>
                  <TD>{{ ($iteration_inst + 1)}}</TD>
                  @php $class_name = (strlen($institucion->name) > 32) ? 'td_font_sm_1' : null; @endphp
                <TD class="{{ $class_name ?? '' }}">{{ $institucion->name ?? '' }}</TD>
                  <TD>{{ $institucion->locations ?? '' }}</TD>
                  <TD>{{ $institucion->state ?? '' }}</TD>
                  @php $iteration_inst++; @endphp
                </TR>
              @endforeach

              @if ($iteration_inst <= $end)
                  @for ($i = $iteration_inst; $i <= $end; $i++)
                    <TR>
                      <TD>{{ ($iteration_inst + 1)}}</TD>
                      <TD>*</TD>
                      <TD>*</TD>
                      <TD>*</TD>
                      @php $iteration_inst++; @endphp
                    </TR>
                  @endfor
              @endif

        </TABLE>

      </td>
      <td valign="top" width="50%">
          <TABLE width="100%" border="1" cellpadding=0 cellspacing=0>
          {{-- <TABLE border="1" cellpadding=0 cellspacing=0 width="100%" id="table_normal"> --}}
              <TR class="tr_strong" style="line-height: 14px; ">
                <TD>N°</TD>
                <TD>Denominación y Epónimo de la Institución Educativa</TD>
                <TD>Localidad</TD>
                <TD>E.F.</TD>
              </TR>
              @php
                // $iteration_inst = 2;
                $start = 2;
                $end = 4;
                $take = 3;
              @endphp
              @foreach ($oinstitucions->slice($start,$take) as $institucion)
                <TR>
                  <TD>{{ ($iteration_inst + 1)}}</TD>
                  <TD>{{ $institucion->name ?? '' }}</TD>
                  <TD>{{ $institucion->locations ?? '' }}</TD>
                  <TD>{{ $institucion->state ?? '' }}</TD>
                  @php $iteration_inst++; @endphp
                </TR>
              @endforeach

              @if ($iteration_inst <= $end)
                  @for ($i = $iteration_inst; $i <= $end; $i++)
                    <TR>
                      <TD>{{ ($iteration_inst + 1)}}</TD>
                      <TD>*</TD>
                      <TD>*</TD>
                      <TD>*</TD>
                      @php $iteration_inst++; @endphp
                    </TR>
                  @endfor
              @endif

           </TABLE>

      </td>
    </tr>
  </table>
