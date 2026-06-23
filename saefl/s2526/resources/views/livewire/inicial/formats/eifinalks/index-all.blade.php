<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Pedagógico Final - {{ $estudiant->name }}</title>
    <link href="{{ asset('vendor/bootstrap/5.3.0/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome/5.2.0/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('css/einicial/format.css') }}" rel="stylesheet">
</head>
<body class="container-fluid py-2 px-1">
    @php
        $grado = $estudiant->grado;
        $seccion = $estudiant->seccion;
        $profesor_guia = $estudiant->profesor_guia;
    @endphp

    @include('livewire.inicial.formats.eifinalks.membrete')

    {{-- Header --}}
    <div class="header text-center text-uppercase mb-2 fw-bold">
        BOLETÍN INFORMATIVO DE EDUCACIÓN INICIAL, {{$lapso->name}} MOMENTO
    </div>

    {{-- Información Básica --}}
    <div class="section text-uppercase">
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Estudiante:</span>
                <span class="info-value">{{ $estudiant->fullname2 }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Cédula:</span>
                <span class="info-value">{{ $estudiant->ci_estudiant }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Grupo:</span>
                <span class="info-value">{{ $grado->name }} {{ $seccion->name }}</span>
            </div>
        </div>
        <div class="info-item mb-2">
            <span class="info-label">Docente:</span>
            <span class="info-value">{{ $profesor_guia->fullname ?? 'N/A' }}</span>
        </div>
    </div>

    {{-- Informes Finales --}}
    @include('livewire.inicial.formats.eifinalks.oficial')
    <div style="page-break-after:always;"></div>
    <div class="text-uppercase mb-2 fw-bold">Componente de Formación</div>
    @include('livewire.inicial.formats.eifinalks.component')


    {{-- Firmas --}}
    <div class="signature-section text-uppercase">
        <div class="row">
            <div class="col-6 text-center">
                <div class="signature-line"></div>
                <div>Firma del Docente</div>
                <div class="mt-4">Fecha: {{ $fecha }}</div>
            </div>
            <div class="col-6 text-center">
                <div class="signature-line"></div>
                <div>Firma del Director</div>
                <div class="mt-4">Fecha: _________________</div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer text-uppercase">
        <p class="mb-1">{{ $institucion->name ?? 'Institución Educativa' }}</p>
        <p class="mb-1">Dirección: {{ $institucion->address ?? 'N/A' }}</p>
        <p class="mt-2 text-muted">
            <small>Documento generado el {{ $fecha }} por {{ Auth::user()->profile->full_name ?? 'Sistema' }}</small>
        </p>
    </div>

</body>
</html>
