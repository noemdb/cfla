@php
    $class_N = '';
    $class_estudiant = '';
    $class_ci = '';
    $class_grado = '';
    $class_action = '';
@endphp
{!! Form::open([
    'route' => 'administracion.inscripciones.asignarStore',
    'method' => 'POST',
    'class' => 'form-signin',
]) !!}

{!! Form::hidden('search', $search) !!}
{!! Form::hidden('grado_id', $grado_id) !!}
{!! Form::hidden('seccion_id', $seccion_id) !!}
{!! Form::hidden('status_preinscripcion', $status_preinscripcion) !!}
{!! Form::hidden('prosecucion_seccion_id', $prosecucion_seccion_id) !!}

<table width="100%" class="table table-striped table-hover table-sm small p-1 " id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_estudiant }}">Estudiante</th>
            <th class="{{ $class_ci }}">Cédula</th>
            <th class="{{ $class_grado }}">Prosecución</th>
            <th class="{{ $class_grado }} text-center">Preinscripción/G.E.</th>
            {{-- <th class="{{ $class_grado }} text-center">Inscripción Académica [Grado||Sección||G.Estable]</th> --}}
            <th class="{{ $class_grado }} text-center">Inscripción Académica [Grado||Sección]</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">
        @foreach ($estudiants as $estudiant)
            @php
                $preinscripcion = $estudiant->preinscripcion;
                $inscripcion = $estudiant->getInscripcion();
                $administrativa = $estudiant->administrativa;
                $fullinscripcion = $estudiant->fullinscripcion ? $estudiant->fullinscripcion : null;
                $prosecucion = $estudiant->prosecucion;
            @endphp

            <tr data-estudiant="{{ $estudiant->id }}" data-id="{{ $estudiant->id }}"
                title="{{ $estudiant->status_blacklist == 'true' ? 'Este estudiante incumplió con el compromiso de pago en las fechas correspondientes.' : null }}"
                class="table-{{ empty($estudiant->administrativa->id) ? 'default' : 'secondary font-weight-bold' }}">

                <td id="td-count" class="{{ $class_N }}">
                    {{ $loop->iteration }}
                </td>
                <td
                    class="{{ $class_estudiant ?? '' }} {{ $estudiant->status_blacklist == 'true' ? 'text-danger' : null }}">
                    @admin
                        {{ $estudiant->id }}
                    @endadmin {{ $estudiant->fullname }}
                    @if ($estudiant->status_blacklist == 'true')
                        <div class=" d-block text-danger">Este estudiante incumplió con el compromiso de pago en las
                            fechas correspondientes.</div>
                    @endif
                </td>
                <td class="{{ $class_estudiant ?? '' }}">
                    {{ $estudiant->ci_estudiant ?? '' }}
                </td>
                <td class="{{ $class_estudiant ?? '' }}">
                    {{ !empty($prosecucion) ? $prosecucion->fullname : null }}
                </td>

                <td
                    class="{{ $class_estudiant ?? '' }} text-center table-{{ $preinscripcion ? 'success' : 'danger' }}">
                    <span class="text-{{ $preinscripcion ? 'success' : 'danger' }} font-weight-bold">
                        {{ $preinscripcion ? $preinscripcion->fullPreinscripcion() : 'NO POSEE' }}
                    </span>
                </td>

                <td class="{{ $class_estudiant ?? '' }} table-{{ $inscripcion ? 'success' : 'danger' }} text-center">
                    @if ($fullinscripcion)
                        <span class="font-weight-bold text-success">
                            {{ $fullinscripcion ?? 'NO POSEE' }}
                        </span>
                    @else
                        <fieldset {{ $estudiant->status_blacklist == 'true' ? 'disabled="disabled"' : null }}>
                            @php $name = 'grado_arr['.$estudiant->id.']'; @endphp
                            {!! Form::select($name, $list_grado, null, [
                                'class' => 'm-1 p-1 btn btn-light',
                                'id' => $name,
                                'placeholder' => '',
                            ]) !!}
                            @php $name = 'seccion_arr['.$estudiant->id.']'; @endphp
                            {!! Form::select($name, ['A' => 'A', 'B' => 'B', 'U' => 'U'], null, [
                                'class' => 'm-1 p-1 btn btn-light',
                                'id' => $name,
                                'placeholder' => '',
                            ]) !!}
                            {{-- @php $name = 'grupo_arr['.$estudiant->id.']'; @endphp
                            {!! Form::select($name,$list_grupo_estable,null,['class'=>'m-1 p-1 btn btn-light','id'=>$name,'placeholder'=>'']) !!} --}}
                        </fieldset>
                    @endif
                </td>

                <td class="{{ $class_action ?? '' }}">
                    <div class="btn-group btn-group-sm">
                        @if ($inscripcion)
                            <a title="Constacia de Inscripción"
                                class="btn btn-dark btn-sm {{ $administrativa ? null : 'disabled' }}"
                                title="{{ $administrativa ? null : 'Sin inscripción administrativa' }}" target="_blank"
                                href="{{ route('administracion.inscripciones.constancia.pdf', $estudiant->id) }}"
                                role="button">
                                <i class="{{ $icon_menus['pdf'] }} fa-1x"></i>
                            </a>
                        @endif
                    </div>
                </td>

            </tr>
        @endforeach

    </tbody>
</table>
@php $disabled = (Session::get('pescolar_ffinal') < Carbon\Carbon::now()) ? true:false @endphp
<fieldset {{ $disabled ? 'disabled=disabled' : null }}>
    {!! Form::submit('Procesar inscripciones', [
        'class' => 'btn-create btn btn-primary btn-block',
        'id' => 'create',
    ]) !!}
</fieldset>


{!! Form::close() !!}


{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
