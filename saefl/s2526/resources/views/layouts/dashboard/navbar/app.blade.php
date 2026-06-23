<a class="navbar-brand col-sm-3 col-md-2 m-0 p-0" href="{{ route('home') }}">
    <img class="{{-- mb-4 --}}" src="{{ asset('images/brand/48/1.png') }}" alt="" width="48" height="48">
    {{ config('app.name', 'Laravel') }}<br>
</a>
<button class="navbar-toggler text-aling-rigth" type="button" data-toggle="collapse" data-target="#navbarTopAdministracion" aria-controls="navbarTopAdministracion" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

{{-- <input class="form-control form-control-dark w-100" type="text" placeholder="Buscar" aria-label="Buscar"> --}}

<div class="collapse navbar-collapse" id="navbarTopAdministracion">
    {{-- {{ Auth::user()->isAdmin() }} --}}
    <ul class="navbar-nav px-3">
        
        @includeWhen(Auth::user()->isAdmin(), 'layouts.dashboard.navbar.admin')

        @includeWhen(Auth::user()->IsAdmon(), 'layouts.dashboard.navbar.admon')

        @includeWhen(Auth::user()->IsControl(), 'layouts.dashboard.navbar.control')

    </ul>
    <small class="font-weight-bold text-light align-top float-right">PE: {{ Session::get('pescolar_name') }}</small>
    
</div>

<button id="btnSidebarCollapse" class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>