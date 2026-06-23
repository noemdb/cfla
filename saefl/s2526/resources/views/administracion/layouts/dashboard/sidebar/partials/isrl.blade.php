
<a title="Cifras Generales para el ISLR" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.isrl.index') }}">
    <i class="{{ $icon_menus['isrl'] }} text-info"></i>
    <b>Balance ISLR</b> - Cifras Generales
</a>

{{-- <a title="Reporte Anualidades y Mensualidades - Pendientes de Pago" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.isrl.conceptopagos.outstanding') }}">
    <i class="{{ $icon_menus['crud'] }} text-info"></i>
    <b>R1</b> - Pendientes de Pago
</a> --}}

<a title="Reporte Anualidades y Mensualidades - Pagados" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.isrl.conceptopagos.paids') }}">
    <i class="{{ $icon_menus['crud'] }} text-info"></i>
    <b>R2</b> - Pagados
</a>

