<div class="alert alert-secondary font-weight-bold">Planes de Estudio</div>
<div class="p-1">

    <nav>
        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
            @foreach($pestudios as $pestudio)
                <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}} font-weight-bold text-uppercase small"
                    id="nav-header-tab-analyzers-{{$pestudio->id}}" data-toggle="tab"
                    href="#nav-content-analyzers-{{$pestudio->id}}" role="tab" aria-controls="nav-home" aria-selected="true">
                    {{$pestudio->name ?? ''}}
                </a>
            @endforeach
        </div>
    </nav>

    <div class="tab-content border border-top-0" id="nav-tabContent">                    
        @foreach ($pestudios as $pestudio)
            <div class="tab-pane fade {{($loop->iteration==1) ? 'show active':''}}" id="nav-content-analyzers-{{$pestudio->id ?? ''}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$pestudio->id ?? ''}}">
                <div class="px-2 pt-4">            
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Grado/Año</th>
                                <th class="text-left">Secciones/Índice de Morosidad</th>
                                <th class="text-left">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grados = $pestudio->getGradosActive() @endphp
                            @foreach ($grados as $grado) 
                                                       
                                <tr>
                                    <td scope="row">{{$grado->name ?? null}}</td>
                                    <td class="text-left">
                                        <div class="px-2">
                                            @php $seccions = $grado->getSeccionsActive() @endphp
                                            @foreach ($seccions as $seccion)
                                                @php $late_index = $seccion->late_index ; $late_index = round($late_index,1); @endphp 
                                                <div class="d-flex justify-content-start">
                                                    <div class="px-2">{{$seccion->name ?? null}}</div>
                                                    <div class="px-2">{{ ($late_index > 0) ? $late_index : '0' }}%</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    @php $late_index = $grado->late_index ; $late_index = round($late_index,1); @endphp
                                    <td class="text-left">{{ ($late_index > 0) ? $late_index : '0' }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

</div>
