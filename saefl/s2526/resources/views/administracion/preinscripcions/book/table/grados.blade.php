
    <table class="table table-striped table-sm">
        <thead class="thead">
            <tr>
                <th>Nombre</th>
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
                @foreach ($grados as $grado)
                    @php
                    $tot_preinscripcions = (!empty($grado->preinscritos()->value)) ? ($grado->preinscritos()->value + $tot_preinscripcions): $tot_preinscripcions ;
                    $tot_varones = (!empty($grado->pre_varones()->value)) ? ($grado->pre_varones()->value + $tot_varones): $tot_varones ;
                    $tot_hembras = (!empty($grado->pre_hembras()->value)) ? ($grado->pre_hembras()->value + $tot_hembras): $tot_hembras ;
                    @endphp
                    <tr>
                        <td scope="row">
                            {{$grado->name ?? ''}}
                        </td>
                        <td>{{$grado->preinscritos()->value ?? ''}}</td>
                        <td>{{$grado->pre_varones()->value ?? ''}}</td>
                        <td>{{$grado->pre_hembras()->value ?? ''}}</td>
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
