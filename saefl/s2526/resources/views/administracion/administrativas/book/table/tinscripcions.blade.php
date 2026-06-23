<table class="table table-striped table-sm">
    <thead class="thead">
        <tr>
            <th>Descripción</th>
            {{-- <th>Pre-Inscritos</th> --}}
            <th>Inscritos</th>
            <th>Varones</th>
            <th>Hembras</th>
            <th>Sin género registrado</th>
            {{-- <th>Retirados</th> --}}
        </tr>
        </thead>
        <tbody>
            @php
                $tot_inscritos = 0;
                $tot_varones = 0;
                $tot_hembras = 0;
                $tot_others = 0;
                // $tot_retirados = 0;
            @endphp                                 
            @foreach ($tinscripcions as $tinscripcion)
                @php
                    $tot_inscritos = (!empty($tinscripcion->a_inscritos()->value)) ? ($tinscripcion->a_inscritos()->value + $tot_inscritos): $tot_inscritos ;
                    $tot_varones = (!empty($tinscripcion->a_varones()->value)) ? ($tinscripcion->a_varones()->value + $tot_varones): $tot_varones ;
                    $tot_hembras = (!empty($tinscripcion->a_hembras()->value)) ? ($tinscripcion->a_hembras()->value + $tot_hembras): $tot_hembras ;
                    $tot_others = (!empty($tinscripcion->others()->value)) ? ($tinscripcion->others()->value + $tot_others): $tot_others ;
                @endphp
                <tr>
                    <td scope="row">
                        {{$tinscripcion->name ?? ''}}<br>
                        <span class="text-muted">{{$tinscripcion->code ?? ''}}</span>
                    </td>
                    {{-- <td>{{$tinscripcion->preinscritos ?? ''}}</td> --}}
                    <td>{{$tinscripcion->a_inscritos()->value ?? ''}}</td>
                    <td>{{$tinscripcion->a_varones()->value ?? ''}}</td>
                    <td>{{$tinscripcion->a_hembras()->value ?? ''}}</td>
                    <td>{{$tinscripcion->others()->value ?? ''}}</td>  
                    {{-- <td>{{$tinscripcion->a_retirados ?? ''}}</td>   --}}
                </tr>                                  
            @endforeach
            <tr>
                <th>TOTAL</th>
                <th>{{$tot_inscritos ?? ''}}</th>
                <th>{{$tot_varones ?? ''}}</th>
                <th>{{$tot_hembras ?? ''}}</th>
                <th>{{$tot_others ?? ''}}</th>
                {{-- <th>&nbsp;</th>  --}}
            </tr>
        </tbody>
</table>