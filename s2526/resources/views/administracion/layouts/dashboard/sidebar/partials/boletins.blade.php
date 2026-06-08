<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Evaluación">Boletines</span>
<div class="dropdown-divider mb-0 mb-0"></div>

<a title="Carga de notas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
href="{{ route('administracion.boletins.indicators') }}">
    <i class="{{ $icon_menus['chartbar'] ?? '' }}  text-dark"></i>
    Indicadores
</a>

<a title="Carga de notas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
href="{{ route('administracion.boletins.index') }}">
    <i class="{{ $icon_menus['notas'] ?? '' }}  text-primary"></i>
    Carga de notas
</a>

<a title="Carga de notas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
href="{{ route('administracion.boletins.carga.xls') }}">
    <i class="{{ $icon_menus['xls'] ?? '' }}  text-success"></i>
    Carga de notas XLS
</a>

<a title="Listado Notas registradas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.boletins.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dark"></i>
    Listado Notas registradas
</a>

<a title="Asignar puntos de ajuste de Notas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.boletins.ajustes') }}">
    <i class="{{ $icon_menus['carga'] ?? '' }}  text-dark"></i>
    Asignar puntos de ajuste
</a>
<a title="Asignar puntos de ajuste de Notas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.boletins.crud_ajuste') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dark"></i>
    Listado puntos de ajuste
</a>

<a title="Sabana de Notas por Grado/Sección/Lapso individuales" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.boletins.sabana') }}">
    <i class="{{ $icon_menus['list'] ?? '' }}  text-info"></i>
    Planilla de registro de notas
</a>

<a title="Registro de Notas por Grado/Sección todos los lapsos y definitiva" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.boletins.sabanafull') }}">
    <i class="{{ $icon_menus['boletin'] ?? '' }}  text-primary"></i>
    Registro de notas
</a>

<a title="Informe de Notas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.boletins.boletin') }}">
    <i class="{{ $icon_menus['boletin'] ?? '' }}  text-dark"></i>
    Informe de notas
</a>
{{-- @admin --}}
<a title="Informe de Corte de Notas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.boletins.corte') }}">
    <i class="{{ $icon_menus['boletin'] ?? '' }}  text-danger"></i>
    Corte de Notas
</a>
{{-- @endadmin --}}

<a title="Resumen final del rendimiento estudiantíl" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.boletins.resumen_final') }}">
    <i class="{{ $icon_menus['boletin'] ?? '' }}  text-info"></i>
    Resumen Final
</a>

<a title="Resumen de Revisión" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.boletin_revisions.index') }}">
    <i class="{{ $icon_menus['boletin'] ?? '' }}  text-danger"></i>
    Resumen Revisión
</a>

<a title="Resumen de Revisión" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.boletins.positions') }}">
    <i class="{{ $icon_menus['boletin'] ?? '' }}  text-success"></i>
    Posiciones
</a>


{{-- <a title="Carga de notas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
href="{{ route('administracion.edescriptivas.index') }}">
    <i class="{{ $icon_menus['notas'] ?? '' }}  text-info"></i>
    Evaluación descriptiva
</a> --}}


{{-- <a title="Listado de Boletines" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.boletins.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dark"></i>
    Listado
</a> --}}
