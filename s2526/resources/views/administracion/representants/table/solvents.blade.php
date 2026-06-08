@php
    $class_N="d-none d-sm-table-cell";
    $class_representant="text-nowrap";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_contacto="d-none d-lg-table-cell";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr style="padding-left:2px;padding-right:2px;">
                <th class="{{ $class_N }}">N</th>
                <th style="padding-left:2px;padding-right:2px;" class="{{ $class_ci }}" class="Identificador">Ident.</th>
                <th style="padding-left:2px;padding-right:2px;" class="{{ $class_representant }}">Representante</th>
                <th style="padding-left:2px;padding-right:2px;" class="{{ $class_representant }}">Estudiantes</th>
                @admin
                <th class="{{ $class_action }}">N. Usuario</th>
                <th class="{{ $class_action }}">GSEmail</th>
                <th class="{{ $class_action }}">Contraseña</th>
                @endadmin
            </tr>
        </thead>

        <tbody id="tdatos">
            @foreach($solvents as $representant)
                @php                    
                    $estudiants = $representant->estudiants;
                    $fullInscripcion = null;
                @endphp

                <tr data-id="{{$representant->id ?? ''}}" data-representant_id="{{$representant->id ?? ''}}">
                    <td id="td-count" class="{{ $class_N ?? '' }}">
                        {{$loop->iteration}}
                    </td>
                    <td id="td-estudiant" class="{{ $class_ci ?? '' }} text-nowrap small">
                        <b> {{ $representant->ci_representant ?? ''}}</b>
                        @if ($representant->status_adviders == 'true')
                            <div><small>DELEGADO</small></div>
                        @endif
                    </td>
                    <td class="small">
                        @include('administracion.representants.partials.href')
                    </td>

                    <td>
                        @foreach ($estudiants as $estudiant)
                            @php $inscripcion = (!empty($estudiant->getInscripcion()->id)) ? $estudiant->getInscripcion()->fullname:null; @endphp
                            @php $fullInscripcion = ($inscripcion) ? $inscripcion : $fullInscripcion; @endphp
                            <div class="small pl-2">-. {{$estudiant->short_name ?? ''}} {{$inscripcion}}</div>
                        @endforeach
                    </td>                    

                    @admin
                        <td style="white-space: wrap !important">
                            @php $user = ($representant->user) ? $representant->user:null ; @endphp
                            {{ ($user) ? $user->username : null }}
                        </td>
                        
                        <td style="white-space: wrap !important">
                            {{ $representant->gsemail ?? ''}}
                        </td>
                        <td style="white-space: wrap !important">
                            @php if ($user) $password = ($user->status_update) ? '###':$user->username ; @endphp
                            {{ $password ?? ''}}
                        </td>
                    @endadmin

                </tr>

            @endforeach

        </tbody>
    </table>

@include('administracion.datatables.particulars.representans.exportBootstrap')

