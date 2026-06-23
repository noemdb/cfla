<table class="table table-striped table-sm">
        {{-- <thead class="thead">
            <tr>
                <th>Grado/Año</th>
                <th>Inscritos</th>
                <th>Varones</th>
                <th>Hembras</th>
                <th>Retirados</th>
                <th class="text-nowrap">No Inscritos</th>
            </tr>
        </thead> --}}

            @php
                $tot_inscritos = 0;
                $tot_varones = 0;
                $tot_hembras = 0;
                $tot_others = 0;
                $tot_retirados = 0;
                $tot_inschistories = 0;
            @endphp 

            @foreach ($pestudios as $item)

            {{-- @php $datas = $item->grados; @endphp --}}
            @php
                $datas = $item->getGradosActive();
                $tot_gr_inscritos = 0;
                $tot_gr_varones = 0;
                $tot_gr_hembras = 0;
                $tot_gr_others = 0;
                $tot_gr_retirados = 0;
                $tot_gr_inschistories = 0;
            @endphp
            
            <tbody>

                <tr class="table-dark text-dark">
                    <th class="pt-4" colspan="6">{{$item->name}}</th>
                </tr>

                {{-- <thead class="thead"> --}}
                    <tr>
                        <th>Nivel</th>
                        <th>Inscritos</th>
                        <th>Varones</th>
                        <th>Hembras</th>
                        <th>Retirados</th>
                        <th class="text-nowrap">No Inscritos</th>
                    </tr>
                {{-- </thead> --}}

                @foreach ($datas as $grado)
                    @php
                        $tot_gr_inscritos = (!empty($grado->inscritos()->value)) ? ($grado->inscritos()->value + $tot_gr_inscritos): $tot_gr_inscritos ;
                        $tot_gr_varones = (!empty($grado->varones()->value)) ? ($grado->varones()->value + $tot_gr_varones): $tot_gr_varones ;
                        $tot_gr_hembras = (!empty($grado->hembras()->value)) ? ($grado->hembras()->value + $tot_gr_hembras): $tot_gr_hembras ;
                        $tot_gr_others = (!empty($grado->others()->value)) ? ($grado->others()->value + $tot_gr_others): $tot_gr_others ;
                        $tot_gr_retirados = (!empty($grado->retirados()->value)) ? ($grado->retirados()->value + $tot_gr_retirados): $tot_gr_retirados ;
                        $tot_gr_inschistories = (!empty($grado->inschistories()->value)) ? ($grado->inschistories()->value + $tot_gr_inschistories): $tot_gr_inschistories ;
                    @endphp
                    <tr>
                        <td class=" text-nowrap"> {{$grado->name ?? ''}}</td>
                        <td>{{$grado->inscritos()->value ?? ''}}</td>
                        <td>{{$grado->varones()->value ?? ''}}</td>
                        <td>{{$grado->hembras()->value ?? ''}}</td>
                        {{-- <td>{{$grado->others()->value ?? ''}}</td> --}}
                        <td>{{$grado->retirados()->value ?? ''}}</td> 
                        <td>{{$grado->inschistories()->value ?? ''}}</td>   
                    </tr>                                  
                @endforeach
                <tr>
                    <th >TOTAL</th>
                    <th>{{$tot_gr_inscritos ?? ''}}</th>
                    <th>{{$tot_gr_varones ?? ''}}</th>
                    <th>{{$tot_gr_hembras ?? ''}}</th>
                    {{-- <th>{{$tot_gr_others ?? ''}}</th>  --}}
                    <th>{{$tot_gr_retirados ?? ''}}</th> 
                    <th>{{$tot_gr_inschistories ?? ''}}</th> 
                </tr>
            </tbody>
            @endforeach

    </table>