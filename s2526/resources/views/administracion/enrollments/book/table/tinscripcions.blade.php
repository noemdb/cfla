<table class="table table-striped table-sm">
    <thead class="thead">
        <tr>
            <th>Descripción</th>
            {{-- <th>Pre-Inscritos</th> --}}
            <th>Inscritos</th>
            <th>Varones</th>
            <th>Hembras</th>
            {{-- <th>Retirados</th> --}}
        </tr>
        </thead>
        <tbody>                                
            @foreach ($tinscripcions as $tinscripcion)
                <tr>
                    <td scope="row">
                        {{$tinscripcion->name ?? ''}}<br>
                        <span class="text-muted">{{$tinscripcion->code ?? ''}}</span>
                    </td>
                    {{-- <td>{{$tinscripcion->preinscritos ?? ''}}</td> --}}
                    <td>{{$tinscripcion->inscritos()->value ?? ''}}</td>
                    <td>{{$tinscripcion->varones()->value ?? ''}}</td>
                    <td>{{$tinscripcion->hembras()->value ?? ''}}</td>
                    {{-- <td>{{$tinscripcion->retirados ?? ''}}</td>   --}}
                </tr>                                  
            @endforeach
        </tbody>
</table>