@if ($pensum)
    @php $asignatura = $pensum->asignatura; @endphp
    <table class="table table-sm small" style="margin-top: 1rem;">
        <thead  class="thead-inverse"  align="left">
            <tr>
                <th>Área de Formación</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ ($asignatura) ? $asignatura->name : null}}</td>
            </tr>
        </tbody>
    </table>
@endif

