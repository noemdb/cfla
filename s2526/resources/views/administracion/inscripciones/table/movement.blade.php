@php
    $class_N="d-none d-sm-table-cell";
    $class_movement="";
    $class_estudiant="";
    $class_institution="";
    $class_ci="d-none d-sm-table-cell";
    $class_planpago="d-none d-sm-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                {{-- Movimiento	Colegio	Grado y sección	Apellidos	Nombres	Fecha de nacimiento	Cédula (si tiene)	Correo electrónico del alumno	Correo electrónico madre	Correo electrónico padre --}}
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_movement }}">Movimiento</th>
                <th class="{{ $class_institution}}">Colegio</th>
                <th class="{{ $class_estudiant }}">Grado y Sección</th>
                <th class="{{ $class_estudiant }}">Apellidos</th>
                <th class="{{ $class_estudiant }}">Nombres</th>
                <th class="{{ $class_estudiant }}">F.Nacimiento</th>
                <th class="{{ $class_estudiant }}">Cédula</th>
                <th class="{{ $class_planpago }}">Correo electrónico</th>
                <th class="{{ $class_planpago }}">Correo electrónico representante</th>
                <th class="{{ $class_planpago }}">Representante</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @php $institucion_name = Session::get('institucion_name'); @endphp

            @foreach($inscripcions as $inscripcion)

                @php 
                    $estudiant = $inscripcion->estudiant;
                    $gradoSeccion = $estudiant->full_inscripcion;
                    $representant = $estudiant->representant;
                    $grado = $estudiant->grado; 
                    $seccion = $estudiant->seccion; 
                    $date_birth = ($estudiant->date_birth) ? $estudiant->date_birth : $estudiant->date_enrollment;
                    $created_at = ($inscripcion) ? $inscripcion->created_at->format('Y-m-d'): null;
                    $movement = ($created_at > $lastdate) ? 'NUEVO' : null;
                @endphp                
                @include('administracion.inscripciones.table.partials.tr')
                
            @endforeach

            @foreach($estudiants_retiros as $estudiant)
                @php 
                    $inscripcion = $estudiant->inscripcion;
                    $gradoSeccion = $estudiant->full_inscripcion;
                    $representant = $estudiant->representant;
                    $grado = $estudiant->grado; 
                    $seccion = $estudiant->seccion; 
                    $date_birth = ($estudiant->date_birth) ? $estudiant->date_birth : $estudiant->date_enrollment;                    
                    $created_at = ($inscripcion) ? $inscripcion->created_at->format('Y-m-d'): null;
                    $movement = 'RETIRADO';
                @endphp
                @include('administracion.inscripciones.table.partials.tr')

            @endforeach

        </tbody>
    </table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.exportBootstrap')
