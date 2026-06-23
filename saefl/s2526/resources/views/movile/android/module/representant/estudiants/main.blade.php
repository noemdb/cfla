<div class="p-2 overflow-hidden">

    {{-- SECTION: REGULAR STUDENTS (BLUE) --}}
    @if (isset($estudiants) && $estudiants->isNotEmpty())

        <div class="border rounded-4 p-2">

            <div class="h3 font-weight-bold bg-white px-4 fw-bold">
                Estudiantes Regulares
            </div>
            <small class="text-primary opacity-75 d-block" style="font-size: 0.7rem; font-weight: 500;">Inscritos
                periódo actual</small>

            <div class="accordion mb-5" id="accordionEstudiantRegulares">
                @foreach ($estudiants as $estudiant)
                    <div class="accordion-item shadow-sm mb-3 rounded-4 border-0 overflow-hidden animate__animated animate__fadeInUp"
                        id="accordion-item-{{ $estudiant->id }}" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                        <h2 class="accordion-header" id="heading-{{ $estudiant->id }}">
                            <button class="accordion-button collapsed bg-white py-3 px-3 transition-all" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseEstudiant-{{ $estudiant->id }}"
                                aria-expanded="false" aria-controls="collapseEstudiant-{{ $estudiant->id }}">
                                <div class="d-flex align-items-center w-100 me-2">
                                    {{-- <div class="rounded-circle bg-primary-gradient text-white d-flex justify-content-center align-items-center me-3 flex-shrink-0 shadow-sm"
                                        style="width: 42px; height: 42px; background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                                        <span class="fw-bold"
                                            style="font-size: 1.1rem;">{{ Str::upper(substr($estudiant->lastname, 0, 1)) }}</span>
                                    </div> --}}
                                    <div class="d-flex flex-column align-items-start overflow-hidden">
                                        <span class="fw-bold text-truncate w-100 mb-1"
                                            style="font-size: 0.95rem; color: #2c3e50;">{{ $estudiant->shortname }}</span>
                                        <div class="d-flex align-items-center flex-wrap gap-1">
                                            <span
                                                class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25"
                                                style="font-size: 0.65rem; font-weight: 700;">ACTIVO</span>
                                            @if ($estudiant->status_prosecution)
                                                <span
                                                    class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25"
                                                    title="Continuidad Confirmada para el próximo año escolar"
                                                    style="font-size: 0.65rem; font-weight: 700;">
                                                    <i class="fas fa-check-double me-1"></i> CONTINUIDAD
                                                </span>
                                            @endif
                                            <small class="text-muted fw-500 ms-1" style="font-size: 0.7rem;">
                                                <i class="fas fa-graduation-cap me-1 opacity-75"></i>
                                                {{ $estudiant->full_inscripcion ?: 'Asignando grado...' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapseEstudiant-{{ $estudiant->id }}" class="accordion-collapse collapse"
                            aria-labelledby="heading-{{ $estudiant->id }}"
                            data-bs-parent="#accordionEstudiantRegulares">
                            <div class="accordion-body p-0 bg-light border-top">
                                @include('movile.android.module.representant.estudiants.complete')
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

    @endif

    {{-- SECTION: ADMISSION / CENSUS (INFO/CYAN) --}}
    @if (isset($catchments) && $catchments->isNotEmpty())
        <div class="py-4 mt-4">
            <div class="border-top w-100 position-relative" style="height: 1px;">
                <span class="position-absolute top-50 start-50 translate-middle bg-white px-4 text-muted fw-bold"
                    style="font-size: 1rem; letter-spacing: 4px; text-transform: uppercase;">
                    Admisión Institucional
                </span>
            </div>
        </div>

        <div
            class="bg-info bg-opacity-10 p-3 rounded-4 border border-info border-opacity-25 mb-4 shadow-sm animate__animated animate__fadeIn">
            <div class="d-flex align-items-center mb-4 p-2">
                <div class="bg-info text-white rounded-3 d-flex justify-content-center align-items-center me-3 shadow-sm"
                    style="width: 45px; height: 45px;">
                    <i class="fas fa-user-plus fa-lg"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-info">Nuevos Ingresos</h5>
                    <small class="text-info opacity-75 d-block" style="font-size: 0.75rem;">{{ $catchments->count() }}
                        solicitud(es) activas</small>
                </div>
            </div>

            <div class="accordion" id="accordionEstudiantCensados">
                @foreach ($catchments as $catchment)
                    @php
                        $interview = $catchment->catchmentInterviews->last();
                        $status = 'En evaluación';
                        $themeColor = 'warning';
                        $iconStatus = 'clock';
                        if ($interview) {
                            if ($interview->accepted) {
                                $status = 'Aceptado';
                                $themeColor = 'success';
                                $iconStatus = 'check-circle';
                            } elseif ($interview->status_standby) {
                                $status = 'Lista de espera';
                                $themeColor = 'secondary';
                                $iconStatus = 'hourglass-half';
                            }
                        }
                        $badgeClass = "bg-{$themeColor}";
                        $borderClass = "border-{$themeColor}";
                    @endphp
                    <div class="accordion-item shadow-sm mb-3 rounded-4 border-0 overflow-hidden"
                        id="accordion-item-catchment-{{ $catchment->id }}">
                        <h2 class="accordion-header" id="headingCatchment-{{ $catchment->id }}">
                            <button class="accordion-button collapsed bg-white py-3 px-3 transition-all" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseCatchment-{{ $catchment->id }}"
                                aria-expanded="false" aria-controls="collapseCatchment-{{ $catchment->id }}">
                                <div class="d-flex align-items-center w-100 me-2">
                                    <div class="rounded-circle {{ $badgeClass }} text-white d-flex justify-content-center align-items-center me-3 flex-shrink-0 shadow-sm"
                                        style="width: 42px; height: 42px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="d-flex flex-column align-items-start overflow-hidden">
                                        <span class="fw-bold text-truncate w-100 mb-1"
                                            style="font-size: 0.95rem; color: #2c3e50;">{{ $catchment->firstname }}
                                            {{ $catchment->lastname }}</span>
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="badge rounded-pill {{ $badgeClass }} bg-opacity-10 text-{{ $themeColor }} me-2 border {{ $borderClass }} border-opacity-25"
                                                style="font-size: 0.65rem; font-weight: 700;">
                                                <i class="fas fa-{{ $iconStatus }} me-1"></i>
                                                {{ Str::upper($status) }}
                                            </span>
                                            <small class="text-muted" style="font-size: 0.65rem;">ID:
                                                #{{ $catchment->id }}</small>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapseCatchment-{{ $catchment->id }}" class="accordion-collapse collapse"
                            aria-labelledby="headingCatchment-{{ $catchment->id }}"
                            data-bs-parent="#accordionEstudiantCensados">
                            <div class="accordion-body p-3 bg-white border-top">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div
                                            class="bg-light p-2 rounded-3 border-start border-3 border-secondary h-100 d-flex flex-column justify-content-center">
                                            <small class="text-muted mb-1"
                                                style="font-size: 0.6rem; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">
                                                <i class="fas fa-id-card text-secondary me-1"></i> Cédula
                                            </small>
                                            <strong class="text-truncate"
                                                style="font-size: 0.85rem;">{{ $catchment->ci_estudiant ?: 'Provisional' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div
                                            class="bg-light p-2 rounded-3 border-start border-3 border-info h-100 d-flex flex-column justify-content-center">
                                            <small class="text-muted mb-1"
                                                style="font-size: 0.6rem; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">
                                                <i class="fas fa-university text-info me-1"></i> Trámite Para
                                            </small>
                                            <strong class="text-truncate"
                                                style="font-size: 0.85rem;">{{ $catchment->grado ? $catchment->grado->name : 'N/A' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="bg-white border rounded-4 p-3 shadow-none">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted fw-bold"
                                                    style="font-size: 0.65rem; text-transform: uppercase;">Estado de la
                                                    Gestión</small>
                                                <small class="text-muted"
                                                    style="font-size: 0.65rem;">{{ Carbon\Carbon::parse($catchment->created_at)->diffForHumans() }}</small>
                                            </div>
                                            <div class="progress mb-3 shadow-none"
                                                style="height: 8px; border-radius: 10px; background-color: #f0f2f5;">
                                                @php
                                                    $percent =
                                                        $status == 'Aceptado'
                                                            ? 100
                                                            : ($status == 'Lista de espera'
                                                                ? 65
                                                                : 35);
                                                @endphp
                                                <div class="progress-bar progress-bar-striped progress-bar-animated {{ $badgeClass }}"
                                                    role="progressbar"
                                                    style="width: {{ $percent }}%; border-radius: 10px;"></div>
                                            </div>
                                            <div
                                                class="p-2 {{ $badgeClass }} bg-opacity-10 rounded-3 text-center border {{ $borderClass }} border-opacity-25">
                                                <span class="fw-bold text-{{ $themeColor }}"
                                                    style="font-size: 0.8rem;">{{ $status }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @include('movile.android.module.representant.estudiants.partials.prosecution')

    {{-- EMPTY STATE --}}
    @if ($estudiants->isEmpty() && $catchments->isEmpty())
        <div class="text-center py-5 px-3">
            <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center mb-4 shadow-sm"
                style="width: 100px; height: 100px;">
                <i class="fas fa-users-slash text-muted fa-3x"></i>
            </div>
            <h5 class="fw-bold text-muted">Sin estudiantes asociados</h5>
            <p class="text-muted small">No se encontraron estudiantes regulares ni solicitudes de ingreso vinculadas a
                su perfil.</p>
        </div>
    @endif

</div>

<style>
    .rounded-4 {
        border-radius: 1rem !important;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .btn:focus,
    .accordion-button:focus {
        box-shadow: none !important;
    }

    .accordion-button:not(.collapsed) {
        background-color: #fff !important;
        color: inherit !important;
    }

    .accordion-button::after {
        background-size: 0.8rem;
    }

    .bg-primary-gradient {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    }

    .fw-500 {
        font-weight: 500;
    }
</style>
