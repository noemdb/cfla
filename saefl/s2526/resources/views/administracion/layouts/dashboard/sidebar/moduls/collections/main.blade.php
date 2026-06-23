{{-- @if (Request::is('*coll_politicals*')) --}}

    @include('administracion.layouts.dashboard.sidebar.partials.collections.coll_politicals')

    @include('administracion.layouts.dashboard.sidebar.partials.collections.coll_nivels')

    {{-- @include('administracion.layouts.dashboard.sidebar.partials.collections.coll_activities') --}}

    @include('administracion.layouts.dashboard.sidebar.partials.collections.coll_promises')

    @include('administracion.layouts.dashboard.sidebar.partials.collections.coll_messeges')

{{-- @endif --}}

{{-- @includeWhen(Request::is('*coll_nivels*'), 'administracion.layouts.dashboard.sidebar.partials.collections.coll_nivels') --}}
{{-- @includeWhen(Request::is('*coll_activities*'), 'administracion.layouts.dashboard.sidebar.partials.collections.coll_activities') --}}
{{-- @includeWhen(Request::is('*coll_promises*'), 'administracion.layouts.dashboard.sidebar.partials.collections.coll_promises') --}}
{{-- @includeWhen(Request::is('*coll_messeges*'), 'administracion.layouts.dashboard.sidebar.partials.collections.coll_messeges') --}}
