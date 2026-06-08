<style>
    .divTable{
        /* display: table; */
        width: 100%;
        padding: 0.01rem;
        /* border: 1px solid #000; */
    }
    .divTableBody {
        /* display: table-row-group; */
    }
    .divTableRow {
        /* display: table-row; */
        /* border: 1px solid rgb(56, 56, 56); */
    }
    .divTableHead {
        font-weight: bold;
        font-size: 110%;
    }
    .divTableCell, .divTableHead {
        border: 1px solid #999999;
        /* display: table-cell; */
        /* display: inline; */
        /* display:block; */
    }
    .divTdCell{
        border: 1px solid #999999;
        padding: 0 !important;
        margin: 0 !important;
    }
    .align-right{
        text-align: right !important;
    }
    .align-left{
        text-align: left !important;
    }
    .align-center{
        text-align: center !important;
    }
    .text-bold{
        font-weight: bold !important;
    }
    .div-nowrap {
        word-spacing: 0em !important;
        white-space: nowrap !important;
    }
    .w-50{
        width: 50% !important;
    }
    .divRow{
        border: 1px solid #999999;
        padding: 0 !important;
        margin: 0 !important;
        line-height: 1rem;
    }
</style>

@for ($n = 0; $n < 14; $n++)
<div class="divRow align-center">{{$n}}</div>
@endfor

{{--
<div class="divTable align-center">
	<div class="divTableBody">
		<div class="divTableRow div-nowrap" >
			<div class="divTableHead">VI. Identificaci&oacute;n del Curso</div>
		</div>
	</div>
	<div class="divTableBody">
		<div class="divTableRow">
			<div class="divTableCell">PLAN DE ESTUDIO</div>
		</div>
		<div class="divTableRow">
			<div class="divTableCell text-bold">{{ $pestudio->name ?? '' }}</div>
		</div>
		<div class="divTableRow">
			<div class="divTableCell">C&Oacute;DIGO</div>
		</div>
		<div class="divTableRow">
			<div class="divTableCell text-bold">{{ $pestudio->code ?? '' }}</div>
		</div>
		<div class="divTableRow">
			<div class="divTableCell">A&Ntilde;O CURSADO</div>
		</div>
		<div class="divTableRow">
			<div class="divTableCell text-bold">{{ $grado->name ?? '' }}</div>
		</div>
		<div class="divTableRow">
			<div class="divTableCell">SECCI&Oacute;N</div>
		</div>
		<div class="divTableRow">
			<div class="divTableCell text-bold">{{ $seccion->name ?? '' }}</div>
        </div>
		<div class="divTableRow">
			<div class="divTableCell text-bold">{{ $seccion->name ?? '' }}</div>
        </div>
		<div class="divTableRow">
			<div class="divTableCell text-bold">{{ $seccion->name ?? '' }}</div>
        </div>
		<div class="divTableRow">
			<div class="divTableCell text-bold">{{ $seccion->name ?? '' }}</div>
        </div>
		<div class="divTableRow">
			<div class="divTableCell text-bold">{{ $seccion->name ?? '' }}</div>
        </div>
		<div class="divTableRow">
			<div class="divTableCell text-bold">{{ $seccion->name ?? '' }}</div>
        </div>
		<div class="divTableRow">
			<div class="divTableCell text-bold">{{ $seccion->name ?? '' }}</div>
        </div>
		<div class="divTableRow">
			<div class="divTableCell text-bold">{{ $seccion->name ?? '' }}</div>
        </div>

		<div class="divTableRow">
			<div class="">
                <table border="1" cellpadding="0" cellspacing="0" class="table-list">
                    <tr>
                        <td class="divTdCell small-90">No DE ESTUDIANTES<br>POR SECCI&Oacute;N</td>
                        <td class="divTdCell small-90">No DE ESTUDIANTES<br>EN ESTA P&Aacute;GINA</td>
                    </tr>
                    <tr>
                        <td class="divTdCell text-bold">{{ $estudiants_full->count() ?? '' }}</td>
                        <td class="divTdCell text-bold">{{ $estudiants->count() ?? '' }}</td>
                    </tr>
                </table>
            </div>
        </div>


	</div>
</div>
--}}

{{-- <table border="1" class="table-list align-center">
    <tr>
        <td class="divTdCell small-90">No DE ESTUDIANTES<br>POR SECCI&Oacute;N</td>
        <td class="divTdCell small-90">No DE ESTUDIANTES<br>EN ESTA P&Aacute;GINA</td>
    </tr>
    <tr>
        <td class="divTdCell text-bold">{{ $estudiants_full->count() ?? '' }}</td>
        <td class="divTdCell text-bold">{{ $estudiants->count() ?? '' }}</td>
    </tr>
</table> --}}



