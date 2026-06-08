@if ($profesor)
    @php $asignatura = ($pensum) ? $pensum->asignatura : null; @endphp
    <table class="table table-sm small" style="margin-top: 1rem;">
        <thead  class="thead-inverse"  align="left">
            <tr>
                <th colspan="2">Profesor</th>
                <th>Área de Formación</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Identificador</td>
                <td>Apellidos y Nombres</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>{{ $profesor->ci_profesor ?? ''}}</td>
                <td>{{$profesor->fullname ?? ''}} </td>
                <td>{{ ($asignatura) ? $asignatura->name : null}}</td>
            </tr>
        </tbody>
    </table>
@endif

