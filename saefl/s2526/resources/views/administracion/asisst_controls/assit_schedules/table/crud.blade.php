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
            <th class="{{ $class_N }}">Ident.</th>
            <th class="{{ $class_N }}">Cédula</th>
            <th class="{{ $class_estudiant }}">Estudiante</th>
            <th class="{{ $class_grado }}">Género</th>
            <th class="{{ $class_grado }}">F.Nacimiento</th>
            {{-- <th class="{{ $class_grado }}">Email</th> --}}
            <th class="{{ $class_grado }}">GSEmail</th>
            <th class="{{ $class_grado }}">N.Usuario</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @forelse($estudiants as $estudiant)

            @php
                $estudiant_id = $estudiant->sid;
                $ci_estudiant = $estudiant->ci_estudiant;
                $name = $estudiant->name;
                $lastname = $estudiant->lastname;
                $gender = $estudiant->gender;
                $date_birth = $estudiant->date_birth;
                $email = $estudiant->email;
            @endphp

            <tr data-id="{{$estudiant['ID'] ?? ''}}">

                <td class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>

                <td class="{{ $class_N }}">
                    {{$estudiant_id ?? null}}
                </td>
                <td class="{{ $class_N }}">
                    {{$ci_estudiant ?? null}}
                </td>
                <td class="{{ $class_estudiant  ?? ''}}">
                    {{ $lastname ?? null}} {{ $name ?? null}}
                    {{-- <span class="">[{{ $ci_estudiant ?? null}}]</span> --}}
                </td>
                <td class="{{ $class_grado ?? '' }}">
                    {{ $estudiant['gender'] ?? ''}}
                </td>
                <td class="{{ $class_grado ?? '' }}">
                    {{ $estudiant['date_birth'] ?? ''}}
                </td>
                {{-- <td class="{{ $class_grado ?? '' }}">
                    {{ $estudiant['email'] ?? ''}}
                </td> --}}
                <td class="{{ $class_grado ?? '' }}">
                    {{ $estudiant->gsemail ?? ''}}
                </td>
                <td class="{{ $class_grado ?? '' }} small">
                    {{ $estudiant->username ?? ''}}
                </td>

            </tr>

        @empty

            <tr> <td colspan="7" class=" small text-center font-weight-bold">NO HAY DATOS</td> </tr>

        @endforelse

    </tbody>
</table>


{{-- partials contentivo de los scripts datatables --}}
@include('datatables.default')