{{-- <table cellpadding="0" cellspacing="0" border="1" class="table-grid small">
    <thead>
      <tr>
        <th colspan="2" style="font-size: 0.7rem !important;font-weight: bold !important; padding-top:0.5rem !important; ">VI. Identificación del Curso</th>
      </tr>
    </thead>
    <tbody align="center">
      <tr>
        <td colspan="2">PLAN DE ESTUDIO</td>
      </tr>
      <tr>
      <td colspan="2" style="font-weight: bold !important;">{{ $pestudio->name ?? '' }}</td>
      </tr>
      <tr>
        <td colspan="2">CÓDIGO</td>
      </tr>
      <tr>
        <td colspan="2" style="font-weight: bold !important;">{{ $pestudio->code ?? '' }}</td>
      </tr>
      <tr>
        <td colspan="2">AÑO CURSADO</td>
      </tr>
      <tr>
        <td colspan="2" style="font-weight: bold !important;">{{ $grado->name ?? '' }}</td>
      </tr>
      <tr>
        <td colspan="2">SECCIÓN</td>
      </tr>
      <tr>
        <td colspan="2" style="font-weight: bold !important;">{{ $seccion->name ?? '' }}</td>
      </tr>
      <tr>
        <td style="font-size: 0.5rem !important">No DE ESTUDIANTES POR SECCIÓN</td>
        <td style="font-size: 0.5rem !important">No DE ESTUDIANTES EN ESTA PÁGINA</td>
      </tr>
      <tr>
        <td style="font-weight: bold !important;">{{ $estudiants_full->count() ?? '' }}</td>
        @php $count = str_pad($estudiants->count(),2, "0", STR_PAD_LEFT); @endphp
        <td style="font-weight: bold !important;">{{ $count ?? '' }}</td>
      </tr>
    </tbody>
</table> --}}


{{-- <style type="text/css">
    .divTable{
        display: table;
        width: 100%;
        padding: 0.01rem;
    }
    .divTableHead {
        font-weight: bold;
        font-size: 110%;
    }
    .divTableRow {
        display: table-row;
    }
    .divTableHeading {
        background-color: #EEE;
        display: table-header-group;
        font-weight: bold;
    }
    .divTableCell, .divTableHead {
        border: 1px solid #999999;
        display: table-cell;
    }
    .divTableFoot {
        background-color: #EEE;
        display: table-footer-group;
        font-weight: bold;
    }
    .divTableBody {
        display: table-row-group;
    }
    .align-right{
        text-align: right !important;
    }
    .align-left{
        text-align: left !important;
    }
    .align-center{
        text-align: center !important;
    }
    .text-bold{
        font-weight: bold !important;
    }
    .div-nowrap {
        word-spacing: 0em !important;
        white-space: nowrap !important;
    }
</style> --}}


{{--
<div class="divTable align-center">
	<div class="divTableBody">
		<div class="divTableRow div-nowrap" >
			<div class="divTableHead">VI. Identificaci&oacute;n del Curso</div>
		</div>
	</div>
	<div class="divTableBody">
		<div class="divTableRow">
			<div class="divTableCell">PLAN DE ESTUDIO</div>
		</div>
		<div class="divTableRow">
			<div class="divTableCell text-bold">{{ $pestudio->name ?? '' }}</div>
		</div>
		<div class="divTableRow">
			<div class="divTableCell">C&Oacute;DIGO</div>
		</div>
		<div class="divTableRow">
			<div class="divTableCell text-bold">{{ $pestudio->code ?? '' }}</div>
		</div>
		<div class="divTableRow">
			<div class="divTableCell">A&Ntilde;O CURSADO</div>
		</div>
		<div class="divTableRow">
			<div class="divTableCell text-bold">{{ $grado->name ?? '' }}</div>
		</div>
		<div class="divTableRow">
			<div class="divTableCell">SECCI&Oacute;N</div>
		</div>
		<div class="divTableRow">
			<div class="divTableCell text-bold">{{ $seccion->name ?? '' }}</div>
		</div>
		<div class="divTableRow">

            <div class="divTable">
                <div class="divTableBody">
                    <div class="divTableRow">
                        <div class="divTableCell small-90">No DE ESTUDIANTES<br>POR SECCI&Oacute;N</div>
                        <div class="divTableCell small-90">No DE ESTUDIANTES<br> EN ESTA P&Aacute;GINA</div>
                    </div>
                    <div class="divTableRow">
                        <div class="divTableCell text-bold small-90">{{ $estudiants_full->count() ?? '' }}</div>
                        <div class="divTableCell text-bold small-90">{{ $estudiants->count() ?? '' }}</div>
                    </div>
                </div>
            </div>

		</div>

	</div>
</div>

--}}

