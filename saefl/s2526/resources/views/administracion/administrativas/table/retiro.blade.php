@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_ci }}">Cédula</th>
                <th class="{{ $class_planpago }}">Plan de Pago</th>
                <th class="{{ $class_planpago }}">Deuda <span class="small">[Bs]</span></th>
                {{-- <th class="{{ $class_deuda }}" title="Deuda Vencida">Deuda (Bs.)</th> --}}
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_grado }}" title="Retiro Académico">R.Académico</th>
                <th class="{{ $class_grado }}" title="Retiro Administrativo">R.Administrativo</th>
                <th class="{{ $class_grado }}" title="Fecha del retiro">Fecha</th>
                {{-- <th class="{{ $class_fecha }}" title="Fecha de Inscripción Administrativa">Fecha</th> --}}
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($estudiants as $estudiant)

                @php 
                    $ammount_expire_bill = $estudiant->ammount_expire_bill; 
                @endphp

                <tr data-id="{{$estudiant->id ?? ''}}" data-ci_estudiant="{{$estudiant->ci_estudiant ?? ''}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td id="td-estudiant-{{ $estudiant->id }}" class="{{ $class_estudiant  ?? ''}}">
                        <a class="btn-link" href="{{ route('administracion.estudiants.index',['search'=>$estudiant->ci_estudiant]) }}">
                            <span class="font-weight-bold text-{{ ($ammount_expire_bill>0) ? 'danger':'dark'}}">
                                {{$estudiant->fullname}}
                            </span>
                        </a>
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_ci ?? '' }}">
                        {{ $estudiant->ci_estudiant ?? ''}}
                    </td>

                    <td id="td-planpago-estudiant-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                        @if (empty($estudiant->administrativa->planpago_id))
                            <span class="badge badge-secondary mt-1" title="SIN PLAN DE PAGO ASIGNADO">.NINGUNO.</span>
                        @else
                            {!!$estudiant->administrativa->planpago->badge ?? ''!!}
                        @endif
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_ci ?? '' }}">
                        {{ f_float($ammount_expire_bill)}}
                    </td>

                    <td class="{{ $class_grado ?? '' }}" >
                        <span class="{{$estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default'}}">
                            {{$estudiant->getInscripcion()->seccion->grado->name ?? ''}} {{$estudiant->getInscripcion()->seccion->name ?? ''}}
                        </span>
                    </td>

                    <td class="{{ $class_grado ?? '' }}" >
                        {{ ($estudiant->rControl) ? 'SI':'NO' }}
                    </td>
                    <td class="{{ $class_grado ?? '' }}" >
                        {{ ($estudiant->rAdmon) ? 'SI':'NO' }}
                    </td>
                    <td class="{{ $class_grado ?? '' }}" >
                        @if ($estudiant->rAdmon || $estudiant->rControl)
                            <div class="text-dark">{{$estudiant->retiro->created_at}}</div>
                        @endif
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">
                        <div class="btn-group btn-group-sm">
                            @php $disabledControl = ($estudiant->rControl && Auth::user()->isControl()) ? 'disabled' : null ; @endphp
                            @php $disabledAdmon = ($estudiant->rAdmon && Auth::user()->isAdmon()) ? 'disabled' : null ; @endphp
                            <a title="Retirar" class="btn-retiro btn btn-danger {{$disabledControl}}  {{$disabledAdmon}}" href="#">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <div id="container_modal"></div>

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.default') --}}
@include('administracion.datatables.exportBootstrap')

@section('scripts')
    @parent
    <script>

        $('.btn-retiro').click(function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Estas seguro de realizar esta acción?',
                text: "No podrás revertir",
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: 'No, cancelar!',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Estoy seguro!'
            }).then((result) => {
                if (result.value) {
                    var row = $(this).parents('tr'); //fila contentiva de la data
                    var id = row.data('id');
                    var ci_estudiant = row.data('ci_estudiant');
                    var ajaxurl = '{{route("administracion.ajax.api.administrativas.retiro", "_id_")}}'; ajaxurl = ajaxurl.replace('_id_', id);
                    var urlNext = '{{route("administracion.estudiants.index", ["search"=>"_ci_"])}}'; urlNext = urlNext.replace('_ci_', ci_estudiant);
                    $.ajax({
                        url: ajaxurl,
                        type: "GET",
                        success: function(data){
                            row.fadeOut(500);
                            Swal.fire({
                                title: data.messenge,
                                text: data.text,
                                // html: 'Para ver más detalles, clic ' + '<a class="text-decoration-none" href="'+urlNext+'">aquí ...</a> ',
                                footer: data.text,
                                icon: 'success'
                            });
                        }
                    });
                }
            });
        });

    </script>
@endsection
