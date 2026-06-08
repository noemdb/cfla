<table class="table table-striped table-sm">
    <thead class="thead">
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
            @foreach ($pestudios as $pestudio)
            @php
                $tot_inscritos = (!empty($pestudio->inscritos()->value)) ? ($pestudio->inscritos()->value + $tot_inscritos): $tot_inscritos ;
                $tot_varones = (!empty($pestudio->varones()->value)) ? ($pestudio->varones()->value + $tot_varones): $tot_varones ;
                $tot_hembras = (!empty($pestudio->hembras()->value)) ? ($pestudio->hembras()->value + $tot_hembras): $tot_hembras ;
                $tot_retirados = (!empty($pestudio->retirados()->value)) ? ($pestudio->retirados()->value + $tot_retirados): $tot_retirados ;
            @endphp
                <tr>
                    <td scope="row">
                        <span class="text-muted">{{$pestudio->code ?? ''}}</span> {{$pestudio->name ?? ''}}
                    </td>
                    <td>{{$pestudio->inscritos()->value ?? ''}}</td>
                    <td>{{$pestudio->varones()->value ?? ''}}</td>
                    <td>{{$pestudio->hembras()->value ?? ''}}</td>
                    <td>{{$pestudio->retirados()->value ?? ''}}</td>  
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