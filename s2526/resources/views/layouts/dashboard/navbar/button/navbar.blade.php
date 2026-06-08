@if (Auth::user()->isAdmin())
    <li class="nav-item p-1">
        <a class="nav-link" title="Administración" href="{{ route('admin.home') }}">
            {{-- <i class="{{ $icon_menus['administracion'] }} fa-2x text-light"></i> --}}
            <i class="fa fa-bookmark" aria-hidden="true"></i>
        </a>
    </li>            
@endif
@if (Auth::user()->isAdmon())
    <li class="nav-item p-1">
        <a class="nav-link" title="Administración" href="{{ route('administracion.home') }}">
            <i class="{{ $icon_menus['administracion'] }} fa-2x text-light"></i>
        </a>
    </li>            
@endif
@if (Auth::user()->isControl())
    <li class="nav-item p-1">
        {{-- <a class="nav-link" title="Administración" href="{{ route('control.home') }}"> --}}
        <a class="nav-link" title="Administración" href="{{ route('administracion.home') }}">
            <i class="{{ $icon_menus['control_estudio'] }} fa-2x text-light"></i>
        </a>
    </li>            
@endif