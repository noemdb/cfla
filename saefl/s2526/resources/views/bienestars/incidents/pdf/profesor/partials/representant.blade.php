

<table width="100%" class="table table-striped table-hover table-sm small p-1" style="padding-bottom:0.5rem;font-size:0.7rem !important">
    <tbody>
        <tr>
            <th align="left">Representante</th>
            <td><b>CI:</b> {{ $representant->ci_representant ?? ''}}</td>
            <td><b>Nombre:</b> {{$representant->name ?? ''}} </td>
        </tr>
    </tbody>
</table>