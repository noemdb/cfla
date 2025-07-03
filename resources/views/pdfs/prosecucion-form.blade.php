<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Planilla de Prosecución</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            margin: 15px;
            padding: 0;
            color: #333;
        }

        .header-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
            padding: 5px;
        }

        .logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .logo-large {
            width: 70px;
            height: 50px;
            object-fit: contain;
        }

        .institution-info {
            text-align: center;
            line-height: 1.2;
        }

        .institution-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .institution-dept {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .period {
            font-size: 10px;
            font-weight: bold;
            color: #666;
        }

        .document-title {
            text-align: center;
            margin: 15px 0;
            padding: 8px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .document-title h1 {
            font-size: 12px;
            font-weight: bold;
            margin: 0 0 3px 0;
            text-transform: uppercase;
        }

        .document-title p {
            font-size: 10px;
            margin: 0;
            color: #666;
        }

        .section {
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            background-color: #e9ecef;
            padding: 4px 8px;
            border-left: 3px solid #28a745;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 120px;
            padding: 2px 8px 2px 0;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            padding: 2px 0;
            vertical-align: top;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9px;
        }

        .students-table th {
            background-color: #dfdfdf;
            color: rgb(124, 124, 124);
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        .students-table td {
            padding: 4px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .students-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .declaration {
            text-align: justify;
            font-size: 9px;
            line-height: 1.4;
            margin-bottom: 12px;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 3px;
        }

        .bottom-section {
            display: table;
            width: 100%;
            margin-top: 15px;
        }

        .qr-section {
            display: table-cell;
            width: 140px;
            text-align: center;
            vertical-align: top;
            padding-right: 15px;
        }

        .qr-section img {
            width: 80px;
            height: 80px;
        }

        .qr-title {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .qr-description {
            font-size: 7px;
            color: #666;
            margin-top: 5px;
        }

        .signature-section {
            display: table-cell;
            vertical-align: top;
        }

        .signature-box {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            background-color: #f8f9fa;
            border-radius: 3px;
            height: 60px;
            position: relative;
        }

        .signature-line {
            position: absolute;
            bottom: 15px;
            left: 10px;
            right: 10px;
            border-top: 1px solid #333;
            padding-top: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .signature-name {
            font-size: 7px;
            color: #666;
            margin-top: 2px;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 7px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }

        .footer p {
            margin: 2px 0;
        }

        .highlight {
            background-color: #fff3cd;
            padding: 1px 3px;
            border-radius: 2px;
        }

        @media print {
            body { margin: 10px; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- Encabezado institucional -->
    <table class="header-table">
        <tr>
            <td width="15%">
                <img class="logo" src="{{ public_path('image/avatar/uecfla.jpg') }}" alt="Logo Institución">
            </td>
            <td width="70%">
                <div class="institution-info">
                    <div class="institution-name">{{ $institution->name ?? 'INSTITUCIÓN EDUCATIVA' }}</div>
                    <div class="institution-dept">DIRECCIÓN ACADÉMICA</div>
                </div>
            </td>
            <td width="15%" style="text-align: right;">
                <img class="logo-large" src="{{ public_path('image/avatar/amigoniano.png') }}" alt="Logo Secundario">
            </td>
        </tr>
    </table>

    <!-- Título del documento -->
    <div class="document-title">
        <h1>Planilla de Confirmación de Prosecución</h1>
        <p>Período Escolar 2025-2026</p>
    </div>

    <!-- Datos del representante -->
    <div class="section">
        <div class="section-title">Datos del Representante</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nombre:</div>
                <div class="info-value highlight"><span class="">{{ $representant->name }}</span></div>
            </div>
            <div class="info-row">
                <div class="info-label">Cédula:</div>
                <div class="info-value">{{ $representant->ci_representant }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Teléfono:</div>
                <div class="info-value">{{ $representant->phone ?? 'No registrado' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha:</div>
                <div class="info-value">{{ $fecha_proceso }}</div>
            </div>
        </div>
    </div>

    <!-- Estudiantes confirmados -->
    <div class="section">
        <div class="section-title">Estudiantes Confirmados para Prosecución</div>
        <table class="students-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="35%">Nombre Completo</th>
                    <th width="15%">Cédula</th>
                    <th width="25%">Grado/Sección</th>
                    <th width="10%">Edad</th>
                    <th width="10%">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estudiants as $index => $estudiant)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $estudiant->fullname }}</strong></td>
                    <td>{{ $estudiant->ci_estudiant }}</td>
                    <td>{{ $estudiant->full_inscripcion ?? 'No asignado' }}</td>
                    <td>{{ $estudiant->age }} años</td>
                    <td style="color: #28a745; font-weight: bold;">CONFIRMADO</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Declaración -->
    <div class="section">
        <div class="section-title">Declaración de Compromiso</div>
        <div class="declaration">
            <p><strong>Yo, {{ $representant->fullname }}, portador(a) de la cédula {{ $representant->ci_representant }}</strong>,
            en mi condición de representante legal de {{ count($estudiants) == 1 ? 'el estudiante mencionado' : 'los estudiantes mencionados' }},
            <strong>CONFIRMO</strong> mediante la presente planilla mi decisión de que
            {{ count($estudiants) == 1 ? 'el mismo continúe' : 'los mismos continúen' }} sus estudios en esta institución
            educativa durante el período escolar 2025-2026.</p>

            <p>Esta confirmación se realiza de manera <strong>voluntaria y consciente</strong>, comprometiéndome a cumplir
            con el acuerdo de convivencia escolar comunitario y todas las normativas institucionales.</p>
        </div>
    </div>

    <!-- Sección inferior: QR y Firma -->
    <div class="bottom-section">
        <div class="qr-section">
            <div class="qr-title">CÓDIGO DE VERIFICACIÓN</div>
            <img src="{{ $qrCode }}" alt="Código QR">
            <div class="qr-description">Escanee para verificar autenticidad</div>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div style="margin-top: 10px; font-size: 8px; color: #666;">
                    &nbsp;
                </div>
                <div class="signature-line">
                    FIRMA DEL REPRESENTANTE
                    <div class="signature-name">{{ $representant->name }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie de página -->
    <div class="footer">
        <p><strong>SAEFL</strong></p>
        <p>Documento generado automáticamente el {{ $fecha_proceso }} | Total de estudiantes: {{ count($estudiants) }}</p>
        {{-- <p>Este documento es válido únicamente con el código QR de verificación</p> --}}
    </div>
</body>
</html>
