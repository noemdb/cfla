<a class="navbar-brand col-sm-3 col-md-2 m-0 p-0" href="{{ route('home') }}">
    <img class="{{-- mb-4 --}}" src="{{ asset('images/brand/48/1.png') }}" alt="" width="48"
        height="48">
    {{ config('app.name', 'Laravel') }}<br>
</a>
<button class="navbar-toggler text-aling-rigth" type="button" data-toggle="collapse"
    data-target="#navbarTopAdministracion" aria-controls="navbarTopAdministracion" aria-expanded="false"
    aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarTopAdministracion">
    <ul class="navbar-nav px-3">

        <li class="nav-item p-1">
            <a class="nav-link" title="{{ Auth::user()->area ?? 'fallo' }}" href="{{ route('profesors.home') }}">
                <i class="{{ $icon_menus[Auth::user()->area] ?? '' }} fa-2x text-light"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*home*') ? 'light' : 'success' }}" title="Inicio"
                href="{{ route('profesors.home') }}">
                <i class="{{ $icon_menus['inicio'] }} fa-1x text-{{ Request::is('*home*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*activities*') ? 'light' : 'success' }}" title="Plan de Actividades"
                href="{{ route('profesors.activities.index') }}">
                <i class="{{ $icon_menus['activities'] }} fa-1x text-{{ Request::is('*activities*') ? 'success' : 'light' }}"></i>
            </a>
        </li>


        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*diagnostics*') ? 'light' : 'success' }}" title="Pregunstas para Diagnósticos"
                href="{{ route('profesors.diagnostics.index') }}">
                <i class="{{ $icon_menus['diagnostics'] ?? null }} fa-1x text-{{ Request::is('*diagnostics*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*pevaluacions*') ? 'light' : 'success' }}" title="Planes de Evaluación"
                href="{{ route('profesors.pevaluacions.crud') }}">
                <i class="{{ $icon_menus['pevaluacion'] }} fa-1x text-{{ Request::is('*pevaluacions*') ? 'success' : 'light' }}"></i>
            </a>
        </li>        

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*evaluacions*') && !Request::is('*pevaluacions*') ? 'light' : 'success' }}"
                title="Evalauaciones/Indicadores/Logros" href="{{ route('profesors.evaluacions.index') }}">
                <i
                    class="{{ $icon_menus['evaluacion'] }} fa-1x text-{{ Request::is('*evaluacions*') && !Request::is('*pevaluacions*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*boletins*') ? 'light' : 'success' }}" title="Registro de Notas"
                href="{{ route('profesors.boletins.index') }}">
                <i
                    class="{{ $icon_menus['notas'] ?? '' }} fa-1x text-{{ Request::is('*boletins*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*competitions*') ? 'light' : 'success' }}" title="Competiciones Académicas"
                href="{{ route('profesors.competitions.index') }}">
                <i
                    class="{{ $icon_menus['competitions'] ?? '' }} fa-1x text-{{ Request::is('*competitions*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        {{-- 
        @if (Auth::user()->profesor->profesor_gestables->count())
            <li class="nav-item nav-link pl-1">
                <a class="btn btn-{{ Request::is('*profesor_gestables*') ? 'light' : 'success' }}"
                    title="Planes de Evaluación a Grupos Estables"
                    href="{{ route('profesors.profesor_gestables.index') }}">
                    <i
                        class="{{ $icon_menus['grupo_estables'] }} fa-1x text-{{ Request::is('*profesor_gestables*') ? 'success' : 'light' }}"></i>
                </a>
            </li>
        @endif
        --}}
        
        @if (Auth::user()->profesor->IsProfesorGuia)
            <li class="nav-item nav-link pl-1">
                <a class="btn btn-{{ Request::is('*profesor_guias*') ? 'light' : 'success' }}" title="Profesor Guía"
                    href="{{ route('profesors.profesor_guias.index') }}">
                    <i class="{{ $icon_menus['profesor_guia'] }} fa-1x text-{{ Request::is('*profesor_guias*') ? 'success' : 'light' }}"></i>
                </a>
            </li>

            <li class="nav-item nav-link pl-1">
                <a class="btn btn-{{ Request::is('*estudiants*') ? 'light' : 'success' }}" title="Estudiantes"
                    href="{{ route('profesors.estudiants.index') }}">
                    <i
                        class="{{ $icon_menus['student_records'] }} fa-1x text-{{ Request::is('*estudiants*') ? 'success' : 'light' }}"></i>
                </a>
            </li>

            <li class="nav-item nav-link pl-1">
                <a class="btn btn-{{ Request::is('*social_actions*') ? 'light' : 'success' }}" title="Actividades Comunitarias"
                    href="{{ route('profesors.social_actions.index') }}">
                    <i class="{{ $icon_menus['social_actions'] }} fa-1x text-{{ Request::is('*social_actions*') ? 'success' : 'light' }}"></i>
                    {{-- <i class="fa fa-user-times" aria-hidden="true"></i> --}}
                </a>
            </li>


        @endif

        <li class="nav-item nav-link pl-1 disabled">
            <a class="btn btn-{{ Request::is('*incidents*') ? 'light' : 'success' }} disabled" title="Incidencias"
                href="{{ route('profesors.incidents.index') }}">
                <i class="{{ $icon_menus['incidents'] }} fa-1x text-{{ Request::is('*incidents*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*edescriptivas*') ? 'light' : 'success' }}"
                title="Evaluaciones Descriptivas" href="{{ route('profesors.edescriptivas.index') }}">
                <i class="{{ $icon_menus['edescriptivas'] }} fa-1x text-{{ Request::is('*edescriptivas*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*debates*') ? 'light' : 'success' }} {{ (Auth::id() <> 11) ? 'disabled':null }}" title="Debates Académicos"
                href="{{ route('profesors.debates.index') }}">
                <i class="{{ $icon_menus['debates'] }} fa-1x text-{{ Request::is('*debates*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*forder*') ? 'light' : 'success' }} disabled" title="Registro de Actas"
                href="#">
                <i class="fa fa-folder fa-1x text-{{ Request::is('*forder*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        @if (Auth::user()->profesor->status_census_taker)
            <li class="nav-item nav-link pl-1">
                <a class="btn btn-{{ Request::is('*census*') ? 'light' : 'success' }}" title="Censo escolar" href="{{ route('profesors.census.index') }}">
                    <i class="{{ $icon_menus['census'] }} fa-1x text-{{ Request::is('*census*') ? 'success' : 'light' }}"></i>
                </a>
            </li>
        @endif

        

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*users*') ? 'light' : 'success' }}" title="Actualziar usuario"
                href="{{ route('profesors.users.index') }}">
                <i
                    class="{{ $icon_menus['user'] ?? '' }} fa-1x text-{{ Request::is('*users*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            @include('profesors.layouts.dashboard.navbar.partials.usermenu')
        </li>

    </ul>
    <small class="font-weight-bold text-light align-middle float-right">
        PE: {{ Session::get('pescolar_name') }}
        <div id="reloj"></div>
    </small>

</div>

<button id="btnSidebarCollapse" class="navbar-toggler collapsed" type="button" data-toggle="collapse"
    data-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>
