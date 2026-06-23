@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_pensum="nosort";
    $class_action="nosort";
@endphp

<table width="100%" class="table table-striped table-sm table-hover table-sm p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_estudiant }}">Estudiante</th>
            @php $pevaluacion = $pensum->pevaluacions->where('seccion_id',$seccion->id)->where('lapso_id',$lapso->id)->first(); @endphp
            @if (!empty($pevaluacion))
                @if (!empty($pevaluacion->evaluacions->first()))
                    @foreach ($pevaluacion->evaluacions as $evaluacion)
                        <th class="{{ $class_pensum }} text-center" title="{{$evaluacion->description ?? ''}}">
                            {{$loop->iteration}}
                        </th>
                    @endforeach
                @else
                    <th class="alert alert-danger text-center">NO HAY EVALUACIONES REGISTRADAS</th>
                @endif
            @else
            <th class="alert alert-danger text-center">NO HAY PLAN DE EVALUACIÓN REGISTRADO</th>
            @endif

            <th class="{{ $class_action ?? '' }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($estudiants as $estudiant)

        <tr data-id="{{$estudiant->id}}">

            {!! Form::open(['route'=>'administracion.boletins.store','method'=>'POST','id'=>'form-nota-'.$estudiant->id,'class'=>'form-nota pb-2', 'role'=>'form-signin']) !!}

            {{ Form::hidden('grado_id', $grado->id,['id'=>'grado_id']) }}
            {{ Form::hidden('lapso_id', $lapso->id,['id'=>'lapso_id']) }}
            {{ Form::hidden('seccion_id', $seccion->id,['id'=>'seccion_id']) }}
            {{ Form::hidden('pensum_id', $pensum->id,['id'=>'pensum_id']) }}

            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>
            <td id="td-users-username-{{ $estudiant->id }}" class="{{ $class_user  ?? ''}}">
                {{-- <a class="btn-link text-dark small" href="{{ route('administracion.estudiants.index',['search'=>$estudiant->ci_estudiant]) }}"> --}}
                    {{$estudiant->fullname}}<br>
                    {{ $estudiant->ci_estudiant ?? ''}}
                {{-- </a> --}}
            </td>

            @if ($pevaluacion)
                @php $studiant_current = $estudiant; @endphp

                @if (!empty($pevaluacion->evaluacions->first()))
                    @foreach ($pevaluacion->evaluacions as $evaluacion)
                        <td class="{{ $class_pensum }} text-center">
                            @php
                                $name_entera = 'nota['.$estudiant->id.']['.$evaluacion->id.']';
                                $name_decimal = 'decimal['.$estudiant->id.']['.$evaluacion->id.']';
                                $minimo =  number_format( (float) $evaluacion->escala->minimo, 2, '.', '');
                                $maximo =  number_format( (float) $evaluacion->escala->maximo, 2, '.', '');
                                $entera = null ;
                                $decimal = null ;
                            @endphp
                            @if (!empty($evaluacion->boletins->where('estudiant_id',$estudiant->id)->first()->nota))
                                @php
                                    $nota = floatval($evaluacion->boletins->where('estudiant_id',$estudiant->id)->first()->nota);
                                    $nota = number_format($nota, 1);
                                    list($entera, $decimal) = explode('.', $nota);
                                @endphp
                            @endif
                            {{-- {{$nota ?? 'null'}} --}}
                            {!! Form::selectRange($name_entera, $minimo, $maximo,$entera,['class'=>'obj-select-'.$estudiant->id,'placeholder' => '']) !!},
                            {!! Form::selectRange($name_decimal, 0, 9,$decimal,['class'=>'obj-select-'.$estudiant->id,'placeholder' => '']) !!}
                        </td>
                    @endforeach
                @else
                    <td></td>
                @endif
            @else
                <td></td>
            @endif

            <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">
                @if (!empty($pevaluacion->evaluacions->first()))
                    <div class="btn-group" role="group" aria-label="Basic example">

                        <button type="submit" class="btn-boletin btn btn-outline-primary" {{$pevaluacion->id ?? 'disabled'}} value="btn-create-boletin_{{$estudiant->id ?? ''}}" id="btn-create-boletin_{{$estudiant->id ?? ''}}">
                            <i class="fa fa-save" aria-hidden="true"></i>
                        </button>

                        <a target="_blank" class="btn btn-outline-danger {{$pevaluacion->id ?? 'disabled'}}" href="{{ route('administracion.boletins.boletin.pdf',$estudiant->id) }}" role="button">
                            <i class="fa fa-file-pdf" aria-hidden="true"></i>
                        </a>

                    </div>
                @endif
            </td>
            {!! Form::close() !!}
        </tr>

    @endforeach

    </tbody>
</table>

@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/DataTables-1.10.16/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/datatable/default.js") }}"></script>
@endsection

@section('scripts')
    @parent

    <script type="text/javascript">
       //ini del evento clic
        $('.btn-boletin').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  //console.log(id);
            var form = $('#form-nota-'+id); //console.log(form.attr('action'));
            // var form = $('.form-nota'); console.log(form.attr('action'));
            var data = form.serialize(); //console.log(data); console.log(data);
            var url = "{{ route('profesors.boletins.store') }}"; console.log(url);
            $.post(url, data, function (result){
                $('.obj-select-'+id).attr('disabled','disabled');
                // Swal.fire({
                //     title: result.messenge,
                //     type: 'success'
                // });
            }).fail(function (result) {
                Swal.fire({
                        title: 'ERROR',
                        type: 'error'
                    });
            });
        });
        //fin del evento clic
    </script>

@endsection

