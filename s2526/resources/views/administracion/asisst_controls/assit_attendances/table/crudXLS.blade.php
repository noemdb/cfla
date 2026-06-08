@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_grado="d-none d-lg-table-cell text-nowrap";
    $class_tipo="d-none d-lg-table-cell";
    $class_escolaridad="d-none d-lg-table-cell";
    $class_gestable="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small py-1" id="table-data-default" >

    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_estudiant }}">Estudiante</th>
            {{-- <th class="{{ $class_estudiant }}">Representante</th> --}}
            <th class="{{ $class_grado }}">Género</th>
            <th class="{{ $class_grado }}">F.Nacimiento</th>
            <th class="{{ $class_grado }}">GSEmail</th>
            <th class="{{ $class_grado }}">N.Usuario</th>
            <th class="{{ $class_grado }}">Activo</th>
            <th class="{{ $class_action }} text-center">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @forelse($estudiantsXLS as $estudiant)

            @php
                $ci_representant = (isset($estudiant['ci_representant'])) ? $estudiant['ci_representant'] : null;
                $representant_name = (isset($estudiant['representant_name'])) ? $estudiant['representant_name'] : null;
                $estudiant_id = (isset($estudiant['estudiant_id'])) ? $estudiant['estudiant_id'] : null;
                $ci_estudiant = (isset($estudiant['ci_estudiant'])) ? $estudiant['ci_estudiant'] : null;
                $name = (isset($estudiant['name'])) ? $estudiant['name'] : null;
                $lastname = (isset($estudiant['lastname'])) ? $estudiant['lastname'] : null;
                $gender = (isset($estudiant['gender'])) ? $estudiant['gender'] : null;
                $date_birth = (isset($estudiant['date_birth'])) ? $estudiant['date_birth'] : null;
                $email = (isset($estudiant['email'])) ? $estudiant['email'] : null;
                $gsemail = (isset($estudiant['gsemail'])) ? $estudiant['gsemail'] : null;
                $username = (isset($estudiant['username'])) ? $estudiant['username'] : null;
                $status_active = (isset($estudiant['status_active'])) ? $estudiant['status_active'] : null;
                $exist = (isset($estudiant['exist'])) ? $estudiant['exist'] : null;
            @endphp

            <tr data-id="{{$estudiant['ID'] ?? ''}}">

                <td class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_estudiant  ?? ''}}">
                    {{ $lastname ?? null}} {{ $name ?? null}}
                    <span class=" small">{{ $ci_estudiant ?? null}}</span>
                    <div class="text-muted pl-2">
                        {{ $representant_name ?? null}}
                        <span class=" small">{{ $ci_representant ?? null}}</span>
                    </div>
                </td>
                {{-- <td class="{{ $class_estudiant  ?? ''}}">
                    {{ $representant_name ?? null}}
                    <span class=" small">{{ $ci_representant ?? null}}</span>
                </td> --}}
                <td class="{{ $class_grado ?? '' }} small">
                    {{ $gender ?? ''}}
                </td>
                <td class="{{ $class_grado ?? '' }} small">
                    {{ $date_birth ?? ''}}
                </td>
                <td class="{{ $class_grado ?? '' }} small">
                    {{ $gsemail ?? ''}}
                </td>
                <td class="{{ $class_grado ?? '' }} small">
                    {{ $username ?? ''}}
                </td>
                <td class="{{ $class_grado ?? '' }} small">
                    {{ ($status_active) ? 'Si' : 'No' }}
                </td>

                <td class=" text-center font-weight-bold {{ $class_action ?? '' }} {{ ($exist) ? 'table-success' : 'table-secondary' }}">
                    {{ ($exist) ? 'Registrado' : 'No registrado' }}
                </td>

            </tr>

        @empty

            <tr> <td colspan="7" class=" small text-center font-weight-bold">NO HAY DATOS</td> </tr>

        @endforelse

    </tbody>
</table>


@php $disabled = ($file_path) ? null : 'disabled' ; @endphp
<fieldset {{$disabled}}>
    {!! Form::open(['route'=>'controls.estudiants.store.xls','method'=>'POST','id'=>'form-nota','class'=>'form-nota pb-2', 'role'=>'form-signin']) !!}

        {{ Form::hidden('file_path', $file_path) }}

            <button type="submit" class="btn-boletin btn btn-primary btn-block w-100">
                <i class="fa fa-save" aria-hidden="true"></i>
                Sincronizar
            </button>

    {!! Form::close() !!}
</fieldset>






{{-- partials contentivo de los scripts datatables --}}
@include('datatables.simple_search')
