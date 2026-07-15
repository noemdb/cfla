<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Planilla de Prosecución</title>
    <style>
        @page {
            margin: 15px 20px;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9.5px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            color: #1a1a2e;
        }

        /* ===== HEADER ===== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 3px;
        }
        .logo {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }
        .logo-large {
            width: 52px;
            height: 45px;
            object-fit: contain;
        }
        .institution-info {
            text-align: center;
            line-height: 1.2;
        }
        .institution-name {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            color: #1a3a5c;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .institution-sub {
            font-size: 10px;
            font-weight: 600;
            color: #2c5282;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* ===== TITLE BAR ===== */
        .title-bar {
            text-align: center;
            margin: 8px 0 12px 0;
            padding: 7px 10px;
            background: #1a3a5c;
        }
        .title-bar h1 {
            font-size: 13px;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            color: #ffffff;
            letter-spacing: 0.8px;
        }
        .title-bar p {
            font-size: 9px;
            margin: 2px 0 0 0;
            color: #cbd5e0;
            letter-spacing: 0.4px;
        }

        /* ===== SECTION ===== */
        .section {
            margin-bottom: 10px;
        }
        .section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 4px 8px;
            border-left: 2.5px solid #2c5282;
            margin-bottom: 6px;
            color: #1a3a5c;
            background: #f0f4f8;
        }

        /* ===== INFO BOX ===== */
        .info-box {
            border: 1px solid #e2e8f0;
            padding: 7px 10px;
            background-color: #f8fafc;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .info-grid td {
            padding: 2px 5px;
            vertical-align: top;
        }
        .info-label {
            font-weight: 600;
            color: #4a5568;
            width: 100px;
            font-size: 9px;
        }
        .info-value {
            font-weight: 600;
            color: #1a1a2e;
            font-size: 9px;
        }
        .info-value.highlight {
            background-color: #ebf4ff;
            padding: 0 3px;
        }

        /* ===== STUDENTS TABLE ===== */
        .students-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
        }
        .students-table thead th {
            background-color: #1a3a5c;
            color: #ffffff;
            padding: 5px 5px;
            text-align: left;
            font-weight: 600;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #1a3a5c;
        }
        .students-table tbody td {
            padding: 4px 5px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .students-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .status-badge {
            background-color: #c6f6d5;
            color: #22543d;
            padding: 1px 5px;
            font-weight: 700;
            font-size: 7.5px;
            letter-spacing: 0.5px;
        }

        /* ===== DECLARATION ===== */
        .declaration-box {
            border: 1px solid #e2e8f0;
            padding: 7px 10px;
            background-color: #f8fafc;
        }
        .declaration-box p {
            text-align: justify;
            font-size: 8.5px;
            line-height: 1.45;
            margin: 3px 0;
            color: #2d3748;
        }

        /* ===== BOTTOM ===== */
        .bottom-section {
            width: 100%;
            margin-top: 12px;
            border-collapse: collapse;
        }
        .bottom-section td {
            vertical-align: top;
        }
        .qr-box {
            text-align: center;
            border: 1px solid #e2e8f0;
            padding: 8px;
            background-color: #ffffff;
            width: 120px;
        }
        .qr-box img {
            width: 65px;
            height: 65px;
        }
        .qr-title {
            font-size: 7.5px;
            font-weight: 700;
            color: #2d3748;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 4px;
        }
        .signature-box {
            border: 1px solid #e2e8f0;
            padding: 10px 12px;
            background-color: #f8fafc;
            text-align: center;
            min-height: 62px;
        }
        .signature-line {
            border-top: 1.5px solid #4a5568;
            padding-top: 4px;
            margin-top: 26px;
            font-size: 8.5px;
            font-weight: 700;
            color: #1a3a5c;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .signature-name {
            font-size: 8px;
            color: #718096;
            margin-top: 2px;
            font-weight: 400;
            text-transform: none;
            letter-spacing: 0;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 7.5px;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
        .footer p {
            margin: 1px 0;
        }

        @media print {
            body { margin: 0; padding: 0; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- ===== ENCABEZADO ===== -->
    <table class="header-table">
        <tr>
            <td width="13%" style="text-align: center;">
                <img class="logo" src="{{ public_path('image/avatar/uecfla.jpg') }}" alt="Logo">
            </td>
            <td width="74%">
                <div class="institution-info">
                    <div class="institution-name">{{ $institution->name ?? 'INSTITUCIÓN EDUCATIVA' }}</div>
                    <div class="institution-sub">DIRECCIÓN ACADÉMICA</div>
                </div>
            </td>
            <td width="13%" style="text-align: center;">
                <img class="logo-large" src="{{ public_path('image/avatar/amigoniano.png') }}" alt="Logo">
            </td>
        </tr>
    </table>

    <!-- ===== TÍTULO ===== -->
    <div class="title-bar">
        <h1>Planilla de Confirmación de Prosecución</h1>
        <p>Período Escolar 2025-2026</p>
    </div>

    <!-- ===== DATOS DEL REPRESENTANTE ===== -->
    <div class="section">
        <div class="section-title">Datos del Representante</div>
        <div class="info-box">
            <table class="info-grid">
                <tr>
                    <td class="info-label">Nombre completo:</td>
                    <td class="info-value highlight">{{ $representant->name }}</td>
                </tr>
                <tr>
                    <td class="info-label">Cédula de identidad:</td>
                    <td class="info-value">{{ $representant->ci_representant }}</td>
                </tr>
                <tr>
                    <td class="info-label">Teléfono:</td>
                    <td class="info-value">{{ $representant->phone ?? 'No registrado' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Fecha de emisión:</td>
                    <td class="info-value">{{ $fecha_proceso }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- ===== ESTUDIANTES ===== -->
    <div class="section">
        <div class="section-title">Estudiantes Confirmados</div>
        <table class="students-table">
            <thead>
                <tr>
                    <th width="4%">#</th>
                    <th width="32%">Nombre Completo</th>
                    <th width="12%">Cédula</th>
                    <th width="23%">Grado / Sección</th>
                    <th width="7%">Edad</th>
                    <th width="10%">Estado</th>
                    <th width="12%">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estudiants as $index => $estudiant)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td><strong>{{ $estudiant->fullname }}</strong></td>
                    <td>{{ $estudiant->ci_estudiant }}</td>
                    <td>{{ $estudiant->full_inscripcion ?? 'No asignado' }}</td>
                    <td style="text-align: center;">{{ $estudiant->age }} años</td>
                    <td style="text-align: center;"><span class="status-badge">CONFIRMADO</span></td>
                    <td>{{ $estudiant->date_prosecution_formatted_full }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ===== DECLARACIÓN ===== -->
    <div class="section">
        <div class="section-title">Declaración de Compromiso</div>
        <div class="declaration-box">
            <p>
                <strong>Yo, {{ $representant->name }},</strong> portador(a) de la cédula de identidad
                <strong>{{ $representant->ci_representant }},</strong> en mi condición de representante legal de
                {{ count($estudiants) == 1 ? 'el estudiante mencionado' : 'los estudiantes mencionados' }},
                <strong>CONFIRMO</strong> mi decisión de que {{ count($estudiants) == 1 ? 'continúe' : 'continúen' }}
                sus estudios en esta institución durante el período 2025-2026, comprometiéndome a cumplir con el
                acuerdo de convivencia escolar y las normativas institucionales.
            </p>
        </div>
    </div>

    <!-- ===== QR + FIRMA ===== -->
    <table class="bottom-section">
        <tr>
            <td width="120" class="qr-box">
                <div class="qr-title">Código de Verificación</div>
                <img src="{{ $qrCode }}" alt="QR">
            </td>
            <td style="padding-left: 12px;">
                <div class="signature-box">
                    <div class="signature-line">
                        FIRMA DEL REPRESENTANTE
                        <div class="signature-name">{{ $representant->name }}</div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- ===== FOOTER ===== -->
    <div class="footer">
        <p><strong>SAEFL</strong> — Documento generado el {{ $fecha_proceso }} | Total: {{ count($estudiants) }} estudiante(s)</p>
    </div>
</body>
</html>
