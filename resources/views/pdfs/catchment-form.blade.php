<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formato de Registro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            text-transform: uppercase !important;
        }

        .text-div {
            line-height: 1.2rem;
        }

        .header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .sub-header {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .logo {
            width: 70px;
            height: 70px;
        }

        .logo-large {
            width: 100px;
            height: 70px;
        }

        .table-header {
            width: 100%;
            text-align: center;
            margin-bottom: 10px;
        }

        .table-header td {
            vertical-align: middle;
        }

        .table-content {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table-content th,
        .table-content td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        .section {
            margin-top: 5px;
            padding: 5px;
        }

        .text-center {
            text-align: center;
        }

        .divider {
            border-top: 1px solid black;
            margin: 15px 0;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <!-- Encabezado con logos -->
    <table class="table-header">
        <tr>
            <td width="12%">
                <img class="logo" src="{{ public_path('image/avatar/uecfla.jpg') }}">
            </td>
            <td width="76%">
                <div class="header">{{ $institution->name }}</div>
                <div class="sub-header">DIRECCIÓN ACADÉMICA</div>
                <div class="sub-header">Censo Escolar 2025 2026</div>
            </td>
            <td width="12%">
                <img class="logo-large" src="{{ public_path('image/avatar/amigoniano.png') }}">
            </td>
        </tr>
    </table>

    <hr class="divider">

    <!-- Contenido -->
    <div class="header">Formato de Registro</div>

    <div class="section">
        <div class="text-div"><strong>Nombre del Estudiante:</strong> {{ $lastname }} {{ $firstname }} </div>
        <div class="text-div"><strong>Fecha de Nacimiento:</strong> {{ $date_birth }}</div>
        <div class="text-div"><strong>Representante:</strong> {{ $representant_name }}</div>
        <div class="text-div"><strong>Cédula:</strong> {{ $representant_ci }}</div>
        <div class="text-div"><strong>Teléfono:</strong> {{ $representant_phone }}</div>
        <div class="text-div"><strong>Teléf. WhatsApp:</strong> {{ $representant_cellphone }}</div>
        <div class="text-div"><strong>Fecha seleccionada para acudir a la institucción:</strong> {{ $day_appointment }}</div>
        <div class="text-div">
            <strong>Grado/Año Seleccionado:</strong>
            {{ $grado->name ?? null }}
        </div>

        <hr style="border: 1px #ccc solid">

        <div>
            Te invitamos cordialmente a visitar nuestro colegio en el día de tu conveniencia, entre el 7 y el 11 de julio a las 2pm. Durante este período, se estarán llevando a cabo las actividades propias del Censo Escolar 2025 - 2026 . ¡Esperamos contar con tu presencia para conocernos mejor y acompañarte en este importante proceso!
        </div>

        <div>
            Por favor asista a la cita acompañado del niño/a
        </div>

    </div>

    <div class="text-center">
        <p>Atte.</p>
        <p><strong>DIRECCIÓN ACADÉMICA</strong></p>
    </div>

    <hr class="divider">

    <!-- Directivos -->
    <h4 class="sub-header">Directivos de la Institución</h4>
    <div class="section">
        <div><strong>{{ $autoridad1->profile_professional }} {{ $autoridad1->fullname }}</strong></div>
        <div>{{ $autoridad1->position }}</div>
        <hr style="border: 1px #ccc solid">
        <div><strong>{{ $autoridad2->profile_professional }} {{ $autoridad2->fullname }}</strong></div>
        <div>{{ $autoridad2->position }}</div>
    </div>

    <hr class="divider">

    <div class="section" style="text-align: center">
        <h3 class="sub-header">Escanea el código QR para descargar el PDF</h3>
        <img src="{{ $qrCode }}" width="128">
    </div>

    <hr class="divider">

    <!-- Pie de página -->
    <footer class="footer">
        AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE, YARACUY, VENEZUELA<br>
        Teléfonos: + 58 424-5891682 || + 58 414-5442298 || + 58 424-5027880 | Correo: colegiofrayluisa@gmail.com || direccionacademica.c.e.cfla@gmail.com || controldeestudios.c.e.cfla@gmail.com
    </footer>

</body>

</html>
