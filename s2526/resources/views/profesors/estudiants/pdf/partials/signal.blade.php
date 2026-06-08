<p>&nbsp;</p>

<div style="text-align:center;font-size:0.8rem !important">

	<table class="table table-sm small">
        <tr>
            <td width="33%">
                {{ $autoridad1->name.' '.$autoridad1->lastname }}<br>
                <span class="text-muted">{{$autoridad1->position ?? ''}}</span>
            </td>
            <td width="33%">
                {{ $profesor->fullname ?? '' }}<br>
                <span class="text-muted">Profesor Guía</span>
            </td>
            <td width="33%">
                {{ $estudiant->representant->name ?? ''}}<br>
                <span class="text-muted">Representante</span>
            </td>
        </tr>
    </table>

    <p style="font-size:0.8rem !important">
        SAN FELIPE a los
        <strong> <u> {{Carbon\Carbon::now()->day}}</u></strong>  días del mes de
        <strong><u>{{Carbon\Carbon::now()->monthName}}</u></strong> de
        <strong><u>{{Carbon\Carbon::now()->year}}</u></strong> .
    </p>

</div>

{{-- <div style="font-weight: bold !important;font-size: 0.8rem !important">LLENAR TODOS LOS CAMPOS DE CARACTER OBLIGATORIO.</div> --}}
