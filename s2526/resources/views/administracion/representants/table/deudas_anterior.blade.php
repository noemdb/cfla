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
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_ci }}">Identificador</th>
                <th class="{{ $class_representant }}">Representante</th>
                <th class="{{ $class_contacto }}">Información de contacto</th>
            </tr>
        </thead>

        <tbody id="tdatos">
            @foreach($representants as $representant)
                {{-- @php
                    $ci_estudiant = '';
                    $fullname = '';
                    $inscripcion = '';
                @endphp
                @if (array_key_exists($representant->id,$estudiants))
                    @foreach ($estudiants[$representant->id] as $estudiant)
                        @php
                            $ci_estudiant = $ci_estudiant . $estudiant['ci_estudiant']. '<br>' ;
                            $fullname = $fullname  . $estudiant['fullname']. '<br>';
                            $inscripcion = $inscripcion  . $estudiant['inscripcion'] .' '. $estudiant['inscripcion']. '<br>';
                        @endphp
                    @endforeach
                @endif --}}

                <tr data-representant_id="{{$representant->id ?? ''}}">

                    <td id="td-count" class="{{ $class_N ?? '' }}">
                        {{$loop->iteration}}
                    </td>

                    <td id="td-estudiant" class="{{ $class_ci ?? '' }}">
                        <b> {{ $representant->ci_representant ?? ''}}</b>
                        <div class="small" style="margin-left:2px;padding-left: 2px; border-top: 1px solid #e9ecef;">
                            {!! $ci_estudiant ?? '' !!}
                        </div>
                    </td>

                    <td id="td-representant-{{ $representant->name }}" class="{{ $class_representant  ?? ''}}">
                        <b>{{$representant->name}}</b>
                        <div class="small" style="margin-left:2px;padding-left: 2px; border-top: 1px solid #e9ecef;">
                            {!! $fullname ?? '' !!}
                        </div>
                    </td>

                    <td id="td-ammount_expire_bill-{{ $representant->id }}" class="{{ $class_contacto ?? '' }} align-middle">
                        {{ $representant->phone ?? ''}} {{ $representant->cellphone ?? ''}} {{ $representant->email ?? ''}}

                        <div class="small" style="margin-left:2px;padding-left: 2px; border-top: 1px solid #e9ecef;">
                            {!! $inscripcion ?? '' !!}
                        </div>
                    </td>

                </tr>

            @endforeach

        </tbody>
    </table>

{{-- </div> --}}

@include('administracion.datatables.exportBootstrap')
