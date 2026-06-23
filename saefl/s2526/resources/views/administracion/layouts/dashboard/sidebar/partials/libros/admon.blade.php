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
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-primary"></i>
    Bancaribe
</a>
{{-- <a title="Libro de Pago Móvil Bancaribe" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.libro.banco',['banco_id'=>"6"]) }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-warning"></i>
    Pago Móvil Bancaribe
</a> --}}
{{-- <a title="Libro de Pago Móvil" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.libro.banco',['banco_id'=>"9"]) }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-danger"></i>
    Pago Móvil Tesoro
</a> --}}

<a title="Libro de Bancaribe" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.libro.banco',['banco_id'=>"2"]) }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-info"></i>
    Banco del Tesosro
</a>

<a title="Libro de Ajustes" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.libro.banco',['banco_id'=>"7"]) }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-secondary"></i>
    Banco de Ajustes
</a>
<a title="Libro de Fondo Divisas" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.libro.banco',['banco_id'=>"8"]) }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-dark"></i>
    Fondo Divisas
</a>

<a title="Libro de Bancaribe USD" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.libro.banco',['banco_id'=>"10"]) }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-primary"></i>
    Bancaribe USD
</a>
{{-- <a title="Libro de Bancaribe" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.administrativas.book') }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-warning"></i>
    Crédito año anterior
</a> --}}
{{-- <a title="Libro de Bancaribe" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.libro.banco',['banco_id'=>"5"]) }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-primary"></i>
    PANDCO
</a> --}}

<a title="Libro de Devoluciones" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="#">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-danger"></i>
    Devoluciones
</a>

<a title="Libro de Bancaribe" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
    href="{{ route('administracion.libro.banco.libro.abonos') }}">
    <i class="{{ $icon_menus['libro'] ?? '' }}  text-info"></i>
    Libro N.Asociados
</a>
