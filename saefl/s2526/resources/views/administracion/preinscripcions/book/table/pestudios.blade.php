
    <table class="table table-striped table-sm">
        <thead class="thead">
            <tr>
                <th>Nombre</th>
                {{-- <th>Pre-Inscritos</th> --}}
                <th>Preinscritos</th>
                <th>Varones</th>
                <th>Hembras</th>
            </tr>
            </thead>
            <tbody>
                @php
                    $tot_preinscripcions = 0;
                    $tot_varones = 0;
                    $tot_hembras = 0;
                    $tot_retirados = 0;
                @endphp                                   
                @foreach ($pestudios as $pestudio)
                    @php
                    $tot_preinscripcions = (!empty($pestudio->preinscripcions()->value)) ? ($pestudio->preinscripcions()->value + $tot_preinscripcions): $tot_preinscripcions ;
                    $tot_varones = (!empty($pestudio->pre_varones()->value)) ? ($pestudio->pre_varones()->value + $tot_varones): $tot_varones ;
                    $tot_hembras = (!empty($pestudio->pre_hembras()->value)) ? ($pestudio->pre_hembras()->value + $tot_hembras): $tot_hembras ;
                    @endphp
                    <tr>
                        <td scope="row">
                            <span class="text-muted">{{$pestudio->code ?? ''}}</span> {{$pestudio->name ?? ''}}
                        </td>
                        <td>{{$pestudio->preinscripcions()->value ?? ''}}</td>
                        <td>{{$pestudio->pre_varones()->value ?? ''}}</td>
                        <td>{{$pestudio->pre_hembras()->value ?? ''}}</td>
                    </tr>                                  
                @endforeach
                <tr>
                    <th>TOTAL</th>
                    <th>{{$tot_preinscripcions ?? ''}}</th>
                    <th>{{$tot_varones ?? ''}}</th>
                    <th>{{$tot_hembras ?? ''}}</th>
                </tr>
            </tbody>
    </table>
