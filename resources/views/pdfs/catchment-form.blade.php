<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formato de Registro - {{ $lastname }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .header-container {
            width: 100%;
            border-bottom: 3px solid #1e6b63;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .institution-name {
            color: #1e6b63;
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .academic-direction {
            color: #d32f2f;
            font-size: 13px;
            font-weight: bold;
            margin-top: 5px;
            letter-spacing: 1px;
        }

        .censo-info {
            font-size: 12px;
            font-weight: normal;
            margin-top: 5px;
            color: #555;
        }

        .logo {
            width: 70px;
            height: auto;
        }

        .logo-large {
            width: 100px;
            height: auto;
        }

        .main-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #1e6b63;
            margin: 20px 0;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .section {
            margin-bottom: 15px;
        }

        .data-row {
            margin-bottom: 6px;
            border-bottom: 1px solid #eee;
            padding-bottom: 2px;
        }

        .data-label {
            font-weight: bold;
            color: #1e6b63;
            width: 180px;
            display: inline-block;
        }

        .data-value {
            color: #000;
        }

        .invitation-box {
            background-color: #f1f9f2;
            border-left: 5px solid #1e6b63;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }

        .invitation-text {
            font-style: italic;
            font-size: 12px;
            color: #1e6b63;
            margin-bottom: 10px;
        }

        .important-note {
            font-weight: bold;
            color: #d32f2f;
            margin-top: 10px;
        }

        .table-content {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border: 1px solid #1e6b63;
        }

        .table-content th {
            background-color: #1e6b63;
            color: white;
            padding: 6px;
            text-align: center;
            font-size: 11px;
            text-transform: uppercase;
        }

        .table-content td {
            border: 1px solid #ddd;
            padding: 6px;
            vertical-align: top;
        }

        .section-header {
            background-color: #1e6b63;
            color: white;
            padding: 5px 10px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-radius: 2px;
        }

        .footer {
            margin-top: 30px;
            border-top: 1px solid #1e6b63;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        .signature-section {
            margin-top: 40px;
            width: 100%;
        }

        .signature-box {
            width: 45%;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }

        .qr-section {
            text-align: center;
            margin-top: 30px;
            padding: 15px;
            border: 1px dashed #1e6b63;
            background-color: #fff;
        }

        ul {
            padding-left: 20px;
            margin-top: 5px;
        }

        li {
            margin-bottom: 3px;
        }
    </style>
</head>

<body>

    <!-- Encabezado -->
    <div class="header-container">
        <table class="header-table">
            <tr>
                <td width="15%" style="text-align: left;">
                    <img class="logo" src="{{ public_path('image/avatar/uecfla.jpg') }}">
                </td>
                <td width="70%" style="text-align: center;">
                    <div class="institution-name">{{ $institution->name }}</div>
                    <div class="institution-name">DIRECCIÓN ACADÉMICA</div>
                    <div class="censo-info">Censo Escolar 2026 - 2027</div>
                </td>
                <td width="15%" style="text-align: right;">
                    <img class="logo-large" src="{{ public_path('image/avatar/amigoniano.png') }}">
                </td>
            </tr>
        </table>
    </div>

    <div class="main-title">Formato de Registro</div>

    <!-- Datos del Estudiante -->
    <div class="section">
        <div class="data-row">
            <span class="data-label">Estudiante:</span>
            <span class="data-value text-uppercase"><strong>{{ $lastname }} {{ $firstname }}</strong></span>
        </div>
        <div class="data-row">
            <span class="data-label">Fecha de Nacimiento:</span>
            <span class="data-value">{{ $date_birth }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Grado/Año Seleccionado:</span>
            <span class="data-value"><strong>{{ $grado->name ?? 'No especificado' }}</strong></span>
        </div>
    </div>

    <!-- Datos del Representante -->
    <div class="section">
        <div class="data-row">
            <span class="data-label">Representante:</span>
            <span class="data-value text-uppercase">{{ $representant_name }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Cédula de Identidad:</span>
            <span class="data-value">{{ $representant_ci }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Teléfono:</span>
            <span class="data-value">{{ $representant_phone }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">WhatsApp:</span>
            <span class="data-value">{{ $representant_cellphone }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">Cita Programada:</span>
            <span class="data-value" style="color: #d32f2f; font-weight: bold;">{{ $day_appointment }}</span>
        </div>
    </div>

    <!-- Mensaje de Bienvenida -->
    <div class="invitation-box">
        <div class="invitation-text">
            Te invitamos cordialmente a visitar nuestro colegio en el día seleccionado de <strong>8:00 AM a 2:00
                PM</strong>.
            Esperamos contar con tu presencia para conocernos mejor y acompañarte en este importante proceso académico.
        </div>
        <div class="important-note">
            * Por favor asista a la cita acompañado del estudiante.
        </div>
    </div>

    <!-- Código de Vestimenta -->
    <div class="section-header">Resumen del Código de Vestimenta</div>

    <table class="table-content">
        <thead>
            <tr>
                <th colspan="2">Educación Primaria (1° a 6° Grado)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="30%"><strong>Uniforme Diario</strong></td>
                <td>Pantalón azul marino, chemise blanca con insignia, zapatos negros/marrón, medias blancas, abrigo
                    azul marino.</td>
            </tr>
            <tr>
                <td><strong>Educación Física</strong></td>
                <td>Mono azul marino, franela blanca con insignia, zapatos deportivos negros o marrón.</td>
            </tr>
        </tbody>
    </table>

    <table class="table-content" style="margin-top: 15px;">
        <thead>
            <tr>
                <th colspan="2">Educación Media General</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="30%"><strong>1° a 3° Año</strong></td>
                <td>Chemise <strong>azul celeste</strong> con insignia. Resto igual a primaria.</td>
            </tr>
            <tr>
                <td><strong>4° y 5° Año</strong></td>
                <td>Chemise <strong>beige</strong> con insignia, zapatos exclusivamente negros.</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 10px; font-size: 10px; color: #444;">
        <strong>Normas Generales:</strong>
        <ul>
            <li>Uniforme completo y limpio.</li>
            <li>Cabello en tono natural, sin accesorios extravagantes.</li>
            <li>Varones: Cabello corto convencional.</li>
        </ul>
    </div>

    <!-- Firmas -->
    <div class="signature-section">
        <div class="signature-box" style="margin-right: 8%;">
            <div style="border-bottom: 1px solid #000; margin-bottom: 5px;"></div>
            <div style="font-weight: bold;">{{ $autoridad1->profile_professional }} {{ $autoridad1->fullname }}</div>
            <div style="font-size: 9px; color: #666;">{{ $autoridad1->position }}</div>
        </div>
        <div class="signature-box">
            <div style="border-bottom: 1px solid #000; margin-bottom: 5px;"></div>
            <div style="font-weight: bold;">{{ $autoridad2->profile_professional }} {{ $autoridad2->fullname }}</div>
            <div style="font-size: 9px; color: #666;">{{ $autoridad2->position }}</div>
        </div>
    </div>

    <!-- QR Code -->
    <div class="qr-section">
        <div style="margin-bottom: 10px; font-weight: bold; color: #1e6b63;">Conserva este código para descargar tu
            comprobante digital</div>
        <img src="{{ $qrCode }}" width="100">
    </div>

    <footer class="footer">
        AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE, YARACUY, VENEZUELA<br>
        Teléfonos: + 58 424-5891682 || + 58 414-5442298 | Correo: colegiofrayluisa@gmail.com
    </footer>

</body>

</html>
