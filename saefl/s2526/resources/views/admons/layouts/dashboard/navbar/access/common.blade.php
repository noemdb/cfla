<li class="nav-item p-1">
    <a class="nav-link" title="{{Auth::user()->area ?? 'fallo'}}" href="{{ route('directors.home') }}">
        {{-- {{Auth::user()->hasRole() ?? 'fallo'}} --}}

        <i class="{{ $icon_menus[Auth::user()->area] ?? ''}} fa-2x text-light"></i>
    </a>
</li>

<li class="nav-item dropdown pl-1">
    @include('directors.layouts.dashboard.navbar.partials.inicio')
</li>
<li class="nav-item dropdown pl-1">
    @include('directors.layouts.dashboard.navbar.partials.configuraciones')
</li>
<li class="nav-item dropdown pl-1">
    @include('directors.layouts.dashboard.navbar.partials.estudiants')
</li>
<li class="nav-item dropdown pl-1">
    @include('directors.layouts.dashboard.navbar.partials.representants')
</li>
