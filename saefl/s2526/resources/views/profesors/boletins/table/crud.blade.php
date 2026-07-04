@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-sm table-hover p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_ci }}">Cédula</th>
                <th class="{{ $class_grado }}">Plan de Pago</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_action }}">Retirar</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($estudiants as $estudiant)
            @php
                $enable_inscription = $estudiant->enable_inscription;
                $status_administrativa = empty($estudiant->administrativa->id);
            @endphp

            <tr data-estudiant="{{$estudiant->id}}" data-estudiant="{{$estudiant->id ?? ''}}" class="table-{{(empty($estudiant->administrativa->id)) ? 'default':'success'}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-users-username-{{ $estudiant->id }}" class="{{ $class_user  ?? ''}}">
                    <a class="btn-link" href="{{ route('administracion.estudiants.index',['search'=>$estudiant->ci_estudiant]) }}">
                        <span class=" font-weight-bold text-{{ ($enable_inscription) ? 'dark':'danger'}}">{{$estudiant->fullname}}</span>
                    </a>
                </td>
                <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_email ?? '' }}">
                    <span class="text-profiles-ci_estudiant-{{ $estudiant->id ?? ''}}">
                        {{ $estudiant->ci_estudiant ?? ''}}
                    </span>
                </td>

                <td id="td-users-{{ $estudiant->id }}" class="{{ $class_state ?? '' }}">
                    {{$estudiant->getInscripcion()->seccion->grado->name ?? ''}} {{$estudiant->getInscripcion()->seccion->name ?? ''}}
                </td>
                <td id="td-administrativa-{{ $estudiant->id }}" class="{{ $class_state ?? '' }}">
                    @if (empty($estudiant->administrativa->planpago_id))
                        <span class="badge badge-secondary mt-1" title="SIN PLAN DE PAGO ASIGNADO">.NINGUNO.</span>
                    @else
                        {!!$estudiant->administrativa->planpago->badge ?? ''!!}
                    @endif
                </td>

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">
                    <div id="div_ctr_{{ $estudiant->id ?? '' }}">
                        <a title="Haz clic para retirar" class="btn-retirar btn btn-danger" href="#" id="btn_retirar_id_{{$estudiant->id}}">
                            <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                            {{-- <i class="fa fa-check" aria-hidden="true"></i> --}}
                        </a>
                    </div>
                </td>

            </tr>
            @endforeach

        </tbody>

    </table>

    @section('scripts')
        @parent
        <script type="text/javascript">
            $(document).ready(function() {
                $('.crt_checkboxes').click(function (e) {
                    var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
                    var name = div.data('name');  console.log(name);
                    var checked = $(this).prop('checked'); console.log(checked);
                    $('#'+name).val(checked); console.log($('#'.name).val());
                });
            });
        </script>
    @endsection

{{-- </div> --}}
{!! Form::open(['route' => ['administracion.retiros.store',':ESTUDIANT_ID'], 'method' => 'POST', 'id'=>'form-retirar', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset("js/models/retiros/retirar.js") }}"></script>
@endsection

{{-- partials contentivo de los scripts datatables --}}
@include('profesors.datatables.default')
