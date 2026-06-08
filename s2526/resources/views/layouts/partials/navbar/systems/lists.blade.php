@php
$user = Auth::user();
$area = ($user) ? $user->area : null ;
@endphp
<div class="dropdown">

    <a class="nav-link dropdown-toggle font-weight-bold" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Sistemas
    </a>

    <div class="dropdown-menu px-2 mx-2" aria-labelledby="triggerId">

        @if ($user->IsAdmin())
            <a class="nav-link text-wrap" href="{{route('admin.home') }}" style="white-space: nowrap">
                <i class="{{ $icon_menus['dashboard'] ?? null}} text-dark"></i> Consola de Administración
            </a>
        @endif

        @if ($user->IsControl())
            <a class="nav-link" href="{{route('app.home') }}" style="white-space: nowrap">
                <i class="{{ $icon_menus['institucion'] ?? null}} text-success"></i> Control de Estudio
            </a>
        @endif

        @if ($user->IsAdmon())
            <a class="nav-link" href="{{route('app.home') }}" style="white-space: nowrap">
                <i class="{{ $icon_menus['institucion'] ?? null}} text-success"></i> Administración
            </a>
        @endif

        @if ($user->isProfesor())
            <a class="nav-link text-wrap" href="{{route('profesors.home') }}" style="white-space: nowrap">
                <i class="{{ $icon_menus['profesor'] ?? null}} text-primary"></i> Profesor
            </a>
        @endif

        @if ($user->IsDirector())
            <a class="nav-link text-wrap" href="{{route('directors.home') }}" style="white-space: nowrap">
                <i class="{{ $icon_menus['autoridades'] ?? null}} text-info"></i> Director
            </a>
        @endif

        @if ($user->IsAcademico())
            <a class="nav-link text-wrap" href="{{route('academicos.home') }}" style="white-space: nowrap">
                <i class="{{ $icon_menus['control_estudio'] ?? null}} text-dark"></i> Direccción Académica
            </a>
        @endif

        @if ($user->IsRepresentant())
            <a class="nav-link text-wrap" href="{{route('representants.home') }}" style="white-space: nowrap">
                <i class="{{ $icon_menus['representante'] ?? null}} text-dark"></i> Representante
            </a>
        @endif

        @if ($user->IsBienestar())
            <a class="nav-link text-wrap" href="{{route('bienestars.home') }}" style="white-space: nowrap">
                <i class="{{ $icon_menus['incidents'] ?? null}} text-success"></i> Coordinación de Bienestar
            </a>
        @endif

        @if ($user->IsEvaluacion())
            <a class="nav-link text-wrap" href="{{route('evaluacions.home') }}" style="white-space: nowrap">
                <i class="{{ $icon_menus['evaluacion'] ?? null}} text-info"></i> Coordinación de Evaluación
            </a>
        @endif

        @if ($user->IsProyecto())
            <a class="nav-link text-wrap" href="{{route('proyectos.home') }}" style="white-space: nowrap">
                <i class="{{ $icon_menus['profile'] ?? null}} text-primary"></i> Coordinación de Proyecto
            </a>
        @endif

        @if ($user->IsLeader())
            <a class="nav-link text-wrap" href="{{route('leaders.home') }}" style="white-space: nowrap">
                <i class="{{ $icon_menus['profile'] ?? null}} text-primary"></i> Jefe de Área de Conocimiento
            </a>
        @endif

        @if ($user->IsInicial())
            <a class="nav-link text-wrap" href="{{route('inicials.home') }}" style="white-space: nowrap">
                <i class="{{ $icon_menus['inicials'] ?? null}} text-primary"></i> Educ. Inicial
            </a>
        @endif

        @if ($user->IsPlanning())
            <a class="nav-link text-wrap" href="{{route('plannings.home') }}" style="white-space: nowrap">
                <i class="{{ $icon_menus['plannings'] ?? null}} text-dark"></i> Planificación
            </a>
        @endif

        @if ($user->IsAudit())
            <a class="nav-link text-wrap" href="{{route('audits.home') }}" style="white-space: nowrap">
                <i class="{{ $icon_menus['audits'] ?? null}} text-dark"></i> Auditoría
            </a>
        @endif

    </div>
</div>

{{-- @switch( Auth::user()->area )
    @case('SISTEMA') @php $href = route('admin.home') ;@endphp @break
    @case('ADMINISTRACION') @php $href = route('app.home');@endphp @break
    @case('CONTROL ESTUDIO') @php $href = route('app.home');@endphp @break
    @case('PROFESORADO') @php $href = route('profesors.home');@endphp @break
    @case('AUTORIDAD') @php $href = route('directors.home');@endphp @break
    @case('ACADEMICO') @php $href = route('academicos.home');@endphp @break
    @case('REPRESENTANTE') @php $href = route('representants.home');@endphp @break
    @case('ESTUDIANTIL') @php $href = route('estudiants.home');@endphp @break
    @case('BIENESTAR') @php $href = route('bienestars.home');@endphp @break
    @case('EVALUACION') @php $href = route('evaluacions.home');@endphp @break
    @case('PROYECTO') @php $href = route('proyectos.home');@endphp @break
@endswitch --}}
