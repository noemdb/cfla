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
                <th style="padding-left:2px;padding-right:2px;" class="{{ $class_planpago }}">Insc.Académica</th>
                <th style="padding-left:2px;padding-right:2px;" class="{{ $class_contacto }}">Información de contacto</th>
                <th class="{{ $class_action }}">Email</th>                
                
                <th class="{{ $class_action }}">N. Usuario</th>
                @admin
                <th class="{{ $class_action }}">GSEmail</th>
                <th class="{{ $class_action }}">Contraseña</th>
                @endadmin
                <th class="{{ $class_action }}">Formalizado</th>
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
                    <td style="white-space: nowrap !important">
                        <span class="small">
                            {{$fullInscripcion ?? null}}
                        </span>
                    </td>
                    <td class="text-wrap">
                        <span class="small">{{ $representant->fullphone ?? ''}}<br>{{ $representant->email ?? ''}}</span>
                    </td>
                    <td style="white-space: wrap !important">
                        {{ $representant->email ?? ''}}
                    </td>
                    
                    
                    <td style="white-space: wrap !important">
                        @php $user = ($representant->user) ? $representant->user:null ; @endphp
                        {{ ($user) ? $user->username : null }}
                    </td>
                    @admin
                    <td style="white-space: wrap !important">
                        {{ $representant->gsemail ?? ''}}
                    </td>
                    <td style="white-space: wrap !important">
                        @php if ($user) $password = ($user->status_update) ? '###':$user->username ; @endphp
                        {{ $password ?? ''}}
                    </td>
                    @endadmin
                    <td style="white-space: wrap !important">
                        {{ ($active) ? 'SI':'NO'}}
                    </td>

                </tr>

            @endforeach

        </tbody>
    </table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.particulars.representans.exportBootstrap')

@section('scripts')
    @parent
@endsection
