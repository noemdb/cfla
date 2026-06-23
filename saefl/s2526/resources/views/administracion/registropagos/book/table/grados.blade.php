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
            @foreach ($grados as $grado)
                <tr>
                    <td scope="row">
                        {{$grado->name ?? ''}}<br>
                        <span class="text-muted">{{$grado->code ?? ''}}</span>
                    </td>
                    {{-- <td>{{$grado->preinscritos ?? ''}}</td> --}}
                    <td>{{$grado->inscritos()->value ?? ''}}</td>
                    <td>{{$grado->varones()->value ?? ''}}</td>
                    <td>{{$grado->hembras()->value ?? ''}}</td>
                    <td>{{$grado->retirados ?? ''}}</td>  
                </tr>                                  
            @endforeach
        </tbody>
</table>