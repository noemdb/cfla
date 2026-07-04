<table class="table table-striped table-inverse">
    <thead class="thead-inverse">
        <tr>
            <th>Descripción</th>
            <th>Pre-Inscritos</th>
            <th>Inscritos</th>
            <th>Varones</th>
            <th>Hembras</th>
            <th>Retirados</th>
        </tr>
        </thead>
        <tbody>                                
            @foreach ($seccions as $seccion)
                <tr>
                    <td scope="row">
                        {{$seccion->name ?? ''}}<br>
                        <span class="text-muted">{{$seccion->code ?? ''}}</span>
                    </td>
                    <td>{{$seccion->preinscritos() ?? ''}}</td>
                    <td>{{$seccion->inscritos ?? ''}}</td>
                    <td>{{$seccion->Varones ?? ''}}</td>
                    <td>{{$seccion->Hembras ?? ''}}</td>
                    <td>{{$seccion->retirados ?? ''}}</td>  
                </tr>                                  
            @endforeach
        </tbody>
</table>