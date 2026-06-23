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
                @php
                    $tot_inscritos = 0;
                    $tot_varones = 0;
                    $tot_hembras = 0;
                    $tot_retirados = 0;
                @endphp                                   
                @foreach ($grados as $grado)
                @php
                    $tot_inscritos = (!empty($grado->inscritos()->value)) ? ($grado->inscritos()->value + $tot_inscritos): $tot_inscritos ;
                    $tot_varones = (!empty($grado->varones()->value)) ? ($grado->varones()->value + $tot_varones): $tot_varones ;
                    $tot_hembras = (!empty($grado->hembras()->value)) ? ($grado->hembras()->value + $tot_hembras): $tot_hembras ;
                    $tot_retirados = (!empty($grado->retirados()->value)) ? ($grado->retirados()->value + $tot_retirados): $tot_retirados ;
                @endphp
                    <tr>
                        <td scope="row">
                            {{$grado->name ?? ''}}<br>
                            <span class="text-muted">{{$grado->code ?? ''}}</span>
                        </td>
                        {{-- <td>{{$grado->preinscritos ?? ''}}</td> --}}
                        <td>{{$grado->inscritos()->value ?? ''}}</td>
                        <td>{{$grado->varones()->value ?? ''}}</td>
                        <td>{{$grado->hembras()->value ?? ''}}</td>
                        <td>{{$grado->retirados()->value ?? ''}}</td>   
                    </tr>                                  
                @endforeach
                <tr>
                    <th>TOTAL</th>
                    <th>{{$tot_inscritos ?? ''}}</th>
                    <th>{{$tot_varones ?? ''}}</th>
                    <th>{{$tot_hembras ?? ''}}</th>
                    <th>{{$tot_retirados ?? ''}}</th> 
                </tr>
            </tbody>
    </table>