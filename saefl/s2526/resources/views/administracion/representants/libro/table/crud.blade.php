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
                <th style="padding-left:2px;padding-right:2px;" class="{{ $class_planpago }}">Insc.Académica</th>
                <th style="padding-left:2px;padding-right:2px;" class="{{ $class_representant }}">Estudiantes</th>
                <th style="padding-left:2px;padding-right:2px;" class="{{ $class_contacto }}">Información de contacto</th>
            </tr>
        </thead>

        <tbody id="tdatos">
            @foreach($representants as $representant)
                @php
                    $ci_estudiant = '';
                    $fullname = '';
                    $inscripcion = '';
                    $fullInscripcion = '';
                    $administrativa = '';
                    $fullEstudiants = '';
                    $status_active = ($representant->status_active=='true') ? true:false;
                    $active = $representant->active;
                    $enable = $representant->enable;
                    $estudiants = $representant->estudiants;
                @endphp
                @foreach ($estudiants as $estudiant)
                    @php
                        $inscripcion = (!empty($estudiant->getInscripcion()->id)) ? $estudiant->getInscripcion()->fullname:null;
                        $fullInscripcion = ($inscripcion) ? $inscripcion : $fullInscripcion;
                        $fullEstudiants .= '-. '.$estudiant->short_name.' '.$inscripcion.'<br>';
                    @endphp                    
                @endforeach

                <tr data-id="{{$representant->id ?? ''}}" data-representant_id="{{$representant->id ?? ''}}" class="{{($active) ? '':'table-danger'}}">
                    <td id="td-count" class="{{ $class_N ?? '' }}">
                        {{$loop->iteration}}
                    </td>
                    <td id="td-estudiant" class="{{ $class_ci ?? '' }} text-nowrap small">
                        <b> {{ $representant->ci_representant ?? ''}}</b>
                    </td>
                    <td class="small">
                        {{-- @include('administracion.representants.partials.href') --}}
                        <b> {{ $representant->name ?? ''}}</b>
                    </td>
                    <td style="white-space: nowrap !important">
                        <span class="small">
                            {{$fullInscripcion ?? null}}
                        </span>
                    </td>
                    <td>
                        {{-- @foreach ($estudiants as $estudiant)
                            @php $inscripcion = (!empty($estudiant->getInscripcion()->id)) ? $estudiant->getInscripcion()->fullname:null; @endphp
                            @php $fullInscripcion = ($inscripcion) ? $inscripcion : $fullInscripcion; @endphp
                            <div class="small pl-2">-. {{$estudiant->short_name ?? ''}} {{$inscripcion}}</div>
                        @endforeach --}}
                        <div class="small pl-2" style="white-space: nowrap">{!!$fullEstudiants ?? ''!!}</div>
                    </td>                    
                    <td class="text-wrap">
                        <span class="small">{{ $representant->fullphone ?? ''}}<br>{{ $representant->email ?? ''}}</span>
                    </td>
                </tr>

            @endforeach

        </tbody>
    </table>

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.default') --}}
@include('administracion.datatables.particulars.representans.exportBootstrap')


@section('scripts')
    @parent
    <script>
        $('.btn-card').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  //console.log(id);
            var modal = '#modal_card';  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.estudiant_card", "_id_")}}';
            ajaxurl = ajaxurl.replace('_id_', id);
            $.ajax({
                url: ajaxurl,
                type: "GET",
                success: function(data){
                    $(container).html(data);
                    $(modal).modal('toggle');
                }
            });
        });
    </script>
@endsection
