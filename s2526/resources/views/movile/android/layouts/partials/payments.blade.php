<a class="nav-link dropdown-toggle text-success" href="#" data-bs-toggle="dropdown" aria-expanded="false">
    {{-- <i class="fa fa-user  fa-2x d-block mx-auto mb-1" aria-hidden="true"></i> --}}
    <i class="{{ $icon_menus['registropagos'] ?? '' }} fa-2x d-block mx-auto mb-1"></i>
    <span class="text-success">Pagos</span>
</a>
<ul class="dropdown-menu">
    <li>
        <a href="{{ route('movile.android.payment') }}" class="dropdown-item {{ Request::is('*payment*') ? 'fw-bold text-success' : null }}" aria-current="page" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Botón de Pago" data-bs-original-title="Botón de Pago">
            <i class="{{ $icon_menus['payment'] ?? '' }} fa-1x"></i>
            <span>Botón de Pago</span>
        </a>
    </li>
    <li><hr class="dropdown-divider"></li>
    <li>
        <a href="{{ route('movile.android.report') }}" class="dropdown-item {{ Request::is('*report*') ? 'fw-bold text-success' : null }}" aria-current="page" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Reporte de Pago" data-bs-original-title="Botón de Pago">
            <i class="{{ $icon_menus['documento'] ?? '' }} fa-1x"></i>
            <span>Reportes de Pago</span>
        </a>
    </li>
</ul>


{{-- <li class="nav-item text-center  border-bottom rounded-0">
    <div class="d-flex justify-content-center">
        <div class="m-2 p-2 border rounded">
            <a href="{{ route('movile.android.payment') }}" class="nav-link {{ Request::is('*payment*') ? 'active alert alert-success text-success fw-bold mb-0' : 'text-success' }}  py-3 " aria-current="page" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Botón de Pago" data-bs-original-title="Botón de Pago">
                <i class="{{ $icon_menus['payment'] ?? '' }} fa-2x d-block mx-auto mb-1"></i>
                <span>Botón de Pago</span>
            </a>
        </div>
        <div class="m-2 p-2 border rounded">
            <a href="{{ route('movile.android.report') }}" class="nav-link {{ Request::is('*report*') ? 'active alert alert-success text-success fw-bold mb-0' : 'text-success' }}  py-3 " aria-current="page" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Botón de Pago" data-bs-original-title="Botón de Pago">
                <i class="{{ $icon_menus['documento'] ?? '' }} fa-2x d-block mx-auto mb-1"></i>
                <span>Reportes de Pago</span>
            </a>
        </div>
    </div>
</li> --}}