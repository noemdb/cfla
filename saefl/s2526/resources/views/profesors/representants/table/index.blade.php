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
                <th style="padding-left:2px;padding-right:2px;" class="{{ $class_contacto }}">Números de teléfonos</th>
                <th class="{{ $class_action }}">Email</th>                
                {{-- <th class="{{ $class_action }}">Acciones</th> --}}
            </tr>
        </thead>

        <tbody id="tdatos">
            @foreach($representants as $representant)
                @php
                    $ci_estudiant = '';
                    $fullname = '';
                    $inscripcion = '';
                    $administrativa = '';
                    $status_active = ($representant->status_active=='true') ? true:false;
                    $active = $representant->active;
                    $enable = $representant->enable;
                    $estudiants = $representant->estudiants;
                    $fullInscripcion = null;
                @endphp

                <tr data-id="{{$representant->id ?? ''}}" data-representant_id="{{$representant->id ?? ''}}" class="{{($active) ? '':'table-danger'}}">
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
                        <b> {{ $representant->name ?? ''}}</b>
                    </td>

                    <td>
                        @foreach ($estudiants as $estudiant)
                            @php $inscripcion = (!empty($estudiant->getInscripcion()->id)) ? $estudiant->getInscripcion()->fullname:null; @endphp
                            @php $fullInscripcion = ($inscripcion) ? $inscripcion : $fullInscripcion; @endphp
                            <div class="small pl-2">-. {{$estudiant->short_name ?? ''}} {{$inscripcion}}</div>
                        @endforeach
                    </td>

                    <td class="text-wrap">
                        <span class="small">{{ $representant->fullphone ?? ''}}</span>
                    </td>

                    <td style="white-space: wrap !important">
                        {{ $representant->email ?? ''}}
                    </td>

                    {{-- <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">

                        <div class="btn-group btn-group-sm">

                        </div>

                    </td> --}}

                </tr>

            @endforeach

        </tbody>
    </table>

@include('profesors.datatables.exportBootstrap')
