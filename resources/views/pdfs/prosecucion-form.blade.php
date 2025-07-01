<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Planilla de Prosecución</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 8px;
            border-left: 4px solid #333;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }
        .value {
            flex: 1;
        }
        .student-list {
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .student-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .student-item:last-child {
            border-bottom: none;
        }
        .student-name {
            font-weight: bold;
            font-size: 13px;
        }
        .student-details {
            color: #666;
            font-size: 11px;
            margin-top: 3px;
        }
        .qr-section {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ config('app.name') }}</div>
        <div class="subtitle">PLANILLA DE CONFIRMACIÓN DE PROSECUCIÓN</div>
        <div class="subtitle">PERÍODO ESCOLAR 2024-2025</div>
    </div>

    <div class="section">
        <div class="section-title">DATOS DEL REPRESENTANTE</div>
        <div class="info-row">
            <span class="label">Nombre Completo:</span>
            <span class="value">{{ $representant->fullname }}</span>
        </div>
        <div class="info-row">
            <span class="label">Cédula de Identidad:</span>
            <span class="value">{{ $representant->ci_representant }}</span>
        </div>
        <div class="info-row">
            <span class="label">Teléfono:</span>
            <span class="value">{{ $representant->phone ?? 'No registrado' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Fecha de Proceso:</span>
            <span class="value">{{ $fecha_proceso }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">ESTUDIANTES CONFIRMADOS PARA PROSECUCIÓN</div>
        <div class="student-list">
            @foreach($estudiants as $estudiant)
                <div class="student-item">
                    <div class="student-name">{{ $estudiant->fullname }}</div>
                    <div class="student-details">
                        CI: {{ $estudiant->ci_estudiant }} |
                        Grado: {{ $estudiant->full_inscripcion ?? 'No asignado' }} |
                        Edad: {{ $estudiant->age }} años
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="section">
        <div class="section-title">DECLARACIÓN</div>
        <p style="text-align: justify; margin-bottom: 15px;">
            Yo, <strong>{{ $representant->fullname }}</strong>, portador(a) de la cédula de identidad
            <strong>{{ $representant->ci_representant }}</strong>, en mi condición de representante legal
            de los estudiantes mencionados anteriormente, confirmo mediante la presente planilla mi decisión
            de que los mismos continúen sus estudios en esta institución educativa durante el período escolar 2024-2025.
        </p>
        <p style="text-align: justify;">
            Esta confirmación se realiza de manera voluntaria y consciente, comprometiéndome a cumplir con
            todas las normativas y reglamentos establecidos por la institución.
        </p>
    </div>

    <div class="qr-section">
        <div style="margin-bottom: 10px;">
            <strong>Código de Verificación</strong>
        </div>
        <img src="{{ $qrCode }}" alt="Código QR" style="width: 120px; height: 120px;">
        <div style="margin-top: 10px; font-size: 10px;">
            Escanee este código para verificar la autenticidad del documento
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                Firma del Representante
            </div>
            <div style="margin-top: 5px; font-size: 10px;">
                {{ $representant->fullname }}
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                Sello de la Institución
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Documento generado automáticamente el {{ $fecha_proceso }}</p>
        <p>{{ config('app.name') }} - Sistema de Gestión Académica</p>
    </div>
</body>
</html>
