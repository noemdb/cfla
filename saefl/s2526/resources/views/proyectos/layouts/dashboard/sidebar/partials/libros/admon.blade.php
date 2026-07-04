<span class="dropdown-header text-center font-weight-bold text-dark bg-light mb-0 pb-0" title="Creditos a favor">
    Libros Inscripciones
</span>
<div class="dropdown-divider mb-0"></div>

<a title="Libro de Inscripciones Administrativas" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.administrativas.book') }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-primary"></i>
    Administrativas
</a>

<span class="dropdown-header text-center font-weight-bold text-dark bg-light mt-1 pt-1 mb-0 pb-0" title="Libros para los bancos">
    Libros Bancos
</span>

<div class="dropdown-divider mb-0"></div>

<a title="Libro de Bancaribe" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.libro.banco',['banco_id'=>"3"]) }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-secondary"></i>
    Bancaribe
</a>
<a title="Libro de Pago Móvil" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.libro.banco',['banco_id'=>"6"]) }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-warning"></i>
    Pago Móvil
</a>

<a title="Libro de Bancaribe" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.libro.banco',['banco_id'=>"2"]) }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-info"></i>
    Banco del Tesosro
</a>


{{-- <a title="Libro de Bancaribe" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.administrativas.book') }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-warning"></i>
    Crédito año anterior
</a> --}}
<a title="Libro de Bancaribe" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.libro.banco',['banco_id'=>"5"]) }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-danger"></i>
    PANDCO
</a>

