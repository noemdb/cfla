@if ($representant)
    <table class="table table-sm small" style="margin-top: 1rem;">
        <thead  class="thead-inverse"  align="left">
            <tr>
                <th colspan="2">Representante</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Identificador</td>
                <td>Apellidos y Nombres</td>
            </tr>
            <tr>
                <td>{{ $representant->ci_representant ?? ''}}</td>
                <td>{{$representant->name ?? ''}} </td>
            </tr>
        </tbody>
    </table>
@endif

