<table class="table-sm" style="margin-top: 0.5rem;">
    <tr>
        <td style="width:40%; vertical-align:top">

            <table class="table-sm">
                <thead style="font-size:0.7rem;background-color:#e0e0e0">
                    <tr align="left">
                        <th style="font-size:0.7rem;width:30%">Abreviación</th>
                        <th style="font-size:0.7rem;width:50%">Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($baremos as $baremo)
                    <tr>
                        <td style="font-size:0.7rem;">{{ $baremo->valoracion ?? ''}} </td>
                        <td style="font-size:0.7rem;">{{ $baremo->description ?? ''}} </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </td>
        <td style="width:60%; vertical-align:top">

            <table class="table-sm">
                <thead style="font-size:0.7rem;background-color:#e0e0e0">
                    <tr><th>OBSERVACIONES</th></tr>
                </thead>
                <tbody>
                    <tr><td style="font-size:0.7rem;">&nbsp;</td></tr>
                    <tr><td style="font-size:0.7rem;">&nbsp;</td></tr>
                    <tr><td style="font-size:0.7rem;">&nbsp;</td></tr>
                    <tr><td style="font-size:0.7rem;">&nbsp;</td></tr>
                    <tr><td style="font-size:0.7rem;">&nbsp;</td></tr>
                </tbody>
            </table>

        </td>
    </tr>
</table>
