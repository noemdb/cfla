<table class="table table-striped table-sm">
    <thead class="thead">
        <tr>
            <th>Descripción</th>
            {{-- <th>Pre-Inscritos</th> --}}
            <th>Inscritos</th>
            <th>Varones</th>
            <th>Hembras</th>
            <th>Sin género registrado</th>
            <th>Retirados</th>
        </tr>
        </thead>
        <tbody> 
            @php
                $tot_inscritos = 0;
                $tot_varones = 0;
                $tot_hembras = 0;
                $tot_others = 0;
                $tot_retirados = 0;
            @endphp                               
            @foreach ($grados as $grado)
                @php
                    $tot_inscritos = (!empty($grado->a_inscritos()->value)) ? ($grado->a_inscritos()->value + $tot_inscritos): $tot_inscritos ;
                    $tot_varones = (!empty($grado->a_varones()->value)) ? ($grado->a_varones()->value + $tot_varones): $tot_varones ;
                    $tot_hembras = (!empty($grado->a_hembras()->value)) ? ($grado->a_hembras()->value + $tot_hembras): $tot_hembras ;
                    $tot_others = (!empty($grado->others()->value)) ? ($grado->others()->value + $tot_others): $tot_others ;
                    $tot_retirados = (!empty($grado->a_retirados()->value)) ? ($grado->a_retirados()->value + $tot_retirados): $tot_retirados ;
                @endphp
                <tr>
                    <td scope="row">
                        {{$grado->name ?? ''}}<br>
                        <span class="text-muted">{{$grado->code ?? ''}}</span>
                    </td>
                    {{-- <td>{{$grado->preinscritos ?? ''}}</td> --}}
                    <td>{{$grado->a_inscritos()->value ?? ''}}</td>
                    <td>{{$grado->a_varones()->value ?? ''}}</td>
                    <td>{{$grado->a_hembras()->value ?? ''}}</td>
                    <td>{{$grado->others()->value ?? ''}}</td>  
                    <td>{{$grado->a_retirados()->value ?? ''}}</td>   
                </tr>                                  
            @endforeach
            <tr>
                <th>TOTAL</th>
                <th>{{$tot_inscritos ?? ''}}</th>
                <th>{{$tot_varones ?? ''}}</th>
                <th>{{$tot_hembras ?? ''}}</th>
                <th>{{$tot_others ?? ''}}</th>
                <th>{{$tot_retirados ?? ''}}</th> 
            </tr>   
        </tbody>
</table>