<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Evaluación">Histórico</span>
<div class="dropdown-divider mb-0 mb-0"></div>

<a title="Histórico de Notas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.historico_notas.index') }}">
    <i class="{{ $icon_menus['historico'] ?? '' }}  text-info"></i>
    Histórico de Notas
</a>
<a title="Carga/Actualización para las Notas Certificadas del período escolar actual" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.historico_notas.carga') }}">
    <i class="{{ $icon_menus['boletin'] ?? '' }}  text-primary"></i>
    Actualización de Notas PE
</a>
{{-- <a title="Listado Histórico de Notas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.historico_notas.create') }}">
    <i class="{{ $icon_menus['nuevo'] ?? '' }}  text-primary"></i>
    Crear H.N.
</a> --}}
<a title="Listado Histórico de Notas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.historico_notas.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dark"></i>
    Certificación de Notas
</a>

{{-- <a title="Histórico de Notas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.historico_notas.index') }}">
    <i class="{{ $icon_menus['notas'] ?? '' }}  text-danger"></i>
    H. Notas
</a>
<a title="Listado de Notas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.historico_notas.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dark"></i>
    Listado de Notas
</a> --}}

{{-- {{ $estudiant->id ?? 'fallo' }} --}}
{{-- <div class="p-1">
    @includeWhen(!empty($estudiant->id),'administracion.estudiants.partials.estudiant')
</div> --}}
