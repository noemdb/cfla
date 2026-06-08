{{-- .navbar-header --}}
@include('admin.layouts.dashboard.navbar.elements.navbar-header')

<ul class="nav navbar-top-links navbar-right">

    {{-- <li> --}}

        {{-- boton para ocultar sidebar --}}
        {{-- @include('admin.layouts.dashboard.navbar.top-links.hide-sidebar') --}}

    {{-- </li> --}}
    
    @if(Auth::user()->getSetting('topnavbar_messages')=='true')

        <li class="dropdown">

            @include('admin.layouts.dashboard.navbar.elements.ajaxmessages')

        </li>

    @endif

    @if(Auth::user()->getSetting('topnavbar_tasks')=='true')

        <li class="dropdown">

            @include('admin.layouts.dashboard.navbar.elements.ajaxtasks')

        </li>

    @endif

    @if(Auth::user()->getSetting('topnavbar_alerts')=='true')

        <li class="dropdown">

            @include('admin.layouts.dashboard.navbar.elements.ajaxalerts')

        </li>

    @endif

    @if(Auth::user()->getSetting('topnavbar_logdbs')=='true')

        <li class="dropdown">

            @include('admin.layouts.dashboard.navbar.elements.ajaxlogdbs')

        </li>

    @endif

    @if(Auth::user()->getSetting('topnavbar_loginouts')=='true')

        <li class="dropdown">

             @include('admin.layouts.dashboard.navbar.elements.ajaxloginouts')

        </li>

    @endif

    <li class="dropdown">

        @include('admin.layouts.dashboard.navbar.elements.user')
        
    </li>

</ul>
<!-- /.navbar-top-links -->

