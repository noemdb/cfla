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
            @foreach ($datos as $dato)
                <tr>
                    <td scope="row">
                        {{$dato->name ?? ''}}<br>
                        <span class="text-muted">{{$dato->code ?? ''}}</span>
                    </td>
                    <td>{{$dato->preinscritos() ?? ''}}</td>
                    <td>{{$dato->inscritos ?? ''}}</td>
                    <td>{{$dato->varones ?? ''}}</td>
                    <td>{{$dato->hembras ?? ''}}</td>
                    <td>{{$dato->retirados ?? ''}}</td>  
                </tr>                                  
            @endforeach
        </tbody>
</table>