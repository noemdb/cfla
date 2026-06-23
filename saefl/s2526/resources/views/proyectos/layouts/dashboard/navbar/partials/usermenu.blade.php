<div class="btn-group">
    <a class="nav-link dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="{{ $icon_menus['user'] ?? '' }} "></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item p-1" href="#">
            @include('layouts.partials.card.user')
        </a>
        <div class="dropdown-divider mb-0"></div>
        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt text-danger"></i>
            {{ __('Salir') }}
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </a>
    </div>
</div>
