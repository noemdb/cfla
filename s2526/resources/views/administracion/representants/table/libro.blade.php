@php 
    $class_N="d-none d-sm-table-cell";
    $class_representant="text-nowrap";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_contacto="d-none d-lg-table-cell text-nowrap";
    $class_action="";
@endphp 

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_ci }}">Identificador</th>
                <th class="{{ $class_representant }}" style="white-space: nowrap !important">Representante</th>
                <th class="{{ $class_contacto }}">Información de contacto</th>
            </tr>
        </thead>

        <tbody id="tdatos">            
        @foreach($representants as $representant)
                @php
                    $ci_estudiant = '';
                    $fullname = '';
                    $inscripcion = '';
                @endphp
                @if (array_key_exists($representant->id,$estudiants))                        
                    @foreach ($estudiants[$representant->id] as $estudiant)
                        @php
                            $ci_estudiant = $ci_estudiant . $estudiant['ci_estudiant'] . '<br> ';
                            $fullname = $fullname . $estudiant['lastname'] .' '. $estudiant['name'] . '<br> ';
                            // $inscripcion = $inscripcion . '<br> ' . $estudiant['inscripcion'] .' '. $estudiant['inscripcion'];
                        @endphp                                                              
                    @endforeach
                @endif

                <tr data-representant_id="{{$representant->id ?? ''}}">
                    
                    <td id="td-count" class="{{ $class_N ?? '' }}">
                        {{$loop->iteration}}
                    </td>

                    <td id="td-estudiant" class="{{ $class_ci ?? '' }}">
                         <b> {{ $representant->ci_representant ?? ''}}</b>
                         <div class="small" style="margin-left:5px">
                            <small> {!! $ci_estudiant !!}</small>
                        </div>                        
                    </td>

                    <td id="td-representant-{{ $representant->name }}" class="{{ $class_representant  ?? ''}}" style="white-space: nowrap !important">
                        <b>{{$representant->name}}</b>
                        
                        <div class="small" style="margin-left:5px">
                            <small>{!! $fullname !!}</small>
                        </div>
                    </td>

                    <td id="td-contacto-{{ $representant->id }}" class="{{ $class_contacto ?? '' }} align-middle">                        
                        <small>{{ $representant->phone ?? ''}} {{ $representant->cellphone ?? ''}} {{ $representant->email ?? ''}}</small>
                    </td>
                    
                </tr>
            @endforeach
            
        </tbody>
    </table>