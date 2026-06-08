<div class="p-1">

    <div class="container-fluid">
        <div class="row">

            @foreach($pestudios as $pestudio)
                <div class="col">
                    <div class="card">                        
                        <div class="card-body">
                            <div class="card-title font-weight-bold">{{$pestudio->name ?? ''}}</div>
                            <div class="card-text">
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr class=" table-secondary">
                                            <th>Grado/Año</th>
                                            <th class="text-left">Sección/Ind. Morosidad</th>
                                            <th class="text-left">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $grados = $pestudio->getGradosActive() @endphp
                                        @foreach ($grados as $grado) 
                                            <tr>
                                                <th scope="row">{{$grado->name ?? null}}</th>
                                                <td class="text-left">
                                                    <div class="px-2">
                                                        @php $seccions = $grado->getSeccionsActiveInscriptionAffect() @endphp
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
                                                <th class="text-left">{{ ($late_index > 0) ? $late_index : '0' }}%</th>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                </div>
            @endforeach           
            
        </div>
    </div>

    

</div>
