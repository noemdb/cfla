<table class="table table-striped table-inverse">
    <thead class="thead-inverse">
        <tr>
            <th>Descripción</th>
            {{-- <th>Pre-Inscritos</th> --}}
            <th>Inscritos</th>
            <th>Varones</th>
            <th>Hembras</th>
            <th>Retirados</th>
        </tr>
        </thead>
        <tbody>                                
            @foreach ($pestudios as $pestudio)
                <tr>
                    <td scope="row">
                        {{$pestudio->name ?? ''}}<br>
                        <span class="text-muted">{{$pestudio->code_oficial ?? ''}}</span>
                    </td>
                    {{-- <td>{{$pestudio->preinscritos ?? ''}}</td> --}}
                    <td>{{$pestudio->inscritos()->value ?? ''}}</td>
                    <td>{{$pestudio->varones()->value ?? ''}}</td>
                    <td>{{$pestudio->hembras()->value ?? ''}}</td>
                    <td>{{$pestudio->retirados ?? ''}}</td>  
                </tr>                                  
            @endforeach
        </tbody>
</table>