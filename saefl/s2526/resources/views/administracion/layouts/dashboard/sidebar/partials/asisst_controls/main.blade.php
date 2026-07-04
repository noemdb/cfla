<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Control de Asistencia">Control de Asistencia</span>
<div class="dropdown-divider mb-0"></div>
@admin
<a title="Horaios Laborales" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
href="{{ route('administracion.asisst_controls.assit_schedules.index') }}">
    <i class="{{ $icon_menus['assit_schedules'] ?? '' }}  text-primary"></i>
    Horaios Laborales
</a>
@endadmin
<a title="Cargar Marajes del BIO" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
href="{{ route('administracion.asisst_controls.assit_attendances.index') }}">
    <i class="{{ $icon_menus['csv'] ?? '' }}  text-success"></i>
    Cargar Marcajes
</a>

<a title="Gestionar datos del personal" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
href="{{ route('administracion.asisst_controls.assit_attendances.setOrderWorker') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-info"></i>
    Gestionar Personal
</a>

<a title="Formatos de Asistencia" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
href="{{ route('administracion.asisst_controls.assit_attendances.format') }}">
    <i class="{{ $icon_menus['pdf'] ?? '' }}  text-dark"></i>
    Formatos de Asistencia
</a>

<a title="Asistencia Personal" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
href="{{ route('administracion.asisst_controls.assit_attendances.personal') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-danger"></i>
    Asistencia Personal
</a>

<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Control de Asistencia">Guías Instrucionales</span>

<a title="Horaios Laborales" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
href="{{ route('administracion.asisst_controls.assit_attendances.help.collectCSV') }}">
    <i class="{{ $icon_menus['ayuda'] ?? '' }}  text-success"></i>
    {{-- <i class="fa fa-question" aria-hidden="true"></i> --}}
    Recolección de Marcajes
</a>
@admin
<a title="Horaios Laborales" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
href="#">
    <i class="{{ $icon_menus['ayuda'] ?? '' }}  text-danger"></i>
    {{-- <i class="fa fa-question" aria-hidden="true"></i> --}}
    Horaios Laborales
</a>
@endadmin
<a title="Cómo cargar marcajes del BIO" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
href="{{ route('administracion.asisst_controls.assit_attendances.help.loadCSV') }}">
    <i class="{{ $icon_menus['ayuda'] ?? '' }}  text-info"></i>
    Cargar Marcajes
</a>
<a title="Formatos de Asistencia" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
href="{{ route('administracion.asisst_controls.assit_attendances.help.GeneratePDF') }}">
    <i class="{{ $icon_menus['ayuda'] ?? '' }}  text-rpimary"></i>
    Formatos de Asistencia
</a>
