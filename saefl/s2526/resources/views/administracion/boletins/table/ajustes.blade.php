@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_estudiant }}">Estudiante</th>

            <th class="{{ $class_estudiant }} small" title="{{$pensum->asignatura->name ?? ''}}">
                {{$pensum->asignatura->code ?? ''}}
            </th>
            <th class="{{ $class_estudiant }} small">
                Ajuste
            </th>

            <th class="{{ $class_action ?? '' }}">Acción</th>

        </tr>
    </thead>

    <tbody id="tdatos">

    @foreach($estudiants as $estudiant)

        @php
            $boletin_ajuste = (!empty($pevaluacion)) ? $pevaluacion->getAjuste($estudiant->id):null;
            $id = (!empty($boletin_ajuste)) ? $boletin_ajuste->id:null;
            $ajuste = (!empty($boletin_ajuste)) ? $boletin_ajuste->ajuste:null;
            $nota = (!empty($estudiant->getNota($lapso_id,$pensum->id))) ? round($estudiant->getNota($lapso_id,$pensum->id),0):null;
            $minimo = ($pensum->pevaluacions->where('seccion_id',$seccion_id)->where('lapso_id',$lapso_id)->first()->escala->minimo) ? : 0;
            $maximo = ($pensum->pevaluacions->where('seccion_id',$seccion_id)->where('lapso_id',$lapso_id)->first()->escala->maximo) ? : 0;
            // $maximo = (!empty($nota)) ? ($maximo - $nota):$maximo;
            $maximo = (!empty($nota)) ? ($maximo - ($nota - $ajuste)) : $maximo;
            $disabled = (Auth::user()->isControlDir()) ? null : (isset($nota) ? ' disabled ' : null);

        @endphp

        <tr data-id="{{ $id ?? '' }}" data-estudiant_id="{{ $estudiant->id ?? '' }}">

            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>

            <td id="td-users-username-{{ $estudiant->id }}" class="{{ $class_user  ?? ''}}">
                {{$estudiant->fullname ?? ''}}
                <div class="small">{{$estudiant->ci_estudiant ?? ''}}</div>
            </td>

            <td class="{{ $class_estudiant ?? ''}}">

                <span style="{{ ($ajuste) ? 'font-style: italic; text-decoration: underline;':null }}" id="nota_{{$estudiant->id}}">
                    {{ ($nota) ? round($nota,0):''}}
                </span>

            </td>

            <td class="{{ $class_estudiant ?? ''}}">

                <fieldset {{ $disabled }}>                
                    @if ($maximo >= 1 && !empty($nota))
                        {!! Form::open(['route'=>'administracion.boletins.store_ajustes','method'=>'POST','id'=>'form-ajuste-'.$estudiant->id]) !!}
                            {{ Form::hidden('pevaluacion_id', $pevaluacion->id) }}
                            {{ Form::hidden('estudiant_id', $estudiant->id) }}
                            {!! Form::selectRange('ajuste', 0, $maximo,$ajuste,['id'=>'obj-select-'.$estudiant->id]) !!}
                        {!! Form::close() !!}
                    @endif
                </fieldset>

            </td>

            <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">
                <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">

                    <button type="button" class="btn-ajuste btn btn-outline-primary" value="btn-create-boletin_{{$estudiant->id ?? ''}}" id="btn-create-boletin_{{$estudiant->id ?? ''}}">
                        <i class="fa fa-save" aria-hidden="true"></i>
                    </button>
                    {{-- <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{ (empty($boletin_ajuste)) ? 'disabled':null}}" href="#" id="btn-destroy_id_{{$estudiant->id}}">
                        <i class="fas fa-trash"></i>
                    </a> --}}

                </div>
            </td>

        </tr>

        @endforeach

    </tbody>

</table>

{{-- {!! Form::open(['route' => ['administracion.boletins.ajuste.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts')  @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection --}}

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.simple')

@section('scripts')
    @parent

    <script type="text/javascript">
       //ini del evento clic
        $('.btn-ajuste').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('estudiant_id');  //console.log(id);
            var nota = $('#nota_'+id)
            var form = $('#form-ajuste-'+id); //console.log(form.attr('action'));
            var data = form.serialize(); //console.log(data); console.log(data);
            var url = "{{ route('administracion.boletins.store_ajustes') }}"; //console.log(url);
            $.post(url, data, function (result){
                $('#obj-select-'+id).attr('disabled','disabled');
                nota.html(result.nota);
                if (result.ajuste) {
                    nota.removeAttr("style");
                    nota.css({"font-style":"italic","text-decoration":"underline"});
                    // $("p").css({"background-color": "yellow", "font-size": "200%"});
                }
                Swal.fire({
                    title: result.messenge,
                    icon: 'success'
                });
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
