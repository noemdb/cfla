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
            @foreach ($pestudios as $pestudio)
                @php
                    $tot_inscritos = (!empty($pestudio->a_inscritos()->value)) ? ($pestudio->a_inscritos()->value + $tot_inscritos): $tot_inscritos ;
                    $tot_varones = (!empty($pestudio->a_varones()->value)) ? ($pestudio->a_varones()->value + $tot_varones): $tot_varones ;
                    $tot_hembras = (!empty($pestudio->a_hembras()->value)) ? ($pestudio->a_hembras()->value + $tot_hembras): $tot_hembras ;
                    $tot_others = (!empty($pestudio->others()->value)) ? ($pestudio->others()->value + $tot_others): $tot_others ;
                    $tot_retirados = (!empty($pestudio->a_retirados()->value)) ? ($pestudio->a_retirados()->value + $tot_retirados): $tot_retirados ;
                @endphp
                <tr>
                    <td scope="row">
                        <span class="text-muted">{{$pestudio->code ?? ''}}</span> {{$pestudio->name ?? ''}} 
                    </td>
                    {{-- <td>{{$pestudio->preinscritos ?? ''}}</td> --}}
                    <td>{{$pestudio->a_inscritos()->value ?? ''}}</td>
                    <td>{{$pestudio->a_varones()->value ?? ''}}</td>
                    <td>{{$pestudio->a_hembras()->value ?? ''}}</td>
                    <td>{{$pestudio->others()->value ?? ''}}</td>  
                    <td>{{$pestudio->a_retirados()->value ?? ''}}</td> 
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